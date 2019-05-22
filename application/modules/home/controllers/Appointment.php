<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends CommonFront {

	function __construct() {
        parent::__construct();  
        date_default_timezone_set('Asia/Kolkata');
        /*if($this->uri->segment(3) == 'createAppointment' || $this->uri->segment(2) == 'appointment'){
            if($this->session->userdata('front_login') == FALSE ){
                redirect(site_url('home/login'));
            }
        }*/
        $this->load->model('Appointment_model');
        $this->load_language_files();    
    }

    function index(){  
        //redirect('home/development');
        $this->check_user_session();   

        /*$isSuscribe = $this->common_model->checkSuscription($this->session->userdata('userId'));

        if($isSuscribe === TRUE){*/
            $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
            $detail['front_scripts'] = array($load_custom_js.'app_list.js');
            $this->load->front_render('appointment',$detail,'');
        /*}else{
            $this->load->front_render('suscribe','');
        } */
    }

    function appointmentList(){
        
        //redirect('home/development');
        $userId         = $this->session->userdata('userId');
        $data['type']   = $this->input->post('type');
        //$data['status'] = $this->input->post('status');
        $where          = array($data['type']=>$userId);
        $data['page']   = $this->input->post('page');

        $limit = 6;
        $start = $data['page'];

        $data['myApp']      = $this->Appointment_model->getAppointmentListPage($where,$limit,$start,$userId,$data);
        $data['myAppCount'] = $this->Appointment_model->countAllgetAppointmentList($where,$limit,$start,$userId,$data);
        
        /* is_next: var to check we have records available in next set or not
         * 0: NO, 1: YES
         */
        $is_next = 0;
        $new_offset = $data['page'] + $limit;
        if($new_offset<$data['myAppCount']){
            $is_next = 1;
        }

        $this->load->view('sent_app_list',$data);

        $myAppHtml = $this->load->view('sent_app_list',$data, true);
        echo json_encode( array('status'=>1, 'html'=>$myAppHtml, 'isNext'=>$is_next, 'newOffset'=>$new_offset) ); exit;
    }

    function createAppointment(){

        //redirect('home/development');
        $this->check_user_session();   
        $userId = decoding($this->uri->segment(4));
        $isExist = $this->common_model->is_data_exists(USERS, array('userId'=>$userId));

        if(!$isExist){
            redirect('home/recordNotFound');
        }
        $detail['userDetail'] = $this->common_model->usersDetail($userId);
        $detail['bizList'] = $this->Appointment_model->getBusinessList();

        /*$isSuscribe = $this->common_model->checkSuscription($this->session->userdata('userId'));

        if($isSuscribe === TRUE){*/
            // $detail['front_scripts'] = array('custom/js/appointment.js');
            $this->load->front_render('create_appointment',$detail,'');
        /*}else{
            $this->load->front_render('suscribe','');
        }*/   
    }

    // for creating new appointment
    function createMyApp(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $forId = decoding($this->uri->segment(4));
        $user_id = $this->session->userdata('userId');

        $bizAdd  = $this->input->post('bizAdd');

        $this->load->library('form_validation');

        if(empty($bizAdd )){

            $this->form_validation->set_rules('bizAdd', lang('app_meet_loc'), 'required|callback__check_lat_long',array('_check_lat_long'=>'Please enter valid address'));
        }

        $this->form_validation->set_rules('appointDate', lang('app_date'), 'required');

        if ($this->form_validation->run() == FALSE){
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/appointment/createAppointment/').encoding($forId).'/');

        } else {

            $date = DateTime::createFromFormat('d/m/Y h:i a', $this->input->post('appointDate'));

            $data['appointById']        = $user_id;
            $data['appointForId']       = $forId;
            $data['appointDate']        = date('Y-m-d',strtotime($this->input->post('appointDate')));
            $data['appointTime']        = date('H:i:s',strtotime($this->input->post('appointDate')));
            $data['appointAddress']     = $this->input->post('bizAdd');
            $data['appointLatitude']    = $this->input->post('bizLat');
            $data['appointLongitude']   = $this->input->post('bizLong');
            $data['offerPrice']         = !empty($this->input->post('offerPrice')) ? $this->input->post('offerPrice') : '';
            $data['business_id']        = !empty($this->input->post('bizId')) ? $this->input->post('bizId') : '';
            $data['offerType']          = $this->input->post('offerType'); // 1:Paid,2:Free
            $data['crd']                = date('Y-m-d H:i:s');

            $where = array('appointById'=>$user_id,'appointForId'=>$data['appointForId'],'isFinish'=>0,'isDelete'=>0);
            //check is data exist
            /* $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if($apoimexist){
                $response = array('status' => 1, 'msg' => 'Appointment is already created', 'url' => base_url('home/appointment/createAppointment/').encoding($forId).'/');
                echo json_encode($response); exit;
            }else{*/

                //get data
                $appoimData = $this->common_model->insertData(APPOINTMENTS,$data);
                if($appoimData){

                    $where = array('userId'=>$data['appointForId'],'isNotification'=>1);
                    $user_info_for = $this->common_model->getsingle(USERS,$where);

                    if($user_info_for){       

                        $registrationIds[] = $user_info_for->deviceToken;

                        if($user_info_for->setLanguage == 'spanish'){
                            $title = 'Nueva cita';
                            $showMsg = ' ha creado una cita contigo.';
                        }else{
                            $title = 'New Appointment';
                            $showMsg = ' has created appointment with you.';
                        }

                        $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                        $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                        $notif_type = 'create_appointment';
                        $notify_for = $user_info_for->userId;                
                       
                        //send notification to user
                        $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appoimData,$notif_type);

                        $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appoimData);

                        $notif_msg['body'] = $body_save; //replace body text with placeholder text
                        //save notification

                        $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                        
                        $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                        $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                    }

                    $response = array('status' => 2, 'msg' => lang('request_sent'), 'url' => base_url('home/appointment/'));
                }else{
                    $response = array('status' => 3, 'msg' => lang('something_wrong'), 'url' => base_url('home/appointment/createAppointment/').encoding($forId).'/');  
                }
            //}
        }
        echo json_encode($response);

    } // End Of Function

    function _check_lat_long(){

        $appointLatitude = $this->input->post('bizLat');
        $appointLongitude = $this->input->post('bizLong');        
        if(empty($appointLatitude) && empty($appointLongitude)){
            return FALSE;
        }
        return True;        
    }

    function updateAppointment(){

        $this->check_user_session();
        $appId = decoding($this->uri->segment(4));
        $userId = decoding($this->uri->segment(5));
        $data_val['userDetail'] = $this->common_model->usersDetail($userId);
        $data_val['bizList'] = $this->Appointment_model->getBusinessList();
        $data_val['appDetail'] = $this->Appointment_model->getAppData($appId,$userId);
        $this->load->front_render('update_appointment',$data_val,'');

    } // End of function

    // to update appointment
    function updateMyApp(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $appId = decoding($this->uri->segment(4));
        $appointForId = $this->input->post('appointForId');
        $user_id = $this->session->userdata('userId');

        $where = array('appId'=>$appId);
        $checkStatus = $this->common_model->getsingle(APPOINTMENTS,$where);

        if($checkStatus){

            if($checkStatus->appointmentStatus == 2){
                $response = array('status' => 0, 'msg' => lang('cant_update_app') , 'url' => base_url('home/appointment/updateAppointment/').encoding($appId).'/'.encoding($appointForId).'/');
                echo json_encode($response); exit;
            }
        }

        $appointDate  = date('Y-m-d',strtotime($this->input->post('appointDate')));
        $appointTime  = date('H:i:s',strtotime($this->input->post('appointDate')));

        $newDate = strtotime($appointDate.' '.$appointTime);
        $currentDate = strtotime(date('Y-m-d H:i:s'));

        if($newDate <= $currentDate ){
            $response = array('status' => 0, 'msg' => lang('one_hour_more') , 'url' => base_url('home/appointment/updateAppointment/').encoding($appId).'/'.encoding($appointForId).'/');
            echo json_encode($response); exit;
        }

        $bizAdd  = $this->input->post('bizAdd');

        $this->load->library('form_validation');

        if(empty($bizAdd )){

            $this->form_validation->set_rules('bizAdd', lang('app_meet_loc'), 'required|callback__check_lat_long',array('_check_lat_long'=>'Please enter valid address'));
        }

        $this->form_validation->set_rules('appointDate', lang('app_date'), 'required');

        if ($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/appointment/updateAppointment/').encoding($appId).'/'.encoding($appointForId).'/');
            echo json_encode($response); exit;

        } else {

            $date = DateTime::createFromFormat('d/m/Y h:i a', $this->input->post('appointDate'));

            $data['appointById']        = $user_id;
            $data['appointForId']       = $appointForId;
            $data['appointDate']        = $appointDate;
            $data['appointTime']        = $appointTime;
            $data['appointAddress']     = $this->input->post('bizAdd');
            $data['appointLatitude']    = $this->input->post('bizLat');
            $data['appointLongitude']   = $this->input->post('bizLong');
            $data['offerPrice']         = !empty($this->input->post('offerPrice')) ? $this->input->post('offerPrice') : '';
            $data['business_id']        = !empty($this->input->post('bizId')) ? $this->input->post('bizId') : '';
            $data['offerType']          = $this->input->post('offerType'); // 1:Paid,2:Free
            $data['upd']                = date('Y-m-d H:i:s');

            
            //check is data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => 0, 'msg' => lang('app_not_exist'), 'url' => base_url('home/appointment/updateAppointment/').encoding($appId).'/'.encoding($appointForId).'/');
                echo json_encode($response); exit;
            }else{

                //upadate data
                
                $isUpdated = $this->common_model->updateFields(APPOINTMENTS, $data,$where);

                if($isUpdated){

                    $whereUser = array('userId'=>$data['appointForId'],'isNotification'=>1);
                    $user_info_for = $this->common_model->getsingle(USERS,$whereUser);

                    if($user_info_for){       

                        $registrationIds[] = $user_info_for->deviceToken;

                        if($user_info_for->setLanguage == 'spanish'){
                            $title = 'Actualizar cita';
                            $showMsg = ' ha modificado la cita con usted.';
                        }else{
                            $title = 'Update Appointment';
                            $showMsg = ' has modified appointment with you.';
                        }

                        $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                        $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                        $notif_type = 'update_appointment';
                        $notify_for = $user_info_for->userId;                
                       
                        //send notification to user
                        $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                        $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                        $notif_msg['body'] = $body_save; //replace body text with placeholder text
                        //save notification

                        $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                        
                       /* if( $webToken != 'website'){
                            $insertdata['webShow'] = 1;
                        }*/
                        $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                        $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                    }

                    $response = array('status' => 2, 'msg' => lang('request_sent'),'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');
                }else{
                    $response = array('status' => 3, 'msg' => lang('something_wrong'), 'url' => base_url('home/appointment/updateAppointment/').encoding($appId).'/'.encoding($appointForId).'/');
                }
            }
        }
        echo json_encode($response);

    } // End Of Function

    // for accept / reject appointment status
    function appointmentStatus(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $userId = $this->session->userdata('userId');
        $appId = $this->input->post('appId');
        $appointeStatus = $this->input->post('appStatus'); // 2 for accept / 3 for reject or delete

        $where = array('appId'=>$appId);

        if($appointeStatus == '2'){ // 2 for accept
           
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => 0, 'msg' => lang('app_not_exist'));
            }

            $updateData['appointmentStatus']    = $appointeStatus;
            $updateData['upd']                  = date('Y-m-d H:i:s');

            //update status
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

            if($appointData){

                $user_info_for = $this->common_model->getByDeviceToken($appId,$userId);

                if($user_info_for){

                    $registrationIds[] = $user_info_for->deviceToken;

                    if($user_info_for->setLanguage == 'spanish'){
                        $showMsg = ' ha confirmado su cita.';
                        $title = 'Cita confirmada';
                    }else{
                        $showMsg = ' has confirmed your appointment.';  
                        $title = 'Appointment confirmed';                 
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'confirmed_appointment';
                    $notify_for = $user_info_for->userId;
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                    
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }
                $response = array('status' => 1, 'msg' => lang('status_updated'),'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');            
            }else{
                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');            
            }

        }elseif($appointeStatus == '3'){ // 3 for reject

            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);

            if(empty($apoimexist)){
                $response = array('status' => 0, 'msg' => lang('app_not_exist'), 'url'=>base_url('home/appointment/'));
            }
            
            $user_info_for = $this->common_model->getDeviceToken($appId,$userId);

            if($user_info_for){   

                $registrationIds[] = $user_info_for->deviceToken; 

                if($user_info_for->setLanguage == 'spanish'){
                    $showMsg = ' ha rechazado su cita.';
                    $title = 'Cita rechazada';
                }else{
                    $showMsg = ' has rejected your appointment.';
                    $title = 'Appointment rejected';                   
                }

                $title = lang('rej_app_title');

                $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'delete_appointment';
                $notify_for = $user_info_for->userId;               
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }

            //delete record
            //$appointData = $this->common_model->deleteData(APPOINTMENTS, $where);

            $updateData['appointmentStatus']    = $appointeStatus;
            $updateData['upd']                  = date('Y-m-d H:i:s');
            
            //update status
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

            if($appointData){

                $response = array('status' => 1, 'msg' => lang('status_updated'), 'url'=>base_url('home/appointment/'));
            }else{

                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url'=>base_url('home/appointment/'));
            }
        }

        echo json_encode($response);

    } // End Of Function

    //to apply counter for appointment
    function applyCounter(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('counterPrice',lang('counter_price_title'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds);
            echo json_encode($response);exit();

        } else {

            $userId         = $this->session->userdata('userId');
            $appId          = $this->input->post('appointId');
            $appointById    = $this->input->post('appointById');
            $where          = array('appId'=>$appId);
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => 0, 'msg' => lang('app_not_exist'));
                echo json_encode($response);exit();
            }
            
            $whereCounter    = array('appId'=>$appId,'isCounterApply'=>'1');
            //check data exist
            $counterExist = $this->common_model->is_data_exists(APPOINTMENTS, $whereCounter);
            if(!empty($counterExist)){
                $response = array('status' => 0, 'msg' => lang('already_counter_applied'));
                echo json_encode($response);exit();
            }

            //update counter price
            $updateData['counterPrice']     = $this->input->post('counterPrice');
            $updateData['isCounterApply']   = '1';
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

            if($appointData){

                $whereUser = array('userId'=>$appointById,'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$whereUser);

                if($user_info_for){           

                    $registrationIds[] = $user_info_for->deviceToken; 
                    $title = lang('counter_app_title');

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Contador';
                        $showMsg = ' ha aplicado un contador en su cita.';
                    }else{
                        $title = 'Counter';
                        $showMsg = ' has applied counter on your appointment.';
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'apply_counter';
                    $notify_for = $user_info_for->userId;                
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => 1, 'msg' => lang('counter_applied_success'),'url'=>base_url('home/appointment/'));
                echo json_encode($response);exit();

            }else{

                $response = array('status' => 0, 'msg' => lang('something_wrong'));
                echo json_encode($response);exit();
            }
        }
    }

    //to update applied counter status(accept / reject) for appointment
    function updateCounter(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId         = $this->session->userdata('userId');
        $appId          = $this->input->post('appId');
        $appointForId   = $this->input->post('appointForId');
        $counterStatus  = $this->input->post('counterStatus'); // 1 for accepted or 2 for rejected

        $where          = array('appId'=>$appId);
        //check data exist
        $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
        if(empty($apoimexist)){
            $response = array('status' => 0, 'msg' => lang('app_not_exist'));
            echo json_encode($response);exit();
        }            

        //update counter price
        if($counterStatus == 1){

            $updateData['counterStatus']     = $counterStatus; // 1 for accepted or 2 for rejected
            $updateData['appointmentStatus'] = 2; // 2 for rejected

        }else{ // 2 for rejected
            
            //delete record
            //$appointData = $this->common_model->deleteData(APPOINTMENTS, $where); 
            $updateData['counterStatus']     = $counterStatus; // 1 for accepted or 2 for rejected
            $updateData['appointmentStatus'] = 3; // 3 for rejected
        }

        $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

        if($appointData){

            $whereUser = array('userId'=>$appointForId,'isNotification'=>1);
            $user_info_for = $this->common_model->getsingle(USERS,$whereUser);
            if($user_info_for){
            
                $registrationIds[] = $user_info_for->deviceToken;

                if($user_info_for->setLanguage == 'spanish'){
                    $title = 'Contador';
                    $showMsg = ' su contador para la cita.';
                    $statusAc = ' ha aceptado';
                    $statusRj = ' ha rechazado';
                }else{
                    $title = 'Counter';
                    $showMsg = ' your counter for appointment.';
                    $statusAc = ' has accepted';
                    $statusRj = ' has rejected';
                }

                $status = ($counterStatus == 1) ? $statusAc : $statusRj; 

                $body_send  = $this->session->userdata('fullName').$status.$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$status.$showMsg; //body to be saved in DB
                $notif_type = 'update_counter';
                $notify_for = $user_info_for->userId;                
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }

            $response = array('status' => 1, 'msg' => lang('status_updated'),'url'=>base_url('home/appointment/'));
            echo json_encode($response);exit();
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response);exit();
        }
    }

    function viewAppOnMap(){

        $this->check_user_session();
        $appId = decoding($this->uri->segment(4));
        $isExist = $this->common_model->is_data_exists(APPOINTMENTS, array('appId'=>$appId));

        if(!$isExist){
            redirect('home/recordNotFound');
        }
        $userId = $this->session->userdata('userId');
        $getAppDetail['detail'] = $this->Appointment_model->getAppData($appId,$userId);

        // for google driving mode time
        $getAppDetail['drivingMode'] = $this->Appointment_model->getGoogleMapTravelMode('driving',$getAppDetail['detail']->ByAddress,$getAppDetail['detail']->appointAddress,$getAppDetail['detail']->appointLatitude,$getAppDetail['detail']->appointLongitude);

        // for google walking mode time
        $getAppDetail['walkingMode'] = $this->Appointment_model->getGoogleMapTravelMode('walking',$getAppDetail['detail']->ByAddress,$getAppDetail['detail']->appointAddress,$getAppDetail['detail']->appointLatitude,$getAppDetail['detail']->appointLongitude);

        $meetingImg = App_Meeting;

        $meetImg = MAP_ICON_MAIL;

        if($getAppDetail['detail']->ByGender == '1' || $getAppDetail['detail']->ByGender == '3'){
            $byImg = App_ICON_MAIL;
            $bcolor = "#a51d29";
        }else{
            $byImg = App_USER_FEMAIL;
            $bcolor = "purple";
        }

        if($getAppDetail['detail']->ForGender == '1' || $getAppDetail['detail']->ForGender == '3'){
            $forImg = App_ICON_MAIL;
            $fcolor = "#a51d29";
        }else{
            $forImg = App_USER_FEMAIL;
            $fcolor = "purple";
        }

        if(!filter_var($getAppDetail['detail']->forImage, FILTER_VALIDATE_URL) === false) { 
            $forImage = $getAppDetail['detail']->forImage;
        }else if(!empty($getAppDetail['detail']->forImage)){ 
            $forImage = AWS_CDN_USER_THUMB_IMG.$getAppDetail['detail']->forImage;
        } else{                    
            $forImage = AWS_CDN_USER_PLACEHOLDER_IMG;
        }

        if(!filter_var($getAppDetail['detail']->byImage, FILTER_VALIDATE_URL) === false) { 
            $byImage = $getAppDetail['detail']->byImage;
        }else if(!empty($getAppDetail['detail']->byImage)){ 
            $byImage = AWS_CDN_USER_THUMB_IMG.$getAppDetail['detail']->byImage;
        } else{                    
            $byImage = AWS_CDN_USER_PLACEHOLDER_IMG;
        }

        $mapData[] = array($getAppDetail['detail']->ByAddress,$getAppDetail['detail']->ByLatitude,$getAppDetail['detail']->ByLongitude,$byImage,$getAppDetail['detail']->ByName,$byImg,$bcolor);
        
        $mapData[] = array($getAppDetail['detail']->appointAddress,$getAppDetail['detail']->appointLatitude,$getAppDetail['detail']->appointLongitude,$meetingImg,'Meeting Location',$meetImg,$fcolor);        

        $mapData[] = array($getAppDetail['detail']->ForAddress,$getAppDetail['detail']->ForLatitude,$getAppDetail['detail']->ForLongitude,$forImage,$getAppDetail['detail']->ForName,$forImg,$bcolor);

        $getAppDetail['data'] = json_encode($mapData);

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $getAppDetail['front_scripts'] = array($load_custom_js.'app_detail.js');
        $this->load->front_render('appointment_map',$getAppDetail,'');
    }

    //cancel appointment
    function cancelAppointment(){

        // check session
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $appId = $this->input->post('appId');
        $where = array('appId'=>$appId);

        //check data exist
        $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);

        if(empty($apoimexist)){

            $response = array('status' => 0, 'msg' => lang('app_not_exist'),'url'=>base_url('home/appointment/'));
            echo json_encode($response);exit;
        }        

        $updateData['appointmentStatus']    = 5;
        $updateData['upd']                  = date('Y-m-d H:i:s');

        //update status
        $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);  

        if($appointData){

            $user_info_for = $this->common_model->getDeviceToken($appId,$userId);

            if($user_info_for){

                $registrationIds[] = $user_info_for->deviceToken; 

                if($user_info_for->setLanguage == 'spanish'){

                    $title = 'Cancelar cita';
                    $showMsg = ' ha cancelado la cita.';
                   
                }else{
                    
                    $title = 'Cancel Appointment';
                    $showMsg = ' has cancelled appointment.';
                } 

                $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'cancel_appointment';
                $notify_for = $user_info_for->userId;      
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());

                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }        

            $response = array('status' => 1, 'msg' => lang('app_request_cancelled'),'url'=>base_url('home/appointment/'));
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
        }
        echo json_encode($response);exit;

    } // End of function

    function finishdeleteAppointment(){
        
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $userId = $this->session->userdata('userId');
        $appId = $this->input->post('appId');
        $appointeStatus = $this->input->post('appStatus'); // for accept / reject or delete

        $where = array('appId'=>$appId);

        //check data exist
        $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
        if(empty($apoimexist)){
            $response = array('status' => 0, 'msg' => lang('app_not_exist'),'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');            
        }

        if($appointeStatus == 'finish'){

            $updateData['isFinish']  = 1;
            $updateData['upd']       = date('Y-m-d H:i:s');

            //update status
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

            if($appointData){

                $user_info_for = $this->common_model->getDeviceToken($appId,$userId);

                if($user_info_for){

                    $registrationIds[] = $user_info_for->deviceToken; 
                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Citas finalizadas';
                        $showMsg = ' ha terminado la cita.';
                       
                    }else{
                        $title = 'Finish Appointment';
                        $showMsg = ' has finished appointment.';
                       
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'finish_appointment';
                    $notify_for = $user_info_for->userId;                
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                   
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }
                $response = array('status' => 1, 'msg' => lang('app_finished'), 'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');
            }else{
                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');
            }

        }elseif($appointeStatus == 'delete'){

            $user_info_for = $this->common_model->getDeviceToken($appId,$userId);

            if($user_info_for){               
                $registrationIds[] = $user_info_for->deviceToken; 

                if($user_info_for->setLanguage == 'spanish'){

                    $title = 'Eliminar cita';
                    $showMsg = ' hha eliminado la cita.';
                   
                } else{

                    $title = 'Delete Appointment';
                    $showMsg = ' has deleted appointment.';
                }

                $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'delete_appointment';
                $notify_for = $user_info_for->userId;                
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }

            //delete record           
            $appointData = $this->common_model->deleteData(APPOINTMENTS, $where);

            if($appointData){
                $response = array('status' => 3, 'msg' => lang('app_deleted'), 'url'=>base_url('home/appointment/'));
            }else{
                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url'=>base_url('home/appointment/viewAppOnMap/').encoding($appId).'/');
            }
        }

        echo json_encode($response);exit;
    }

    function appointmentPayment(){

        $this->load->library('Stripe');
        //check for auth
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('stripeToken',lang('stripe_token'),'trim|required');        
      
        if($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds);
            echo json_encode($response);exit();

        }else{

            $userId         = $this->session->userdata('userId');
            $email          = $this->session->userdata('email');
            $stripeToken    = $this->input->post('stripeToken');
            $payment        = $this->input->post('amount');
            $appointmentId  = $this->input->post('appId');
            $appointForId   = $this->input->post('appForId');

            $where = array('appId'=>$appointmentId);
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            
            if(empty($apoimexist)){
                $response = array('status' => 0, 'msg' => lang('app_not_exist'));
                echo json_encode($response);exit();
            }

            $customer_id = $this->Appointment_model->getStripeCustomerId();
        
            if($customer_id === FALSE){
                $response = array('status' => 0, 'msg' => lang('something_wrong'));
                echo json_encode($response);exit();
            }

            if(empty($customer_id)){
                //create customer if ID not found
                $stripe_res = $this->stripe->save_card_id($email, $stripeToken); //create a customer
                
                if($stripe_res['status'] == false){
                    $response = array('status' => 0, 'msg' => $stripe_res['message']);
                    echo json_encode($response);exit();
                }
                
                $customer_id = $stripe_res['data']->id;  //customer ID
                
                //save customer ID in our DB for future use
                $update = $this->Appointment_model->saveCustomerId($customer_id);
                
                //some problem in updating customer ID
                if(!$update){
                    $response = array('status' => 0, 'msg' => lang('something_wrong'));
                    echo json_encode($response);exit();
                }
            }else{
                //update stripe token 
                $updataData['description'] = $email;
                $updataData['source']      = $stripeToken;
                $stripe_res_upd = $this->stripe->update_customer($customer_id,$updataData);
            }

            $getOrganiserBankAcc = $this->common_model->getBankAccountDetail($appointForId);

            if(!empty($getOrganiserBankAcc)){
                $data = array(
                    'amount'=>$payment,
                    'customerId'=>$customer_id,
                    'bankAccId'=>$getOrganiserBankAcc->accountId,
                    "currency"=>"USD"
                );
                $isPaymentDone = $this->stripe->owner_pay_byBankId($data);

                if(!empty($isPaymentDone['data']) && $isPaymentDone['status'] === true){

                    $finalData['transactionId']     = $isPaymentDone['data']->balance_transaction;
                    $finalData['chargeId']          = $isPaymentDone['data']->id;
                    $finalData['paymentStatus']     = 'succeeded';
                    $finalData['amount']            = $payment;
                    $finalData['crd']               = date('Y-m-d H:i:s');
                    $finalData['paymentType']       = 7;
                    $finalData['transactionType']   = "stripeToBank";
                    $finalData['user_id']           = $userId;
                    $finalData['referenceId']       = $appointmentId;
                    $finalData['paymentDetail']     = json_encode($isPaymentDone);

                    //check data exist
                    $wherePay = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>7,'referenceId'=>$appointmentId,'transactionType'=>"stripeToBank");
                    $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);
                    if(!empty($paymentExist)){
                        $response = array('status' => 0, 'msg' => lang('payment_already_done'));
                        echo json_encode($response);exit();
                    }

                    // save final payment in
                    $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$finalData);

                    if($insertId){

                        //update status               
                        $updateData['appointmentStatus'] = 4; // payment done   
                        $updateData['counterStatus'] = 3; // payment done              
                        
                        $isUpdated = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

                        if($isUpdated){

                            $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$appointForId,'isNotification'=>1));

                            if($user_info_for){ 

                                $registrationIds[] = $user_info_for->deviceToken; 

                                if($user_info_for->setLanguage == 'spanish'){
                                    $title = 'Pago de cita';
                                    $showMsg = ' pagó por su cita.';
                                   
                                }else{
                                    $title = 'Appointment Payment';
                                    $showMsg = ' paid for your appointment.';
                                   
                                }

                                $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                                $notif_type = 'appointment_payment';
                                $notify_for = $user_info_for->userId;                
               
                                //send notification to user
                                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appointmentId,$notif_type);

                                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appointmentId);

                                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                //save notification

                                $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                            }

                            $response = array('status' => 1, 'msg' => lang('payment_done'));
                            echo json_encode($response);exit();
                        }else{
                            $response = array('status' => 0, 'msg' => lang('something_wrong'));  
                            echo json_encode($response);exit();
                        }
                    }
                }else{
                   $response = array('status' => 0, 'msg' => $isPaymentDone['message']);
                   echo json_encode($response);exit();  
                }                                        
            }else{
                $response = array('status' => 0, 'msg' => lang('transaction_err'));
                echo json_encode($response);exit();
            }
        }

    } // End Of Function

    function giveReview(){

        //check for auth
        $auth_res = $this->check_ajax_auth();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('rating',lang('rating'),'trim|required');
        $this->form_validation->set_rules('comment',lang('comment'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds);
            echo json_encode($response);exit();

        } else {

            $user_id = $this->session->userdata('userId');

            $data['by_user_id']        = $user_id;
            $data['for_user_id']       = $this->input->post('receiverId');            
            $data['rating']            = $this->input->post('rating');
            $data['comment']           = $this->input->post('comment');
            $data['referenceId']       = $this->input->post('referenceId');
            $data['reviewType']        = 1; // 1 for Appointment and 2 for Event
            $data['crd']               = date('Y-m-d H:i:s');

            $where = array('by_user_id'=>$data['by_user_id'],'for_user_id'=>$data['for_user_id'],'referenceId'=>$data['referenceId']);
            //check data exist
            $reviewExist = $this->common_model->is_data_exists(REVIEW, $where);
            if(!empty($reviewExist)){
                $response = array('status' => 0, 'msg' => lang('already_reviewed'));
                echo json_encode($response);exit();
            }

            //insert data
            $appoimData = $this->common_model->insertData(REVIEW,$data);
            if($appoimData){

                $where = array('userId'=>$data['for_user_id'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);

                if($user_info_for){
                    
                    $registrationIds[] = $user_info_for->deviceToken; 
                    
                    if($user_info_for->setLanguage == 'spanish'){

                        $title = 'Recomendaciones de citas';
                        $showMsg = ' te ha dado una revisión.';
                       
                    } else{

                        $title = 'Appointment Reviews';
                        $showMsg = ' has given you a review.';
                        
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'review_appointment';
                    $notify_for = $user_info_for->userId;               
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$data['referenceId'],$notif_type);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$data['referenceId']);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => 1, 'msg' => lang('review_sent'));
                echo json_encode($response);exit();
            }else{
                $response = array('status' => 0, 'msg' => lang('something_wrong'));  
                echo json_encode($response);exit();
            }
        }
    }

} // End Of Class