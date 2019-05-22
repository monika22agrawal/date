<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

    function getAllResult($limit,$start,$searchArray){
        
        $newData = array(); 
        //$userId = !empty($this->session->userdata('userId')) ? $this->session->userdata('userId') : '0';
        // for miles 6371 & for km 3959
        $km = 50;        

        if(!empty($searchArray['latitude']) && !empty($searchArray['longitude'])){

            $lat    = $searchArray['latitude'];
            $long   = $searchArray['longitude'];

        }elseif(!empty($this->session->userdata('lat')) && !empty($this->session->userdata('long'))){

            $lat    = $this->session->userdata('lat');
            $long   = $this->session->userdata('long');

        }else{

            $lat    = 22.7196;
            $long   = 75.8577;
        }

        $whereAge = '';
        if(!empty($searchArray['minAge']) && !empty($searchArray['maxAge'])){
            $whereAge = "u.age BETWEEN ".$searchArray['minAge']." AND ".$searchArray['maxAge']."";
        }

        $whereNewUser = '';
        if(!empty($searchArray['userOnlineStatus']) && $searchArray['userOnlineStatus'] == '3'){
            $whereNewUser = "( u.crd` >= curdate() - INTERVAL 7 DAY)";
        }

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        //if lat and long is empty then return no recrord found
        if(empty($lat) && empty($long)){
            return $newData;
        }  
        
        $this->db->select('
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
            ) as image, COALESCE(w.name,"NA") as work, u.userId, u.fullName, COALESCE(u.address,"NA") as address, u.latitude, u.longitude, u.showOnMap, u.gender, u.mapPayment, u.showTopPayment, u.age, u.crd, COALESCE(user_rating.total_rating, "0") as total_rating, 
            ( 6371 * acos( cos( radians( '.$lat.'  ) ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance
        ');

        //$this->db->having('distance <= ' . $km);               

        $this->db->from(USERS.' as u');

        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

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

        if(!empty($searchArray['gender'])){
            ($searchArray['gender'] == '1' || $searchArray['gender'] == '2' || $searchArray['gender'] == '3') ? $this->db->where(array('u.gender'=>$searchArray['gender'])) : '';
        }
            
        //($val == '1') ? $this->db->where(array('u.showOnMap'=>'1')) : '';
        (!empty($searchArray['searchName'])) ? $this->db->like(array('u.fullName'=>trim($searchArray['searchName']))) : '';
        (!empty($whereAge)) ? $this->db->where($whereAge) : '';
        (!empty($whereNewUser)) ? $this->db->where($whereNewUser) : '';
        $this->db->where(array('u.status'=>'1'));

        if($this->session->userdata('front_login') == true && $this->session->userdata('userId') != ''){
            $this->db->where('u.userId !=',$this->session->userdata('userId'));
        }

        $this->db->group_by('u.userId');

        $this->db->limit($limit,$start);
        $this->db->order_by('distance');
        //$this->db->order_by('u.showTopPayment','desc');
        $this->db->order_by('u.userId','desc');

        $req = $this->db->get();

        if($req->num_rows()){

            $this->load->model('User_model');

            $detail =  $req->result();

            foreach ($detail as $k => $value) {

                $newData[$k] = array(
                    
                    'userId'        => $value->userId,
                    'fullName'      => $value->fullName,
                    'totalRating'   => $value->total_rating,
                    'age'           => $value->age,
                    'address'       => $value->address,
                    'latitude'      => $value->latitude,
                    'longitude'     => $value->longitude,
                    'showOnMap'     => $value->showOnMap,
                    'work'          => $value->work,
                    'gender'        => $value->gender,
                    'profileImage'  => $value->image,
                    'showTopPayment'=> $value->showTopPayment,
                    'mapPayment'    => $value->mapPayment,
                    'isAppointment' => $this->User_model->checkAppointment($value->userId)
                );
            }
        }
        
        return $newData;
    }

    function countAllResult($searchArray) {

       $newData = array(); 
       //$userId = !empty($this->session->userdata('userId')) ? $this->session->userdata('userId') : '0';
        // for miles 6371 & for km 3959
        $km = 50;        

        if(!empty($searchArray['latitude']) && !empty($searchArray['longitude'])){

            $lat = $searchArray['latitude'];
            $long = $searchArray['longitude'];

        }elseif(!empty($this->session->userdata('lat')) && !empty($this->session->userdata('long'))){

            $lat    = $this->session->userdata('lat');
            $long   = $this->session->userdata('long');

        }else{

            $lat    = 22.7196;
            $long   = 75.8577;
        }

        $whereAge = '';
        if(!empty($searchArray['minAge']) && !empty($searchArray['maxAge'])){
            $whereAge = "u.age BETWEEN ".$searchArray['minAge']." AND ".$searchArray['maxAge']."";
        }

        $whereNewUser = '';
        if(!empty($searchArray['userOnlineStatus']) && $searchArray['userOnlineStatus'] == '3'){
            $whereNewUser = "( u.crd` >= curdate() - INTERVAL 7 DAY)";
        }

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;  

        //if lat and long is empty then return no recrord found
        if(empty($lat) && empty($long)){
            return '0';
        }

        $this->db->select('
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
            END ) as image, COALESCE(w.name,"NA") as work, u.userId, u.fullName, COALESCE(u.address,"NA") as address, u.latitude, u.longitude, u.showOnMap, u.gender, u.mapPayment, u.showTopPayment, u.age, u.crd, COALESCE(user_rating.total_rating, "0") as total_rating, ( 6371 * acos( cos( radians( '.$lat.'  ) ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance');

        //$this->db->having('distance <= ' . $km);                       

        $this->db->from(USERS.' as u');

        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

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

        if(!empty($searchArray['gender'])){
            ($searchArray['gender'] == '1' || $searchArray['gender'] == '2' || $searchArray['gender'] == '3') ? $this->db->where(array('u.gender'=>$searchArray['gender'])) : '';
        }
            
        //($val == '1') ? $this->db->where(array('u.showOnMap'=>'1')) : '';
        (!empty($searchArray['searchName'])) ? $this->db->like(array('u.fullName'=>trim($searchArray['searchName']))) : '';
        (!empty($whereAge)) ? $this->db->where($whereAge) : '';
        (!empty($whereNewUser)) ? $this->db->where($whereNewUser) : '';
        $this->db->where(array('u.status'=>'1'));

        if($this->session->userdata('front_login') == true && $this->session->userdata('userId') != ''){
            $this->db->where('u.userId !=',$this->session->userdata('userId'));
        }
        $this->db->group_by('u.userId');
        $this->db->order_by('distance');
        //$this->db->order_by('u.showTopPayment','desc');
        $this->db->order_by('u.userId','desc');   
        //$this->db->order_by('u.userId','desc');    
        $req = $this->db->get()->num_rows();
       
        return $req;
    }
    
    // current near by you or top rated user's for showing home page
    function getTopUsers(){

        $newData = array(); 
        //$userId = !empty($this->session->userdata('userId')) ? $this->session->userdata('userId') : '0';
        // for miles 6371 & for km 3959
        //$km = 50;

        $lat = $this->session->userdata('lat');
        $long = $this->session->userdata('long');

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        if($lat=="" && $long==''){

            $this->db->select('
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
                    END ) as image, COALESCE(w.name,"NA") as work, u.userId, u.fullName, u.address, u.latitude, u.longitude, u.showOnMap, u.gender, u.mapPayment, u.showTopPayment, u.age, COALESCE(user_rating.total_rating, "0") as total_rating');
        }else{
       
            $this->db->select('
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
                    END ) as image, COALESCE(w.name,"NA") as work, u.userId, u.fullName, COALESCE(u.address,"NA") as address, u.latitude, u.longitude, u.showOnMap, u.gender, u.mapPayment, u.showTopPayment, u.age, COALESCE(user_rating.total_rating, "0") as total_rating, ( 6371 * acos( cos( radians( '.$lat.'  ) ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance');  
                //$this->db->having('distance <= ' . $km);        
            $this->db->order_by('distance');
        }       

        $this->db->from(USERS.' as u');

        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

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
        
        $this->db->where(array('u.status'=>'1'));

        if($this->session->userdata('front_login') == true && $this->session->userdata('userId') != ''){
            $this->db->where('u.userId !=',$this->session->userdata('userId'));
        }
        $this->db->group_by('u.userId');
        
        if($lat=="" && $long=='')
            $this->db->order_by('total_rating','desc'); 

        $this->db->order_by('u.showTopPayment','desc');
        $this->db->order_by('u.userId','desc');

        $req = $this->db->get();

        if($req->num_rows()){

            $this->load->model('User_model');
            
            $detail =  $req->result();

            foreach ($detail as $k => $value) {

                $newData[$k] = array(

                    'userId'        => $value->userId,
                    'fullName'      => $value->fullName,
                    'totalRating'   => $value->total_rating,
                    'age'           => $value->age,
                    'address'       => $value->address,
                    'latitude'      => $value->latitude,
                    'longitude'     => $value->longitude,
                    'showOnMap'     => $value->showOnMap,
                    'work'          =>$value->work,
                    'gender'        =>$value->gender,
                    'profileImage'  =>$value->image,
                    'showTopPayment'=>$value->showTopPayment,
                    'mapPayment'    =>$value->mapPayment,
                    'isAppointment' =>$this->User_model->checkAppointment($value->userId)
                );
            }
        }
        
        return $newData;
    }

    // current top event's for showing home page
    function getLatestEvent(){

        $defaultImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_EVENT_IMG_PATH; 
        
        $this->db->select(' 
            e.eventId, e.eventName, e.eventStartDate, e.eventPlace, 
            IF(e.privacy = "1","Public","Private") as privacy,
            
            (
                case 
                    when( eImg.eventImage = "" OR eImg.eventImage IS NULL) 
                        THEN "'.$defaultImg.'"
                    ELSE
                        eImg.eventImage
                END 
            ) as eventImageName
        ');

        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_IMAGE.' as eImg','e.eventId = eImg.event_id','left');
        $this->db->order_by('eventId','DESC');
        $this->db->group_by('e.eventId');

        $this->db->limit(4);

        $query = $this->db->get();

        if($query->num_rows() > 0){

            $eventData = $query->result();
       
            return $eventData;
        }

    } // End of function

} // End of class