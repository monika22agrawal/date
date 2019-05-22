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
        $imgUrl     = AWS_CDN_USER_THUMB_IMG;
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
        $this->db->where(array('userId!=' => $current_user_id,'u.status'=>1)); //ignore current user
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

} // End Of Class

       