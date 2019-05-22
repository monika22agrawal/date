<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends CommonService {

	function __construct(){

		parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Appointment_model');
        $this->lang->load('appointment_messages_lang', $this->appLang);  //load response lang file
	}

    /*function test_get(){
        $registrationIds = array('emzWEvdluqY:APA91bEckgt5_W88v5UcOLyLQOcnqyA_e9IP3EIUmdDlqJODG-hw7ISAtEA6EEJggbGxQjm6kypeSOJwPADDavOD0zf-EK8bhtwBZYU1WeT7Byw9RAL_T72YtefKY0oP51eSLJncrpfpltM2vQji9JmbM9y21pCycQ');
        $var = $this->notification_model->send_notification($registrationIds,array('title'=>'title','body'=>'body'));
        pr($var);die;
    }*/
	
    function makeAppointment_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('appointForId',lang('app_for_id'),'trim|required');
        $this->form_validation->set_rules('appointDate',lang('app_date'),'trim|required');
        $this->form_validation->set_rules('appointTime',lang('app_time'),'trim|required');
        $this->form_validation->set_rules('appointAddress',lang('app_address'),'trim|required');
        $this->form_validation->set_rules('appointLatitude',lang('app_address_lat'),'trim|required');
        $this->form_validation->set_rules('appointLongitude',lang('app_address_long'),'trim|required');
        $this->form_validation->set_rules('offerType',lang('app_ofr_type'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $user_id = $this->authData->userId;

            $data['appointById']        = $user_id;
            $data['appointForId']       = $this->post('appointForId');
            $data['business_id']        = $this->post('businessId');
            $data['appointDate']        = date('Y-m-d',strtotime($this->post('appointDate')));
            $data['appointTime']        = date('H:i:s',strtotime($this->post('appointTime')));
            $data['appointAddress']     = $this->post('appointAddress');
            $data['appointLatitude']    = $this->post('appointLatitude');
            $data['appointLongitude']   = $this->post('appointLongitude');
            $data['offerPrice']         = $this->post('offerPrice');
            $data['offerType']          = $this->post('offerType'); // 1:Paid,2:Free
            $data['crd']                = date('Y-m-d H:i:s');

            $where = array('userId'=>$data['appointForId']);
            $user_info_for = $this->common_model->getsingle(USERS,$where);

            $type = $user_info_for->appointmentType;

           /* if($type != $data['offerType']){ // if offerType and appointment for user 
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(158));
                $this->response($response);
            }*/

            //insert data
            $appoimData = $this->common_model->insertData(APPOINTMENTS,$data);
            
            if($appoimData){

                if($user_info_for->isNotification == 1){
                    
                    $registrationIds[] = $user_info_for->deviceToken;

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Nueva cita';
                        $showMsg = ' ha creado una cita contigo.';
                    }else{
                        $title = 'New Appointment';
                        $showMsg = ' has created appointment with you.';
                    }

                    $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
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

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(126));
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
            }
        }
       $this->response($response);
    }

    function updateAppointment_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('appointForId',lang('app_for_id'),'trim|required');
        $this->form_validation->set_rules('appointDate',lang('app_date'),'trim|required');
        $this->form_validation->set_rules('appointTime',lang('app_time'),'trim|required');
        $this->form_validation->set_rules('appointAddress',lang('app_address'),'trim|required');
        $this->form_validation->set_rules('appointLatitude',lang('app_address_lat'),'trim|required');
        $this->form_validation->set_rules('appointLongitude',lang('app_address_long'),'trim|required');
        $this->form_validation->set_rules('offerType',lang('app_ofr_type'),'trim|required');
        $this->form_validation->set_rules('appointmentId',lang('app_id'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $user_id = $this->authData->userId;
            $appId   = $this->post('appointmentId');

            $where          = array('appId'=>$appId);
            $checkStatus = $this->common_model->getsingle(APPOINTMENTS,$where);

            if($checkStatus){

                if($checkStatus->appointmentStatus == 2){
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(192));
                    $this->response($response);
                }
            }

            $data['appointById']        = $user_id;
            $data['appointForId']       = $this->post('appointForId');
            $data['business_id']        = $this->post('businessId');
            $data['appointDate']        = date('Y-m-d',strtotime($this->post('appointDate')));
            $data['appointTime']        = date('H:i:s',strtotime($this->post('appointTime')));
            $data['appointAddress']     = $this->post('appointAddress');
            $data['appointLatitude']    = $this->post('appointLatitude');
            $data['appointLongitude']   = $this->post('appointLongitude');
            $data['offerPrice']         = $this->post('offerPrice');
            $data['offerType']          = $this->post('offerType'); // 1:Paid,2:Free
            $data['upd']                = date('Y-m-d H:i:s');

            
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
                $this->response($response);
            }  

            //update data
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $data,$where);
            
            if($appointData){

                $where = array('userId'=>$data['appointForId'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);

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
                   
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(161));
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
            }
        }
       $this->response($response);
    }

	//get appointment list
	function getAppointment_get(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId     = $this->authData->userId;
        $offset     = $this->get('offset'); 
        $limit      = $this->get('limit');
        $listType   = $this->get('listType');  // appointment list type received, sent, finish

        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit = 10; 
        }

        $appointById    = array('appointById'=>$userId);
        $appointForId   = array('appointForId'=>$userId);

        if($listType == 'received') {

            $appoimData = $this->Appointment_model->getAppointmentList('','',$appointForId,$offset,$limit,$listType);  

        }elseif ($listType == 'sent') {
           
            $appoimData = $this->Appointment_model->getAppointmentList('','',$appointById,$offset,$limit,$listType);  

        }elseif ($listType == 'finished') {
            
            $appoimData = $this->Appointment_model->getAppointmentList($appointById,$appointForId,'',$offset,$limit,$listType);

        }elseif ($listType == 'all') {
            
            $appoimData = $this->Appointment_model->getAppointmentList($appointById,$appointForId,'',$offset,$limit,$listType = 'all');  
        }

        if($appoimData){

        	$response = array('status' => SUCCESS, 'message'=>'OK','date'=>date('Y-m-d H:i:s'),'appoimData' => $appoimData);

        } else{

        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        }
        $this->response($response);
	}

    //get appointment list
    function getAppointmentDetail_get(){
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $appId = $this->get('appointId');
        $userId = $this->authData->userId;
        $where = array('appId'=>$appId);
        //check data exist
        $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
        if(empty($apoimexist)){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
            $this->response($response);
        }
        //get data
        $appoimData = $this->common_model->getAppData($appId,$userId);
        if($appoimData){
            $response = array('status' => SUCCESS, 'message'=>'OK','date'=>date('Y-m-d H:i:s'),'appoimData' => $appoimData);
        } else{
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
        }   
        $this->response($response);
    }

    //to apply counter for appointment
    function applyCounter_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('appointId',lang('app_id'),'trim|required');
        $this->form_validation->set_rules('counterPrice',lang('counter_price_title'),'trim|required');
        $this->form_validation->set_rules('appointById',lang('app_by_id'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId         = $this->authData->userId;
            $appId          = $this->post('appointId');
            $appointById    = $this->post('appointById');
            $where          = array('appId'=>$appId);
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
                $this->response($response);
            }
            
            $whereCounter    = array('appId'=>$appId,'isCounterApply'=>'1');
            //check data exist
            $counterExist = $this->common_model->is_data_exists(APPOINTMENTS, $whereCounter);
            if(!empty($counterExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(157));
                $this->response($response);
            }

            //update counter price
            $updateData['counterPrice']     = $this->post('counterPrice');
            $updateData['isCounterApply']   = '1';
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

            if($appointData){

                $whereUser = array('userId'=>$appointById,'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$whereUser);
                if($user_info_for){               
                    $registrationIds[] = $user_info_for->deviceToken; 

                    if($user_info_for->setLanguage == 'spanish'){
                        $title = 'Contador';
                        $showMsg = ' ha aplicado un contador en su cita.';
                    }else{
                        $title = 'Counter';
                        $showMsg = ' has applied counter on your appointment.';
                    }

                    $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
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

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(129));
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            }
        }
        $this->response($response);
    }

     //to update applied counter status(accept / reject) for appointment
    function updateCounter_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('appointId',lang('app_id'),'trim|required');
        $this->form_validation->set_rules('counterStatus',lang('app_counter_status'),'trim|required');
        $this->form_validation->set_rules('appointForId',lang('app_for_id'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId         = $this->authData->userId;
            $appId          = $this->post('appointId');
            $appointForId   = $this->post('appointForId');
            $counterStatus  = $this->post('counterStatus'); // 1 for accepted or 2 for rejected

            $where          = array('appId'=>$appId);
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            if(empty($apoimexist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
                $this->response($response);
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
                    $registrationIds[] = $user_info_for->deviceToken;

                    $body_send  = $this->authData->fullName.$status.$showMsg; //body to be sent with current notification
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

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(129));
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            }
        }
        $this->response($response);
    }

	//change appointment status for accept or reject
	function changeAppointmentStatus_post(){

		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $userId = $this->authData->userId;
        $appId = $this->post('appointId');
        $appointStatus = $this->post('appointeStatus'); // 2 for accept & 3 for reject
        $where = array('appId'=>$appId);

        //check data exist
		$apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
		if(empty($apoimexist)){
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
        	$this->response($response);
        }

        if ($appointStatus == 2) { // for accept appointment request

            $updateData['appointmentStatus']    = $appointStatus;
            $updateData['upd']                  = date('Y-m-d H:i:s');
            //$msg =  lang('confirmed_appoint');
            $msg =  'confirmed';

        }elseif ($appointStatus == 3) { // for reject appointment request

            $updateData['appointmentStatus']    = $appointStatus;
            $updateData['upd']                  = date('Y-m-d H:i:s');
            //$msg =  lang('rejected_appoint');
            $msg =  'rejected';
        }

        //update status
        $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

        if($appointData){

            $user_info_for = $this->common_model->getByDeviceToken($appId,$userId);

            if($user_info_for){
                         
                $registrationIds[] = $user_info_for->deviceToken;

                if($msg =  'confirmed'){

                    if($user_info_for->setLanguage == 'spanish'){
                        $showMsg = ' ha confirmado su cita.';
                        $title = 'Cita confirmada';
                    }else{
                        $showMsg = ' has confirmed your appointment.';  
                        $title = 'Appointment confirmed';                 
                    }

                }elseif ($msg =  'rejected') {
                   
                    if($user_info_for->setLanguage == 'spanish'){
                        $showMsg = ' ha rechazado su cita.';
                        $title = 'Cita rechazada';
                    }else{
                        $showMsg = ' has rejected your appointment.';
                        $title = 'Appointment rejected';                   
                    }

                }                
                
                $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'confirmed_appointment';
                $notify_for = $user_info_for->userId;                
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }

        	$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(129));
        }else{
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
        }
        $this->response($response);
	}

    function appointmentPayment_post(){

        $this->load->library('Stripe');
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');        
        $this->form_validation->set_rules('amount',lang('app_pay_amt'),'trim|required');        
        $this->form_validation->set_rules('appointmentId',lang('app_id'),'trim|required');
        $this->form_validation->set_rules('appointForId',lang('app_for_id'),'trim|required');       
      
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        }else{

            $userId         = $this->authData->userId;
            $email          = $this->authData->email;
            $stripeToken    = $this->post('token');
            $payment        = $this->post('amount');
            $appointmentId  = $this->post('appointmentId');
            $appointForId   = $this->post('appointForId');

            $where = array('appId'=>$appointmentId);
            //check data exist
            $apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
            
            if(empty($apoimexist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
                $this->response($response);
            }

            $customer_id = $this->Appointment_model->getStripeCustomerId();
        
            if($customer_id === FALSE){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }

            if(empty($customer_id)){
                //create customer if ID not found
                $stripe_res = $this->stripe->save_card_id($email, $stripeToken); //create a customer
                
                if($stripe_res['status'] == false){
                    $response = array('status' => FAIL, 'message' => $stripe_res['message']);
                    $this->response($response);
                }
                
                $customer_id = $stripe_res['data']->id;  //customer ID
                
                //save customer ID in our DB for future use
                $update = $this->Appointment_model->saveCustomerId($customer_id);
                
                //some problem in updating customer ID
                if(!$update){
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                    $this->response($response);
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
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(163));
                        $this->response($response);
                    }

                    // save final payment in
                    $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$finalData);

                    if($insertId){

                        //update status               
                        $updateData['appointmentStatus'] = 4; // payment done            
                        
                        $isUpdated = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

                        $updateCounterData['counterStatus'] = 3; // payment done            
                        
                        $isUpdated = $this->common_model->updateFields(APPOINTMENTS, $updateCounterData,$where);

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

                                $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
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

                            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(146));
                        }else{
                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
                        }
                    }
                }else{
                   $response = array('status' => FAIL, 'message' => $isPaymentDone['message']);  
                }                                        
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(164));
            }
        }
        $this->response($response);
    }

	//finish appointment
	function finishAppointment_post(){

		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId = $this->authData->userId;
        $appId   = $this->post('appointId');
        $where = array('appId'=>$appId);
        //check data exist
		$apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);
		if(empty($apoimexist)){
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
        	$this->response($response);
        }
        
        //update status
        $updateData['isFinish'] 	= 1;
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

                $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                $notif_type = 'finish_appointment';
                $notify_for = $user_info_for->userId;                
               
                //send notification to user
                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                //save notification

                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
            }

        	$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(129));
        }else{
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
        }
        $this->response($response);
	}

	//delete / cancel appointment
	function deleteAppointment_post(){

		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId = $this->authData->userId;
        $type = $this->post('type'); // cancel or deleted for message
        $appId = $this->post('appointId');
        $where = array('appId'=>$appId);

        //check data exist
		$apoimexist = $this->common_model->is_data_exists(APPOINTMENTS, $where);

		if(empty($apoimexist)){
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(139));
        	$this->response($response);
        }

        if($type == 'deleted'){


        }elseif($type == 'cancel'){

            $updateData['appointmentStatus']    = 5;
            $updateData['upd']                  = date('Y-m-d H:i:s');

            //update status
            $appointData = $this->common_model->updateFields(APPOINTMENTS, $updateData,$where);

        }
        
        $user_info_for = $this->common_model->getDeviceToken($appId,$userId);

        if($user_info_for){

            $registrationIds[] = $user_info_for->deviceToken;

            if($type == 'deleted'){

                if($user_info_for->setLanguage == 'spanish'){

                    $title = 'Eliminar cita';
                    $showMsg = ' hha eliminado la cita.';
                   
                } else{

                    $title = 'Delete Appointment';
                    $showMsg = ' has deleted appointment.';
                } 

            } else{

                if($user_info_for->setLanguage == 'spanish'){

                    $title = 'Cancelar cita';
                    $showMsg = ' ha cancelado la cita.';
                   
                }else{
                    
                    $title = 'Cancel Appointment';
                    $showMsg = ' has cancelled appointment.';
                } 

            }

            $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
            $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
            $notif_type = 'delete_appointment';
            $notify_for = $user_info_for->userId;         
           
            //send notification to user
            $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$appId,$notif_type);

            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$appId);

            $notif_msg['body'] = $body_save; //replace body text with placeholder text
            //save notification

            $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
        }

        if($type == 'deleted'){
            //delete record
            $appointData = $this->common_model->deleteData(APPOINTMENTS, $where);
        }

        if($appointData){

        	$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(129));
        }else{
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
        }
        $this->response($response);
	}

    function giveReview_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('rating',lang('rating'),'trim|required');
        $this->form_validation->set_rules('comment',lang('comment'),'trim|required');
        $this->form_validation->set_rules('receiverId',lang('app_rec_id'),'trim|required');
        $this->form_validation->set_rules('referenceId',lang('app_ref_id'),'trim|required');
        $this->form_validation->set_rules('reviewType',lang('app_rev_type'),'trim|required');

        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $user_id = $this->authData->userId;

            $data['by_user_id']        = $user_id;
            $data['for_user_id']       = $this->post('receiverId');
            $data['rating']            = $this->post('rating');
            $data['comment']           = $this->post('comment');
            $data['referenceId']       = $this->post('referenceId');
            $data['reviewType']        = $this->post('reviewType'); // 1 for Appointment and 2 for Event
            $data['crd']               = date('Y-m-d H:i:s');

            $where = array('by_user_id'=>$data['by_user_id'],'for_user_id'=>$data['for_user_id'],'referenceId'=>$data['referenceId']);
            //check data exist
            $reviewExist = $this->common_model->is_data_exists(REVIEW, $where);
            if(!empty($reviewExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(160));
                $this->response($response);
            }

            //insert data
            $appoimData = $this->common_model->insertData(REVIEW,$data);

            if($appoimData){

                $where = array('userId'=>$data['for_user_id'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);

                if($user_info_for){

                    $registrationIds[] = $user_info_for->deviceToken;

                    if($data['reviewType'] == 1){

                        if($user_info_for->setLanguage == 'spanish'){

                            $title = 'Recomendaciones de citas';
                            $showMsg = ' te ha dado una revisión.';
                           
                        } else{

                            $title = 'Appointment Reviews';
                            $showMsg = ' has given you a review.';
                            
                        } 

                    }else{

                        if($user_info_for->setLanguage == 'spanish'){

                            $title = 'Recomendaciones de eventos';
                            $showMsg = ' te ha dado una revisión.';
                           
                        } else{

                            $title = 'Event Reviews';
                            $showMsg = ' has given you a review.';
                            
                        }
                    }
                    

                    $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = ($data['reviewType'] == '1') ? 'review_appointment' : 'review_event';
                    $notify_for = $user_info_for->userId;               
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$data['referenceId'],$notif_type,$notify_for);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$data['referenceId'] ,'createrId'=>$notify_for);

                    $notif_msg['body'] = $body_save; //replace body text with placeholder text
                    //save notification

                    $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
                    $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
                    $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                }

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(159));

            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
            }
        }
       $this->response($response);
    }

    //get appointment list
    function getAppointmentReview_get(){
        
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId     = $this->get('userId') ? $this->get('userId') : $this->authData->userId;
        $offset     = $this->get('offset');
        $limit      = $this->get('limit');
        $reviewType = $this->get('reviewType'); // 1 for Appointment and 2 for Event
        $eventId    = $this->get('eventId');

        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit = 10; 
        }

        $reviewList = $this->common_model->getReviewsList($userId,$offset,$limit,$reviewType,$eventId); 

        if($reviewList){

            $response = array('status' => SUCCESS, 'message'=>'OK','date'=>date('Y-m-d H:i:s'),'reviewList' => $reviewList);

        } else{

            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        }
        $this->response($response);
    }

} //end of class
