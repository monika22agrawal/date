<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CommonFront {

	function __construct() {
        
        parent::__construct();
        /*if($this->uri->segment(3) == 'createEvent' || $this->uri->segment(2) == 'event'){
            if($this->session->userdata('front_login') == FALSE ){
                redirect(site_url('home/login'));
            }
        }*/
        date_default_timezone_set('Asia/Kolkata');
        $this->load_language_files();    
        $this->load->model('Event_model');
    }

    function index(){

        $this->check_user_session();
        $userId = $this->session->userdata('userId');

        /*$isSuscribe = $this->common_model->checkSuscription($userId);

        if($isSuscribe === TRUE){*/
            $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
            $data_val['front_scripts'] = array($load_custom_js.'events_list.js',$load_custom_js.'event_chat.js');
            $data_val['friendListCount'] = $this->common_model->totalFriendCount($userId);
            $this->load->front_render('events',$data_val,'');
        /*}else{
            $this->load->front_render('suscribe','');
        }*/
         	
    } // end of function

    // to show friends for event's invitation 
    function getInvitationUserList(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
              
        //user params
        //gender = 1: Male, 2:Female, 3:Transgender, 4:All
        $other_param = array('gender', 'privacy');
        foreach ($other_param as $key => $val){

            $param_val = $this->input->post($val);
            if(!empty($param_val)){
                $event_data[$val] = $param_val;                 
            }
        }

        $event_data['eventId']   = $this->input->post('eventId');
        
        //search or filter params
        $search = array();
        $search_param = array('name', 'latitude', 'longitude', 'rating','address', 'city', 'state', 'country');
        
        foreach ($search_param as $key => $val){

            $param_val = $this->input->post($val);
            if(!empty($param_val)){
                $search[$val] = $param_val;                 
            }
        }

        $event_data['limit']    = 6;
        $event_data['offset']   = $this->input->post('page');
        
        $user_list = $this->Event_model->getInvitationUserList($event_data, $search);
        $data['usersCount'] = $this->Event_model->countAllInvUsers($event_data, $search);

        $response_data['friendList'] = $user_list;
        $response_data['memberId'] = $this->input->post('memberId') ? $this->input->post('memberId') : '';

        /* is_next: var to check we have records available in next set or not
         * 0: NO, 1: YES
         */
        $is_next = 0;
        $new_offset = $event_data['offset'] + $event_data['limit'];
        if($new_offset<$data['usersCount']){
            $is_next = 1;
        }

        $userListHtml = $this->load->view('event_friend_list',$response_data,true);

        echo json_encode( array('status'=>1, 'html'=>$userListHtml, 'isNext'=>$is_next, 'newOffset'=>$new_offset) ); exit;        
    }

    function createEvent(){

        $this->check_user_session();
        $this->load->model('Appointment_model');
        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $data_val['front_scripts'] = array($load_custom_js.'create_event.js',$load_custom_js.'event_chat.js');
        
        $userId = $this->session->userdata('userId');
        $data_val['userDetail'] = $this->common_model->usersDetail($userId);

        $data_val['bizList'] = $this->Appointment_model->getBusinessList();     
        $this->load->front_render('create_event',$data_val,'');

    } // end of function

    // save data of create event
    function createEventSubmit(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $this->load->model('image_model');

        $paymentType = $this->input->post('payment');
        $this->load->library('form_validation');

        $bizAdd  = $this->input->post('bizAdd');

        if(empty($bizAdd )){

            $this->form_validation->set_rules('bizAdd', lang('event_address'), 'required|callback__check_lat_long',array('_check_lat_long'=>lang('valid_address')));
        }
                 
        $this->form_validation->set_rules('eventName',lang('event_name_place'),'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('eventStartDate',lang('event_start_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventEndDate',lang('event_end_datetime_place'),'trim|required');            
        $this->form_validation->set_rules('privacy',lang('event_privacy'),'trim|required');
        $this->form_validation->set_rules('payment',lang('event_payment'),'trim|required');  
        $this->form_validation->set_rules('userLimit',lang('event_user_lmt'),'trim|required');  
        //$this->form_validation->set_rules('eventUserType',lang('event_user_typ'),'trim|required');
        $this->form_validation->set_rules('memberId',lang('event_friend'),'trim|required');

        if($paymentType == 1){
            $this->form_validation->set_rules('eventAmount',lang('event_amt'),'trim|required');
            $this->form_validation->set_rules('currencySymbol',lang('event_cur'),'trim|required');
        }

        if(empty($_FILES['eventImage']['name'])){
            $this->form_validation->set_rules('eventImage','Event image','required');
        }
      
        if($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds);

        } else {

            $eventImage = '';
            $folder = 'event';

            if(!empty($_FILES['eventImage']['name'])){
                $eventImage = $this->image_model->updateMedia('eventImage',$folder);
            }

            if(!empty($eventImage) && is_array($eventImage)){
                $response = array('status'=>22,'msg'=>$eventImage['error']);
                echo json_encode($response);exit();
                return;
            }            

            $userId = $this->session->userdata('userId');
            
            $date = date('Y-m-d H:i:s');
            $eventData = array();     

            // when event's payment type is paid
            if($paymentType == 1){
                $currency = $this->input->post('currencySymbol'); 
                $getCurrency = explode(',', $currency);
                $eventData['eventAmount']       = $this->input->post('eventAmount');
                $eventData['currencySymbol']    = $getCurrency[1];
                $eventData['currencyCode']      = $getCurrency[0];
            }

            $eventData['eventOrganizer']    = $userId;
            $eventData['eventName']         = $this->input->post('eventName');
            $eventData['eventStartDate']    = date("Y-m-d H:i:s", strtotime($this->input->post('eventStartDate')));
            $eventData['eventEndDate']      = date("Y-m-d H:i:s", strtotime($this->input->post('eventEndDate')));
            $eventData['eventPlace']        = $this->input->post('bizAdd');
            $eventData['eventLatitude']     = $this->input->post('bizLat');
            $eventData['eventLongitude']    = $this->input->post('bizLong');
            $eventData['privacy']           = $this->input->post('privacy');        // 1 for Public, 2 for Private
            $eventData['payment']           = $paymentType;                         // 1 for Paid, 2 for Free
            $eventData['userLimit']         = $this->input->post('userLimit');
            $eventData['eventUserType']     = $this->input->post('eventUserTypeG');  // 1 for Male,2 for Female,3 for Both
            $eventData['groupChat']         = $this->input->post('groupChat');      //  1 for Yes, 0 for No            
            $eventData['business_id']       = !empty($this->input->post('bizId')) ? $this->input->post('bizId') : '';
            $eventData['crd'] = $date;

            $friendId =  explode(',', $this->input->post('memberId'));
            $isCreated = $this->Event_model->createEvent($eventData,$friendId,$eventImage);
            
            if($isCreated){

                $response_data =  array('eventId'=>$isCreated); //created event ID
                
                $where_detail = array('e.eventId' => $isCreated);
                $event_detail = $this->Event_model->getEventImageDetail($where_detail);

                $response_data['event']     =  $event_detail;
                $response_data['eventName'] =  $eventData['eventName'];

                $userName = $this->session->userdata('fullName');
                // send notifications as background process
                shell_exec("php /var/www/html/index.php home event createEventNotification '".$isCreated."' '".$userId."' '".$eventData['eventName']."' '".$userName."' >> /var/www/html/bgNotification_log.txt &");

                $response = array('status' => 1, 'msg' => lang('event_created_success'),'imgData' => $response_data, 'url' =>base_url('home/event/myEventDetail/').encoding($isCreated).'/');
            } else{
                $response = array('status' => 0, 'msg' => lang('something_wrong'));
            }
        }
        echo json_encode($response);

    } // End Of Function

    function _check_lat_long(){
        
        $eventLatitude = $this->input->post('bizLat');
        $eventLongitude = $this->input->post('bizLong');        
        if(empty($eventLatitude) && empty($eventLongitude)){
            return FALSE;
        }
        return True;        
    }

    // for sending background notifications of multiple users
    function createEventNotification($eventId,$userId,$eventName,$userName){   
        
        $this->common_model->createEventBgNotification($eventId,$userId,$eventName,$userName);

    } // end of function

    //add single event image
    function addEventImage(){
        
        //check for auth
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $event_id = $this->input->post('eventId');
        
        //check if event exist
        $where = array('eventId'=>$event_id);
        $eventExist = $this->common_model->is_data_exists(EVENTS, $where);
        if($eventExist === FALSE){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);exit;
        }

        $this->load->model('image_model');
        //Get event image count and check if event image limit is reached (For event max 5 images can be uploaded)
        $where = array('event_id'=>$event_id);
        $image_count = $this->common_model->get_total_count(EVENT_IMAGE, $where);
        if($image_count >= 5){
            $response = array('status' => 0, 'msg' => lang('event_max_img'));
            echo json_encode($response);exit;
        }
        
        if(empty($_FILES['eventImage']['name'])){
            $response = array('status' => 0, 'msg' => lang('event_img_require'));
            echo json_encode($response);exit;
        }
        
        //all good here, we can proceed to upload image now
        $folder = 'event';
        $event_image = $this->image_model->updateMedia('eventImage',$folder);
        if(is_array($event_image) && array_key_exists("error",$event_image)){
            $response = array('status' => 0, 'msg' => strip_tags($event_image['error']));
            echo json_encode($response);exit;
        }
        
        $insert_id = $this->Event_model->addEventImage($event_id, $event_image);
        if(!$insert_id){
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response);exit;
        }
        
        $event_detail = $this->common_model->eventsImage($event_id);

        $response_arr =  array('eventImg'=>$event_detail);

        $html = $this->load->view('new_event_img',$response_arr, true); 

        $response = array('status' => 1, 'msg' => lang('event_img_upload'), 'html'=>$html);
        echo json_encode($response);exit;
        
    }

    //delete event image
    function deleteEventImages(){
        
        //check for auth
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        $event_image_id = $this->input->post('eventImageId');
        $event_id = $this->input->post('eventId');
        $where = array('eventImgId'=>$event_image_id);
        $image_detail = $this->common_model->getsingle(EVENT_IMAGE, $where);
        
        if(empty($image_detail)){
            $response = array('status' => 0, 'msg' => lang('event_no_record'));
            echo json_encode($response);exit;
        }
        
        //Check event image count and prevent deletion of last image (Atleast one image is required for an event)
        $where_count = array('event_id'=>$image_detail->event_id);
        $image_count = $this->common_model->get_total_count(EVENT_IMAGE, $where_count);
        if($image_count == 1){
            $response = array('status' => 0, 'msg' => lang('event_img_validation'));
            echo json_encode($response);exit;
        }
        
        $this->load->model('image_model');
        $this->common_model->deleteData(EVENT_IMAGE, $where); //delete image record

        $file_name = $image_detail->eventImage;
        $img_path = FCPATH.'uploads/event/';
        $this->image_model->unlinkFile($img_path, $file_name); //unlink image from server directory   

        $event_detail = $this->common_model->eventsImage($event_id);

        $response_arr =  array('eventImg'=>$event_detail);

        $html = $this->load->view('new_event_img',$response_arr, true); 

        $response = array('status' => 1, 'msg' => lang('event_img_deleted'), 'html'=>$html);     
        
        //$response = array('status' => 1, 'msg' => 'Image deleted successfully');
        echo json_encode($response);exit;
    }

    // to show my event's request list using ajax
    function eventRequestList(){

        $this->check_user_session();
        
        $data['offset']     = $this->input->post('offsetReq'); 
        $data['limit']      = $this->input->post('limitReq');
        $data['userId']     = $this->session->userdata('userId');
        $result['eventRequest'] = $this->common_model->eventRequestListCount($data);        
        if($data['offset'] == 0){
            $result['eventReqCount'] = $this->common_model->countAllEventRequest($data);
        }
        $result['offset'] = $data['offset'];
        
        $this->load->view('event_request_list',$result);

    } // end of function

    // to show my event's list using ajax
    function myEventList(){

        $this->check_user_session();        
        $data['offset']     = $this->input->post('offset'); 
        $data['limit']      = $this->input->post('limit');
        $data['userId']     = $this->session->userdata('userId');
        $result['myEvent']    = $this->common_model->myEventListCount($data);

        if($data['offset'] == 0){
            $result['myEventCount'] = $this->common_model->countAllMyEvent($data);
        }
        $result['offset'] = $data['offset'];
                
        $this->load->view('my_event_list',$result);

    } // end of function

    function myEventDetail(){        
        
        if(isset($_GET['eventId'])){
            redirect('home/event/myEventDetail/'.encoding($_GET['eventId']).'/');
        }

        $eventId = decoding($this->uri->segment(4));
        $isExist = $this->common_model->is_data_exists(EVENTS, array('eventId'=>$eventId));

        if(!$isExist){
            redirect('home/recordNotFound');
        }

        //session does not exist
        if(!$this->session->userdata('userId')){
            $eventId = decoding($this->uri->segment(4));
            $result['myEventDetail'] = $this->Event_model->getpublicEventDetail($eventId); 
            echo $this->load->view('frontend_includes/front_header',$result,TRUE);
            echo $this->load->view('public_event_detail',$result,TRUE);
            echo $this->load->view('frontend_includes/front_footer',$result,TRUE); exit;
            //$this->load->front_render('public_event_detail',$result,''); exit;
        }

        //session exists
        $this->check_user_session();
        $data['userId'] = $this->session->userdata('userId');
        $data['eventId'] = $eventId;
        $result['myEventDetail'] = $this->common_model->myEventDetail($data);

        $reviewType = 2;
        $result['eventReviewList'] = $this->common_model->getReviewsList($data['userId'],'','',$reviewType,$eventId);

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $result['front_scripts'] = array($load_custom_js.'my_event_detail.js');
        $this->load->front_render('my_event_detail',$result,'');

    } // end of function

    // show ivited friend list using ajax
    function invitedMembers(){

        $this->check_user_session();   
        $data['offset']  = $this->input->post('offset'); 
        $data['limit']   = $this->input->post('limit');
        $data['userId']  = $this->session->userdata('userId');
        $data['eventId'] = $this->input->post('eventId');
        $result['invitedList'] = $this->common_model->invitedMemberCount($data);
        //if($data['offset']==0){
            $result['inviteMemCount'] = $this->common_model->countAllInvitedMember($data);
        //}
        $result['offset'] = $data['offset'];
        
        $this->load->view('invited_member',$result);

    } // end of function

    // show joined friend list using ajax    
    function joinedMembers(){

        $this->check_user_session();   
        $data['offset']  = $this->input->post('joinOffset'); 
        $data['limit']   = $this->input->post('joinLimit');
        $data['userId']  = $this->session->userdata('userId');
        $data['eventId'] = $this->input->post('eventId');
        $data['type'] = $this->input->post('type');
        $result['joinedList'] = $this->common_model->joinedMemberCount($data);
        //if($data['offset']==0){
            $result['joinMemCount'] = $this->common_model->countAllJoinedMember($data);
        //}
        $result['offset'] = $data['offset'];
        
        $this->load->view('joined_member',$result);

    } // end of function

    function eventRequestDetail(){

        if(isset($_GET['eventId'])){
            if (isset($_GET['compId'])) {
                redirect('home/event/eventRequestDetail/'.encoding($_GET['eventId']).'/?compId='.encoding($_GET['compId']).'/');
            }
            if (isset($_GET['eventMemId'])) {
                redirect('home/event/eventRequestDetail/'.encoding($_GET['eventId']).'/?eventMemId='.encoding($_GET['eventMemId']).'/');
            }
        }
        
        $eventId = decoding($this->uri->segment(4));

        $isExist = $this->common_model->is_data_exists(EVENTS, array('eventId'=>$eventId));

        if(!$isExist){
            redirect('home/recordNotFound');
        }
        
        //session does not exist
        if(!$this->session->userdata('userId')){
            $eventId = decoding($this->uri->segment(4));
            $result['myEventDetail'] = $this->Event_model->getpublicEventDetail($eventId);  
            echo $this->load->view('frontend_includes/front_header',$result,TRUE);
            echo $this->load->view('public_event_detail',$result,TRUE);
            echo $this->load->view('frontend_includes/front_footer',$result,TRUE); exit;
        }

        //session exists
        $this->check_user_session();
        $compId =  $eventMemId = '';
        if(isset($_GET['compId'])){
           
            $compId =  decoding($_GET['compId']);
            $where = array('compId'=>$compId);
            $eventExist = $this->common_model->is_data_exists(COMPANION_MEMBER, $where);

        }elseif (isset($_GET['eventMemId'])) {
            
            $eventMemId =  decoding($_GET['eventMemId']);
            $where = array('eventMemId'=>$eventMemId);
            $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);
            
        }else{
            $eventExist = false;
        }
       
        if(!$eventExist){
           
            redirect('home/recordNotFound');
        }
        
        $data['userId'] = $this->session->userdata('userId');
        $data['eventId'] = $eventId;
        
        if(!empty($compId)){

            $data['compId'] = $compId;
            $result['reqDetail'] = $this->common_model->sharedEventRequestDetail($data);

        }else{
            
            $data['eventMemId'] = $eventMemId;
            $result['reqDetail'] = $this->common_model->eventRequestDetail($data);
        }
        
        $reviewType = 2;        
        $result['eventReviewList'] = $this->common_model->getReviewsList($data['userId'],'','',$reviewType,$eventId);

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $result['front_scripts'] = array($load_custom_js.'event_request_detail.js',$load_custom_js.'event_chat.js');
        $this->load->front_render('event_request_detail',$result,'');

    } // end of function

    // show joined member on event request detail page    
    function joinedMembersReqEvent(){

        $this->check_user_session();   
        $data['offset']  = $this->input->post('joinOffset'); 
        $data['limit']   = $this->input->post('joinLimit');
        $data['userId']  = $this->session->userdata('userId');
        $data['eventId'] = $this->input->post('eventId');
        $data['type'] = $this->input->post('type');
        $result['joinedList'] = $this->common_model->joinedMemberCount($data);
        if($data['offset']==0){
            $result['joinMemCount'] = $this->common_model->countAllJoinedMember($data);
        }
        $result['offset'] = $data['offset'];
        
        $this->load->view('event_reques_join_members',$result);

    } // end of function


    // show companion member on event request detail page    
    function companionMembersReqEvent(){

        $this->check_user_session();   
        $data['offset']  = $this->input->post('compOffset'); 
        $data['limit']   = $this->input->post('compLimit');
        $data['userId']  = $this->session->userdata('userId');
        $data['eventId'] = $this->input->post('eventId');
        $data['type'] = $this->input->post('type');
        $result['compList'] = $this->common_model->companionMemberCount($data);
        if($data['offset']==0){
            $result['compMemCount'] = $this->common_model->countAllCompanionMember($data);
        }
        $result['offset'] = $data['offset'];
        
        $this->load->view('event_request_companion_members',$result);

    } // end of function

    //delete event
    function deleteEvent(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $eventId = $this->input->post('eventId');
        $where = array('eventId'=>$eventId);
        //check data exist
        $eventExist = $this->common_model->is_data_exists(EVENTS, $where);

        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);
        }
        
        //delete event
        $isDelete = $this->common_model->deleteEvent($eventId,$userId);
        switch ($isDelete) {

            case 'ED': // event deleted
               $response=array('status'=>1, 'msg' => lang('event_deleted'), 'url' => base_url('home/event'));
            break;                 
            
            case 'NE': // somthing going wrong
                $response = array('status' => 2, 'msg' => lang('event_not_exist'), 'url' => base_url('home/event'));
            break; 

            case 'JM': // joined member
                $response=array('status'=>3, 'msg' => lang('mem_joined_event'), 'url' => base_url('home/event'));
            break; 
          
            default:
                $response=array('status'=>4, 'msg' => lang('something_wrong'), 'url' => base_url('home/event'));
            break;                
        }        
        echo json_encode($response);

    } // End of function

     // remove invited and joind member from list
    function removeMember(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId       = $this->session->userdata('userId');
        $memberType   = $this->input->post('memberType'); // invited or joined
        $eventMemId   = $this->input->post('eventMemId');
        $eventId      = $this->input->post('eventId');

        $where = array('eventMemId'=>$eventMemId);

        //check data exist
        $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);
        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_mem_not_exist'), 'url' => base_url('home/event'));
            echo json_encode($response);exit;
        }

        if($memberType == 'invited'){
            //get total count
            $getwhere = array('event_id'=>$eventId,'memberStatus' => 0,'memberType !=' =>1);

            $invitedCount = $this->common_model->get_total_count(EVENT_MEMBER, $getwhere);
            if($invitedCount > 1){
                // delete member
                $inviteWhere = array('eventMemId'=>$eventMemId,'memberStatus' => 0);
                $invitedData = $this->common_model->deleteData(EVENT_MEMBER, $inviteWhere);
                if($invitedData){
                    $response = array('status' => 1, 'msg' => lang('mem_removed'), 'url' => base_url('home/event/myEventDetail/').encoding($eventId).'/');
                }else{
                    $response = array('status' => 2, 'msg' => lang('something_wrong'));
                }
            }else{
                $response = array('status' => 3, 'msg' => lang('comp_not_removed'));
            }

        }elseif ($memberType == 'joined') {

            //get total count
            $getwhereMem = array('event_id'=>$eventId,'memberStatus' => 2,'memberType !=' =>1);
            $joinedCount = $this->common_model->get_total_count(EVENT_MEMBER, $getwhereMem);

            if($joinedCount){
                
                // to get member id for deleting companion member also
                $joinWhere = array('eventMemId'=>$eventMemId,'memberStatus' => 2);
                /*$getMemId = $this->common_model->getsingle(EVENT_MEMBER,$joinWhere);
                if($getMemId){
                    // delete companion member
                    $compWhere = array('invitedBy'=>$getMemId->memberId);
                    $this->common_model->deleteData(EVENT_MEMBER, $compWhere);
                }*/
                // delete member
                $joinedData = $this->common_model->deleteData(EVENT_MEMBER, $joinWhere);
                if($joinedData){
                    $response = array('status' => 1, 'msg' => lang('mem_removed'), 'url' => base_url('home/event/myEventDetail/').encoding($eventId).'/');
                }else{
                    $response = array('status' => 2, 'msg' => lang('something_wrong'));
                }
            }else{
                $response = array('status' => 3, 'msg' => lang('cant_remove_mem'));
            }

        }else{
            $response = array('status' => 4, 'msg' => lang('invalid_mem_type'));
        }
              
        echo json_encode($response);

    } // End of function


    //join member or reject event request
    function joinMember(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $eventId = $this->input->post('eventId');
        $memberId = $this->input->post('memberId');
        $status = $this->input->post('status'); // 1 for accept and 2 for reject
        $where = array('memberId'=>$userId,'event_id'=>$eventId);
        //check data exist
        //$eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);

        $eventExist = $this->common_model->getsingle(EVENT_MEMBER,$where);

        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);
        }

        $checkWhere = array('eventId'=>$eventId);
        $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

        if($getEventData){

            if($status == 1){ // for accept request

                $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                if($checkUserLimit === TRUE){
                    //update status
                    if($getEventData->payment == 1){
                        $updateData['memberStatus'] = 2;

                    }elseif ($getEventData->payment == 2){
                        $updateData['memberStatus'] = 1;
                    }
                    
                    $isUpdated = $this->common_model->updateFields(EVENT_MEMBER, $updateData,$where);

                    $this->common_model->deleteData(COMPANION_MEMBER,array('companionMemId'=>$userId,'event_id'=>$eventId));

                    if($isUpdated){

                        $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$getEventData->eventOrganizer,'isNotification'=>1));

                        if($user_info_for){               
                            $registrationIds[] = $user_info_for->deviceToken; 

                            if($user_info_for->setLanguage == 'spanish'){
                                $title = 'Unirse al evento';
                                $showMsg = ' se unió a tu evento : ';
                            }else{
                                $title = 'Join Event';
                                $showMsg = ' joined your event : ';
                            }

                            $body_send  = $this->session->userdata('fullName').$showMsg.$getEventData->eventName.'.'; //body to be sent with current notification
                            $body_save  = '[UNAME]'.$showMsg.'[ENAME]'; //body to be saved in DB
                            $notif_type = 'join_event';
                            $notify_for = $user_info_for->userId;                
                            $eventMemId = $eventExist->eventMemId;

                            //send notification to user
                            $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                            $notif_msg['body'] = $body_save; //replace body text with placeholder text
                            //save notification

                            $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                        }

                        $response = array('status' => 1, 'msg' => lang('joined_sucess'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/');
                    }else{
                        $response = array('status' => 0, 'msg' => lang('something_wrong'));  
                    }
                }else{
                    $response = array('status' => 0, 'msg' => lang('user_limit_exceed'));
                }
            }elseif($status == 2){ // for reject
                
                $this->common_model->deleteData(EVENT_MEMBER,array('memberId'=>$userId,'event_id'=>$eventId));
                $response = array('status' => 2, 'msg' => lang('event_req_rej_msg'), 'url' => base_url('home/event'));

            }else{
                
                $response = array('status' => 0, 'msg' => lang('something_wrong'));
            }
        } 
        echo json_encode($response);

    } // End of function

    // if event's payment type is paid for member
    function eventPayment(){

        $this->load->library('Stripe');
        $this->load->model('Payment_model');
        $this->check_user_session();

        $userId = $this->session->userdata('userId');

        $token  = $this->input->post('stripeToken');
        $eventId = $this->input->post('eventIdPay');
        $memberId = $this->input->post('memberIdPay');
        $eventMemId = $this->input->post('eventMemIdPay');
        $payment = $this->input->post('eventAmount');
        $email = $this->session->userdata('email');

        $where = array('memberId'=>$memberId,'event_id'=>$eventId);
        //check data exist
        $eventExist = $this->common_model->getsingle(EVENT_MEMBER, $where);

        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);
        }

        $checkWhere = array('eventId'=>$eventId);
        $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

        if($getEventData){

            $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

            if($checkUserLimit === TRUE){

                if($token){

                    $customer_id = $this->Payment_model->getStripeCustomerId();
        
                    if($customer_id === FALSE){
                        $res['status'] = 2; $res['msg'] = lang('something_wrong');
                        echo json_encode($res); exit;
                    }
                    
                    if(empty($customer_id)){
                        //create customer if ID not found
                        $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                        
                        if($stripe_res['status'] == false){
                            $res['status'] = 3; $res['msg'] = $stripe_res['message'];
                            echo json_encode($res); exit;
                        }
                        
                        $customer_id = $stripe_res['data']->id;  //customer ID
                        
                        //save customer ID in our DB for future use
                        $update = $this->Payment_model->saveCustomerId($customer_id);
                        
                        //some problem in updating customer ID
                        if(!$update){
                            $res['status'] = 4; $res['msg'] = lang('something_wrong');
                            echo json_encode($res); exit;
                        }
                    }
                    
                    /* to pay stripe and save detail in db*/
                    /*$result = $this->stripe->pay_by_card_id($payment,$customer_id); //pay
                       
                    if(!empty($result['data']) && $result['status'] === true){*/
                        
                        $getOrganiserBankAcc = $this->common_model->getOrganiserBankAccId($eventId);

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
                                $finalData['paymentType']       = 3;
                                $finalData['transactionType']   = "stripeToBank";
                                $finalData['user_id']           = $userId;
                                $finalData['referenceId']       = $eventId;
                                $finalData['paymentDetail']     = json_encode($isPaymentDone);

                                //check data exist
                                $wherePay = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>3,'referenceId'=>$eventId,'transactionType'=>"stripeToBank");

                                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);

                                if(!empty($paymentExist)){

                                    $response = array('status' => 5, 'msg' => lang('payment_already_done'));
                                    echo json_encode($response);
                                }

                                /* to pay stripe and save detail in db*/
                                /*$payData['transactionId'] = $result['data']->balance_transaction;
                                $payData['chargeId'] =  $result['data']->id;
                                $payData['paymentStatus'] =  $result['data']->status;
                                $payData['amount'] = $payment;
                                $payData['crd'] = date('Y-m-d H:i:s');
                                $payData['paymentType'] =3;
                                $payData['user_id'] = $this->session->userdata('userId');
                                $payData['referenceId'] =$eventId;

                                //check data exist
                                $wherePay = array('user_id'=>$payData['user_id'],'paymentStatus'=>'succeeded','paymentType'=>3,'referenceId'=>$eventId);
                                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);

                                if(!empty($paymentExist)){
                                    $response = array('status' => 6, 'msg' => 'Payment is already done');
                                    echo json_encode($response);
                                }

                                $this->common_model->insertData(PAYMENT_TRANSACTIONS,$payData);*/
                                /*to pay stripe and save detail in db*/

                                // save final payment in
                                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$finalData);

                                if($insertId){

                                    //update status               
                                    $updateData['memberStatus'] = 1;           
                                    
                                    $isUpdated = $this->common_model->updateFields(EVENT_MEMBER, $updateData,$where);
                                    if($isUpdated){

                                        $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$getEventData->eventOrganizer,'isNotification'=>1));

                                        if($user_info_for){               
                                            $registrationIds[] = $user_info_for->deviceToken; 

                                            if($user_info_for->setLanguage == 'spanish'){
                                                $title = 'Pago del evento';
                                                $showMsg = ' ha pagado por su evento : ';
                                            }else{
                                                $title = 'Event Payment';
                                                $showMsg = ' has paid for your event : ';
                                            }

                                            $body_send  = $this->session->userdata('fullName').$showMsg.$getEventData->eventName.'.'; //body to be sent with current notification
                                            $body_save  = '[UNAME]'.$showMsg.'[ENAME].'; //body to be saved in DB
                                            $notif_type = 'event_payment';
                                            $notify_for = $user_info_for->userId;
                                            $eventMemId = $eventExist->eventMemId;              
                                           
                                            //send notification to user
                                            $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                                            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                                            $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                            //save notification

                                            $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                                            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                                            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                                        }

                                        $response = array('status' => 1, 'msg' => lang('payment_done'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/?eventMemId='.encoding($eventMemId));
                                        echo json_encode($response);
                                    }else{
                                        
                                        $response = array('status' => 7, 'msg' => lang('something_wrong'));
                                        echo json_encode($response);
                                    }
                                }
                            }else{
                                $response = array('status' => 8, 'msg' => $isPaymentDone['message']);
                                echo json_encode($response);
                            }                                        
                        }else{
                            $response = array('status' => 9, 'msg' => lang('transaction_err'));
                            echo json_encode($response);
                        }
                    /*}else{
                        $response = array('status' => 10, 'msg' => $result['message']);
                        echo json_encode($response);
                    }*/
                }else{
                    $response = array('status' => 11, 'msg' => lang('something_wrong'));
                    echo json_encode($response);
                }
            }else{
                $response = array('status' => 12, 'msg' => lang('user_limit_exceed'));
                echo json_encode($response);
            }
        }else{
            $response = array('status' => 13, 'msg' => lang('event_not_exist'));
            echo json_encode($response);
        }

    } // End of function


    // to show friends for event's share 
    function allSharemember(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
        
        //user params
        //gender = 1: Male, 2:Female, 3:Transgender, 4:All
        $other_param = array('gender', 'privacy');
        foreach ($other_param as $key => $val){

            $param_val = $this->input->post($val);
            if(!empty($param_val)){
                $event_data[$val] = $param_val;                 
            }
        }

        $event_data['eventId']   = $this->input->post('eventId');
        $event_data['gender']    = $this->input->post('gender') ? explode(',', $this->input->post('gender')) : '';
        
        //search or filter params
        $search = array();
        $search_param = array('name', 'latitude', 'longitude', 'rating','address', 'city', 'state', 'country');
        
        foreach ($search_param as $key => $val){

            $param_val = $this->input->post($val);
            if(!empty($param_val)){
                $search[$val] = $param_val;                 
            }
        }
        
        $event_data['limit']    = 6;
        $event_data['offset']   = $this->input->post('page');

        $user_list = $this->Event_model->getInvitationUserList($event_data, $search);
        $data['usersCount'] = $this->Event_model->countAllInvUsers($event_data, $search);

        $response_data['shareMem'] = $user_list;
        $response_data['memberId'] = $this->input->post('memberId') ? $this->input->post('memberId') : '';
        /* is_next: var to check we have records available in next set or not
         * 0: NO, 1: YES
         */
        $is_next = 0;
        $new_offset = $event_data['offset'] + $event_data['limit'];
        if($new_offset<$data['usersCount']){
            $is_next = 1;
        }

        $userListHtml = $this->load->view('share_member',$response_data,true);

        echo json_encode( array('status'=>1, 'html'=>$userListHtml, 'isNext'=>$is_next, 'newOffset'=>$new_offset) ); exit;

    } // end of function


    //share event to multiple members 
    function shareMember(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $eventId = $this->input->post('eventId');
        $memberId = $this->input->post('memberId');
        $eventMemId = $this->input->post('eventMemId');

        $friendId = explode(',', $memberId);
    
        $checkWhere = array('eventId'=>$eventId);
        $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

        if($getEventData){
            $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

            if($checkUserLimit === TRUE){
                // insert shared member as companion
                $isInsert = $this->Event_model->shareEvent($userId,$eventId,$friendId);                    

                if($isInsert){
                    $userName = $this->session->userdata('fullName');
                    // send notifications as background process
                    shell_exec("php /var/www/html/index.php service event shareEventBgNotification '".$eventMemId."' '".$userId."' '".$getEventData->eventName."' '".$userName."' '".$getEventData->eventId."' >> /var/www/html/bgNotification_log.txt &");

                    $response = array('status' => 1, 'msg' => lang('shared_sucess'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/');
                }else{
                    $response = array('status' => 2, 'msg' => lang('something_wrong'));  
                }
            }else{
                $response = array('status' => 3, 'msg' => lang('user_limit_exceed'));
            }                    
        }else{
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
        }          
        
        echo json_encode($response);

    } // End of function

    // for sending background notifications of multiple users
    function shareEventBgNotification($eventMemId,$userId,$eventName,$userName,$eventId){
        
        $this->common_model->shareEventBgNotification($eventMemId,$userId,$eventName,$userName,$eventId);

    } // end of function


    // companion status accept/reject for event
    function companionMemberStatus(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId     = $this->session->userdata('userId');
        $eventId    = $this->input->post('eventId');
        $status     = $this->input->post('status'); // status 1 for accept request or 2 for reject request
        $eventMemId = $this->input->post('eventMemId');

        $where      = array('companionMemId'=>$userId,'eventMem_Id'=>$eventMemId,'event_id'=>$eventId);
        //check data exist
        $eventExist = $this->common_model->getsingle(COMPANION_MEMBER, $where);

        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);exit;
        }

        $checkComp = array('eventMemId'=>$eventMemId);
        $getCompData = $this->common_model->getsingle(EVENT_MEMBER,$checkComp);

        $checkWhere = array('eventId'=>$eventId);
        $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);
        
        if($getCompData){

            if($status == 1){ // to accept request

                $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                if($checkUserLimit === TRUE){
               
                    //update status
                    if($getEventData->payment == 1){ // 1:Paid

                        $updateData['companionMemberStatus'] = 2;

                    }elseif ($getEventData->payment == 2) { // 2:Free

                        $updateData['companionMemberStatus'] = 1;
                    }

                    //check status already exist or not
                    $compExist = $this->common_model->getsingle(COMPANION_MEMBER, array('eventMem_Id'=>$eventMemId,'event_id'=>$eventId,'companionMemberStatus'=>1));

                    if(!empty($compExist)){

                        $response = array('status' => 0, 'msg' => lang('comp_already_accept'));
                        $this->response($response);
                    }                    

                    $updateData['upd'] = date('Y-m-d H:i:s'); 
                    $isUpdated = $this->common_model->updateFields(COMPANION_MEMBER, $updateData,$where);

                    $this->common_model->updateFields(COMPANION_MEMBER,array('companionMemberStatus'=>4),array('eventMem_Id'=>$eventMemId,'companionMemId !='=>$userId));

                    $this->common_model->deleteData(COMPANION_MEMBER,array('companionMemId'=>$userId,'eventMem_Id !='=>$eventMemId,'event_id'=>$eventId,'companionMemberStatus'=>0));

                    $this->common_model->deleteData(EVENT_MEMBER,array('memberId'=>$userId,'event_id'=>$eventId));

                    if($isUpdated){

                        $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$getCompData->memberId,'isNotification'=>1));

                        if($user_info_for){               
                            $registrationIds[] = $user_info_for->deviceToken; 

                            if($user_info_for->setLanguage == 'spanish'){
                                $title = 'Compañero Aceptada';
                                $showMsg = ' aceptó su solicitud de evento.';
                            }else{
                                $title = 'Companion Accept';
                                $showMsg = ' accepted your event request.';
                            }

                            $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                            $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                            $notif_type = 'companion_accept';
                            $notify_for = $getEventData->eventOrganizer;
                            $compId = $eventExist->compId;               
                           
                            //send notification to user
                            $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                            $notif_msg['body'] = $body_save; //replace body text with placeholder text
                            //save notification

                            $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                        }

                        $response = array('status' => 1, 'msg' => lang('req_accept_sucess'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/');
                    }else{
                        $response = array('status' => 2, 'msg' => lang('something_wrong'));  
                    }
                }else{
                    $response = array('status' => 5, 'msg' => lang('user_limit_exceed'));
                }
            }elseif ($status == 2) { // to reject request

                //update status               
                $updateData['companionMemberStatus'] = 3;           
                    
                $isUpdated = $this->common_model->updateFields(COMPANION_MEMBER, $updateData,$where);
                if($isUpdated){

                    $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$getCompData->memberId,'isNotification'=>1));

                    if($user_info_for){               
                        $registrationIds[] = $user_info_for->deviceToken; 
                        
                        if($user_info_for->setLanguage == 'spanish'){
                            $title = 'Compañero Rechazada';
                            $showMsg = ' rechazó tu solicitud de evento.';
                        }else{
                            $title = 'Companion Reject';
                            $showMsg = ' rejected your event request.';
                        }

                        $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                        $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                        $notif_type = 'companion_reject';
                        $notify_for = $getEventData->eventOrganizer; 
                        $compId = $eventExist->compId;               
                       
                        //send notification to user
                        $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId,$eventMemId='',$notif_type,$notify_for);

                        $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$compId,'eventMemId'=>'','createrId'=>$notify_for);

                        $notif_msg['body'] = $body_save; //replace body text with placeholder text
                        //save notification

                        $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                        $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                        $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                    }

                    $response = array('status' => 3, 'msg' => lang('req_rejected'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/');
                }else{
                    $response = array('status' => 4, 'msg' => lang('something_wrong'));
                }
            }
        }        
        echo json_encode($response);

    } // End of function

    // if event's payment type is paid for companion
    function companionPayment(){

        $this->load->library('Stripe');
        $this->load->model('Payment_model');
        $this->check_user_session();

        $userId = $this->session->userdata('userId');
        $eventId = $this->input->post('eventIdPay');
        $compId = $this->input->post('compIdPay');
        $eventMemId = $this->input->post('eventMemIdPay');
        $payment = $this->input->post('eventAmount');
        $token  = $this->input->post('stripeToken');
        $email = $this->session->userdata('email');
        
        $where = array('memberId'=>$userId,'event_id'=>$eventId);
        //check data exist
        $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);

        if(empty($eventExist)){
            $response = array('status' => 0, 'msg' => lang('event_not_exist'));
            echo json_encode($response);
        }

        $checkWhere = array('eventId'=>$eventId);
        $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

        if($getEventData){

            $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

            if($checkUserLimit === TRUE){

                if($token){

                    $checkPaymentWhere = array('compId'=>$compId,'companionMemberStatus'=>1,'event_id'=>$eventId);

                    $memPayment = $this->common_model->getsingle(COMPANION_MEMBER, $checkPaymentWhere);

                    if(!empty($memPayment)){
                        $response = array('status' => 14, 'msg' => lang('mem_already_joined'));
                        echo json_encode($response);
                    }

                    $customer_id = $this->Payment_model->getStripeCustomerId();
        
                    if($customer_id === FALSE){
                        $res['status'] = 2; $res['msg'] = lang('something_wrong');
                        echo json_encode($res); exit;
                    }
                    
                    if(empty($customer_id)){
                        //create customer if ID not found
                        $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                        
                        if($stripe_res['status'] == false){
                            $res['status'] = 3; $res['msg'] = $stripe_res['message'];
                            echo json_encode($res); exit;
                        }
                        
                        $customer_id = $stripe_res['data']->id;  //customer ID
                        
                        //save customer ID in our DB for future use
                        $update = $this->Payment_model->saveCustomerId($customer_id);
                        
                        //some problem in updating customer ID
                        if(!$update){
                            $res['status'] = 4; $res['msg'] = lang('something_wrong');
                            echo json_encode($res); exit;
                        }
                    }

                    /* to pay stripe and save detail in db*/
                    /*$result = $this->stripe->pay_by_card_id($payment,$customer_id); //pay

                    if(!empty($result['data']) && $result['status'] === true){*/

                        $getOrganiserBankAcc = $this->common_model->getOrganiserBankAccId($eventId);

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
                                $finalData['paymentType']       = 4;
                                $finalData['transactionType']   = "stripeToBank";
                                $finalData['user_id']           = $userId;
                                $finalData['referenceId']       = $eventId;
                                $finalData['paymentDetail']     = json_encode($isPaymentDone);

                                //check data exist
                                $wherePay = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>4,'referenceId'=>$eventId,'transactionType'=>"stripeToBank");
                                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);
                                if(!empty($paymentExist)){

                                    $response = array('status' => 5, 'msg' => lang('payment_already_done'));
                                        echo json_encode($response);exit;
                                }

                                /* to pay stripe and save detail in db*/
                                /*$payData['transactionId'] = $result['data']->balance_transaction;
                                $payData['chargeId'] =  $result['data']->id;
                                $payData['paymentStatus'] =  $result['data']->status;
                                $payData['amount'] = $payment;
                                $payData['crd'] = date('Y-m-d H:i:s');
                                $payData['paymentType'] =4;
                                $payData['user_id'] = $this->session->userdata('userId');
                                $payData['referenceId'] =$eventId;

                                //check data exist
                                $wherePay = array('user_id'=>$payData['user_id'],'paymentStatus'=>'succeeded','paymentType'=>4,'referenceId'=>$eventId);
                                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);

                                if(!empty($paymentExist)){
                                    $response = array('status' => 6, 'msg' => 'Payment is already done');
                                    echo json_encode($response);
                                }

                                $this->common_model->insertData(PAYMENT_TRANSACTIONS,$payData);*/
                                /*to pay stripe and save detail in db*/

                                // save final payment in db
                                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$finalData);

                                if($insertId){

                                    //update status               
                                    $updateData['companionMemberStatus'] = 1;                
                                    $updateData['upd'] = date('Y-m-d H:i:s');                
                                    $updateWhere = array('compId'=>$this->input->post('compIdPay'));
                                    $isUpdated = $this->common_model->updateFields(COMPANION_MEMBER, $updateData,$updateWhere);

                                    if($isUpdated){

                                        $user_info_for = $this->common_model->getsingle(USERS,array('userId'=>$getEventData->eventOrganizer,'isNotification'=>1));

                                        if($user_info_for){               
                                            $registrationIds[] = $user_info_for->deviceToken; 

                                            if($user_info_for->setLanguage == 'spanish'){
                                                $title = 'Pago del evento';
                                                $showMsg = ' ha pagado por el compañero compañero.';
                                            }else{
                                                $title = 'Event Payment';
                                                $showMsg = ' has paid for companion partner.';
                                            }

                                            $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                                            $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                                            $notif_type = 'companion_payment';
                                            $notify_for = $user_info_for->userId;            
                                           
                                            //send notification to user
                                            $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId,$eventMemId='',$notif_type,$notify_for);

                                            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$compId,'eventMemId'=>'','createrId'=>$notify_for);

                                            $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                            //save notification

                                            $insertdata = array('notificationBy'=>$userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>date('Y-m-d H:i:s'));
                                            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$userId,'notificationType'=>$notif_type);
                                            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                                        }

                                        $response = array('status' => 1, 'msg' => lang('payment_done'), 'url' => base_url('home/event/eventRequestDetail/').encoding($eventId).'/?eventMemId='.encoding($this->input->post('eventMemIdPay')));
                                        echo json_encode($response);exit;
                                    }else{
                                        
                                        $response = array('status' => 7, 'msg' => lang('something_wrong'));
                                        echo json_encode($response);exit;
                                    }
                                }
                            }else{
                                $response = array('status' => 8, 'msg' => $isPaymentDone['message']);
                                echo json_encode($response);exit;
                            }                                        
                        }else{
                            $response = array('status' => 9, 'msg' => lang('transaction_err'));
                            echo json_encode($response);exit;
                        }                        
                    /*}else{
                        $response = array('status' => 10, 'msg' => $result['message']);
                        echo json_encode($response);
                    }*/
                }else{
                    $response = array('status' => 11, 'msg' => lang('something_wrong'));
                    echo json_encode($response);
                }             
            }else{
                $response = array('status' => 12, 'msg' => lang('user_limit_exceed'));
                echo json_encode($response);exit;
            }
        }else{
            $response = array('status' => 13, 'msg' => lang('event_not_exist'));
            echo json_encode($response);exit;
        }        

    } // End of function

    function updateEvent(){

        $this->check_user_session();

        $this->load->model('Appointment_model'); 

        $data['eventId'] = decoding($this->uri->segment(4));

        $data['userId']  = $this->session->userdata('userId');

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $data_val['front_scripts'] = array($load_custom_js.'update_event.js',$load_custom_js.'event_chat.js');

        $data_val['eventDetail']   = $this->common_model->myEventDetail($data);

        $data_val['userDetail']    = $this->common_model->usersDetail($data['userId']);

        $data_val['bizList']       = $this->Appointment_model->getBusinessList();        

        $this->load->front_render('update_event',$data_val,'');
        
    } // End of function

    // to update event
    function updateEventData(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $this->load->library('form_validation');

        $paymentType = $this->input->post('payment');
        $this->load->library('form_validation');

        $bizAdd  = $this->input->post('bizAdd');

        if(empty($bizAdd )){

            $this->form_validation->set_rules('bizAdd', 'Event address', 'required|callback__upcheck_lat_long',array('_check_lat_long'=>lang('valid_address')));
        }

        $this->form_validation->set_rules('eventName',lang('event_name_place'),'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('eventStartDate',lang('event_start_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventEndDate',lang('event_end_datetime_place'),'trim|required');            
        /*$this->form_validation->set_rules('privacy','Privacy','trim|required');
        $this->form_validation->set_rules('payment','Payment','trim|required');  
        $this->form_validation->set_rules('userLimit','User limit','trim|required'); 
        $this->form_validation->set_rules('eventUserType','Event user type','trim|required');*/ 
        $this->form_validation->set_rules('memberId',lang('event_friend'),'trim|required');
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required'); 

        if($paymentType == 1){
            $this->form_validation->set_rules('eventAmount',lang('event_amt'),'trim|required');
            $this->form_validation->set_rules('currencySymbol',lang('event_cur'),'trim|required');
        }

        if ($this->form_validation->run() == FALSE){
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/event/updateEvent'));
        } else {

            $userId = $this->session->userdata('userId');
            $eventId = $this->input->post('eventId');
            $where = array('eventId'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->is_data_exists(EVENTS, $where);

            if(empty($eventExist)){
                $response = array('status' => 3, 'msg' => lang('event_not_exist'));
                echo json_encode($response);exit;
            }

            // check member join or not
            /*$checkEventMemStatus = $this->common_model->checkUpdateEvent($eventId,$userId);

            if($checkEventMemStatus == TRUE){
                $response = array('status' => 4, 'msg' => 'Some members are join this event, so you cannot update this event.');
                echo json_encode($response);exit;
            } */   

            // when event's payment type is paid
            //$eventData['eventAmount'] = $eventData['currencySymbol'] = $eventData['currencyCode'] ='';
            if($paymentType == 1){
                $currency = $this->input->post('currencySymbol'); 
                $getCurrency = explode(',', $currency);
                $eventData['eventAmount']       = $this->input->post('eventAmount');
                $eventData['currencySymbol']    = $getCurrency[1];
                $eventData['currencyCode']      = $getCurrency[0];
            }
            
            $eventData['eventOrganizer']    = $userId;
            $eventData['eventName']         = $this->input->post('eventName');
            $eventData['eventStartDate']    = date("Y-m-d H:i:s", strtotime($this->input->post('eventStartDate')));
            $eventData['eventEndDate']      = date("Y-m-d H:i:s", strtotime($this->input->post('eventEndDate')));
            $eventData['eventPlace']        = $this->input->post('bizAdd');
            $eventData['eventLatitude']     = $this->input->post('bizLat');
            $eventData['eventLongitude']    = $this->input->post('bizLong');

            if($this->input->post('privacy'))
                $eventData['privacy']           = $this->input->post('privacy');        // 1 for Public, 2 for Private

            if($paymentType)
                $eventData['payment']           = $paymentType;                          // 1 for Paid, 2 for Free

            if($this->input->post('userLimit'))                       
                $eventData['userLimit']         = $this->input->post('userLimit');

            if($this->input->post('eventUserTypeG'))
                $eventData['eventUserType']     = $this->input->post('eventUserTypeG');  // 1 for Male,2 for Female,3 for Both
            
            $eventData['groupChat']         = $this->input->post('groupChat');      //  1 for Yes, 0 for No            
            $eventData['business_id']       = !empty($this->input->post('bizId')) ? $this->input->post('bizId') : '';

            $friendId =  explode(',', $this->input->post('memberId'));
          
            $eventData['upd'] = date('Y-m-d H:i:s');                 

            $isUpdated = $this->Event_model->updateEvent($eventData,$eventId,$userId,$friendId);

            if($isUpdated == TRUE){

                $userName = $this->session->userdata('fullName');

                $where_detail = array('e.eventId' => $eventId);
                $event_detail = $this->Event_model->getEventImageDetail($where_detail);
                
                $response_data['event'] =  $event_detail;
                $response_data['eventName'] =  $eventData['eventName'];

                // send notifications as background process
                shell_exec("php /var/www/html/index.php home event createEventNotification '".$eventId."' '".$userId."' '".$eventData['eventName']."' '".$userName."' >> /var/www/html/bgNotification_log.txt &");

                $response = array('status' => 1, 'msg' => lang('event_updated') ,'imgData' => $response_data, 'url' => base_url('home/event'));  
            } else{
                $response = array('status' => 2, 'msg' => lang('something_wrong'), 'url' => base_url('home/event/updateEvent'));
            }               
        }
        echo json_encode($response);
        
    } // End of function


    function _upcheck_lat_long(){
        
        $eventLatitude = $this->input->post('bizLat');
        $eventLongitude = $this->input->post('bizLong');        
        if(empty($eventLatitude) && empty($eventLongitude)){
            return FALSE;
        }
        return True;        
    }

    function giveReview(){

        //check for auth
        $auth_res = $this->check_ajax_auth();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('rating','Rating','trim|required');
        $this->form_validation->set_rules('comment','Comment','trim|required');

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
            $data['reviewType']        = 2; // 1 for Appointment and 2 for Event
            $data['crd']               = date('Y-m-d H:i:s');

            $where = array('by_user_id'=>$data['by_user_id'],'for_user_id'=>$data['for_user_id'],'referenceId'=>$data['referenceId']);
            //check data exist
            $reviewExist = $this->common_model->is_data_exists(REVIEW, $where);
            if(!empty($reviewExist)){
                $response = array('status' => 0, 'msg' => lang('already_reviewed'));
                echo json_encode($response);exit();
            }

            //insert data
            $eventData = $this->common_model->insertData(REVIEW,$data);

            if($eventData){

                $where = array('userId'=>$data['for_user_id'],'isNotification'=>1);
                $user_info_for = $this->common_model->getsingle(USERS,$where);

                if($user_info_for){
                    $registrationIds[] = $user_info_for->deviceToken; 
                    
                    if($user_info_for->setLanguage == 'spanish'){

                        $title = 'Recomendaciones de eventos';
                        $showMsg = ' te ha dado una revisión.';
                       
                    } else{

                        $title = 'Event Reviews';
                        $showMsg = ' has given you a review.';
                        
                    }

                    $body_send  = $this->session->userdata('fullName').$showMsg; //body to be sent with current notification
                    $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                    $notif_type = 'review_event';
                    $notify_for = $user_info_for->userId;               
                   
                    //send notification to user
                    $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$data['referenceId'],$notif_type,$notify_for);

                    $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$data['referenceId'],'createrId'=>$notify_for);

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