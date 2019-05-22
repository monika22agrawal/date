<?php 
Class User_model extends CI_Model {

	function activeInactive($id){
        $this->db->select('*');  
        $this->db->where('userId',$id);
        $sql = $this->db->get(USERS)->row();
        if($sql->status == 0){
            $this->db->update(USERS,array('status'=> '1'),array('userId'=>$id));
            return array('message'=>'Active');
        }else{
            $this->db->update(USERS,array('status'=> '0'),array('userId'=>$id));
            return array('message'=>'Inactive');
        }
    }

    function indentityProofStatus($id,$status) {

        $this->db->where(array('userId'=>$id))->update(USERS,array('isVerifiedId'=>$status));

        $where = array('userId'=>$id,'isNotification'=>1);
        $user_info_for = $this->common_model->getsingle(USERS,$where);

        if($user_info_for){

            $registrationIds[] = $user_info_for->deviceToken;

            $title = "ID Verification";
            $varifyStatus = $status==1 ? 'approved' : 'disapproved';
            $body_send  = 'Your id verification has been '.$varifyStatus.'.'; //body to be sent with current notification
            $body_save  = 'Your id verification has been '.$varifyStatus.'.';; //body to be saved in DB
            $notif_type = 'id_verification';
            $notify_for = $user_info_for->userId;                
           
            //send notification to user
            $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$status,$notif_type);
        }

        return $status;
    }

    // get my event detail record by eventId
    function myEventDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            e.eventId, e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace,
            IF(e.privacy = 1,"Public","Private") as privacy,IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, e.userLimit,
            (
                case 
                    when( e.eventUserType = "" or e.eventUserType IS NULL)
                        THEN "NA"
                    when( e.eventUserType !="" AND e.eventUserType =1)
                        THEN "Male"
                    when( e.eventUserType !="" AND e.eventUserType =2) 
                        THEN "Female"
                    ELSE 
                        "Both" 
                END 
            ) as eventUserType, u.fullName,
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
                END 
            ) as profileImage
        ');
        $this->db->from(EVENTS.' as e');
        $this->db->join(USERS.' as u','u.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->where(array('eventId'=>$data['eventId']));
        $this->db->group_by('u.userId');
        $query = $this->db->get();
        $result = $query->row();
        return $result;
    } // end of function

    function getAppDetail($appId){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            a.*, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress,
            IF(uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address) as ForAddress,
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId)
                        )
                END
            ) as forImage
    
        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');

        $this->db->where('a.appId',$appId);

        $this->db->group_by('uImg.user_id');
        $this->db->group_by('ufImg.user_id');
        
        $query = $this->db->get();
        $respnse = $query->row();
        return $respnse;
    } 

    function getPaymentDetail($where){

         $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            pt.*,u.fullName,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  /*userImgId not empty get image using userImgId */
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
                END 
            ) as image
        ');
        $this->db->from(PAYMENT_TRANSACTIONS.' as pt');
        $this->db->join(USERS.' as u',"pt.user_id = u.userId");
        $this->db->join(USERS_IMAGE.' as uImg',"pt.user_id = uImg.user_id",'left');
        $this->db->where('id',$where);

        $query = $this->db->get();
        
        if($query->num_rows()){
           $result  = $query->row();
           return $result;
        }else{
            return FALSE; 
        }
    }

    function getPaymentStatusDetail($where){
        $this->db->distinct();
        $this->db->select('paymentType');
        $this->db->where('user_id',$where);
        $this->db->from(PAYMENT_TRANSACTIONS);
        $query = $this->db->get();
        if($query->num_rows()){
           $result  = $query->result();
           return $result;
        }else{
            return FALSE; 
        }
    }

    function getAppointment($data){

        $date = date('Y-m-d H:i:s');

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            a.*, CONCAT( a.appointDate,SPACE(1),appointTime ) as appointDateTime, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            IF(u.address IS NULL or u.address ="" or u.address ="0","NA",u.address) as ByAddress,
            IF(uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address) as ForAddress,
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
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
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId)
                        )
                END
            ) as forImage
        ');

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');

            $this->db->group_start();
                $this->db->where($data['where']);
                $this->db->or_where($data['or_where']);
            $this->db->group_end();

        $this->db->where(array('a.isDelete'=>0));
        $this->db->where(array('CONCAT(a.appointDate,SPACE(1),appointTime) > ' => $date));

        $this->db->group_by('uImg.user_id');
        $this->db->group_by('ufImg.user_id');
        $this->db->group_by('appId');
    }
    
    function countAllApp($data){

        $this->getAppointment($data);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function appList($data){
        $this->getAppointment($data);
        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        //lq();
        if($query->num_rows() >0){

            $userData = $query->result();

            return $userData;
        }
        return array();
    }	

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

    // get user's info from multiple tables
    function usersDetail($userId){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $dImg       = AWS_CDN_USER_PLACEHOLDER_IMG;
        $uImg       = AWS_CDN_USER_LARGE_IMG;
        $imgUrl     = AWS_CDN_USER_IMG_PATH;        
        $result     = new stdClass();

        $this->db->select('
            u.gender, u.otpVerified, u.crd, u.authToken, u.isNotification, u.email, u.userId, u.latitude, u.longitude, u.city, u.state, u.country, u.showOnMap, u.birthday, u.deviceToken, uwm.work_id, uem.edu_id, u.countryCode, u.contactNo, u.mapPayment, u.showTopPayment, u.bizSubscriptionStatus,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  /*userImgId not empty get image using userImgId */
                        (SELECT 
                            (case 
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial =1)
                                    THEN  image
                                ELSE
                                    image
                               END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END ) as imgName,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE
                        (SELECT
                            (case
                                when( image = "" OR  image IS NULL)
                                    THEN "'.$defaultImg.'"
                                when(  image !="" AND isSocial = 1)
                                    THEN  image
                                ELSE
                                    concat("'.$imgUrl.'", image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage,
            (
                case 
                    when( u.relationship = "" or u.relationship IS NULL) 
                        THEN "NA"
                    when( u.relationship !="" AND u.relationship ="1") 
                        THEN "Single"
                    when( u.relationship !="" AND u.relationship ="2") 
                        THEN "Married"
                    when( u.relationship !="" AND u.relationship ="3") 
                        THEN "Divorced"
                    ELSE "Widowed" END ) as relationship,(case 
                    when( u.language = "" or u.language IS NULL)
                        THEN "NA"
                    when( u.language !="") 
                        THEN u.language
                    ELSE 
                        "NA"
                END ) as language, 
                u.age, COALESCE(u.fullName,"") as fullName, COALESCE(u.address,"NA") as address, u.about, u.height, u.weight, u.socialId, u.socialType, u.mapPayment, u.showTopPayment, u.subscriptionId, u.subscriptionStatus, u.bizSubscriptionId, u.bizSubscriptionStatus, u.eventType, u.appointmentType, u.idWithHand, u.isVerifiedId, u.isFaceVerified, u.faceImage, COALESCE(w.name,"NA") as work, COALESCE(edu.education,"NA") as education, COALESCE(GROUP_CONCAT(DISTINCT int.interest),"") as game, COALESCE(GROUP_CONCAT(DISTINCT uim.interest_id),"") as uIntId,COALESCE(user_rating.total_rating, "0") as totalRating');

        $this->db->from(USERS.' as u');

        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');

        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');
        
        $this->db->join(USERS_EDUCATION.' as uem','u.userId = uem.user_id','left');
        $this->db->join(EDUCATION.' as edu','uem.edu_id = edu.eduId','left');
        
        $this->db->join(USERS_INTEREST_MAPPING.' as uim','u.userId = uim.user_id','left');
        $this->db->join(INTERESTS.' as int','uim.interest_id = int.interestId','left');

        //to get user total average rating (Round off the number to nearest integer)
        $user_rating  = '
                    (
                        SELECT
                            ROUND(AVG(rating)) as total_rating, for_user_id 
                        FROM `'.REVIEW.'`
                        GROUP BY 
                            for_user_id
                    ) as user_rating
                    ';
        $this->db->join($user_rating , 'u.userId = user_rating.for_user_id', 'left');

        $this->db->where('u.userId',$userId);
        $this->db->group_by('uImg.user_id');
        $query = $this->db->get();
        
        $result1 = $query->row();

        if($result1){
            $result = $result1;
        }

        if(!empty($userId)){

            // friends count
           
            $friendCount = $this->db->query('SELECT * FROM '.FRIENDS.' WHERE byId = "'.$userId.'" OR forId ="'.$userId.'" ');
             $result->totalFriends = $friendCount->num_rows();
            // like count
            $likeCount = $this->db->query('SELECT * FROM '.LIKES.' WHERE likeUserId ="'.$userId.'" ');
            $result->totalLikes = $likeCount->num_rows();

            // visit count
            $visitCount = $this->db->query('SELECT * FROM '.VISITORS.' WHERE visit_for_id ="'.$userId.'" ');
            $result->totalVisits = $visitCount->num_rows();

            // favorite count
            $favCount = $this->db->query('SELECT * FROM '.FAVORITES.' WHERE user_id ="'.$userId.'" ');
            $result->totalFavorites = $favCount->num_rows();

            // appointment review count
            $appCount = $this->db->query('SELECT * FROM '.REVIEW.' WHERE for_user_id ="'.$userId.'" AND reviewType = 1 ');
            //lq();
            $result->totalAppReview = $appCount->num_rows();

            // event review count
            $eventCount = $this->db->query('SELECT * FROM '.REVIEW.' WHERE for_user_id ="'.$userId.'" AND reviewType = 2 ');
            $result->totalEventReview = $eventCount->num_rows();

            //check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, array('user_id'=>$userId));
            $result->bankAccountStatus = 0;

            if(!empty($userPaymentExist)){
                $result->bankAccountStatus = 1;
            }

            //check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BUSINESS, array('user_id'=>$userId));
            $result->isBusinessAdded = '0';
            if(!empty($userPaymentExist)){
                $result->isBusinessAdded = '1';
            }

            $images = $this->usersImage($userId);
            $result->profileImage = !empty($images) ? $images[0]->image : AWS_CDN_USER_PLACEHOLDER_IMG;
        }        
        
        // for notification isRead 1    
        $notType = array('add_like','add_favorite','friend_request','accept_request');

        $this->db->where('notificationBy',$userId);
        $this->db->where_in('notificationType',$notType);
        $this->db->update(NOTIFICATIONS,array('isRead'=>1));
        //pr($result);
        return $result;
    }

    //get all user image from user_image table
    function usersImage($userId){
        
        $image = array();
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            (
                case
                    when( uImg.image = "" OR uImg.image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1)
                        THEN image
                    ELSE
                        concat("'.$imgUrl.'",image)
                END 
            ) as image, uImg.userImgId,
            (
                case
                    when( image = "" OR image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( image !="" AND isSocial = 1)
                        THEN image
                    ELSE
                        image
                END 
            ) as imgName
        ');

        $this->db->from(USERS_IMAGE.' as uImg');

        $this->db->where('user_id',$userId);
        $this->db->order_by('userImgId','DESC');

        $query  = $this->db->get();
        $image = $query->result();

        return $image;
    }
}