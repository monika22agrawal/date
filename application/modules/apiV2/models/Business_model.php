<?php 
class Business_model extends CI_Model {

	
	// to get customer id from users table 
    function getStripeCustomerId(){

        $getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->authData->userId));
        if($getCusId){
            return $getCusId->stripeCustomerId;
        }else{
            return FALSE;
        }

    } // End of function

    // to save customer id in db
    function saveCustomerId($customer_id){

        $where = array('userId'=>$this->authData->userId);
        $isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

        if($isUpdated){
            return TRUE;
        }

    } // End of function

    // to save subscription payment detail in db 
    function savePaymentDetails($payment_data){

        $isInsert = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$payment_data);
        if($isInsert){
            return TRUE;
        }else{
            return FALSE;
        }

    } // End of function

    // if subdcription is cancelled then set blank value in subscription id and status cancel
    function getSubscriptionId(){

        $isSubsId = $this->common_model->getsingle(USERS,array('userId'=>$this->authData->userId));

        return !empty($isSubsId) ? $isSubsId->bizSubscriptionId : FALSE;
        
    } // End of function


    // get business detail
    function getBusinessDetail($userId){

        $defaultImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_BIZ_IMG_PATH;

        $this->db->select(
            'biz.businessId,biz.businessName,biz.businessAddress,biz.businesslat,biz.businesslong,            
            (case 
            when( biz.businessImage = "" OR biz.businessImage IS NULL) 
            THEN "'.$defaultImg.'"
            ELSE
            concat("'.$imgUrl.'",biz.businessImage) 
            END ) as businessImage,
            u.userId,
            u.bizSubscriptionId,
            u.bizSubscriptionStatus');

        $this->db->from(BUSINESS.' as biz');

        $this->db->join(USERS.' as u','u.userId = biz.user_id');

        $this->db->where(array('biz.user_id' => $userId));

        $req = $this->db->get();

        if($req->num_rows()){

            return $req->row();
        }
        return FALSE;
    }

     // get business detail
    function getBusinessList($data){

        // for miles 6371 & for km 3959
        $km = 50;

        $defaultImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_BIZ_IMG_PATH;

        $this->db->select(
            'biz.businessId,biz.businessName,biz.businessAddress,biz.businesslat,biz.businesslong,            
            (case 
            when( biz.businessImage = "" OR biz.businessImage IS NULL) 
            THEN "'.$defaultImg.'"
            ELSE
            concat("'.$imgUrl.'",biz.businessImage) 
            END ) as businessImage,
            u.userId,
            u.bizSubscriptionId,
            u.bizSubscriptionStatus, ( 6371 * acos( cos( radians( '.$data['latitude'].'  ) ) * cos( radians( biz.businesslat ) ) * cos( radians( biz.businesslong ) - radians('.$data['longitude'].') ) + sin( radians('.$data['latitude'].') ) * sin( radians( biz.businesslat ) ) ) ) AS distance'); 

        $this->db->from(BUSINESS.' as biz');

        $this->db->join(USERS.' as u','u.userId = biz.user_id');
        $this->db->where(array('u.bizSubscriptionId !=' => ' '));
        $this->db->having('distance <= ' . $km);

        $req = $this->db->get();

        if($req->num_rows()){

            return $req->result();
        }
        return FALSE;
    }
       
} //end of class

       