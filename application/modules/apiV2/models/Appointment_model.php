<?php 
class Appointment_model extends CI_Model {

    function getAppointmentList($appointById,$appointForId,$where,$offset,$limit,$listType = ''){

        $date = date('Y-m-d H:i:s');
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            
            a.*, CONCAT(a.appointDate,SPACE(1),a.appointTime) as appointDateTime, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress, IF(uf.address IS NULL or uf.address = "" or uf.address ="0","NA",uf.address) as ForAddress, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                            ELSE
                                concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as byImage,
            (
                case
                    when( MIN(ufImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                            ELSE
                                concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId))
                END 
            ) as forImage

        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');
        
        if($where && $listType != 'all'){

            $this->db->where($where);
            $this->db->where(array('a.isDelete'=>0,'a.isFinish'=>0));

        }elseif($listType == 'all'){

            $this->db->group_start();
                $this->db->where(array('a.appointById'    =>$this->authData->userId));
                $this->db->or_where(array('a.appointForId'=>$this->authData->userId));
            $this->db->group_end();

        }elseif($listType == 'finished'){
           
            $this->db->group_start();
                $this->db->where(array('appointById'=>$this->authData->userId));
                $this->db->or_where(array('appointForId'=>$this->authData->userId));
            $this->db->group_end();
            $this->db->where(array('a.isFinish'=>1));

        }else{

            $this->db->where($where);
        } 

        if($listType == 'all' || $listType == 'received'){

            $this->db->where(array('a.appointmentStatus !='=> 5));
        }      
        
        $this->db->where(array('CONCAT(a.appointDate,SPACE(1),a.appointTime) > '=>$date));

        $this->db->limit($limit, $offset);

        $this->db->group_by('appId');
        $this->db->group_by('uImg.user_id');
        $this->db->group_by('ufImg.user_id');

        $this->db->order_by('appId','DESC');

        $query = $this->db->get();
        $result = $query->result();

        // for notification isRead 1
        $notType = array('create_appointment','delete_appointment');

        $this->db->where('notificationBy',$this->authData->userId);
        $this->db->or_where('notificationFor',$this->authData->userId);
        $this->db->where_in('notificationType',$notType);
        $this->db->update(NOTIFICATIONS,array('isRead'=>1));

        return $result;
    }

    function getAppData($appId,$userId){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_IMG_PATH;

        $this->db->select('
            a.*, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, 
            IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress, 
            IF(uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address) as ForAddress,
            u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                                ELSE
                                    concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as byImage,
            (
                case
                    when( MIN(ufImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                                ELSE
                                    concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId))
                END 
            ) as forImage,
            IF(biz.businessId IS NULL or biz.businessId ="" or biz.businessId ="0","",biz.businessId) as businessId,
            IF(biz.businessName IS NULL or biz.businessName ="" or biz.businessName ="0","",biz.businessName) as businessName,
            IF(biz.businessAddress IS NULL or biz.businessAddress ="" or biz.businessAddress ="0","",biz.businessAddress) as businessAddress,
            IF(biz.businesslat IS NULL or biz.businesslat ="" or biz.businesslat ="0","",biz.businesslat) as businesslat,
            IF(biz.businesslong IS NULL or biz.businesslong ="" or biz.businesslong ="0","",biz.businesslong) as businesslong,            
            (
                case 
                    when( biz.businessImage = "" OR biz.businessImage IS NULL) 
                        THEN "'.$defaultImgBiz.'"
                    ELSE
                        concat("'.$imgUrlBiz.'",biz.businessImage) 
                END 
            ) as businessImage,
            IF(reviewBy.by_user_id IS NULL or reviewBy.by_user_id ="" or reviewBy.by_user_id ="0","",reviewBy.by_user_id) as reviewByUserId,
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

        $this->db->where(array('a.appId'=>$appId,'a.isDelete'=>0));
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

    // for getting bank account detail of event's and appointment organiser
    function getBankAccountDetail($userId){

        $where = array('user_id'=>$userId);
        $getRespose = $this->common_model->getsingle(BANK_ACCOUNT_DETAILS,$where);       
        return $getRespose;   
    }// End function

    function getByDeviceToken($appId,$userId){

        $this->db->select('appointById');
        $this->db->from(APPOINTMENTS);
        $this->db->where(array('appId'=>$appId,'appointForId'=>$userId));
        $req = $this->db->get();

        if($req->num_rows()){

            $user_id = $req->row()->appointById;

            $where = array('userId'=>$user_id,'isNotification'=>1);
            $user_info = $this->common_model->getsingle(USERS,$where);
           
            return $user_info;
        }
        return false;
    }

    function getDeviceToken($appId,$userId){

        $this->db->select('IF(appointForId = "'.$userId.'",appointById,appointForId) as user_id');
        $this->db->from(APPOINTMENTS);
        $this->db->where('appId',$appId);
        $req = $this->db->get();

        if($req->num_rows()){
            $user_id = $req->row()->user_id;
            $where = array('userId'=>$user_id,'isNotification'=>1);
            $user_info = $this->common_model->getsingle(USERS,$where);
           
            return $user_info;
        }
        return false;
    }
}

       