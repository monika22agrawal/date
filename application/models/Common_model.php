<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Common Model
 * Consist common DB methods which will be commonly used throughout the project
 */
class Common_model extends CI_Model {
    
    /* Check user login and set session */
    function isLogin($data, $table){
        $email = $data["email"];
        $where = array('email'=>$email);
     
        $sql = $this->db->select('*')->where($where)->get($table);
        if($sql->num_rows()){
            $user = $sql->row();
            //verify password- It is good to use php's password hashing functions so we are using password_verify fn here
            if(password_verify($data['password'], $user->password)){
                $session_data['id']     = $user->id ;
                $session_data['name']   =     $user->name ;
                $session_data['email']      = $user->email ;
                $session_data['isLogin']    = TRUE ;
                $session_data['profileImage'] = $user->profileImage;
                $this->session->set_userdata($session_data);
                return TRUE;
            }
            else{
               return FALSE; 
            }
        }
        return FALSE;
    }
        
    /* <!--INSERT RECORD FROM SINGLE TABLE--> */
    function insertData($table, $dataInsert) {
        
        $this->db->insert($table, $dataInsert);
        return $this->db->insert_id();
    }

    /* <!--UPDATE RECORD FROM SINGLE TABLE--> */
    function updateFields($table, $data, $where){

        $this->db->update($table, $data, $where);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    function deleteData($table,$where){

        $this->db->where($where);
        $this->db->delete($table); 
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }   
    }
    
    /* ---GET SINGLE RECORD--- */
    function getsingle($table, $where = '', $fld = NULL, $order_by = '', $order = '') {

        if ($fld != NULL) {
            $this->db->select($fld);
        }
        $this->db->limit(1);

        if ($order_by != '') {
            $this->db->order_by($order_by, $order);
        }
        if ($where != '') {
            $this->db->where($where);
        }

        $q = $this->db->get($table);
        //$num = $q->num_rows();
        return $q->row();
    }
    
    /* ---GET MULTIPLE RECORD--- */
    function getAll($table, $order_fld = '', $order_type = '', $select = 'all', $limit = '', $offset = '',$group_by='') {

        $data = array();
        if ($select == 'all') {
            $this->db->select('*');
        } else {
            $this->db->select($select);
        }
        if($group_by !=''){
            $this->db->group_by($group_by);
        }
        $this->db->from($table);

        $clone_db = clone $this->db;
        $total_count = (int) $clone_db->get()->num_rows();

        if ($limit != '' && $offset != '') {
            $this->db->limit($limit, $offset);
        } else if ($limit != '') {
            $this->db->limit($limit);
        }
        if ($order_fld != '' && $order_type != '') {
            $this->db->order_by($order_fld, $order_type);
        }
        $q = $this->db->get();
        $num_rows = $q->num_rows();
        if ($num_rows > 0) {
            foreach ($q->result() as $rows) {
                $data[] = $rows;
            }
            $q->free_result();
        }
        return array('total_count' => $total_count,'result' => $data);
    }
    
    //get single record using join
    function GetSingleJoinRecord($table, $field_first, $tablejointo, $field_second,$field_val='',$where="") {

        $data = array();
        if(!empty($field_val)){
            $this->db->select("$field_val");
        }else{
            $this->db->select("*");
        }
        $this->db->from("$table");
        $this->db->join("$tablejointo", "$tablejointo.$field_second = $table.$field_first","inner");
        if(!empty($where)){
            $this->db->where($where);
        }
        $q = $this->db->get();
        return $q->row();  //return only single record
    }

    /* Get mutiple records using join */ 
    function GetJoinRecord($table, $field_first, $tablejointo, $field_second,$field_val='',$where="",$group_by='',$order_fld='',$order_type='', $limit = '', $offset = '') {

        $data = array();
        if(!empty($field_val)){
            $this->db->select("$field_val");
        }else{
            $this->db->select("*");
        }
        $this->db->from("$table");
        $this->db->join("$tablejointo", "$tablejointo.$field_second = $table.$field_first","inner");
        if(!empty($where)){
            $this->db->where($where);
        }
        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }

        $clone_db = clone $this->db;
        $total_count = (int) $clone_db->get()->num_rows();

        if ($limit != '' && $offset != '') {
            $this->db->limit($limit, $offset);
        } else if ($limit != '') {
            $this->db->limit($limit);
        }
        if(!empty($order_fld) && !empty($order_type)){
            $this->db->order_by($order_fld, $order_type);
        }
        $q = $this->db->get();
        return $q->result();
    }
    
    /*Get records joining 3 tables*/
    function GetJoinRecordThree($table, $field_first, $tablejointo, $field_second,$tablejointhree,$field_three,$table_four,$field_four,$field_val='',$where="" ,$group_by="",$order_fld='',$order_type='', $limit = '', $offset = '') {

        $data = array();
        if(!empty($field_val)){
            $this->db->select("$field_val");
        }else{
            $this->db->select("*");
        }
        $this->db->from("$table");
        $this->db->join("$tablejointo", "$tablejointo.$field_second = $table.$field_first",'inner');
        $this->db->join("$tablejointhree", "$tablejointhree.$field_three = $table_four.$field_four",'inner');
        if(!empty($where)){
            $this->db->where($where);
        }

        if(!empty($group_by)){
            $this->db->group_by($group_by);
        }
        $clone_db = clone $this->db;
        $total_count = (int) $clone_db->get()->num_rows();

        if ($limit != '' && $offset != '') {
            $this->db->limit($limit, $offset);
        } else if ($limit != '') {
            $this->db->limit($limit);
        }
        
        if(!empty($order_fld) && !empty($order_type)){
            $this->db->order_by($order_fld, $order_type);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $data[] = $rows;
            }
            $q->free_result();
        }
        return array('total_count' => $total_count,'result' => $data);
    }

    function getAllwhereIn($table,$where = '',$column ='',$wherein = '', $order_fld = '', $order_type = '', $select = 'all', $limit = '', $offset = '',$group_by='') {

        $data = array();
        if ($order_fld != '' && $order_type != '') {
            $this->db->order_by($order_fld, $order_type);
        }
        if ($select == 'all') {
            $this->db->select('*');
        } else {
            $this->db->select($select);
        }
        $this->db->from($table);
        if ($where != '') {
            $this->db->where($where);
        }
        if ($wherein != '') {
            $this->db->where_in($column,$wherein);
        }
        if($group_by !=''){
            $this->db->group_by($group_by);
        }

        $clone_db = clone $this->db;
        $total_count = (int) $clone_db->get()->num_rows();

        if ($limit != '' && $offset != '') {
            $this->db->limit($limit, $offset);
        } else if ($limit != '') {
            $this->db->limit($limit);
        }

        $q = $this->db->get();
        $num_rows = $q->num_rows();
        if ($num_rows > 0) {
            foreach ($q->result() as $rows) {
                $data[] = $rows;
            }
            $q->free_result();
        }
        return array('total_count' => $total_count,'result' => $data);
    }
    
    /* Exceute a custom build query- Useful when we are not able to build queries using CI DB methods*/
    function custom_query($myquery){

        $query = $this->db->query($myquery);
        return $query->result_array();
    }

    /*check if any value exists in and return row if found*/
    function is_id_exist($table,$key,$value){

        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($key,$value);
        $ret = $this->db->get()->row();
        if(!empty($ret)){
            return $ret;
        }
        else
            return FALSE;
    }
    
    /*get single value based on table key*/
    function get_field_value($table, $where, $key){

        $this->db->select($key);
        $this->db->from($table);
        $this->db->where($where);
        $ret = $this->db->get()->row();
        if(!empty($ret)){
            return $ret->$key;
        }
        else
            return FALSE;
    }
    
    //get total records of any table
    function get_total_count($table, $where=''){

        $this->db->from($table);
        if(!empty($where))
            $this->db->where($where);
        
        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }
    
    /* delete attachment file from folder and table */
    function delete_attachment($ref_id, $ref_table, $att_name=''){

        $del_where = array('reference_id'=>$ref_id, 'reference_table'=>$ref_table); $file_folder = '';
        switch ($ref_table){
            case USERS:
                $file_folder = USER_AVATAR_PATH;
                break;
            case CATEGORIES:
                $file_folder = CATEGORY_IMAGE_PATH;
                break;
            case ALBUMS:
                $file_folder = ALBUM_IMAGE_PATH;
                break;
        }
        
        if(empty($file_folder))
            return;
        
        $att_data = $this->getAllwhere(ATTACHMENTS, $del_where);  //get all attachments of reference
        if(!empty($att_data['result'])){
            foreach($att_data['result'] as $row){
                delete_file($file_folder.$row->attachment_name, FCPATH);  //delete attachment from server
            }
        }
        $this->deleteData(ATTACHMENTS,$del_where);  //delete attachment entries from table
    }
    
    /* check if given data exists in table - Very useful fn */
    function is_data_exists($table, $where){

        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        $rowcount = $query->num_rows();
        if($rowcount==0){
            return false; //record not found
        }
        else {
            return true; //returns true if record found
        }
    }  

    /*GET MULTIPLE RECORD*/
    function getAllwhere($table, $where = '', $order_fld = '', $order_type = '', $select = 'all', $limit = '', $offset = '',$group_by='') {

        $data = array();
        if ($order_fld != '' && $order_type != '') {
            $this->db->order_by($order_fld, $order_type);
        }
        if ($select == 'all') {
            $this->db->select('*');
        } else {
            $this->db->select($select);
        }
        $this->db->from($table);
        if ($where != '') {
            $this->db->where($where);
        }
        if(!empty($group_by)){
            $this->db->group_by($group_by); 
        }

        $clone_db = clone $this->db;
        $total_count = (int) $clone_db->get()->num_rows();

        if ($limit != '' && $offset != '') {
            $this->db->limit($limit, $offset);
        } else if ($limit != '') {
            $this->db->limit($limit);
        }
        $q = $this->db->get();
        $num_rows = $q->num_rows();
        if ($num_rows > 0) {
            foreach ($q->result() as $rows) {
                $data[] = $rows;
            }
            $q->free_result();
        }
        return array('total_count' => $total_count,'result' => $data);
    }

    function get_records_by_id($table,$single,$where,$select,$order_by_field,$order_by_value ){

        if(!empty($select)){
            $this->db->select($select);
        }
        
        if(!empty($where)){
            $this->db->where($where);
        }
        
        if(!empty($order_by_field) && !empty($order_by_value)){
            $this->db->order_by($order_by_field, $order_by_value);
        }
        
        $query = $this->db->get($table);
        $result = $query->result_array();
        if(!empty($result)){
            if($single){
                $result = $result[0];
            }else{
                $result = $result;
            }  
        } else{
           $result = 0; 
        }
        return $result;
    }

    // get user's info from multiple tables
    function usersDetail($userId){

        $single     = lang('rel_single');
        $married    = lang('rel_married');
        $divorced   = lang('rel_divorced');
        $widowed    = lang('rel_widowed');

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $dImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $uImg = AWS_CDN_USER_LARGE_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;      
        $result = new stdClass();

        $this->db->select('
            u.gender,u.deviceToken, u.otpVerified, u.crd, u.authToken, u.isNotification, u.email, u.userId, u.latitude, u.longitude, u.city, u.state, u.country, u.showOnMap, u.birthday, u.deviceToken, uwm.work_id, uem.edu_id, u.countryCode, u.contactNo, u.eventInvitation,
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
                        THEN "'.$single.'"
                    when( u.relationship !="" AND u.relationship ="2") 
                        THEN "'.$married.'"
                    when( u.relationship !="" AND u.relationship ="3") 
                        THEN "'.$divorced.'"
                    ELSE "'.$widowed.'" 
                END 
            ) as relationship,
            (
                case 
                    when( u.language = "" or u.language IS NULL)
                        THEN "NA"
                    when( u.language !="") 
                        THEN u.language
                    ELSE 
                        "NA"
                END 
            ) as language, 
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

    function totalFriendCount($id){
        
        $friendCount = $this->db->query('SELECT count(friendId) as totalFriends FROM '.FRIENDS.' WHERE byId = "'.$id.'" OR forId ="'.$id.'" ');
        return $friendCount->row()->totalFriends;
    }

    //get all user image from user_image table
    function usersImage($userId){
        
        $image = array();
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $image = $this->db->select(

        USERS_IMAGE.'.image,userImgId,
            (
            SELECT
                COUNT(user_img_id) as totalLikes
                FROM '.PROFILE_IMAGE_LIKES.'
                WHERE user_img_id = userImgId
            ) as totalImglikes,

            (
            SELECT
                IF(like_by_user_id = "'.$userId.'","1","0")
                FROM '.PROFILE_IMAGE_LIKES.'
                WHERE user_img_id = userImgId
            ) as isLike,
            
            (
                case
                    when( '.USERS_IMAGE.'.image = "" OR '.USERS_IMAGE.'.image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( '.USERS_IMAGE.'.image !="" AND '.USERS_IMAGE.'.isSocial = 1)
                        THEN image
                    ELSE
                        concat("'.$imgUrl.'",image)
                END 
            ) as image, '.USERS_IMAGE.'.userImgId,
            (
                case
                    when( image = "" OR image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( image !="" AND isSocial = 1)
                        THEN image
                    ELSE
                        image
                END 
            ) as imgName'

        )
        ->order_by(USERS_IMAGE.'.userImgId','DESC')
        ->get_where(USERS_IMAGE,array('user_id' => $userId))
        ->result();

        return $image;
    }

    //check for isfavorite user 
    function checkfavorite($userId,$myId){
        
        $this->db->select('favId');
        $this->db->from(FAVORITES);
        $this->db->where(array('user_id'=>$myId,'favUserId'=>$userId));
        $isFaverite = $this->db->get()->row();
        $result = new stdClass();
        if($isFaverite){
            //isFaverite = 1 for feverite and 0 for not feveritr
            $result->isFavorite ="1";
        }else{
            $result->isFavorite ="0";
        }

        return $result;
    }
    
    //check for islike user 
    function checkLike($userId,$myId){

        $this->db->select('likeId');
        $this->db->from(LIKES);
        $this->db->where(array('user_id'=>$myId,'LikeUserId'=>$userId));
        $isLike = $this->db->get()->row();
        $result = new stdClass();
        if($isLike){
            //isLike = 1 for like and 0 for not like
            $result->isLike ="1";
        }else{
            $result->isLike ="0";
        }
        return $result;
    }

    // get all friend's record
    function friendList($data){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            uf.friendId, IF(uf.byId = "'.$data['userId'].'",COALESCE(wf.name,""),COALESCE(w.name,"")) as work, 
            IF(uf.byId = "'.$data['userId'].'",uf.forId,uf.byId) as userId,
            IF(uf.byId = "'.$data['userId'].'",u2.fullName,u1.fullName) as fullName,
            IF(uf.byId = "'.$data['userId'].'",u2.gender,u1.gender) as gender,
            IF(uf.byId = "'.$data['userId'].'",u2.eventInvitation,u1.eventInvitation) as eventInvitation,            
            (
                case

                when (uf.byId = "'.$data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$data['userId'].'" && uImg.image = "") 
                    THEN "'.$defaultUserImg.'"
            
                when (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)

                when (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                    THEN IF(uf.byId = "'.$data['userId'].'",concat("'.$userImg.'",ufImg.image),concat("'.$userImg.'",uImg.image))
                ELSE
                    "'.$defaultUserImg.'"
                END
            ) as profileImage,
            (
                case

                when (uf.byId = "'.$data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$data['userId'].'" && uImg.image = "") 
                    THEN "'.$defaultUserImg.'"
            
                when (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)

                when (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)
                ELSE
                    "'.$defaultUserImg.'"
                END
            ) as webProfileImage
        ');

        $this->db->from(FRIENDS.' as uf');

        $this->db->join(USERS.' as u1','uf.byId = u1.userId'); 
        $this->db->join(USERS.' as u2','uf.forId = u2.userId');

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = uf.byId','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = uf.forId','left');

        $this->db->join(USERS_WORK.' as uwm','uf.byId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $this->db->join(USERS_WORK.' as uwf','uf.forId = uwf.user_id','left');
        $this->db->join(WORKS.' as wf','uwf.work_id = wf.workId','left');

        $this->db->group_start();
            $this->db->where('uf.forId',$data['userId']);
            $this->db->or_where('uf.byId',$data['userId']);
        $this->db->group_end();
        
        $where = array('u1.status'=>'1','u2.status'=>'1');

        $this->db->where($where);

        if(!empty($data['searchText'])){

            $this->db->group_start();
                $this->db->group_start();
                    $this->db->like('u1.fullName',$data['searchText'],'after');
                    $this->db->where('u1.userId !=',$data['userId']);
                $this->db->group_end();
                $this->db->or_group_start();
                    $this->db->like('u2.fullName',$data['searchText'],'after');
                    $this->db->where('u2.userId !=',$data['userId']);
                $this->db->group_end();
            $this->db->group_end();
        }           
        
        $this->db->order_by('u1.fullName','asc');
        $this->db->order_by('u2.fullName','asc');
        $this->db->group_by('u1.userId');
        $this->db->group_by('u2.userId');            
    }

    // get list for pagination
    function friendListCount($data){

        $this->friendList($data);
        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();

            // for notification isRead 1
            $notType = array('accept_request');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return $userData;
        }
        return array();
    }

    // count of friends
    function countAllFriend($data){

        $this->friendList($data);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // get all record of requests
    function requestRecord($data){

        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            COALESCE(w.name,"") as work, u.userId, u.fullName,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultUserImg.'"
                    ELSE  
                        (SELECT (case 
                            when( image = "" OR  image IS NULL)
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial =1)
                                THEN  image
                            ELSE
                                concat("'.$userImg.'", image)
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as profileImage,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultUserImg.'"
                    ELSE  
                        (SELECT (case 
                            when( image = "" OR  image IS NULL)
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial =1)
                                THEN  image
                            ELSE
                                image
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as webProfileImage
        ');
            
        $this->db->from(USERS.' as u');

        $this->db->join(REQUESTS.' as reqf','reqf.requestBy = u.userId'); 

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = reqf.requestBy','left');

        $this->db->join(USERS_WORK.' as uwm','reqf.requestBy = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $where = array('u.status'=>'1','reqf.requestStatus'=>1,'reqf.requestFor'=>$data['userId']);
        $this->db->where($where);
        !empty($data['searchText']) ? $this->db->like('u.fullName',$data['searchText'],'after') : '';
        
        $this->db->order_by('u.fullName','asc');
        $this->db->group_by('u.userId');
    }

    // get list for pagination
    function requestListCount($data){

        $this->requestRecord($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();
            // for notification isRead 1
            $notType = array('friend_request');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return $userData;
        }
        return array();
    }

    // count of request
    function countAllRequest($data){

        $this->requestRecord($data);
        $query = $this->db->get();                    
                
        return $query->num_rows();       
    }

    function getAppData($appId,$userId){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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

    function getEducationList(){

        $getEdu = $this->db->select('eduId,education,eduInSpanish')->where(array('status'=>1))->order_by('education','ASC')->get(EDUCATION);
        if($getEdu->num_rows()){

            return $getEdu->result();
        }
        return false;   
    }

    function getWorkList(){

        $getWork = $this->db->select('workId,name,nameInSpanish')->where(array('status'=>1))->order_by('name','ASC')->get(WORKS);
        if($getWork->num_rows()){

            return $getWork->result();
        }
        return false;   
    }

    /*function getInterestList(){

        $getInt = $this->db->select('interestId,interest')->where(array('status'=>1))->order_by('interest','ASC')->get(INTERESTS);
        if($getInt->num_rows()){

            return $getInt->result();
        }
        return false;   
    }*/
   // 'SELECT ins.interestId , ins.interest, uim.user_id FROM users_interest_mapping AS uim RIGHT JOIN interests AS ins ON uim.interest_id = ins.interestId WHERE ins.type=0 OR (ins.type=1 AND uim.user_id=3) GROUP BY ins.interestId '

    function getInterestList($userId){
        
        $this->db->select('ins.interestId,ins.interest,uim.user_id');

        $this->db->from(USERS_INTEREST_MAPPING.' as uim');

        $this->db->join(INTERESTS.' as ins','uim.interest_id = ins.interestId','right');

        $this->db->group_start();
            $this->db->where(array('ins.status'=>1,'ins.type'=>0));
        $this->db->group_end();

        $this->db->or_group_start();
            $this->db->or_where(array('ins.type'=>1));          
            $this->db->where(array('uim.user_id'=>$userId,'ins.status'=>1));
        $this->db->group_end();
      
        $this->db->order_by('interest','ASC');
        $this->db->group_by('ins.interestId');

        $getInt = $this->db->get();
       
        if($getInt->num_rows()){

            return $getInt->result();
        }
        return false;   
    }

    function getFriendRequestStatus($id,$u_id){
        //check for request send or recived
        $query = $this->db->query('SELECT * FROM '.REQUESTS.' WHERE requestBy = "'.$id.'" AND requestFor ="'.$u_id.'" OR requestBy = "'.$u_id.'" AND requestFor = "'.$id.'" ');
        $isRequest = $query->row();
        $result = new stdClass();
        if($isRequest){
            // if isRequest = 1= send and 2= recieved and 0 = not send
            if($isRequest->requestBy == $u_id)
            {
                $result->isRequest = "1"; //request send
                $result->isFriend  = "0"; //not friend
            }else{
                $result->isRequest = "2"; //request receved
                $result->isFriend  = "0"; //not friend
            }
            
        }else{
            //check for friend
            $query = $this->db->query('SELECT * FROM '.FRIENDS.' WHERE byId = "'.$id.'" AND forId ="'.$u_id.'" OR byId = "'.$u_id.'" AND forId = "'.$id.'" ');
            $isFriend = $query->row();
            if($isFriend){
                //if isfriend = 1 for friend and 0 = not friend
                $result->isRequest = "0"; //request not send 
                $result->isFriend  = "1"; //  is friend 
            }else{
                $result->isRequest = "0"; //request not send 
                $result->isFriend  = "0"; //not friend
            }
        }        
        return $result;
    }

    //replace user placeholder name with user full name
    function replace_user_placeholder_name($user_id, $body){
        $u_name = '';
        $full_name = $this->common_model->get_field_value(USERS, array('userId'=>$user_id), 'fullName');
        if($full_name){
            $u_name = ucfirst($full_name);
        }
        $body = str_replace("[UNAME]", $u_name, $body);
        return $body;
    }

    //replace event placeholder name with event name
    function replace_event_placeholder_name($eventId, $body){
        $e_name = '';
        $event_res = $this->common_model->get_field_value(EVENTS, array('eventId'=>$eventId), 'eventName');
        
        if(!empty($event_res)){
            $e_name = ucfirst($event_res);
        }
        $body = str_replace("[ENAME]", $e_name, $body);
        return $body;
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

    // get all friend's record
    function friendList_invite($data){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            uf.friendId, 
            IF(uf.byId = "'.$data['userId'].'",COALESCE(wf.name,""),COALESCE(w.name,"")) as work,
            IF(uf.byId = "'.$data['userId'].'",uf.forId,uf.byId) as userId,
            IF(uf.byId = "'.$data['userId'].'",u2.fullName,u1.fullName) as fullName,
            IF(uf.byId = "'.$data['userId'].'",u2.gender,u1.gender) as gender,
            IF(uf.byId = "'.$data['userId'].'",u2.eventInvitation,u1.eventInvitation) as eventInvitation,
            IF(uf.byId = "'.$data['userId'].'",e2.privacy,e1.privacy) as privacy,
            IF(uf.byId = "'.$data['userId'].'",e2.eventOrganizer,e1.eventOrganizer) as owner,
            (
                case 
                    when (uf.byId = "'.$data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$data['userId'].'" && uImg.image = "") 
                        THEN "'.$defaultUserImg.'"
                
                    when (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                        THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)

                    when (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                        THEN IF(uf.byId = "'.$data['userId'].'",concat("'.$userImg.'",ufImg.image),concat("'.$userImg.'",uImg.image))
                    ELSE
                        "'.$defaultUserImg.'"
                END
            ) as profileImage,
            (
                case 
                    when( uf.byId = '.$data['userId'].' &&  mem2.memberStatus = 1)
                        THEN 1
                    when( uf.byId = '.$data['userId'].' && mem2.memberStatus = 2)
                        THEN 2
                    when( uf.forId = '.$data['userId'].' && mem1.memberStatus = 1)
                        THEN 1
                    when( uf.forId = '.$data['userId'].' && mem1.memberStatus = 2)
                        THEN 2
                    when( uf.byId = '.$data['userId'].' &&  comp2.companionMemberStatus = 1)
                        THEN 1
                    when( uf.byId = '.$data['userId'].' && comp2.companionMemberStatus = 2)
                        THEN 2
                    when( uf.forId = '.$data['userId'].' && comp1.companionMemberStatus = 1)
                        THEN 1
                    when( uf.forId = '.$data['userId'].' && comp1.companionMemberStatus = 2)
                        THEN 2
                    ELSE
                        ""
                END 
            ) as memberStatus
        ');

        $this->db->from(FRIENDS.' as uf');

        $this->db->join(USERS.' as u1','uf.byId = u1.userId'); 
        $this->db->join(USERS.' as u2','uf.forId = u2.userId'); 

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = uf.byId','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = uf.forId','left');

        $this->db->join(EVENT_MEMBER.' as mem1','uf.byId = mem1.memberId AND mem1.event_id = "'.$data['eventId'].'"','left');
        $this->db->join(EVENT_MEMBER.' as mem2','uf.forId = mem2.memberId AND mem2.event_id = "'.$data['eventId'].'"','left');

        $this->db->join(COMPANION_MEMBER.' as comp1','uf.byId = comp1.companionMemId AND comp1.event_id = "'.$data['eventId'].'"','left'); 
        $this->db->join(COMPANION_MEMBER.' as comp2','uf.forId = comp2.companionMemId AND comp2.event_id = "'.$data['eventId'].'"','left');

        $this->db->join(EVENTS.' as e1','mem1.event_id = e1.eventId AND e1.eventId = "'.$data['eventId'].'"','left'); 
        $this->db->join(EVENTS.' as e2','mem2.event_id = e2.eventId AND e2.eventId = "'.$data['eventId'].'"','left'); 

        $this->db->join(USERS_WORK.' as uwm','uf.byId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $this->db->join(USERS_WORK.' as uwf','uf.forId = uwf.user_id','left');
        $this->db->join(WORKS.' as wf','uwf.work_id = wf.workId','left');

        $this->db->group_start();
            $this->db->where('uf.forId',$data['userId']);
            $this->db->or_where('uf.byId',$data['userId']);
        $this->db->group_end();
        
        if( $data['gender']==1 || $data['gender']==2 ){
            $where = array('u1.status'=>'1','u2.status'=>'1');
            $this->db->where('IF(uf.byId = "'.$data['userId'].'",u2.gender,u1.gender)=',$data['gender']);
        }else{
            $where = array('u1.status'=>'1','u2.status'=>'1');
        }
        
        $this->db->where($where);

        if(!empty($data['searchText'])){

            $this->db->group_start();
                $this->db->group_start();
                    $this->db->like('u1.fullName',$data['searchText'],'after');
                    $this->db->where('u1.userId !=',$data['userId']);
                $this->db->group_end();
                $this->db->or_group_start();
                    $this->db->like('u2.fullName',$data['searchText'],'after');
                    $this->db->where('u2.userId !=',$data['userId']);
                $this->db->group_end();
            $this->db->group_end();
        }     
        
        $this->db->order_by('u1.fullName','asc');
        $this->db->order_by('u2.fullName','asc');
        $this->db->group_by('u1.userId');
        $this->db->group_by('u2.userId');            
    }

    // get list for pagination
    function friendListCount_invite($data){

        $this->friendList_invite($data);
        $this->db->limit($data['limit'],$data['offset']);
        $query = $this->db->get();
        
        if($query->num_rows() >0){
            $userData = $query->result();
            return $userData;
        }
        return array();
    }

    // count of friends
    function countAllFriend_invite($data){

        $this->friendList_invite($data);
        $query = $this->db->get();
        return $query->num_rows();
    
    } // end of function

    // background notifications for create event notifications
    function createEventBgNotification($eventId,$userId,$eventName,$userName){

        $memberId = $this->get_records_by_id(EVENT_MEMBER,'',array('event_id'=>$eventId,'memberId !='=>$userId),'memberId, eventMemId','','' );

        if($memberId){

            $notif_type = 'create_event';            
           
            $companionId= '';

            // save notification for multiple users
            foreach ($memberId as $memId) { 

                $where = array('userId'=>$memId['memberId'],'isNotification'=>1);
                $user_info_for = $this->getsingle(USERS,$where);
                if($user_info_for){

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Nuevo evento';
                        $showMsg = ' te invito a asistir a un evento : ';
                    }else{
                        $title = 'New Event';
                        $showMsg = ' invited you to attend an event : ';
                    }

                    $body_send  = $userName.$showMsg.$eventName.'.'; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg.'[ENAME].'; //body to be saved in DB

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


    // background notifications for share event notifications
    function shareEventBgNotification($eventMemId,$userId,$eventName,$userName,$eventId){

        $companionMemId = $this->get_records_by_id(COMPANION_MEMBER,'',array('eventMem_Id'=>$eventMemId),'companionMemId,compId','','' );

        if($companionMemId){

            $notif_type = 'share_event';

            // save notification for multiple users
            foreach ($companionMemId as $memId) {

                $where = array('userId'=>$memId['companionMemId'],'isNotification'=>1);
                $user_info_for = $this->getsingle(USERS,$where);
                if($user_info_for){

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Compartir evento';
                        $showMsg = ' ha compartido un evento : ';
                    }else{
                        $title = 'Share Event';
                        $showMsg = ' has shared an event : ';
                    }
                        
                    $body_send  = $userName.$showMsg.$eventName; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg.'[ENAME]'; //body to be saved in DB
                    
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

    // count of all event's
    function countAllMyEvent($data){

        $this->myEventList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

    // get all event's request record
    function eventRequestList($data){
        
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG; 

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

    // count of all event's
    function countAllEventRequest($data){

        $this->eventRequestList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function

    // get my event detail record by eventId
    function myEventDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl     = AWS_CDN_USER_THUMB_IMG;

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
        $result->joinedCompMemberCount = $this->joinedCompMemberCount($data);
        $result->invitedMemberCount = $this->countAllInvitedMember($data);

        $joinedMember = $this->joinedMember($data);
        $invitedMember = $this->invitedMember($data);      

        if($result){

            $result->reviewStatus = $this->getReview($user_id,$data['eventId']);
            $result->eventImage = $this->eventsImage($data['eventId']);
            $eventReview = $this->getReviewsList($user_id,'0','100','2',$data['eventId']);    
                
            $result->eventReviewCount = count($eventReview);
            // for notification isRead 1
            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');
            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));           
            return array('detail'=>$result,'joinedMember'=>$joinedMember,'invitedMember'=>$invitedMember,'companionMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass(),'companionMemberAccept'=>new stdClass());  
        }
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

    function getReview($userId,$eventId){

        $userReviewExist = $this->common_model->is_data_exists(REVIEW, array('by_user_id'=>$userId,'reviewType'=>2,'referenceId'=>$eventId));
    
        if(!empty($userReviewExist)){
           return 1;
        }
        return 0;
    }

    // get shared/companion event request detail record by eventId
    function sharedEventRequestDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;
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
                                    concat("'.$imgUrl.'",uImg.image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as profileImage,
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
                                    uImg.image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as profileImageName,
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
                                    concat("'.$imgUrl.'",uImg1.image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg1.userImgId))
                END 
            ) as ownerImage,
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
                                    uImg1.image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg1.userImgId))
                END 
            ) as ownerImageName,

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
            $result->joinedCompMemberCount = $this->joinedCompMemberCount($data);
            $result->reviewStatus = $this->getReview($user_id,$data['eventId']);

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
            return array('detail'=>$result,'joinedMember'=>$joinedMember,'companionMember'=>array(),'invitedMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass(),'companionMemberAccept'=>new stdClass());  
        }

    } // end of function

    // get event request detail record by eventId
    function eventRequestDetail($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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
                                    concat("'.$imgUrl.'",uImg.image)
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as profileImage,
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
                                    uImg.image
                            END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
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
            $result->joinedCompMemberCount = $this->joinedCompMemberCount($data);

            $result->reviewStatus = $this->getReview($user_id,$data['eventId']);

            $joinedMember = $this->joinedMember($data);

            $result->companionMemberCount = $this->countAllCompanionMember($data); 

            $companionMember = $this->companionMember($data);
            $companionMemberAccept = $this->companionMemberAccept($data);

            $result->confirmedCount = $this->getEventcoinfirmedMemberCount($data['eventId']);

            $result->eventImage = $this->eventsImage($data['eventId']);

            $eventReview = $this->getReviewsList($user_id,'0','100','2',$data['eventId']);    
                
            $result->eventReviewCount = count($eventReview);

            $notType = array('create_event','join_event','event_payment','share_event','companion_accept','companion_reject','companion_payment');

            $this->db->where(array('notificationFor'=>$data['userId'],'referenceId'=>$data['eventId']));
            $this->db->where_in('notificationType',$notType);

            $this->db->update(NOTIFICATIONS,array('isRead'=>1)); 

            return array('detail'=>$result,'joinedMember'=>$joinedMember,'companionMember'=>$companionMember,'invitedMember'=>array(),'eventReview'=>!empty($eventReview) ? $eventReview[0] : new stdClass(),'companionMemberAccept'=>$companionMemberAccept);  
        }
    } // end of function

    // get companion three companion member by eventId
    function companionMember($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            comp.compId, u.fullName, 
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
                                    concat("'.$imgUrl.'",uImg.image) 
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
                                when( image = "" OR  image IS NULL )
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
        
        if($query->num_rows() > 0){

            $userData = $query->result();

            return $userData;
        }              
        return array();
        
    } // end of function

    // get accepted only one companion member by eventId
    function companionMemberAccept($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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
                                concat("'.$imgUrl.'",uImg.image) 
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
            ) as userImgName
        ');

        $this->db->from(COMPANION_MEMBER.' as comp');

        $this->db->join(EVENT_MEMBER.' as em','em.eventMemId = comp.eventMem_Id AND em.memberId='.$data['userId'].'','left');    
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');           
        $this->db->join(USERS.' as u','u.userId = comp.companionMemId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = comp.companionMemId','left');

        $this->db->where(array('em.event_id'=>$data['eventId'],'comp.status'=>1));
        $this->db->group_start();
            $this->db->where(array('comp.companionMemberStatus'=>1));
            $this->db->or_where(array('comp.companionMemberStatus'=>2));
        $this->db->group_end();

        $this->db->group_by('u.userId');

        $query = $this->db->get();
        
        if($query->num_rows() > 0){

            $userData = $query->row();

            return $userData;
        }              
        return array();
        
    } // end of function

    // get companion member list by eventId
    function companionMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

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
                                concat("'.$imgUrl.'",uImg.image) 
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
            ) as userImgName
        ');

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

        if( $query->num_rows() > 0 ){

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

    // joined companion member count 
    function joinedCompMemberCount($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;
        // 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request,6=Request cancel
        $this->db->select('compId');

        $this->db->from(COMPANION_MEMBER.' as comp');
      
        $this->db->where(array('comp.event_id'=>$data['eventId'],'comp.status'=>1));

        $this->db->group_start();
            $this->db->where(array('comp.companionMemberStatus'=>1));
            $this->db->or_where(array('comp.companionMemberStatus'=>2));
        $this->db->group_end();

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

    // for delete event
    function deleteEvent($eventId,$userId){

        // check if any member join or not
        $this->db->select('e.eventId,mem.eventMemId,e.eventEndDate');
        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_MEMBER.' as mem','e.eventId = mem.event_id');
        $this->db->where(array('e.eventId'=>$eventId,'e.eventOrganizer'=>$userId,'mem.memberId !='=>$userId));
        $this->db->group_start();
            $this->db->where(array('mem.memberStatus'=>1));
            $this->db->or_where(array('mem.memberStatus'=>2));
        $this->db->group_end();
        $query = $this->db->get()->row();
        if($query){
            if($query->eventEndDate < date('Y-m-d H:i:s')){
                $checkWhere = array('eventId'=>$eventId,'eventOrganizer'=>$userId);
                $eventData = $this->deleteData(EVENTS, $checkWhere);
                return 'ED'; // event deleted
            }else{
                return 'JM'; // joined member
            }
        }else{
            //delete record
            $checkWhere = array('eventId'=>$eventId,'eventOrganizer'=>$userId);
            $eventData = $this->deleteData(EVENTS, $checkWhere);
            if($eventData){
                return 'ED'; // event deleted
            }else{
                return 'NE'; // something going wrong
            }
        }
    } // end of function


    // check for update event
    function checkUpdateEvent($eventId,$userId){

        // check if any member join or not
        $this->db->select('e.eventId,mem.eventMemId');
        $this->db->from(EVENTS.' as e');
        $this->db->join(EVENT_MEMBER.' as mem','e.eventId = mem.event_id');
        $this->db->where(array('e.eventId'=>$eventId,'e.eventOrganizer'=>$userId,'mem.memberId !='=>$userId));
        $this->db->group_start();
            $this->db->where(array('mem.memberStatus'=>1));
            $this->db->or_where(array('mem.memberStatus'=>2));
        $this->db->group_end();
        $query = $this->db->get()->row();
        if($query){
            return TRUE; // joined member
        }else{
            return FALSE; 
        }
    } // end of function

    // to check event;s member status
    function checkMemberStatus($where){

        $this->db->select('eventMemId');
        $this->db->from(EVENT_MEMBER);
        $this->db->where($where);
        $this->db->group_start();
            $this->db->where(array('memberStatus'=>1));
            $this->db->or_where(array('memberStatus'=>2));
        $this->db->group_end();
        $query = $this->db->get()->row();
        if($query){
            return true; //returns true if record found
        }else{
            return false; //record not found
        }
    }// End function

    //get notification list on website
    function getNotificationListWeb($offset,$limit,$userId){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_THUMB_IMG;
       
        $this->db->select('n.notId,n.isRead,n.notificationBy,n.notificationFor,n.referenceId,n.message,n.notificationType,n.crd,u.fullName,u.userId');
        $this->db->from(NOTIFICATIONS.' as n');
        $this->db->join(USERS.' as u','u.userId = n.notificationBy','left');
        $this->db->where(array('n.notificationFor'=>$userId));
        $this->db->limit($limit,$offset);
        $this->db->order_by('n.notId','DESC');
        $req  = $this->db->get();
        
        //lq();
        $result = $req->result();
        if($req->num_rows()){       
            $this->common_model->updateFields(NOTIFICATIONS,array('webShow' => 1), array( 'notificationFor' => $this->session->userdata('userId')));
            foreach($result as $k=>$v){
                $notif_payload = json_decode($v->message);
                
                //if notification is related to post then get event name
                if($v->notificationType == 'create_event' || $v->notificationType == 'join_event' || $v->notificationType=='event_payment' || $v->notificationType=='share_event' || $v->notificationType=='companion_accept' || $v->notificationType=='companion_reject' || $v->notificationType=='companion_payment'){
                    //replace placeholder name with real event name
                    $notif_payload->body = $this->common_model->replace_event_placeholder_name($notif_payload->referenceId, $notif_payload->body);
                }
                
                //get fullName of user
                $notif_payload->body = $this->common_model->replace_user_placeholder_name($v->notificationBy, $notif_payload->body);
                
                $result[$k]->message = $notif_payload;

                $img = $this->db->select('(case
                when( MIN(uImg.userImgId) IS NULL) 
                THEN "'.$defaultUserImg.'" 
                ELSE  
                (SELECT 
                    (case 
                        when( image = "" OR  image IS NULL) 
                            THEN "'.$defaultUserImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            image
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END ) as profileImage')->where(array('user_id'=>$v->notificationBy))->get(USERS_IMAGE.' as uImg')->row();
                
                $v->image = isset($img->profileImage) ? $img->profileImage : '';

                $v->timeElapsed = time_elapsed_string($v->crd); //add time_elapsed key to show time elapsed in user friendly string
            }
        }       
        return $result;

    } // End Function

    //get notification list on app side
    function getNotificationList($offset,$limit,$userId){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_THUMB_IMG;
       
        $this->db->select('n.notId,n.isRead,n.notificationBy,n.notificationFor,n.referenceId,n.message,n.notificationType,n.crd,u.fullName');
        $this->db->from(NOTIFICATIONS.' as n');
        $this->db->join(USERS.' as u','u.userId = n.notificationBy','left');
        $this->db->where(array('n.notificationFor'=>$userId));
        $this->db->limit($limit,$offset);
        $this->db->order_by('n.notId','DESC');
        $req  = $this->db->get();
        
        //lq();
        $result = $req->result();
        if($req->num_rows()){           
            
            foreach($result as $k=>$v){
                $notif_payload = json_decode($v->message);
                
                //if notification is related to post then get event name
                if($v->notificationType == 'create_event' || $v->notificationType == 'join_event' || $v->notificationType=='event_payment' || $v->notificationType=='share_event' || $v->notificationType=='companion_accept' || $v->notificationType=='companion_reject' || $v->notificationType=='companion_payment'){
                    //replace placeholder name with real event name
                    $notif_payload->body = $this->common_model->replace_event_placeholder_name($notif_payload->referenceId, $notif_payload->body);
                }
                
                //get fullName of user
                $notif_payload->body = $this->common_model->replace_user_placeholder_name($v->notificationBy, $notif_payload->body);
                
                $result[$k]->message = $notif_payload;

                $img = $this->db->select('(case
                when( MIN(uImg.userImgId) IS NULL) 
                THEN "'.$defaultUserImg.'" 
                ELSE  
                (SELECT 
                    (case 
                        when( image = "" OR  image IS NULL) 
                            THEN "'.$defaultUserImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            concat("'.$userImg.'", image) 
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage')->where(array('user_id'=>$v->notificationBy))->get(USERS_IMAGE.' as uImg')->row();
                
                $v->image = isset($img->profileImage) ? $img->profileImage : '';

                $v->timeElapsed = time_elapsed_string($v->crd); //add time_elapsed key to show time elapsed in user friendly string
            }
        }        
        return $result;

    } // End Function


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

    // for getting bank account detail of event's and appointment organiser
    function getBankAccountDetail($userId){

        $where = array('user_id'=>$userId);
        $getRespose = $this->common_model->getsingle(BANK_ACCOUNT_DETAILS,$where);       
        return $getRespose;   
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

    function optionDataUpdate($table,$data){

        $where=array('option_name'=>$data['option_name']);
        $response=$this->is_data_exists(OPTIONS,$where);
        if($response){ // Check page exist or not
          $this->db->update($table, $data, $where);
          return true;
        }else{
          $this->db->insert($table, $data);
          return true;
        }
    }// End function

    // Option data Retrive
    function optionDataRetrive($table,$data){
        $where=array('option_name'=>$data['option_name']);
        $response=$this->is_data_exists(OPTIONS,$where);
        if($response){ // Check page exist or not
          $query=$this->db->get_where($table, $where);
          return $query->row();
        }else{
          return  array();
        }
    }// End function

    function paymentDetail($id){
        $res = $this->db->select('*')->where('user_id',$id)->get(PAYMENT_TRANSACTIONS);
        if($res->num_rows()){
            $result = $res->row();
            return $result;
        }else{
            return FALSE;
        }
    }// End function

    // to check user is on free trial or subscribed
    function checkSuscription($id){
        
        $res = $this->db->select('subscriptionId,subscriptionStatus,crd')->where('userId',$id)->get(USERS);

        if($res->num_rows()){
            
            $result = $res->row();
            
            // for free trial user can access whole features
            $freetrialDate = date('Y-m-d H:i:s', strtotime($result->crd. ' + 30 days')); 
            
            if($freetrialDate < date('Y-m-d H:i:s') && ($result->subscriptionId == '' && $result->subscriptionStatus == '0')){                
                return FALSE;                
            }else{                
                return TRUE;
            }
        }else{
            return FALSE;
        }

    }// End function


    // to get appointment and event reviews list
    function getReviewsList($userId,$offset,$limit,$where,$eventId=''){

        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            IF(review.rating IS NULL or review.rating ="" or review.rating ="0","",review.rating) as rating,
            IF(review.reviewType IS NULL or review.reviewType ="" or review.reviewType ="0","",review.reviewType) as reviewType,
            IF(review.comment IS NULL or review.comment ="" or review.comment ="0","",review.comment) as comment,
            IF(review.crd IS NULL or review.crd ="" or review.crd ="0","",review.crd) as crd,
            IF(users.fullName IS NULL or users.fullName ="" or users.fullName ="0","",users.fullName) as fullName,
            IF(users.userId IS NULL or users.userId ="" or users.userId ="0","",users.userId) as userId,
            IF(event.eventId IS NULL or event.eventId ="" or event.eventId ="0","",event.eventId) as eventId,
            IF(event.eventName IS NULL or event.eventName ="" or event.eventName ="0","",event.eventName) as eventName,
            IF(event.eventStartDate IS NULL or event.eventStartDate ="" or event.eventStartDate ="0","",event.eventStartDate) as eventStartDate,
            IF(event.eventOrganizer IS NULL or event.eventOrganizer ="" or event.eventOrganizer ="0","",event.eventOrganizer) as eventOrganizer,
            IF(em.eventMemId IS NULL or em.eventMemId ="" or em.eventMemId ="0","",em.eventMemId) as eventMemId,
            IF(em.memberId IS NULL or em.memberId ="" or em.memberId ="0","",em.memberId) as memberId,
            IF(comp.compId IS NULL or comp.compId ="" or comp.compId ="0","",comp.compId) as compId,
            IF(comp.companionMemId IS NULL or comp.companionMemId ="" or comp.companionMemId ="0","",comp.companionMemId) as companionUserId,
            IF(app.appId IS NULL or app.appId ="" or app.appId ="0","",app.appId) as appId,
            IF(app.appointById IS NULL or app.appointById ="" or app.appointById ="0","",app.appointById) as appointById,
            IF(app.appointForId IS NULL or app.appointForId ="" or app.appointForId ="0","",app.appointForId) as appointForId,
            (
                case 
                    when( comp.companionMemId = '.$userId.')
                        THEN "Shared Event"
                    when( em.memberId = '.$userId.')
                        THEN "Administrator"
                    ELSE
                        ""
                    END
            ) as ownerType,
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
            $this->db->join(EVENTS.' as event','event.eventId = review.referenceId','left');
            $this->db->join(EVENT_MEMBER.' as em','em.event_id = event.eventId AND em.memberId='.$userId.'','left'); 
            $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = em.eventMemId AND comp.companionMemberStatus = 1 AND comp.companionMemId='.$userId.'','left'); 

            $this->db->join(APPOINTMENTS.' as app','app.appId = review.referenceId','left');

            $this->db->where(array('review.referenceId'=>$eventId,'review.reviewType'=>2));

        }else{
            $this->db->join(EVENTS.' as event','event.eventId = review.referenceId','left');
            $this->db->join(EVENT_MEMBER.' as em','em.event_id = event.eventId AND em.memberId='.$userId.'','left');
            $this->db->join(COMPANION_MEMBER.' as comp','comp.eventMem_Id = em.eventMemId AND comp.companionMemberStatus = 1 AND comp.companionMemId='.$userId.'','left');

            $this->db->join(APPOINTMENTS.' as app','app.appId = review.referenceId','left');
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

    // to convert byte to mb
    function convertByteToMb($bytes) {

        $bytes = number_format($bytes / 1048576, 2);

        return $bytes;
    }

} //end of class

/* Do not close php tags */