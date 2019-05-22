<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CommonFront {

	function __construct() {
        parent::__construct();
           
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('User_model');  
        $this->load_language_files();
        if($this->uri->segment(3) == 'updateProfile' || $this->uri->segment(3) == 'userProfile'){
            if($this->session->userdata('front_login') == FALSE ){
                redirect(site_url('home/login'));
            }
        }  
    }

    function changePassword(){

        $auth_res = $this->check_ajax_auth();

        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $this->check_user_session();
        $pwd = $this->input->post('oldP');
        $existPass = $this->User_model->getBuyerData($pwd);

        if($existPass == true){
            $userData = array(
                'password'=>password_hash($this->input->post('newP'), PASSWORD_DEFAULT)
            );        
            $data = $this->User_model->updatePassword($userData);
            echo json_encode(array('status'=>1));
            exit();
        }        
        echo json_encode(array('status'=>0));
        exit();                   
    } 

    function userProfile(){

        $this->check_user_session();

        $this->load->model('Subscription_model');
        $this->load->library('Stripe');

        $userId = $this->session->userdata('userId');

        $detail['works'] = $this->common_model->getWorkList();
        $detail['education'] = $this->common_model->getEducationList();
        $detail['interest'] = $this->common_model->getInterestList($userId);
        $detail['imgCount'] = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$userId));

        $detail['profile'] = $this->common_model->usersDetail($userId);
        
        $detail['images'] = $this->common_model->usersImage($userId);

        /* for premium subscription*/
        $detail['planDetail'] = $this->stripe->get_plan(STRIPE_PLAN_ID);
        $subsId = $this->Subscription_model->getSubscriptionId();
        
        if($subsId){
            $detail['subsDetail'] = $this->stripe->get_subscription($subsId);    
        }
        /* end premium subscription*/

        /* for business subscription*/
        $detail['bizPlanDetail'] = $this->stripe->get_plan(BUSINESS_PLAN_ID);
        $bizSubsId = $this->Subscription_model->getBizSubscriptionId();
        
        if($bizSubsId){
            $detail['bizSubsDetail'] = $this->stripe->get_subscription($bizSubsId);    
        }
        /* end business subscription*/

        $detail['bizDetail'] = $this->User_model->getBusinessDetail($userId);

        $detail['result'] = $this->common_model->getAll('country','iso','ASC','phonecode,iso');

        // to get bank account detail
        $detail['bankDetail'] = $this->common_model->getBankAccountDetail($userId);

        $detail['front_styles'] = array('css/semantic.min.css');  //load css

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $front_asset = AWS_CDN_FRONT_JS;
        $detail['front_scripts'] = array($load_custom_js.'profile.js',$front_asset.'semantic.min.js'); //load js

    	$this->load->front_render('profile',$detail,'');
    }

    function userOverviewTab(){
        
        $userId = $this->session->userdata('userId');
        $detail['profile'] = $this->common_model->usersDetail($userId);
        $detail['bizDetail'] = $this->User_model->getBusinessDetail($userId);
        $overView = $detail;
        $overView['type'] = 1;
        echo json_encode( array('overView'=>$this->load->view('overview_page',$detail, true),'uDetail'=>$this->load->view('overview_page',$overView, true)));exit;
    }

    function userImgSlider(){
        
        $userId = $this->session->userdata('userId');
        $detail['images'] = $this->common_model->usersImage($userId);        
        echo json_encode( array('slider'=>$this->load->view('new_img',$detail, true)));exit;
    }

    // to update notification status
    function notificationStatus(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        if($this->input->post('status')==0){
            $notiStatus = 0;
        }else{
            $notiStatus = 1;            
        }
        $res = array();
        $where = array('userId'=>$this->session->userdata('userId'));         
        //update status
        $result = $this->common_model->updateFields(USERS, array('isNotification'=>$notiStatus),$where);
        
        if($result){            
            $response = array('status' => 1, 'msg' => '');          
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));  
        } 
        echo json_encode($response); exit;   
    }

    function addProfileImage(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $res = array();
        $userId = $this->session->userdata('userId');
        $imgCount = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$userId));
        $res['img_count']=$imgCount;

        if($imgCount == 5){

            $res['status']=2;
            $res['msg']=lang('upload_maxfive_img');
            echo json_encode($res); exit;
        }

        $this->load->model('image_model');
        $countImg = $this->input->post('imgCount');
        $profileImage = '';  
        

        if(!empty($_FILES['profileImage']['name'])){

            // start detect image
            $imgFile['name']       = $_FILES['profileImage']['name'];
            $imgFile['type']       = $_FILES['profileImage']['type'];
            $imgFile['tmp_name']   = $_FILES['profileImage']['tmp_name'];
            $imgFile['size']       = $_FILES['profileImage']['size'];

            $megabytes = $this->common_model->convertByteToMb($imgFile['size']); // get image size in mb
            
            $imageUrl = $compressImage ='';
            if($megabytes > 2){
                $folder = 'faceProof';

                $compressImage = $this->image_model->upload_n_compress('profileImage',$folder);                

                if(!empty($compressImage) && is_array($compressImage)){
                    //$response = array('status'=>0,'msg'=>$compressImage['error']);
                    $response = array('status'=>0,'msg'=>lang('face_detect_common_msg'));
                    echo json_encode($response);exit;
                    return; 
                }

                $imageUrl = base_url().'uploads/faceProof/'.$compressImage;
            }

            if($imgFile['tmp_name'] != ''){

                $this->load->library('face_plus');  
       
                //$isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                if(!empty($imageUrl)){

                    $isDetected     = $this->face_plus->detectFaceImageUrl($imageUrl);

                }else{

                    $isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                }

                $detectedData   =  '';
                
                if (isset($isDetected->faces)&&empty($isDetected->faces)){
                    $detectedData = lang('face_detect_msg');
                }

                if (isset($isDetected) && is_string($isDetected)){
                    $detectedData = lang($isDetected);
                }

                if(!empty($compressImage)){

                    $file_name = $compressImage;
                    $img_path = FCPATH.'uploads/faceProof/';
                    $this->image_model->unlinkFile($img_path, $file_name); //unlink image from server directory  
                }  

                if(!empty($detectedData)){
                    $response = array('status'=>0,'msg'=>$detectedData);
                    echo json_encode($response);exit();
                    return;
                }
                $folder = 'profile';
                $profileImage = $this->image_model->updateMedia('profileImage',$folder);
            } 
            // end detect image

            if(!empty($profileImage) && is_array($profileImage)) {

                $data['error'] = $profileImage['error'];
                $response = array('status' => 0, 'msg' => $data['error'], 'url' => '');
                echo json_encode($response); exit; 

            }else{

                if(!empty($profileImage)){

                    $img['image']       = $profileImage;                

                    $img['user_id']     = $userId;
                    $new = array();
                    $new['newImg']      = AWS_CDN_USER_THUMB_IMG.$profileImage;
                    $new['countImg']    =  $countImg;

                    $new['lastId'] = $this->User_model->addProfileImage($img);

                    $new['imgCount'] = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$userId));

                    if( $new['lastId'] ){

                        $new['images']      = $this->common_model->usersImage($userId);
                        $res['status']      = 1;
                        $res['msg']         ='Succefully uploaded';
                        $res['sliderImg']   = $this->load->view('new_img',$new, true); 
                        $new['type']        = 1;
                        $res['html']        = $this->load->view('new_img',$new, true); 
                        echo json_encode($res); exit;                                 
                    }
                }

                $res['status']  = 0;
                $res['msg']     = lang('img_upload_err');
                echo json_encode($res); exit;
            }
        }        
    }

    function updateProfileImage(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $this->load->model('image_model');
        $userId = $this->session->userdata('userId');

        $userImgId = $this->input->post('userImgId');
        $profileImage = '';            
        if(!empty($_FILES['profileImage']['name'])){
            $folder = 'profile';
            $profileImage = $this->image_model->updateMedia('profileImage',$folder);

            if(is_array($profileImage)) {
                $data['error'] = $profileImage['error'];
                $response = array('status' => 0, 'msg' => $data['error'], 'url' => '');
                echo json_encode($response); exit;     
            }else{
                $img['image'] = $profileImage;

                $viewData = $this->User_model->updateProfileImage($userImgId, $userId, $img);
                if($viewData == TRUE){            
                    $response = array('status' => 1, 'msg' => lang('updated_success'), 'url' => '');          
                }else{
                    $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url' => '');  
                } 
            }
        }
        echo json_encode($response); exit;      
    }

    function deleteProfileImages(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId             = $this->session->userdata('userId');
        $userImgId          = $this->input->post('userImgId');
        $imgCount           = $this->input->post('imgCount');
        $faceVerifyStatus   = $this->input->post('faceVerifyStatus');

        $viewData = $this->User_model->deleteProfileImages($userImgId, $userId , $imgCount, $faceVerifyStatus);
        $imgCount = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$this->session->userdata('userId')));
        
        if($viewData == TRUE){
            echo json_encode(array('status'=>1,'remcount'=>$imgCount));            
        }else{
            echo json_encode(array('status'=>0,'remcount'=>$imgCount));      
        }          
    }

    function updateUserProfileData(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $id = $this->session->userdata('userId');

        $this->load->library('form_validation');
    
        $this->form_validation->set_rules('work_id', lang('work'), 'required');
        $this->form_validation->set_rules('edu_id', lang('education'), 'required');
        $this->form_validation->set_rules('height', lang('height'), 'required');
        $this->form_validation->set_rules('weight', lang('weight'), 'required');
        $this->form_validation->set_rules('unit', lang('unit'), 'required');
        $this->form_validation->set_rules('relationship', lang('relationship'), 'required');

        if(empty($this->input->post('language')))
            $this->form_validation->set_rules('language', lang('language'), 'required');

        if(empty($this->input->post('interest_id')))
            $this->form_validation->set_rules('interest_id', lang('interest'), 'required');
        //$this->form_validation->set_rules('interest_id', 'Interest', 'required');

        if ($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/user/userProfile')); 

        } else {         
            
            $userData['about'] = trim($this->input->post('about'));
            $userData['height'] = $this->input->post('height');
            $userData['weight'] = $this->input->post('weight').' '.$this->input->post('unit');
            $userData['Relationship'] = $this->input->post('relationship');
            $userData['language'] = !empty($this->input->post('language')) ? implode(',',$this->input->post('language')) :'';
            $userData['isProfileUpdate']  = '1';         

            $eduId = $this->input->post('edu_id');
            $workId = $this->input->post('work_id');
            $interestId = $this->input->post('interest_id'); 
          
            $userData['upd'] = date('Y-m-d H:i:s');                 
           
            $isUpdated = $this->User_model->updateUserProfileData($userData,$id,$eduId,$workId,$interestId);

            if($isUpdated == TRUE){

                $response = array('status' => 1, 'msg' => lang('profile_updated_success') , 'url' => base_url('home/user/userProfile'));  

            }  else{
                
                $response = array('status' => 0, 'msg' => lang('something_wrong') , 'url' => base_url('home/user/userProfile'));
            }
            
        }
        echo json_encode($response);
    }

    function updateUserBasicInfo(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $id = $this->session->userdata('userId');

        $this->load->library('form_validation');

        $this->form_validation->set_rules('fullName', lang('full_name'), 'required');
        $this->form_validation->set_rules('birthday', lang('birthday_placeholder'), 'required');
        $this->form_validation->set_rules('gender', lang('gender'), 'required');
        $this->form_validation->set_rules('showOnMap', lang('show_on_map'), 'required');
        $this->form_validation->set_rules('appointmentType', lang('appointment_type'), 'required');
        $this->form_validation->set_rules('address', lang('address'), 'required|callback__check_lat_long',array('_check_lat_long'=>lang('valid_address')));        

        $gender = $this->input->post('gender');
        if($gender == '2'){
            $this->form_validation->set_rules('eventInvitation', lang('event_invitation'), 'required');
        }

        if ($this->form_validation->run() == FALSE){
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/user/userProfile'));  
        } else {           
               
            $userData['fullName']       = $this->input->post('fullName');
            $userData['birthday']       = date('Y-m-d',strtotime($this->input->post('birthday')));
            
            $userData['gender']         = $gender;
            $userData['showOnMap']      = $this->input->post('showOnMap');
            $userData['appointmentType'] = $this->input->post('appointmentType');

            if($gender == '2'){
                $userData['eventInvitation'] = $this->input->post('eventInvitation');
            }else{
                $userData['eventInvitation'] = '3';
            }
            
	        $userData['isProfileUpdate']  = '1';
            $diff = (date('Y') - date('Y',strtotime($this->input->post('birthday'))));
            $userData['age']    = $diff;

            if(!empty($this->input->post('latitude')) && !empty($this->input->post('longitude')) && $this->input->post('address')){
                $userData['latitude']   = $this->input->post('latitude');
                $userData['longitude']  = $this->input->post('longitude');
                $userData['address']    = $this->input->post('address');
                $userData['city']       = $this->input->post('city');
                $userData['state']      = $this->input->post('state');
                $userData['country']    = $this->input->post('country');
            }
          
            $userData['upd'] = date('Y-m-d H:i:s');                 
           
            $isUpdated = $this->User_model->updateUserProfileData($userData,$id);

            if($isUpdated == TRUE){

                $response = array('status' => 1, 'msg' => lang('profile_updated_success') , 'url' => base_url('home/user/userProfile'));  

            }  else{

                $response = array('status' => 0, 'msg' => lang('something_wrong') , 'url' => base_url('home/user/userProfile'));
            }            
        }
        echo json_encode($response);
    }

    function _check_lat_long(){
        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');        
        if(empty($latitude) && empty($longitude)){
            return FALSE;
        }
        return True;        
    }

    function favouriteList(){
        
        $this->check_user_session();
        $userId = $this->session->userdata('userId');
        $data['page'] = $this->input->post('page');
        $limit = 3;
        $offset = $data['page']*$limit;
        $data['favUser'] = $this->User_model->getMyFaoriteList($userId,$offset,$limit);

        $this->load->view('favourite_list',$data);
    }

    function userDetail(){

        if(isset($_GET['userId'])){
            redirect('home/user/userDetail/'.encoding($_GET['userId']).'/');
        }

    	$userId = decoding($this->uri->segment(4));
        $isExist = $this->common_model->is_data_exists(USERS, array('userId'=>$userId));

        if(!$isExist){
            redirect('home/recordNotFound');
        }

        $myId = $this->session->userdata('userId');

        if(!empty($myId)){
            //check data exist
            $where = array('visit_by_id'=>$myId,'visit_for_id'=>$userId);
            $isExist = $this->common_model->is_data_exists(VISITORS, $where);
            if(empty($isExist)){
                // to insert visitors record for showing total visitor
               $this->common_model->insertData(VISITORS,array('visit_by_id'=>$myId,'visit_for_id'=>$userId)); 
            }
        }    

    	$detail['userDetail']   = $this->common_model->usersDetail($userId);
        $detail['images']       = $this->common_model->usersImage($userId);
        $detail['fav']          = $this->common_model->checkfavorite($userId,$myId);
        $detail['like']         = $this->common_model->checkLike($userId,$myId);
        $detail['appStatus']    = $this->User_model->checkAppointment($userId);
        $detail['bizDetail']    = $this->User_model->getBusinessDetail($userId);
        $detail['requestStatus'] = $this->common_model->getFriendRequestStatus($userId,$myId);
        
        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $detail['front_scripts'] = array($load_custom_js.'add_remove_fav.js');
        $this->load->front_render('near_user_detail',$detail,'');
    }

    // add and delete my favorite user
    function addRemoveFavorite(){
        
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $user_id    = $this->session->userdata('userId');
        $isFavorite = $this->input->post('isFavorite');
        $favUserId  = decoding($this->input->post('favUserId')); 

        $where = array('user_id'=>$user_id,'favUserId'=>$favUserId);

        //check for delete
        if($isFavorite==0){
            
            $result = $this->common_model->deleteData(FAVORITES,$where);
            //check for data delete yes or not
            if($result){
                $response = array('status' => 1, 'msg' => lang('fav_remove_success'), 'url' => base_url('home/user/userDetail/').encoding($favUserId).'/');
            }else{
                $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url' => base_url('home/user/userDetail/').encoding($favUserId).'/');
            }

        }else{ // isfavorite = 1 for add fav
            
            $insertData['user_id']   = $user_id;
            $insertData['favUserId'] = $favUserId;
            $insertData['crd']       = date('Y-m-d H:i:s');
       
            $result = $this->common_model->insertData(FAVORITES,$insertData);
            //check for data insert yes or not
            if($result){

                $where = array('userId'=>$favUserId,'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);
                if($user_info_for){               
                    $registrationIds[] = $user_info_for->deviceToken; 

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Favoritos';
                        $showMsg = ' te agregó como favorito.';
                    }else{
                        $title = 'Favorite';
                        $showMsg = ' added you as a favorite.';
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'add_favorite';
                    $notify_for = $user_info_for->userId;                
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$favUserId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                   
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => 2, 'msg' => lang('fav_added_success'), 'url' => base_url('home/user/userDetail/').encoding($favUserId).'/');
            }else{
                $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url' => base_url('home/user/userDetail/').encoding($favUserId).'/');                
            }            
        }
        echo json_encode($response);    
    }

    //  remove my favorite from list
    function removeFavoriteFromList(){
        
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $favId = $this->input->post('favId');

        $where = array('favId'=>$favId);
        //check for data exits
        $exist = $this->common_model->is_data_exists(FAVORITES, $where);
        if($exist){

            $result = $this->common_model->deleteData(FAVORITES,$where);
            //check for data delete yes or not
            if($result){
                $response = array('status' => 1, 'msg' => lang('fav_remove_success'), 'url' => base_url('home/user/userProfile/'));
            }else{
                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url' => base_url('home/user/userProfile/'));
            }
                     
        }else{
            $response = array('status' => 0, 'message' => lang('not_found'));   
        }
       
        echo json_encode($response);    
    }


    // add and delete my like user
    function addRemoveLike(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $user_id = $this->session->userdata('userId');
        $isLike = $this->input->post('isLike');
        $likeUserId = decoding($this->input->post('likeUserId'));

        $where = array('user_id'=>$user_id,'likeUserId'=>$likeUserId);
        //check for delete
        if($isLike==0){            
           
            $result = $this->common_model->deleteData(LIKES,$where);
            //check for data delete yes or not
            if($result){
                $response = array('status' => 1, 'msg' => lang('like_remove_success'), 'url' => base_url('home/user/userDetail/').encoding($likeUserId).'/');
            }else {
                $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url' => base_url('home/user/userDetail/').encoding($likeUserId).'/');
            }

        }else{
           
            $insertData['user_id']   = $user_id;
            $insertData['likeUserId'] = $likeUserId;
            $insertData['crd']       = date('Y-m-d H:i:s');
       
            $result = $this->common_model->insertData(LIKES,$insertData);
            //check for data insert yes or not
            if($result){

                $where = array('userId'=>$likeUserId,'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);
                if($user_info_for){               
                    $registrationIds[] = $user_info_for->deviceToken; 

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Me gusta';
                        $showMsg = ' le gusta tu perfil.';
                    }else{
                        $title = 'Like';
                        $showMsg = ' likes your profile.';
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'add_like';
                    $notify_for = $user_info_for->userId;                
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$likeUserId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                 
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => 2, 'msg' => lang('like_added_success'), 'url' => base_url('home/user/userDetail/').encoding($likeUserId).'/');
            }else{
                $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url' => base_url('home/user/userDetail/').encoding($likeUserId).'/');  
            }
        } 
        echo json_encode($response);          
    }

    // for showing friend list using ajax
    function friendList(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $result['page']       = $this->input->post('page');
        $data['limit']        = 6;
        $data['offset']       = $result['page']*$data['limit'];        
        $data['userId']       = $this->session->userdata('userId');

        $data['eventId']      = '';
        $result['friendList'] = $this->common_model->friendListCount($data);
        
        //if($data['offset']==0){
            $result['friendListCount'] = $this->common_model->countAllFriend($data);
        //}
        
        $this->load->view('friend_list',$result);
    }

    //unfriend or delete friends
    function unfriend(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $friendId = $this->input->post('friendId');;
        $where    = array('friendId'=>$friendId);
        //check data exist 
        $exist    = $this->common_model->is_data_exists(FRIENDS,$where);
        if(!$exist){
            $response = array('status' => 2, 'msg'=> lang('not_found'), 'url'=>base_url('home/user/userProfile'));
            echo json_encode($response); exit;
        }
        //delete record
        $delete = $this->common_model->deleteData(FRIENDS,$where);
        if($delete){
            //check data deleted yes or not
            $response = array('status'=> 1, 'msg'=> lang('unfriend_success'), 'url'=>base_url('home/user/userProfile'));
            echo json_encode($response); exit;
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url'=>base_url('home/user/userProfile'));
            echo json_encode($response); exit;
        } 
    }

    // for showing friend request list using ajax
    function friendRequestList(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $result['page']       = $this->input->post('page');
        $data['limit']        = 6;
        $data['offset']       = $result['page']*$data['limit'];        
        $data['userId']       = $this->session->userdata('userId');

        $result['requestList'] = $this->common_model->requestListCount($data);
        //if($data['offset']==0){
            $result['requestListCount'] = $this->common_model->countAllRequest($data);
        //}
        
        $this->load->view('friend_request_list',$result);
    }


     //send friend request
    function sendFriendRequest(){
        
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $user_id    = $this->session->userdata('userId');
        $requestFor = $this->input->post('requestFor');
        
        $where = array('requestBy'=>$user_id,'requestFor'=>$requestFor);
      
        //check for data exist
        $exist = $this->common_model->is_data_exists(REQUESTS,$where);
        if($exist){
            $response = array('status' => 2, 'msg'=> lang('request_already_sent'), 'url'=>base_url('home/user/userDetail/').encoding($requestFor).'/');
            echo json_encode($response); exit;
        }
        $data['requestBy']  = $user_id;
        $data['requestFor'] = $requestFor;
        $data['crd']        = date('Y-m-d H:i:s');
        //data insert in request table
        $result = $this->common_model->insertData(REQUESTS,$data);
        //check for data inserted yes or not
        if($result){

            $where = array('userId'=>$requestFor,'isNotification'=>1);
            $user_info_for = $this->common_model->getsingle(USERS,$where);
            if($user_info_for){               
                $registrationIds[] = $user_info_for->deviceToken; 

                if($user_info_for->setLanguage == 'spanish'){
                    $title = 'Solicitud de amistad';
                    $showMsg = ' te ha enviado una solicitud de amistad.';
                }else{
                    $title = 'Friend Request';
                    $showMsg = ' has sent you a friend request.';
                }

                $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'friend_request';
                $notify_for = $user_info_for->userId;                
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$requestFor, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }
            $response = array('status' => 1, 'msg' => lang('request_send_success'), 'url'=>base_url('home/user/userDetail/').encoding($requestFor).'/',);
            echo json_encode($response);exit;
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'), 'url'=>base_url('home/user/userDetail/').encoding($requestFor).'/');
            echo json_encode($response);exit;
        }
    }

    //friend request for accept / reject / cancel
    function friendRequest(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $user_id    = $this->session->userdata('userId');
        $requestFor = $this->input->post('requestFor');
        $status     = $this->input->post('status');

        $isExist = $this->common_model->is_data_exists(USERS, array('userId'=>$requestFor));

        if(!$isExist){
            redirect('home/recordNotFound');
        }

        $where = array('requestBy'=>$user_id,'requestFor'=>$requestFor);
        
        // status ==2 for accept request and status == 3 for reject/delete request
        if($status == "2"){

            //check for data exist
            $where = array('requestBy'=>$requestFor,'requestFor'=>$user_id);
            $exist = $this->common_model->is_data_exists(REQUESTS,$where);
            if(!$exist){
                $response = array('status' => 0,'msg' => lang('not_found'));
                echo json_encode($response); exit;
            }
            //FRIENDS
            $insertData['byId']  = $user_id;
            $insertData['forId'] = $requestFor;
            $insertData['crd']   = date('Y-m-d H:i:s');
            //delete request from request table
            $where  = array('requestBy'=>$requestFor,'requestFor'=>$user_id);
            $delete = $this->common_model->deleteData(REQUESTS,$where);
            //insert record in friend table
            $result = $this->common_model->insertData(FRIENDS,$insertData);
            //check data inserted in table
            if($result){

                $where = array('userId'=>$requestFor,'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);
                if($user_info_for){               
                    $registrationIds[] = $user_info_for->deviceToken; 
                    
                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Solicitud aceptada';
                        $showMsg = ' ha aceptado tu solicitud de amistad.';
                    }else{
                        $title = 'Request Accepted';
                        $showMsg = ' has accepted your friend request.';
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'accept_request';
                    $notify_for = $user_info_for->userId;                
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$requestFor, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                    
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status'=> 1,'msg'=>lang('request_accepted'));
                echo json_encode($response); exit;
            }else{
                $response = array('status' => 3, 'msg' => lang('something_wrong'));
                echo json_encode($response); exit;
            }
        }else{    // status == 3 for reject/delete request          

            //check for data exist
            $where = array('requestBy'=>$user_id,'requestFor'=>$requestFor);
            $requestExist = $this->common_model->is_data_exists(REQUESTS,$where);

            if($requestExist){
                
                // cancle own sent request
                $delete = $this->common_model->deleteData(REQUESTS,$where);
                //check request delete yes or not
                if($delete){
                    $response = array('status' => 4,'msg' => lang('request_canceled'));
                    echo json_encode($response); exit;
                }
            }else{
                
                //delete request if rejected
                $where  = array('requestBy'=>$requestFor,'requestFor'=>$user_id);

                $delete = $this->common_model->deleteData(REQUESTS,$where);
                //check request delete yes or not
                if($delete){
                    $response = array('status'=> 2,'msg'=>lang('request_rejected'));
                    echo json_encode($response); exit;
                }else{
                    $response = array('status' => 3, 'msg' =>lang('request_reject_or_accept'));
                    echo json_encode($response); exit;
                }
            }            
        }              
    }

    // to get appointment and event reviews list
    function appReviewList(){

        $userId = $this->input->post('id');

        $result['page'] = $this->input->post('page');
        $type = $this->input->post('type');

        $limit = 12;
        $offset = $result['page']*$limit;
        
        $result['appReviewList'] = $this->common_model->getReviewsList($userId,$offset,$limit,$type); 
        $result['userId'] = $userId;
        
        $this->load->view('app_event_review_list',$result);
    }
}