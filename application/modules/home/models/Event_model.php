<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends CI_Model {

    // Get latlong from address using curl
    function getLatLong($address){ 

        if(!empty($address)){
           
            $formattedAddr = str_replace(' ','+',$address);

            $url = 'https://maps.google.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($response);
            if(isset($output->results[0]->geometry->location->lat)){
                $data['latitude']  = $output->results[0]->geometry->location->lat; 
                $data['longitude'] = $output->results[0]->geometry->location->lng;
                if(!empty($data)){
                    return $data;
                }else{
                    return false;
                }
            }else{
                return false;   
            }
        }else{
            return false;   
        }

    } //End Function

    function createEvent($eventData,$friendId,$eventImage){
        
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

            if($eventData->eventOrganizer == $this->session->userdata('userId')){
                $insertBatch[] = array(
                    'event_id' => $eventData->eventId,
                    'memberId' => $this->session->userdata('userId'),
                    'memberType' => 1
                );
            }
            $this->db->insert_batch(EVENT_MEMBER,$insertBatch);

            if(!empty($eventImage)){
                
                $imgData['eventImage'] = $eventImage;
                $imgData['user_id'] = $this->session->userdata('userId');                  
                $imgData['event_id'] = $eventData->eventId;                  

                $this->db->insert(EVENT_IMAGE,$imgData);
            }

            return $eventId;
        }
        return FALSE;  

    } // End Of Function

    //add single image for an event
    function addEventImage($event_id, $event_image){
        $data['event_id'] = $event_id;
        $data['eventImage'] = $event_image;
        $data['user_id'] = $this->session->userdata('userId');
        return $this->common_model->insertData(EVENT_IMAGE, $data);
    }

    // get all my event's record
    function getEventImageDetail($where){

        $defaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_EVENT_IMG_PATH; 
        
        $this->db->select('eImg.eventImgId, 
            (
                CASE 
                    when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                THEN "'.$defaultImg.'"
                    ELSE
                    eImg.eventImage
                END 
            ) as eventImage');

        $this->db->from(EVENTS.' as e');

        $this->db->join(EVENT_IMAGE.' as eImg','e.eventId = eImg.event_id');

        $this->db->where($where);
        $this->db->group_by('eImg.eventImgId');

        $query = $this->db->get();
        
        return $query->row();
        
    } // end of function

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

    function getInvUserList($event_data, $search){

        $gender = $event_data['gender'] ? $event_data['gender'] : '';

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;
        $current_user_id = $this->session->userdata('userId');
        
        // for miles 6371 & for km 3959
        $km = 50; //raduis of 50km
        
        $select_dist = '';
       /* if(isset($search['latitude']) && isset($search['longitude'])){
            $select_dist = '( 
                6371 * acos( cos( radians( '.$search['latitude'].'  ) ) * cos( radians( u.latitude ) ) * 
                cos( radians( u.longitude ) - radians('.$search['longitude'].') ) + 
                sin( radians('.$search['latitude'].') ) * sin( radians( u.latitude ) ) )
            ) AS distance,';
            $this->db->having('distance <= ' . $km); //location radius
            $this->db->order_by('distance','ASC');
        }*/
        
        $this->db->select(
            'e.eventOrganizer, u.userId, u.fullName, u.age, u.gender, u.address, u.latitude, u.longitude, u.gender, u.eventInvitation,
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
                                            image
                                    END 
                                ) as image 
                            FROM '.USERS_IMAGE.' 
                            WHERE 
                                userImgId = MAX( uImg.userImgId )
                        )
                END 
            ) as profileImage, 
            (
                case 
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
                END 
            ) as memberStatus'
        );

        $this->db->from(USERS.' as u');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = u.userId ','left');
        $this->db->join(EVENT_MEMBER.' as em','em.memberId = u.userId AND em.event_id = "'.$event_data['eventId'].'"','left');
        $this->db->join(COMPANION_MEMBER.' as comp','comp.companionMemId = u.userId AND comp.event_id = "'.$event_data['eventId'].'"','left');
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id AND em.event_id = "'.$event_data['eventId'].'"','left');
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
           // $rating_arr = explode(",",$search['rating']);
            $this->db->where_in('user_rating.total_rating', $search['rating']);
        }
        
        $this->db->group_by('u.userId');
        $this->db->order_by("u.fullName", "asc");
        /*$query = $this->db->get();
        return $query->result();*/
    }

    // get joined member list for pagination
    function getInvitationUserList($event_data, $search){

        $this->getInvUserList($event_data, $search);

        $this->db->limit($event_data['limit'],$event_data['offset']);

        $query = $this->db->get();
        
        if($query->num_rows() > 0){

            $userData = $query->result();

            return $userData;
        }
        return array();

    } // end of function

    // count of all joined member list
    function countAllInvUsers($event_data, $search){

        $this->getInvUserList($event_data, $search);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

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

    function getpublicEventDetail($eventId){

        $defaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl     = AWS_CDN_EVENT_IMG_PATH; 
        
        $this->db->select(' 
            e.eventId, e.eventName, e.eventStartDate, e.eventEndDate, e.eventPlace, e.groupChat, e.eventLatitude, e.eventLongitude,
            IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, e.userLimit, 
            IF(e.privacy = "1","Public","Private") as privacy,
            (
                case 
                    when( e.eventUserType = "" or e.eventUserType IS NULL) 
                        THEN "NA"
                    when( e.eventUserType !="" AND e.eventUserType = 1) 
                        THEN "Male"
                    when( e.eventUserType !="" AND e.eventUserType = 2) 
                        THEN "Female"
                    when( e.eventUserType !="" AND e.eventUserType = 3) 
                        THEN "Transgender"
                    ELSE 
                        "All" 
                END ) as eventUserType, u.fullName,
            ( 
                case 
                    when( uImg.image = "" OR uImg.image IS NULL) 
                        THEN "'.$defaultImg.'"
                    when( uImg.image !="" AND uImg.isSocial = 1) 
                        THEN uImg.image
                    ELSE
                        uImg.image 
                END ) as profileImageName
        ');

        $this->db->from(EVENTS.' as e');
        $this->db->join(USERS.' as u','u.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->where(array('e.eventId'=>$eventId,'e.status'=>1));
        $this->db->order_by('eventId','DESC');
        $this->db->group_by('e.eventId');

        $this->db->limit(4);

        $query = $this->db->get();

        if($query->num_rows() > 0){

            $eventData = $query->row();

            $eventData->eventImage = $this->eventsImage($eventId);
       
            return $eventData;
        }
    }

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

} // End Of Class