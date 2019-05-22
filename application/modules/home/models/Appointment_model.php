<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_model extends CI_Model {

    // Get latlong from address using curl
    function getGoogleMapTravelMode($mode,$source,$destination,$disLat="",$distLong=""){

        $lat = $this->session->userdata('lat');
        $long = $this->session->userdata('long');

        $formattedSource =  $lat.','.$long;
        //$formattedDestination = str_replace(' ','+',$destination);
        $formattedDestination = $disLat.','.$distLong;

        $url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$formattedSource.'&destination='.$formattedDestination.'&avoid=highways&mode='.$mode.'&key='.GOOGLE_API_KEY.'';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($response);
       
        if($output->status == 'OK'){
            
            $data['time']  = $output->routes[0]->legs[0]->duration->text; 
            return $data;
            
        }else{
            return false;   
        }

    } //Enf Function

    // to get customer id from users table 
    function getStripeCustomerId(){

        $getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));
        if($getCusId){
            return $getCusId->stripeCustomerId;
        }else{
            return FALSE;
        }

    } // End of function

    // to save customer id in db
    function saveCustomerId($customer_id){

        $where = array('userId'=>$this->session->userdata('userId'));
        $isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

        if($isUpdated){
            return TRUE;
        }

    } // End of function

    // get business detail
    function getBusinessList(){

        // for miles 6371 & for km 3959
        $km = 50;

        $defaultImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_BIZ_THUMB_IMG;

        if(!empty($this->session->userdata('lat'))){
            $latitude = $this->session->userdata('lat');
            $longitude = $this->session->userdata('long');
        }else{
            $latitude = '22.7196';
            $longitude = '75.8577';
        }

        $this->db->select(
            'biz.businessId, biz.businessName, biz.businessAddress, biz.businesslat, biz.businesslong,            
            biz.businessImage, u.userId, u.bizSubscriptionId, u.bizSubscriptionStatus, ( 6371 * acos( cos( radians( '.$latitude.'  ) ) * cos( radians( biz.businesslat ) ) * cos( radians( biz.businesslong ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( biz.businesslat ) ) ) ) AS distance'); 

        $this->db->from(BUSINESS.' as biz');

        $this->db->join(USERS.' as u','u.userId = biz.user_id');
        //$this->db->where(array('u.bizSubscriptionStatus' => '1'));
        $this->db->having('distance <= ' . $km);

        $req = $this->db->get();

        if($req->num_rows()){

            return $req->result();
        }
        return FALSE;
    }

    function getAppointmentList($where,$limit,$start,$userId,$data){

        $date = date('Y-m-d H:i:s');
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select(
            'a.*,CONCAT(a.appointDate,SPACE(1),a.appointTime) as appointDateTime,u.fullName as ByName,uf.fullName as ForName,u.gender as ByGender,uf.gender as ForGender,IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress,IF(uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address) as ForAddress,u.latitude as ByLatitude,uf.latitude as ForLatitude,u.longitude as ByLongitude,uf.longitude as ForLongitude,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE 
                        (SELECT 
                            (
                                case 
                                    when( image = "" OR  image IS NULL)
                                        THEN "'.$defaultImg.'"
                                    when(  image !="" AND isSocial =1)
                                        THEN  image
                                    ELSE
                                        image
                                END 
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as byImage,
            (
                case
                    when( MIN(ufImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (
                                case 
                                    when( image = "" OR  image IS NULL)
                                        THEN "'.$defaultImg.'"
                                    when(  image !="" AND isSocial =1)
                                        THEN  image
                                    ELSE
                                        image
                                END 
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId))
                END 
            ) as forImage
        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');

        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');

        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');
       
        if(($data['type'] == 'appointForId') || ($data['type'] == 'appointById')){

            $this->db->group_start();
                $this->db->where($where);

                $this->db->where(array('a.isFinish'=>0));

            $this->db->group_end();

            $this->db->group_start();

                $this->db->group_start();
                    $this->db->where(array('appointForId'=>$userId));
                    $this->db->where_not_in('appointmentStatus', array('3','5'));
                $this->db->group_end();

                $this->db->or_group_start();
                    $this->db->where(array('appointById'=>$userId));
                $this->db->group_end();

            $this->db->group_end();

        }elseif($data['type'] == 'all'){
          
            $this->db->group_start();
                $this->db->where(array('a.isDelete'=>0,'appointById'=>$userId));
                $this->db->or_where(array('a.isDelete'=>0,'appointForId'=>$userId));
            $this->db->group_end();

            $this->db->group_start();
                $this->db->where(array('a.isDelete'=>0,'appointForId'=>$userId));
                $this->db->where_not_in('appointmentStatus', array('3','5'));
            $this->db->group_end();

            $this->db->or_group_start();
                $this->db->where(array('a.isDelete'=>0,'appointById'=>$userId));
            $this->db->group_end();

        }else{
         
            $this->db->group_start();
                $this->db->where(array('appointById'=>$userId));
                $this->db->or_where(array('appointForId'=>$userId));
            $this->db->group_end();
            $this->db->where(array('a.isDelete'=>0,'a.isFinish'=>1));
        }   
        
        //$this->db->where(array('CONCAT(a.appointDate,SPACE(1),a.appointTime) > '=>$date));

        $this->db->group_by('appId');

        $this->db->group_by('uImg.user_id');

        $this->db->group_by('ufImg.user_id');

        $this->db->order_by('appId','DESC');
    }

    // get list for pagination
    function getAppointmentListPage($where,$limit,$start,$userId,$data){

        $this->getAppointmentList($where,$limit,$start,$userId,$data);

        $this->db->limit($limit,$start);

        $query = $this->db->get();

        if($query->num_rows() > 0){

            $result = $query->result();

            // for notification isRead 1
            $notType = array('create_appointment','delete_appointment');

            //$this->db->where('notificationBy',$userId);
            $this->db->where('notificationFor',$userId);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            
            return $result;
        }
        return array();

    } // end of function

    // count of all event's
    function countAllgetAppointmentList($where,$limit,$start,$userId,$data){

        $this->getAppointmentList($where,$limit,$start,$userId,$data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

    function getAppData($appId,$userId){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_THUMB_IMG;

        $this->db->select('

            a.*, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress, IF(uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address) as ForAddress, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,

            (case
                when( MIN(uImg.userImgId) IS NULL)
                    THEN "'.$defaultImg.'"
                ELSE  
                    (SELECT (case
                        when( image = "" OR  image IS NULL)
                            THEN "'.$defaultImg.'"
                        when(  image !="" AND isSocial = 1)
                            THEN  image
                        ELSE
                            image
                    END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                    
            END ) as byImage,

            (case
                when( MIN(ufImg.userImgId) IS NULL)
                    THEN "'.$defaultImg.'"
                ELSE  
                    (SELECT (case 
                        when( image = "" OR  image IS NULL)
                            THEN "'.$defaultImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            image
                    END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId))

            END ) as forImage,

            IF(biz.businessId IS NULL or biz.businessId ="" or biz.businessId ="0","",biz.businessId) as businessId,

            IF(biz.businessName IS NULL or biz.businessName ="" or biz.businessName ="0","",biz.businessName) as businessName, 

            IF(biz.businessAddress IS NULL or biz.businessAddress ="" or biz.businessAddress ="0","",biz.businessAddress) as businessAddress, 

            IF(biz.businesslat IS NULL or biz.businesslat ="" or biz.businesslat ="0","",biz.businesslat) as businesslat, 

            IF(biz.businesslong IS NULL or biz.businesslong ="" or biz.businesslong ="0","",biz.businesslong) as businesslong, 

            biz.businessImage,

            IF(reviewBy.by_user_id IS NULL or reviewBy.by_user_id ="" or reviewBy.by_user_id ="0","", reviewBy.by_user_id) as reviewByUserId,

            IF(reviewBy.rating IS NULL or reviewBy.rating ="" or reviewBy.rating ="0","",reviewBy.rating) as reviewByRating,

            IF(reviewBy.comment IS NULL or reviewBy.comment ="" or reviewBy.comment ="0","",reviewBy.comment) as reviewByComment,

            IF(reviewBy.crd IS NULL or reviewBy.crd ="" or reviewBy.crd ="0","",reviewBy.crd) as reviewByCreatedDate,

            IF(reviewFor.for_user_id IS NULL or reviewFor.for_user_id ="" or reviewFor.for_user_id ="0","",reviewFor.by_user_id) as reviewForUserId,

            IF(reviewFor.rating IS NULL or reviewFor.rating ="" or reviewFor.rating ="0","",reviewFor.rating) as reviewForRating,

            IF(reviewFor.comment IS NULL or reviewFor.comment ="" or reviewFor.comment ="0","",reviewFor.comment) as reviewForComment,

            IF(reviewFor.crd IS NULL or reviewFor.crd ="" or reviewFor.crd ="0","",reviewFor.crd) as reviewForCreatedDate
        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');
        $this->db->join(BUSINESS.' as biz','biz.businessId = a.business_id','left');
        $this->db->join(REVIEW.' as reviewBy','reviewBy.by_user_id = a.appointById AND reviewBy.referenceId = "'.$appId.'"','left');
        $this->db->join(REVIEW.' as reviewFor','reviewFor.by_user_id = a.appointForId AND reviewFor.referenceId = "'.$appId.'"','left');

        $this->db->where(array('a.appId'=>$appId));

        $this->db->group_by('uImg.user_id');
        $this->db->group_by('ufImg.user_id');
        
        $query = $this->db->get();
        $respnse = $query->row();

        // for notification isRead 1
        $notType = array('confirmed_appointment','finish_appointment');

        //$this->db->where('notificationBy',$userId);
        $this->db->where('notificationFor',$userId);
        $this->db->where_in('notificationType',$notType);
        $this->db->update(NOTIFICATIONS,array('isRead'=>1));

        return $respnse;
    }

} // End of class