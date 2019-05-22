<?php 
class Event_model extends CI_Model {

    function createEvent($eventData,$friendId,$image){
        
        $eventId = $this->common_model->insertData(EVENTS,$eventData);

        $insertBatch = array();
        if($eventId){
            $where = array('eventId'=>$eventId);
            $eventData = $this->common_model->getsingle(EVENTS,$where);

            foreach ($friendId as $value) {
                
                $insertBatch[] = array(
                    'event_id' => $eventData->eventId,
                    'memberId' => $value,
                    'memberType' => 0
                );
            }
            if($eventData->eventOrganizer == $this->authData->userId){
                $insertBatch[] = array(
                    'event_id' => $eventData->eventId,
                    'memberId' => $this->authData->userId,
                    'memberType' => 1
                );
            }

            $imgData = array();
            if(is_array($image) && !empty($image)){
                
                $i = 0;
                foreach($image as $val) {
                   
                    $imgData[$i]['eventImage'] = $val;
                    $imgData[$i]['user_id'] = $this->authData->userId;                  
                    $imgData[$i]['event_id'] = $eventData->eventId;                  

                    $this->db->insert(EVENT_IMAGE,$imgData[$i]);
                    
                    $i++;
                }
            }
            $this->db->insert_batch(EVENT_MEMBER,$insertBatch);
            return $eventId;
        }
        return FALSE;  

    } // End Of Function

    function shareEvent($userId, $eventId,$friendId){

        $checkWhere = array('event_id'=>$eventId,'memberId'=>$userId);
        $getEventData = $this->common_model->getsingle(EVENT_MEMBER,$checkWhere);

        if($getEventData){
            $insertBatch = array();

            foreach ($friendId as $value) {
                
                $where = array('eventMem_Id' => $getEventData->eventMemId,'companionMemId' => $value);
                //check data exist
                $eventMemExist = $this->common_model->is_data_exists(COMPANION_MEMBER, $where);

                if(!$eventMemExist){
                    $insertBatch = array(
                        'eventMem_Id' => $getEventData->eventMemId,
                        'companionMemId' => $value,
                        'event_id' => $eventId,
                        'upd'=> date('Y-m-d H:i:s')
                    );
                    $this->db->insert(COMPANION_MEMBER,$insertBatch);
                }
            }
            return $getEventData->eventMemId;       
        }
        return FALSE;  

    } // End Of Function

    function updateEvent($eventData,$eventId,$userId,$friendId){

        $updateWhere = array('eventId'=>$eventId,'eventOrganizer'=>$userId);
        $isUpdated = $this->common_model->updateFields(EVENTS, $eventData,$updateWhere);

        // delete users who are not exist in friends id
        $checkWhere = array('event_id'=>$eventId,'memberType' => 0);
        $this->db->where($checkWhere);
        $this->db->where_not_in('memberId',$friendId);
        $this->db->delete(EVENT_MEMBER);

        // get all members id according to event
        $this->db->select('memberId');
        $this->db->where(array('event_id'=>$eventId,'memberType' => 0));
        $sql = $this->db->get(EVENT_MEMBER);

        $insertBatch = array();

        if($sql->num_rows()){ 

            $memIds = $sql->result();

            $myArr = array();

            foreach ($memIds as $value) {
                $myArr[] = $value->memberId;        // stored members id in array      
            }

            $ids = array_diff($friendId,$myArr);    // find member id which are diff in both array ($friendId / $myArr)

            foreach ($ids as $value) {              // insert ids
                
                $insertBatch[]  = array(
                    'event_id'  => $eventId,
                    'memberId'  => $value,
                    'memberType' => 0
                );
            }
            if($insertBatch)
                $this->db->insert_batch(EVENT_MEMBER,$insertBatch);

        }else{

            foreach ($friendId as $value) {
                
                $insertBatch[]  = array(
                    'event_id'  => $eventId,
                    'memberId'  => $value,
                    'memberType' => 0
                );
            }

            $this->db->insert_batch(EVENT_MEMBER,$insertBatch);
        }    

        return TRUE;
    }
    
    //add single image for an event
    function addEventImage($event_id, $event_image){
        $data['event_id'] = $event_id;
        $data['eventImage'] = $event_image;
        $data['user_id'] = $this->authData->userId;
        return $this->common_model->insertData(EVENT_IMAGE, $data);
    }
    
    // get all my event's record
    function getEventImageDetail($where){

        $defaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_EVENT_IMG_PATH; 
        
        $this->db->select('eImg.eventImgId, 
            (CASE 
            when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
            THEN "'.$defaultImg.'"
            ELSE
            concat("'.$imgUrl.'", eImg.eventImage) 
            END ) as eventImage');
        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_IMAGE.' as eImg','e.eventId = eImg.event_id');
        $this->db->where($where);
        $this->db->group_by('eImg.eventImgId');
        $query = $this->db->get();
        return $query->row();
        
    } // end of function
    
    
    function getInvitationUserList($event_data, $search){
        
        $gender = $event_data['userGender'] ? explode(',',$event_data['userGender']) : '';

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl     = AWS_CDN_USER_IMG_PATH;
        $current_user_id = $this->authData->userId;
        
        // for miles 6371 & for km 3959
        $km = 50; //raduis of 50km
        
        $select_dist = '';
        /*if(isset($search['latitude']) && isset($search['longitude'])){
            $select_dist = '( 
                6371 * acos( cos( radians( '.$search['latitude'].'  ) ) * cos( radians( u.latitude ) ) * 
                cos( radians( u.longitude ) - radians('.$search['longitude'].') ) + 
                sin( radians('.$search['latitude'].') ) * sin( radians( u.latitude ) ) )
            ) AS distance,';
            $this->db->having('distance <= ' . $km); //location radius
            $this->db->order_by('distance','ASC');
        }*/
        
        $this->db->select(
            'u.userId, u.fullName, u.age, u.gender, u.address, u.latitude, u.longitude, u.gender, u.eventInvitation,
            COALESCE(user_rating.total_rating, "0") as total_rating, '. $select_dist .'
            (
                CASE
                    WHEN( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  /*userImgId not empty get image using userImgId */
                        (
                            SELECT 
                                (
                                    CASE 
                                        WHEN( image = "" OR  image IS NULL)
                                            THEN "'.$defaultImg.'"
                                        WHEN(  image !="" AND isSocial =1)
                                            THEN  image
                                        ELSE
                                            concat( "'.$imgUrl.'", image )
                                    END 
                                ) as image 
                            FROM '.USERS_IMAGE.' 
                            WHERE 
                                userImgId = MAX( uImg.userImgId )
                        )
                END 
            ) as profileImage, (case 
                                    when(em.memberStatus = 1)
                                        THEN 1
                                    when(em.memberStatus = 2)
                                        THEN 2
                                    when(comp.companionMemberStatus = 1)
                                        THEN 1
                                    when(comp.companionMemberStatus = 2)
                                        THEN 2
                                    ELSE
                                        ""
                                END ) as memberStatus'
        );

        $this->db->from(USERS.' as u');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = u.userId ','left');
        $this->db->join(EVENT_MEMBER.' as em','em.memberId = u.userId AND em.event_id = "'.$event_data['eventId'].'"','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.companionMemId = u.userId AND comp.event_id = "'.$event_data['eventId'].'"','left');
        $this->db->where('userId!=', $current_user_id); //ignore current user
        (!empty($gender)) ? $this->db->where_in('u.gender', $gender) : ""; //filter by gender
        
        //filter by event privacy
        if(isset($event_data['privacy'])){

            $this->db->group_start();
                $this->db->where('u.eventInvitation', $event_data['privacy']);
                $this->db->or_where('u.eventInvitation', 3); //3:Both (public and private)
            $this->db->group_end();
        }
        
        isset($search['name']) ? $this->db->like('u.fullName', $search['name'], 'both') : ""; //search by name

        if(!empty($search['address'])){
            $this->db->group_start();
           
                $this->db->like('u.address', $search['address']);
               
                //ORDER BY FIELD VALUE
                $loc = $search['address'];
                
                $this->db->order_by("CASE WHEN u.address LIKE '".$this->db->escape_like_str($loc)."%' THEN 1 ELSE 0 END", "DESC", FALSE);
               
                //$this->db->where('1', '1');
                if(!empty($search['city'])){
                    $this->db->or_like('u.city', $search['city'], 'both');
                    //$this->db->or_like('u.address', $search['city'], 'both');
                    $c = $search['city'];
                    $this->db->order_by("CASE WHEN u.city LIKE '".$this->db->escape_like_str($c)."%' THEN 1 ELSE 0 END", "DESC", FALSE);
                }

                if(!empty($search['state'])){
                    $s = $search['state'];
                    $this->db->or_like('u.state', $search['state'], 'both');
                    $this->db->order_by("CASE WHEN u.state LIKE '".$this->db->escape_like_str($s)."%' THEN 1 ELSE 0 END", "DESC", FALSE);
                }

                if(!empty($search['country'])){
                    $con = $search['country'];
                    $this->db->or_like('u.country', $search['country'], 'both');
                    $this->db->order_by("CASE WHEN u.country LIKE '".$this->db->escape_like_str($con)."%'  THEN 1 ELSE 0 END", "DESC", FALSE);
                }
            $this->db->group_end();
        }
        
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
            
        if(isset($search['rating'])){
            $rating_arr = explode(",",$search['rating']);
            $this->db->where_in('user_rating.total_rating', $rating_arr);
        }
        
        $this->db->group_by('u.userId');
        $this->db->order_by('u.userId','desc');
        $this->db->limit($event_data['limit'], $event_data['offset']);
        $query = $this->db->get();
        //lq();
        return $query->result();
    }

    // background notifications for create event notifications
    function createEventBgNotification($eventId,$userId,$eventName,$userName){

        $memberId = $this->common_model->get_records_by_id(EVENT_MEMBER,'',array('event_id'=>$eventId,'memberId !='=>$userId),'memberId, eventMemId','','' );

        if($memberId){

            $title = lang('new_event_title');
            $body_send  = $userName.lang('event_invite_mem').$eventName.'.'; //body to be sent with current notification
            $body_save  = '[UNAME]'.lang('event_invite_mem').'[ENAME].'; //body to be saved in DB
            $notif_type = 'create_event';            
           
            $companionId= '';

            // save notification for multiple users
            foreach ($memberId as $memId) { 

                $where = array('userId'=>$memId['memberId'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);
                if($user_info_for){
                    $deviceToken = $user_info_for->deviceToken;  // getting multiple users device token   

                    //send notification to user

                    $this->notification_model->send_push_notification_for_event(array($deviceToken), $title, $body_send,$eventId,$companionId,$memId['eventMemId'], $notif_type,$userId);        

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$companionId,'eventMemId'=>$memId['eventMemId'],'createrId'=>$userId);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$memId['memberId'], 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                    $notification_where = array('notificationFor'=>$memId['memberId'],'notificationBy'=>$userId,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }
            }            
        }

    } // end of function


    // get all my event's record
    function myEventList($data){

        $defaultImg = base_url().AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_EVENT_IMG_PATH; 
        
        $this->db->select(' 
            e.eventId, e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace, e.eventLatitude, e.eventLongitude, IF(e.privacy = "1","Public","Private") as privacy,IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode,
            (
                case 
                    when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                        THEN "'.$defaultImg.'"
                    ELSE
                        concat("'.$imgUrl.'",eImg.eventImage) 
                END 
            ) as eventImage,
            (
                case 
                    when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                        THEN "'.$defaultImg.'"
                    ELSE
                        eImg.eventImage
                END 
            ) as eventImageName, count(em.memberId) as joinMemCount 
        ');

        $this->db->from(EVENTS.' as e');

        $this->db->join(EVENT_IMAGE.' as eImg','e.eventId = eImg.event_id','left');
        $this->db->join(EVENT_MEMBER.' as em','e.eventId = em.event_id AND em.memberStatus!=0','left');

        $this->db->where(array('e.eventOrganizer'=>$data['userId'],'e.status'=>1));
        (!empty($data['searchText'])) ? $this->db->like('eventName',$data['searchText'],'after') : '';

        $this->db->order_by('eventId','DESC');
        $this->db->group_by('e.eventId');
        
    } // end of function

    // get list for pagination
    function myEventListCount($data){

        $this->myEventList($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();

            // for notification isRead 1
            $notType = array('create_event');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
       
            return $userData;
        }
        return array();

    } // end of function


    // get all event's request record
    function eventRequestList($data){
        
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH; 

        $eventDefaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $eventImgUrl = AWS_CDN_EVENT_IMG_PATH; 

        $user_id = $data['userId']; 
        // 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request,6=Request cancel
        $this->db->select('
            COALESCE(comp.compId,"") as compId, em.eventMemId, em.memberId,
            IF(em.memberId = '.$user_id.',
                (
                    case 
                        when( em.memberStatus = 1 && e.payment = 1 )
                            THEN 1
                        when( em.memberStatus = 2 && e.payment = 1 ) 
                            THEN 2
                        when( em.memberStatus = 1 && e.payment = 2 ) 
                            THEN 3
                        when( em.memberStatus = 3) 
                            THEN 4
                        ELSE
                            5
                    END 
                ),
                (
                    case 
                        when( comp.companionMemberStatus = 1 && e.payment = 1 )
                            THEN 1
                        when( comp.companionMemberStatus = 2 && e.payment = 1 ) 
                            THEN 2
                        when( comp.companionMemberStatus = 1 && e.payment = 2 ) 
                            THEN 3
                        when( comp.companionMemberStatus = 3 ) 
                            THEN 4
                        when( comp.companionMemberStatus = 4 ) 
                            THEN 6
                        ELSE
                            5
                    END 
                )
            ) as memberStatus, e.eventId, e.crd,e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace, 
            IF(e.privacy = 1,"Public","Private") as privacy,IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, e.groupChat, 
            IF(em.memberId = '.$user_id.',u1.fullName,u2.fullName) as fullName,IF(em.memberId = '.$user_id.',u1.userId,u2.userId) as userId,
                (
                    case 
                        when( comp.companionMemId = '.$user_id.')
                            THEN "Shared Event"
                        when( em.memberId = '.$user_id.')
                            THEN "Administrator"
                        END
                ) as ownerType,
                (
                    case 

                        when ( em.memberId = '.$user_id.' && uImg1.image = "" ) || ( comp.companionMemId = '.$user_id.' && uImg2.image = "" ) 
                            THEN "'.$defaultImg.'"
                    
                        when ( em.memberId = '.$user_id.' && uImg1.image != "" && uImg1.isSocial = 1 ) || ( comp.companionMemId = '.$user_id.' && uImg2.image != "" && uImg2.isSocial = 1 )

                            THEN IF( em.memberId = '.$user_id.',uImg1.image,uImg2.image )

                        when ( em.memberId = '.$user_id.' && uImg1.image != "" && uImg1.isSocial = 0 ) || ( comp.companionMemId = '.$user_id.' && uImg2.image != "" && uImg2.isSocial = 0 )

                            THEN IF( em.memberId = '.$user_id.',concat("'.$imgUrl.'",uImg1.image ),concat( "'.$imgUrl.'",uImg2.image ) )
                        ELSE
                            "'.$defaultImg.'"
                    END

                ) as profileImage,
                (
                    case 

                        when ( em.memberId = '.$user_id.' && uImg1.image = "" ) || ( comp.companionMemId = '.$user_id.' && uImg2.image = "" ) 
                            THEN "'.$defaultImg.'"
                    
                        when ( em.memberId = '.$user_id.' && uImg1.image != "" && uImg1.isSocial = 1 ) || ( comp.companionMemId = '.$user_id.' && uImg2.image != "" && uImg2.isSocial = 1 )

                            THEN IF( em.memberId = '.$user_id.',uImg1.image,uImg2.image )

                        when ( em.memberId = '.$user_id.' && uImg1.image != "" && uImg1.isSocial = 0 ) || ( comp.companionMemId = '.$user_id.' && uImg2.image != "" && uImg2.isSocial = 0 )

                            THEN IF( em.memberId = '.$user_id.',uImg1.image ,uImg2.image )
                        ELSE
                            "'.$defaultImg.'"
                    END

                ) as webProfileImage,
                (
                    case 
                        when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                            THEN "'.$eventDefaultImg.'"
                        ELSE
                            concat("'.$eventImgUrl.'",eImg.eventImage) 
                    END ) as eventImage,
                (
                    case 
                        when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                            THEN "'.$eventDefaultImg.'"
                        ELSE
                            eImg.eventImage
                    END 

                ) as eventImageName
            ');

        $this->db->from(EVENT_MEMBER.' as em');

        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');
        $this->db->join(EVENT_IMAGE.' as eImg','e.eventId = eImg.event_id','left');
        $this->db->join(USERS.' as u1','u1.userId = e.eventOrganizer','left');
        $this->db->join(USERS.' as u2','u2.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg1','u1.userId = uImg1.user_id','left');
        $this->db->join(USERS_IMAGE.' as uImg2','u2.userId = uImg2.user_id','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = em.eventMemId AND comp.companionMemberStatus != 4','left');

        $this->db->where(array('em.membertype !='=> 1,'em.memberStatus !='=> 3,'e.status'=>1));

        (!empty($data['searchText'])) ? $this->db->like('e.eventName',$data['searchText'],'after') : '';

            $this->db->group_start();
                $this->db->where(array( 'comp.companionMemId' => $data['userId']));
                $this->db->or_where(array('em.memberId'=> $data['userId']));
            $this->db->group_end();

        $this->db->order_by('e.eventId','DESC');
        $this->db->group_by('em.eventMemId');
        $this->db->group_by('eImg.event_id');

    } // end of function

    // get list for pagination
    function eventRequestListCount($data){

        $this->eventRequestList($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();

        if($query->num_rows() >0){

            $userData = $query->result();

            // for notification isRead 1
            $notType = array('create_event');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return $userData;
        }
        return array();

    } // end of function


    // get my event detail record by eventId
    function myEventDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl     = AWS_CDN_USER_IMG_PATH;

        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_IMG_PATH;

        $user_id = $data['userId'];

        $this->db->select('
            e.eventId, e.groupChat, e.eventLatitude, e.eventLongitude, e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace, IF(e.privacy = 1,"Public","Private") as privacy,IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, e.userLimit, e.business_id, COALESCE(user_rating.total_rating, "0") as organizerRating,
            e.eventUserType, u.fullName,
            ( 
                case 
                    when( uImg.image = "" OR uImg.image IS NULL) 
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1) 
                        THEN uImg.image
                    ELSE
                        concat("'.$imgUrl.'",uImg.image) 
                END ) as profileImage,
            ( 
                case 
                    when( uImg.image = "" OR uImg.image IS NULL) 
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1) 
                        THEN uImg.image
                    ELSE
                        uImg.image 
                END ) as profileImageName, 
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
                END ) as businessImage, 
            (
                case 
                    when( biz.businessImage = "" OR biz.businessImage IS NULL) 
                        THEN "'.$defaultImgBiz.'"
                    ELSE
                        concat("'.$imgUrlBiz.'",biz.businessImage) 
                END ) as businessImageName, COALESCE(GROUP_CONCAT(DISTINCT em.memberId),"") as memberIds');

        $this->db->from(EVENTS.' as e');

        $this->db->join(USERS.' as u','u.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(EVENT_MEMBER.' as em','e.eventId = em.event_id AND memberType=0','left');
        $this->db->join(BUSINESS.' as biz','biz.businessId = e.business_id','left');

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
        $this->db->join($user_rating , 'e.eventOrganizer = user_rating.for_user_id', 'left');

        $this->db->where(array('e.eventId'=>$data['eventId'],'e.status'=>1));
        $this->db->group_by('u.userId');

        $query = $this->db->get(); 
        $result = $query->row();

        $data['type'] = 'myEvent';

        $result->joinedMemberCount = $this->countAllJoinedMember($data);
        $result->invitedMemberCount = $this->countAllInvitedMember($data);

        $joinedMember = $this->joinedMember($data);
        $invitedMember = $this->invitedMember($data);      

        if($result){

            $result->reviewStatus = $this->getReview($user_id);
            $result->eventImage = $this->eventsImage($data['eventId']);
            $eventReview = $this->getReviewsList($user_id,'0','100','2',$data['eventId']);    
                
            $result->eventReviewCount = count($eventReview);
            // for notification isRead 1
            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');
            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));           
            return array('detail'=>$result,'joinedMember'=>$joinedMember,'invitedMember'=>$invitedMember,'companionMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass());  
        }
    } // end of function

    //get all user image from user_image table
    function eventsImage($eventId){
        
        $image = array();
        $defaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_EVENT_IMG_PATH;         

        $this->db->select('
            (case 
                when( eventImage = "" OR eventImage IS NULL) 
                    THEN "'.$defaultImg.'"
                ELSE
                    concat("'.$imgUrl.'",eventImage) 
            END ) as eventImage,
            (case 
                when( eventImage = "" OR eventImage IS NULL) 
                    THEN "'.$defaultImg.'"
                ELSE
                    eventImage
            END ) as eventImageName,
            eventImgId');

        $this->db->from(EVENT_IMAGE);
        $this->db->where('event_id',$eventId);

        $query  = $this->db->get();

        $image = $query->result();

        return $image;
    }

    // joined member list record 
    function joinedMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;
        // 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request,6=Request cancel
        $this->db->select('
            e.eventId, e.eventEndDate, mem.status as memBlockStatus, mem.eventMemId, mem.memberId, u1.fullName as memberName, 
            u1.userId as memberUserId,
            (case
                when( MIN(u1Img.userImgId) IS NULL)
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
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(u1Img.userImgId))
            END ) as memberImage,
            (case
                when( MIN(u1Img.userImgId) IS NULL)
                    THEN "'.$defaultImg.'"
                ELSE 
                (SELECT 
                    (case 
                        when( image = "" OR  image IS NULL)
                            THEN "'.$defaultImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            image
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(u1Img.userImgId))
            END ) as memberImageName,
            (case 
                when( mem.memberStatus = 1 && e.payment = 1) 
                    THEN 1
                when( mem.memberStatus = 2 && e.payment = 1) 
                    THEN 2
                when( mem.memberStatus = 1 && e.payment = 2)
                    THEN 3
                when( mem.memberStatus = 3) 
                    THEN 4
                ELSE
                    ""
            END ) as memberStatus,COALESCE(user_rating_mem.total_rating, "0") as totalRatingMem,
            COALESCE(comp.compId,"") as companionEventMemberId,
            COALESCE(u2.userId, "") as companionUserId, COALESCE(comp.status,"") as compBolckStatus,
            COALESCE(u2.fullName,"") as companionName, COALESCE(comp.companionMemId,"") as companionId,
            (case 
                when( comp.companionMemberStatus = 1 && e.payment = 1 ) 
                    THEN 1
                when( comp.companionMemberStatus = 2 && e.payment = 1) 
                    THEN 2
                when( comp.companionMemberStatus = 1 && e.payment = 2 ) 
                    THEN 3
                ELSE
                    ""
            END ) as companionMemberStatus,
            (case
                when( MIN(u2Img.userImgId) IS NULL)
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
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(u2Img.userImgId))
            END ) as companionImage,
            (case
                when( MIN(u2Img.userImgId) IS NULL)
                    THEN "'.$defaultImg.'"
                ELSE 
                (SELECT 
                    (case 
                        when( image = "" OR  image IS NULL)
                            THEN "'.$defaultImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            image
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(u2Img.userImgId))
            END ) as companionImageName,COALESCE(user_rating_comp.total_rating, "0") as totalRatingComp'
        );

        $this->db->from(EVENT_MEMBER.' as mem');
        $this->db->join(COMPANION_MEMBER.' as comp','mem.eventMemId = comp.eventMem_Id AND comp.event_id = '.$data['eventId'].' AND comp.status = 1 AND (comp.companionMemberStatus=1 OR comp.companionMemberStatus = 2)','left');
        $this->db->join(EVENTS.' as e','e.eventId = mem.event_id','left');
        $this->db->join(USERS.' as u1','u1.userId = mem.memberId','left');
        $this->db->join(USERS.' as u2','u2.userId = comp.companionMemId','left');
        $this->db->join(USERS_IMAGE.' as u1Img','u1Img.user_id = mem.memberId','left');       
        $this->db->join(USERS_IMAGE.' as u2Img','u2Img.user_id = comp.companionMemId','left');
        //to get user total average rating (Round off the number to nearest integer)
        $user_rating_mem  = '
                    (
                        SELECT
                            ROUND(AVG(rating)) as total_rating, for_user_id 
                        FROM `'.REVIEW.'`
                        GROUP BY 
                            for_user_id
                    ) as user_rating_mem
                    ';
        $this->db->join($user_rating_mem , 'u1.userId = user_rating_mem.for_user_id', 'left');

        //to get user total average rating (Round off the number to nearest integer)
        $user_rating_comp  = '
                    (
                        SELECT
                            ROUND(AVG(rating)) as total_rating, for_user_id 
                        FROM `'.REVIEW.'`
                        GROUP BY 
                            for_user_id
                    ) as user_rating_comp
                    ';

        $this->db->join($user_rating_comp , 'u2.userId = user_rating_comp.for_user_id', 'left');

        $this->db->where(array('mem.event_id'=>$data['eventId'],'mem.status'=>1));

        ($data['type'] == 'myEvent') ? $this->db->where(array('mem.memberId !=' =>$data['userId'])) : '';

        $this->db->group_start();
            $this->db->where(array('mem.memberStatus'=>1));
            $this->db->or_where(array('mem.memberStatus'=>2));
        $this->db->group_end();

        /*$this->db->group_start();
            $this->db->where(array('comp.companionMemberStatus'=>1));
            $this->db->or_where(array('comp.companionMemberStatus'=>2));
        $this->db->group_end();*/

        $this->db->order_by('mem.eventMemId','DESC');
        $this->db->group_by('mem.eventMemId');

    } // end of function


    // get joined member list for pagination
    function joinedMemberCount($data){        

        $this->joinedMemberList($data);

        $this->db->limit($data['limit'],$data['offset']);
        
        $query = $this->db->get();
        //lq();
        if($query->num_rows() > 0){

            $userData = $query->result();

            $eventCreaterDetail = $this->eventCreaterData($data);

            return array('list' => $userData, 'eventCreaterDetail' =>  $eventCreaterDetail);
        }
        return array();

    } // end of function

    // count of all joined member list
    function countAllJoinedMember($data){

        $this->joinedMemberList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

    function eventCreaterData($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
                u.userId, u.fullName,
                (case
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
                END ) as userImage,
                COALESCE(user_rating_mem.total_rating, "0") as totalRating

            ');
            $this->db->from(USERS.' as u');
            $this->db->join(EVENTS.' as e','e.eventOrganizer = u.userId','left');
            $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = e.eventOrganizer','left');
            //to get user total average rating (Round off the number to nearest integer)
            $user_rating_mem  = '
                        (
                            SELECT
                                ROUND(AVG(rating)) as total_rating, for_user_id 
                            FROM `'.REVIEW.'`
                            GROUP BY 
                                for_user_id
                        ) as user_rating_mem
                        ';
            $this->db->join($user_rating_mem , 'u.userId = user_rating_mem.for_user_id', 'left');    
            $this->db->group_by('uImg.user_id');
            $this->db->where(array('e.eventId' => $data['eventId']));

            $query = $this->db->get();

            if($query->num_rows()){
                $detail = $query->row();
            }

            $eventCreaterDetail = $detail ? $detail : new stdClass();

            return $eventCreaterDetail;
    }

     // get invited member list by eventId
    function invitedMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('em.eventMemId,em.memberId,em.event_id as eventId,e.eventEndDate,u.fullName,
        (case
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
            END ) as userImg,
            (case
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
                            image
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as userImgName, IF(w.name IS NULL or w.name ="","NA",w.name) as workName');

        $this->db->from(EVENT_MEMBER.' as em');
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');
        $this->db->join(USERS.' as u','u.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = em.memberId','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');
        $this->db->where(array('em.memberStatus' => 0,'em.event_id' => $data['eventId'],'em.memberId !=' => $data['userId']));
        $this->db->group_by('u.userId');
        $this->db->order_by('em.eventMemId','DESC');

    } // end of function

    // get joined member list for pagination
    function invitedMemberCount($data){

        $this->invitedMemberList($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();

            return array('list' => $userData, 'eventCreaterDetail' =>  new stdClass());
        }
        return array();

    } // end of function

    // count of all joined member list
    function countAllInvitedMember($data){

        $this->invitedMemberList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

    // get three joined member by eventId
    function joinedMember($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            em.eventMemId, em.memberId, em.event_id as eventId, u.fullName, COALESCE(u1.fullName,"") as compName, COALESCE(comp.companionMemId,"") as companionMemId,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL )
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
                END ) as userImg,
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
                                image
                           END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END ) as userImgName, 
            (
                case
                    when( MIN(uImg1.userImgId) IS NULL)
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
                           END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg1.userImgId))
                END ) as compImg,
            (
                case
                    when( MIN(uImg1.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE 
                    (SELECT 
                        (case 
                            when( image = "" OR  image IS NULL)
                                THEN "'.$defaultImg.'"
                            when(  image !="" AND isSocial =1)
                                THEN  image
                            ELSE
                                image
                           END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg1.userImgId))
                END ) as compImgName'
            );

        $this->db->from(EVENT_MEMBER.' as em');

        $this->db->join(USERS.' as u','u.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = em.memberId','left');
        $this->db->join(COMPANION_MEMBER.' as comp','em.eventMemId = comp.eventMem_Id AND comp.companionMemberStatus=1','left');
        $this->db->join(USERS.' as u1','u1.userId = comp.companionMemId','left');
        $this->db->join(USERS_IMAGE.' as uImg1','uImg1.user_id = comp.companionMemId','left');
    
        $this->db->where(array('em.event_id'=>$data['eventId'],'em.status'=>1));

        ($data['type'] == 'myEvent') ? $this->db->where(array('em.memberId !=' =>$data['userId'])) : '';

        $this->db->group_start();
            $this->db->where(array('em.memberStatus'=>1));
            $this->db->or_where(array('em.memberStatus'=>2));
        $this->db->group_end();

        $this->db->group_by('u.userId');
        $this->db->order_by('em.eventMemId','ASC');

        $this->db->limit(3);

        $query = $this->db->get();
        
        if($query->num_rows() >0){

            $userData = $query->result();

            return $userData;
        }
        return array();

    } // end of function

    // get invited three member by eventId
    function invitedMember($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            em.eventMemId, em.memberId, em.event_id as eventId, u.fullName,
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
            ) as userImg,
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
                                    image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as userImgName,
            IF(w.name IS NULL or w.name ="","NA",w.name) as workName
        ');

        $this->db->from(EVENT_MEMBER.' as em');

        $this->db->join(USERS.' as u','u.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = em.memberId','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $this->db->where(array('em.memberStatus'=>0,'em.event_id'=>$data['eventId'],'em.memberId !=' =>$data['userId']));

        $this->db->group_by('u.userId');
        $this->db->order_by('em.eventMemId','ASC');

        $this->db->limit(3);

        $query = $this->db->get();
        
        if($query->num_rows() >0){

            $userData = $query->result();

            return $userData;
        }
        return array();

    } // end of function

    // get shared/companion event request detail record by eventId
    function sharedEventRequestDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;
        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_IMG_PATH;
        $user_id = $data['userId'];
        $compId = $data['compId'];

        // 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request,6=Request cancel
        $this->db->select('
            em.eventMemId, em.memberId, e.eventId, e.groupChat, e.eventName, e.eventOrganizer, e.eventStartDate, 
            e.eventEndDate, e.eventPlace, e.eventLatitude, e.eventLongitude, 
            IF(e.privacy = 1,"Public","Private") as privacy, 
            IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, 
            e.userLimit, COALESCE(user_rating.total_rating, "0") as organizerRating, 
            e.eventUserType,
            (case
                when( comp.companionMemberStatus = 1 && e.payment = 1)
                    THEN 1
                when( comp.companionMemberStatus = 2 && e.payment = 1)
                    THEN 2
                when( comp.companionMemberStatus = 1 && e.payment = 2)
                    THEN 3
                when( comp.companionMemberStatus = 3)
                    THEN 4
                when( comp.companionMemberStatus = 4)
                    THEN 6
                ELSE
                    5
            END ) as memberStatus, u.fullName,u1.fullName as ownerName, 

            (case
                when( uImg.image = "" OR uImg.image IS NULL)
                    THEN "'.$defaultImg.'"
                when( uImg.image !="" AND uImg.isSocial = 1)
                    THEN uImg.image
                ELSE
                    concat("'.$imgUrl.'",uImg.image)
                END ) as profileImage,
            (case
                when( uImg.image = "" OR uImg.image IS NULL)
                    THEN "'.$defaultImg.'"
                when( uImg.image !="" AND uImg.isSocial = 1)
                    THEN uImg.image
                ELSE
                    uImg.image
                END ) as profileImageName,
            (case
                when( uImg1.image = "" OR uImg1.image IS NULL)
                    THEN "'.$defaultImg.'"
                when( uImg1.image !="" AND uImg1.isSocial = 1)
                    THEN uImg1.image
                ELSE
                    concat("'.$imgUrl.'",uImg1.image)
                END ) as ownerImage,
            (case
                when( uImg1.image = "" OR uImg1.image IS NULL)
                    THEN "'.$defaultImg.'"
                when( uImg1.image !="" AND uImg1.isSocial = 1)
                    THEN uImg1.image
                ELSE
                    uImg1.image
                END ) as ownerImageName, 

            /*IF(review.by_user_id = '.$user_id.', 1, 0) as reviewStatus,*/

            IF(comp.compId = '.$compId.',"Shared_Event","Shared_Event") as ownerType, 

            IF(biz.businessId IS NULL or biz.businessId ="" or biz.businessId ="0","",biz.businessId) as businessId,

            IF(biz.businessName IS NULL or biz.businessName ="" or biz.businessName ="0","",biz.businessName) as businessName,

            IF(biz.businessAddress IS NULL or biz.businessAddress ="" or biz.businessAddress ="0","",biz.businessAddress) as businessAddress,

            IF(biz.businesslat IS NULL or biz.businesslat ="" or biz.businesslat ="0","",biz.businesslat) as businesslat,

            IF(biz.businesslong IS NULL or biz.businesslong ="" or biz.businesslong ="0","",biz.businesslong) as businesslong,  

            (case 
                when( biz.businessImage = "" OR biz.businessImage IS NULL) 
                    THEN "'.$defaultImgBiz.'"
                ELSE
                    concat("'.$imgUrlBiz.'",biz.businessImage) 
            END ) as businessImage'
        );

        $this->db->from(EVENT_MEMBER.' as em');

        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');
        $this->db->join(BUSINESS.' as biz','biz.businessId = e.business_id','left');
        $this->db->join(USERS.' as u','u.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(USERS.' as u1','u1.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg1','u1.userId = uImg1.user_id','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = em.eventMemId','left');
        //$this->db->join(REVIEW.' as review','review.referenceId = e.eventId AND review.reviewType = 2','left');

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
        $this->db->join($user_rating , 'e.eventOrganizer = user_rating.for_user_id', 'left');

        $this->db->where(array('em.membertype !='=> 1,'em.memberStatus !='=> 3,'e.eventId'=>$data['eventId'],'e.status'=>1,'comp.compId' => $compId));     

        $this->db->group_by('em.eventMemId');
        //$this->db->group_by('review.referenceId');

        $query = $this->db->get();

        $result = $query->row();
        
        if($result){
            
            $data['type'] = 'request';

            $result->joinedMemberCount = $this->countAllJoinedMember($data);
            $result->reviewStatus = $this->getReview($user_id);

            $joinedMember = $this->joinedMember($data);

            $result->companionMemberCount = '';

            $companionMember = $this->companionMember($data);

            $result->confirmedCount = $this->getEventcoinfirmedMemberCount($data['eventId']);

            $result->eventImage = $this->eventsImage($data['eventId']);

            $eventReview = $this->getReviewsList($user_id,'0','100','2',$data['eventId']);    
                
            $result->eventReviewCount = count($eventReview);

            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');
            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return array('detail'=>$result,'joinedMember'=>$joinedMember,'companionMember'=>array(),'invitedMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass());  
        }

    } // end of function

    // to get appointment and event reviews list
    function getReviewsList($userId,$offset,$limit,$where,$eventId=''){

        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            IF(review.rating IS NULL or review.rating ="" or review.rating ="0","",review.rating) as rating,
            IF(review.comment IS NULL or review.comment ="" or review.comment ="0","",review.comment) as comment,
            IF(review.crd IS NULL or review.crd ="" or review.crd ="0","",review.crd) as crd,
            IF(users.fullName IS NULL or users.fullName ="" or users.fullName ="0","",users.fullName) as fullName,
            IF(users.userId IS NULL or users.userId ="" or users.userId ="0","",users.userId) as userId,
            (case
                when( MIN(uImg.userImgId) IS NULL) 
                    THEN "'.$defaultUserImg.'" 
                ELSE  
                    (SELECT 
                        (case 
                            when( image = "" OR  image IS NULL) 
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial = 1)
                                THEN  image
                            ELSE
                                concat("'.$userImg.'", image) 
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage,
            (case
                when( MIN(uImg.userImgId) IS NULL) 
                    THEN "'.$defaultUserImg.'" 
                ELSE  
                    (SELECT 
                        (case 
                            when( image = "" OR  image IS NULL) 
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial = 1)
                                THEN  image
                            ELSE
                                image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as webShowImg');

        $this->db->from(REVIEW.' as review');

        $this->db->join(USERS.' as users','users.userId = review.by_user_id','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = review.by_user_id','left');

        if(!empty($eventId)){

            $this->db->where(array('review.referenceId'=>$eventId,'review.reviewType'=>2));

        }else{

            $this->db->where(array('review.for_user_id'=>$userId,'review.reviewType'=>$where));
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('review.reviewId','DESC');
        $this->db->group_by('review.reviewId');
        
        $req = $this->db->get();
        $res = array();
        if($req->num_rows()){
            $res = $req->result();
        }
        return $res;
    }

    // check user limit exceed
    function getEventcoinfirmedMemberCount($eventId){

        $this->db->select('(count(mem.memberId)+count(comp.companionMemId)) as totalMember');
        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_MEMBER.' as mem','mem.event_id = e.eventId','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = mem.eventMemId AND comp.companionMemberStatus=1 AND comp.status=1','left');
        $this->db->where(array('mem.memberStatus'=>1,'e.eventId'=>$eventId,'e.status'=>1,'mem.status'=>1));
        $req = $this->db->get();
        if($req->num_rows()){
            $res = $req->row();
            return $res->totalMember;            
        }else{
            return '0';
        }
    }// End function

    function getReview($userId){

        $userReviewExist = $this->common_model->is_data_exists(REVIEW, array('by_user_id'=>$userId,'reviewType'=>2));
    
        if(!empty($userReviewExist)){
           return 1;
        }
        return 0;
    }

    // get companion three companion member by eventId
    function companionMember($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            comp.compId, u.fullName, 
            (
                case 
                    when( uImg.image = "" OR uImg.image IS NULL) 
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1) 
                        THEN uImg.image
                    ELSE
                        concat("'.$imgUrl.'",uImg.image) 
                END 
            ) as userImg,
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
                                    image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as userImgName
        ');

        $this->db->from(COMPANION_MEMBER.' as comp');

        $this->db->join(EVENT_MEMBER.' as em','em.eventMemId = comp.eventMem_Id AND em.memberId='.$data['userId'].'','left');    
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');           
        $this->db->join(USERS.' as u','u.userId = comp.companionMemId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = comp.companionMemId','left');

        $this->db->where(array('em.event_id'=>$data['eventId'],'comp.status'=>1));

        $this->db->group_by('u.userId');
        $this->db->order_by('comp.companionMemId','ASC');

        $this->db->limit(3);

        $query = $this->db->get();
        
        if($query->num_rows() >0){

            $userData = $query->result();

            return $userData;
        }              
        return array();
        
    } // end of function

    // get event request detail record by eventId
    function eventRequestDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_IMG_PATH;

        $user_id = $data['userId'];
        $eventMemId = $data['eventMemId'];

        // 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request,6=Request cancel
        $this->db->select('

            em.eventMemId, em.memberId, e.eventId, e.groupChat, e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace, e.eventLatitude, e.eventLongitude,
            IF(e.privacy = 1,"Public","Private") as privacy,
            IF(e.payment = "1","Paid","Free") as payment,
            e.eventAmount, e.currencySymbol, e.currencyCode, e.userLimit, COALESCE(user_rating.total_rating, "0") as organizerRating,
            e.eventUserType,
            (
                case
                    when( em.memberStatus = 1 && e.payment = 1 )
                        THEN 1
                    when( em.memberStatus = 2 && e.payment = 1)
                        THEN 2
                    when( em.memberStatus = 1 && e.payment = 2 )
                        THEN 3
                    when( em.memberStatus = 3) 
                        THEN 4
                    ELSE
                        5
                END 
            ) as memberStatus, u.fullName,
            (
                case
                    when( uImg.image = "" OR uImg.image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1)
                        THEN uImg.image
                    ELSE
                        concat("'.$imgUrl.'",uImg.image)
                END 
            ) as profileImage,
            (
                case
                    when( uImg.image = "" OR uImg.image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1)
                        THEN uImg.image
                    ELSE
                        uImg.image
                END 
            ) as profileImageName,

            /*IF(review.by_user_id = '.$user_id.', 1, 0) as reviewStatus,*/

            IF(em.eventMemId = '.$eventMemId.', "Administrator", "Administrator") as ownerType,

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
            ) as businessImage
        ');

        $this->db->from(EVENT_MEMBER.' as em');

        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');
        $this->db->join(USERS.' as u','u.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(BUSINESS.' as biz','biz.businessId = e.business_id','left');
        $this->db->join(REVIEW.' as review','review.referenceId = e.eventId AND review.reviewType = 2','left');

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
        $this->db->join($user_rating , 'e.eventOrganizer = user_rating.for_user_id', 'left');   

        $this->db->where(array('em.memberId'=>$data['userId'],'em.membertype !='=> 1,'e.eventId'=>$data['eventId'],'e.status'=>1));
        $this->db->group_by('em.eventMemId');
        $this->db->group_by('review.referenceId');

        $query = $this->db->get();
        $result = $query->row();
        
        if($result){

            $data['type'] = 'request';

            $result->joinedMemberCount = $this->countAllJoinedMember($data);

            $result->reviewStatus = $this->getReview($user_id);

            $joinedMember = $this->joinedMember($data);

            $result->companionMemberCount = $this->countAllCompanionMember($data); 

            $companionMember = $this->companionMember($data);

            $result->confirmedCount = $this->getEventcoinfirmedMemberCount($data['eventId']);

            $result->eventImage = $this->eventsImage($data['eventId']);

            $eventReview = $this->getReviewsList($user_id,'0','100','2',$data['eventId']);    
                
            $result->eventReviewCount = count($eventReview);

            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');

            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);

            $this->db->update(NOTIFICATIONS,array('isRead'=>1)); 

            return array('detail'=>$result,'joinedMember'=>$joinedMember,'companionMember'=>$companionMember,'invitedMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass());  
        }

    } // end of function

    // get companion member list by eventId
    function companionMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            e.eventId, e.eventAmount, e.groupChat, e.currencySymbol, comp.*,
            (
                case 
                    when( comp.companionMemberStatus = 1 && e.payment = 1)
                        THEN 1
                    when( comp.companionMemberStatus = 2 && e.payment = 1) 
                        THEN 2
                    when( comp.companionMemberStatus = 1 && e.payment = 2) 
                        THEN 3
                    when( comp.companionMemberStatus = 3 ) 
                        THEN 4
                    when( comp.companionMemberStatus = 4 ) 
                        THEN 6
                    ELSE
                        "5"
                END 
            ) as companionMemberStatus,
            e.eventId,e.eventEndDate,u.fullName,
            (
                case 
                    when( uImg.image = "" OR uImg.image IS NULL) 
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1) 
                        THEN uImg.image
                    ELSE
                        concat("'.$imgUrl.'",uImg.image) 
                END 
            ) as userImg,
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
                                image
                           END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                    END 
            ) as userImgName'
        );

        $this->db->from(COMPANION_MEMBER.' as comp');

        $this->db->join(EVENT_MEMBER.' as em','em.eventMemId = comp.eventMem_Id AND em.memberId='.$data['userId'].'','left');    
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');           
        $this->db->join(USERS.' as u','u.userId = comp.companionMemId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = comp.companionMemId','left');

        $this->db->where(array('em.event_id'=>$data['eventId'],'comp.status'=>1));      
        $this->db->group_by('u.userId');
        $this->db->order_by('comp.upd','DESC');

    } // end of function

    // get companion member list for pagination
    function companionMemberCount($data){

        $this->companionMemberList($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();

            return array('list' => $userData, 'eventCreaterDetail' =>  new stdClass());
        }
        return array();

    } // end of function

    // count of all joined member list
    function countAllCompanionMember($data){

        $this->companionMemberList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

     // check user limit exceed
    function getEventMemberCount($eventId){

        $this->db->select('e.userLimit,(count(mem.memberId)+count(comp.companionMemId)) as totalMember');
        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_MEMBER.' as mem','mem.event_id = e.eventId','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = mem.eventMemId AND comp.companionMemberStatus=1','left');
        $this->db->where(array('mem.memberStatus'=>1,'e.eventId'=>$eventId));
        $req = $this->db->get();
        if($req->num_rows()){
            $res = $req->row();
            
            if( $res->totalMember < $res->userLimit){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }// End function

    // to get event organiser's bank account id
    function getOrganiserBankAccId($eventId){

        $where = array('eventId'=>$eventId);
        $getRespose = $this->common_model->getsingle(EVENTS,$where);
        $getDetail = new stdClass();
        if(!empty($getRespose)){
            $checkAcc = array('user_id'=>$getRespose->eventOrganizer);
            $getDetail = $this->common_model->getsingle(BANK_ACCOUNT_DETAILS,$checkAcc);            
        }
        return $getDetail;
    }

    // background notifications for share event notifications
    function shareEventBgNotification($eventMemId,$userId,$eventName,$userName,$eventId){

        $companionMemId = $this->common_model->get_records_by_id(COMPANION_MEMBER,'',array('eventMem_Id'=>$eventMemId),'companionMemId,compId','','' );

        if($companionMemId){
                
            $title = lang('share_event_title');

            $body_send  = $userName.lang('event_share').$eventName; //body to be sent with current notification
            $body_save  = '[UNAME]'.lang('event_share').'[ENAME]'; //body to be saved in DB
            $notif_type = 'share_event';

            // save notification for multiple users
            foreach ($companionMemId as $memId) {

                $where = array('userId'=>$memId['companionMemId'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);
                if($user_info_for){
                    $deviceToken = $user_info_for->deviceToken;  // getting multiple users device token    

                    //send notification to user
                    $this->notification_model->send_push_notification_for_event(array($deviceToken), $title, $body_send,$eventId,$memId['compId'],$eventMemId='',$notif_type,$userId);     

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$memId['compId'],'eventMemId'=>'','createrId'=>$userId);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$memId['companionMemId'], 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                    $notification_where = array('notificationFor'=>$memId['companionMemId'],'notificationBy'=>$userId,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }
            }            
        }

    } // end of function

    // for getting bank account detail of event's and appointment organiser
    function getBankAccountDetail($userId){

        $where = array('user_id'=>$userId);
        $getRespose = $this->common_model->getsingle(BANK_ACCOUNT_DETAILS,$where);       
        return $getRespose;   
    }// End function

} // End Of Class

       