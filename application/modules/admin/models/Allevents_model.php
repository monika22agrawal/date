<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allevents_model extends CI_Model {

    //var $table , $column_order, $column_search , $order =  '';

    var $column_order   = array(null,'e.eventName','e.eventPlace','e.eventAmount','e.status'); //set column field database for datatable orderable
    var $column_search  = array('u.fullName','e.eventName','e.eventPlace'); //set column field database for datatable searchable
    var $col_filter     = array('e.eventStartDate','e.privacy','e.payment','e.eventPlace','DATE(e.eventStartDate)','DATE(e.eventEndDate)');
    var $order          = array('e.eventId' => 'DESC');  // default order
    var $where          = '';
    var $group_by       = 'e.eventId'; 
    
    public function __construct(){
        parent::__construct();
    }

    function prepare_query(){
       
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('e.*,u.fullName
        ,(case 
            when(uImg.image = "" OR uImg.image IS NULL) 
            THEN "'.$defaultImg.'"
            when( uImg.image !="" AND uImg.isSocial = 1) 
            THEN uImg.image
            ELSE
            concat("'.$imgUrl.'",uImg.image) 
           END ) as profileImage');
        
        $this->db->from(EVENTS.' as e');
        $this->db->join(USERS.' as u','u.userId = e.eventOrganizer','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = u.userId','left');       
    }

    //prepare post list query
    private function posts_get_query()
    {
        $this->prepare_query();
        $i = 0;
        foreach ($this->column_search as $emp) // loop column 
        {
            if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
                $_POST['search']['value'] = $_POST['search']['value'];
            } else
                $_POST['search']['value'] = '';

            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $this->db->group_start();
                    $this->db->like(($emp), $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like(($emp), $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
            
        if(!empty($this->group_by)){
            $this->db->group_by($this->group_by);
        }

         //for category filter
        $count_val = count($_POST['columns']);
        //print_r($_POST['columns']);
        for($i=0;$i<=$count_val-1;$i++){
        
            if(!empty($_POST['columns'][$i]['search']['value'])){ 

                $this->db->where(array($this->col_filter[$i] => $_POST['columns'][$i]['search']['value']));

            }
        }

        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_list()
    {
        $this->posts_get_query();
		if(isset($_POST['length']) && $_POST['length'] < 1) {
			$_POST['length']= '10';
		} else
		$_POST['length']= $_POST['length'];
		
		if(isset($_POST['start']) && $_POST['start'] > 1) {
			$_POST['start']= $_POST['start'];
		}
        $this->db->limit($_POST['length'], $_POST['start']);
		//print_r($_POST);die;
        $query = $this->db->get(); 
        return $query->result();
    }

    function count_filtered()
    {
        $this->posts_get_query();
        $query = $this->db->get();
        //lq();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->prepare_query();
        return $this->db->count_all_results();
    }

    function eventBlockUnblock($id){
        $this->db->select('status');  
        $this->db->where('eventId',$id);
        $sql = $this->db->get(EVENTS)->row();
        if($sql->status == 0){
            $this->db->update(EVENTS,array('status'=> '1'),array('eventId'=>$id));
            return array('message'=>'Unblock');
        }else{
            $this->db->update(EVENTS,array('status'=> '0'),array('eventId'=>$id));
            return array('message'=>'Block');
        }
    }

    function blockUnblockMem($id) {

        $data = $this->db->select('status,eventMemId')->from(EVENT_MEMBER)->where($id)->get();
        $res = $data->row();
        $memStatus = $res->status;
        $memId = $res->eventMemId;
        
        if($memStatus == 1){
            $status = "0";
        }else{
            $status = "1";
        }
        $this->db->where($id)->update(EVENT_MEMBER,array('status'=>$status));
        //$this->db->where(array('eventMem_Id'=>$memId))->update(COMPANION_MEMBER,array('status'=>$status));
        $this->db->where(array('eventMem_Id'=>$memId));
        $this->db->group_start();
            $this->db->where('companionMemberStatus',1);
            $this->db->or_where('companionMemberStatus',2);
        $this->db->group_end();
        $this->db->update(COMPANION_MEMBER,array('status'=>$status));
        return  $status;
    }

    function blockUnblockComp($id) {

        $data = $this->db->select('status')->from(COMPANION_MEMBER)->where($id)->get();
        $compStatus = $data->row()->status;
        
        if($compStatus == 1){
            $status = "0";
        }else{
            $status = "1";
        }
        $this->db->where($id)->update(COMPANION_MEMBER,array('status'=>$status));
        return  $status;
    }

    // joined member list record 
    function joinedMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;
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
        $this->db->join(COMPANION_MEMBER.' as comp','mem.eventMemId = comp.eventMem_Id AND comp.event_id = '.$data['eventId'].' AND (comp.companionMemberStatus=1 OR comp.companionMemberStatus = 2)','left');
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

        $this->db->where(array('mem.event_id'=>$data['eventId']));

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
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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

     // get my event detail record by eventId
    function myEventDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;      

        $defaultImgBiz = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrlBiz = AWS_CDN_BIZ_THUMB_IMG;  

        $this->db->select('
            e.eventId, e.groupChat, e.eventLatitude, e.eventLongitude, e.eventName, e.eventOrganizer, e.eventStartDate, e.eventEndDate, e.eventPlace, IF(e.privacy = 1,"Public","Private") as privacy,IF(e.payment = "1","Paid","Free") as payment, e.eventAmount, e.currencySymbol, e.currencyCode, e.userLimit, e.business_id, COALESCE(user_rating.total_rating, "0") as organizerRating,
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

            $result->eventImage = $this->eventsImage($data['eventId']);
            // for notification isRead 1
            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');
            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));           
            return array('detail'=>$result,'joinedMember'=>$joinedMember,'invitedMember'=>$invitedMember,'companionMember'=>array(),'eventReview'=> new stdClass());  
        }
    } // end of function

     // get invited member list by eventId
    function invitedMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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

}