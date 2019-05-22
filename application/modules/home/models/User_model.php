<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    function getBuyerData($pwd){

        $check = $this->db->get_where(USERS,array('userId'=>$this->session->userdata('userId')))->row();
        if(!empty($check)){             
            if(password_verify($pwd,$check->password)){
                return TRUE;
            }
        }
        return FALSE;
    }

    function updatePassword($pwd){
        $where = array('userId'=>$this->session->userdata('userId'));
        $this->db->update('users', $pwd, $where);
        return TRUE;
    }

    //check for apointment
    function checkAppointment($userId){

        $u_id = $this->session->userdata('userId');

        $query = $this->db->query('SELECT * FROM '.APPOINTMENTS.' WHERE isFinish = 0 AND isDelete = 0 AND appointById = "'.$userId.'" AND appointForId ="'.$u_id.'" OR isFinish = 0 AND isDelete = 0 AND appointById = "'.$u_id.'" AND appointForId = "'.$userId.'" ');
        
        $isAppointment = $query->row();
        $result = new stdClass();
        if($isAppointment){
            // if isAppointment = 1 for send appointment and 2 for receved apointment, 0 no appiontment
            if($isAppointment->appointById == $u_id){

                $result->isAppointment = 1 ; //send appointment
            }else{
                $result->isAppointment = 2; //receved appointment
            }

        }else{
            $result->isAppointment = 0; // no appointment
        }
        return $result;
    }

    function getMyFaoriteList($userId,$offset,$limit){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('f.*,u.fullName,
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
            END ) as userImg , IF(w.name IS NULL or w.name ="","NA",w.name) as workName');

        $this->db->from(FAVORITES.' as f');

        $this->db->join(USERS.' as u','u.userId = f.favUserId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = f.favUserId','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');

        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $this->db->where(array('f.user_id'=>$userId));
        $this->db->limit($limit, $offset);
        $this->db->group_by('u.userId');
        $this->db->order_by('favId','DESC');
        $query  = $this->db->get();
        $result = $query->result();
        
        return $result;
    }

	function addProfileImage($img){
        
        $this->db->insert(USERS_IMAGE, $img);
        $lastId = $this->db->insert_id();
        return $lastId;
    }

    function updateProfileImage($userImgId, $userId, $img){

        $where = array('userImgId'=>$userImgId,'user_id'=>$userId,'isSocial'=>'0');
        $this->db->update(USERS_IMAGE, $img, $where);

        return TRUE;
    }

    function deleteProfileImages($userImgId, $userId, $imgCount, $faceVerifyStatus){

        $this->db->where(array('userImgId'=>$userImgId,'user_id'=>$userId));
        $this->db->delete(USERS_IMAGE);

        if ($imgCount == 1 && $faceVerifyStatus == 1) {
            $where = array('userId'=>$userId);
            $this->db->update(USERS, array('faceImage'=>'','isFaceVerified'=>'0'), $where);
        }
        
        return true; 
    }

    // to update user's profile
    function updateUserProfileData($userData,$id,$eduId='',$workId='',$intName=''){

        $this->db->where('userId',$id);
        $this->db->update(USERS,$userData);

        if(!empty($intName)){

            $checkInt = $this->db->select('*')->where(array('user_id'=>$id))->get(USERS_INTEREST_MAPPING);
            $this->db->where('user_id',$id);
            $this->db->delete(USERS_INTEREST_MAPPING);
            
            $newdata = array();
           
            for($i=0;$i<count($intName);$i++) {

                $interest = $this->common_model->is_id_exist(INTERESTS,"interest",$intName[$i]);

                if(!empty($interest)){
                    
                    $newdata[] = array(
                    'interest_id'=>$interest->interestId,
                    'user_id'=>$id
                    );
                }else{
                    
                    $insert['interest'] = $intName[$i];
                    $insert['type']     = 1;
                    $insert['crd']      = date('Y-m-d H:i:s');
                    $insert['upd']      = date('Y-m-d H:i:s');
                    // $intrest_id      = $this->common_model->insertData(INTERESTS,$insert);

                    $this->db->insert(INTERESTS, $insert);
                    $interest_id = $this->db->insert_id();
                    //print_r($intrest_id);die;
                    $newdata[] = array(
                        'interest_id' => $interest_id,
                        'user_id'     => $id
                    );
                }
            }
            $this->db->insert_batch(USERS_INTEREST_MAPPING,$newdata);           
        }
        
        if(!empty($eduId)){

            $this->db->where('user_id',$id);
            $this->db->delete(USERS_EDUCATION);
            $this->db->insert(USERS_EDUCATION,array('edu_id'=>$eduId,'user_id'=>$id));
        }

        if(!empty($workId)){

            $this->db->where('user_id',$id);
            $this->db->delete(USERS_WORK);
            $this->db->insert(USERS_WORK,array('work_id'=>$workId,'user_id'=>$id));
        }
        return TRUE;
    }

    // get invited member list by eventId
    function invitedMemberList($data){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $this->db->select('em.eventMemId,em.memberId,em.event_id as eventId,e.eventEndDate,u.fullName,(case 
            when( uImg.image = "" OR uImg.image IS NULL) 
            THEN "'.$defaultImg.'"
            when( uImg.image !="" AND uImg.isSocial = 1) 
            THEN uImg.image
            ELSE
            concat("'.$imgUrl.'",uImg.image) 
            END ) as userImg , IF(w.name IS NULL or w.name ="","NA",w.name) as workName');

        $this->db->from(EVENT_MEMBER.' as em');
        $this->db->join(EVENTS.' as e','e.eventId = em.event_id','left');
        $this->db->join(USERS.' as u','u.userId = em.memberId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = em.memberId','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');
        $this->db->where(array('em.memberStatus'=>0,'em.event_id'=>$data['eventId'],'em.memberId !=' =>$data['userId']));
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

            return $userData;
        }
        return array();

    } // end of function

    // count of all joined member list
    function countAllInvitedMember($data){

        $this->invitedMemberList($data);
        $query = $this->db->get();
        return $query->num_rows();

    } // end of function


    // get business detail
    function getBusinessDetail($userId){

        $defaultImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_BIZ_THUMB_IMG;

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

}