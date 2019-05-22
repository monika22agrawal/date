<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CommonService {

    function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');       
        $this->load->model('Event_model');
        $this->lang->load('event_message_lang', $this->appLang);  //load response lang file
    }
    
    // for creating new event
    function createEvent_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $paymentType = $this->post('payment');
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventName',lang('event_name_place'),'trim|required');
        $this->form_validation->set_rules('eventStartDate',lang('event_start_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventEndDate',lang('event_end_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventPlace',lang('event_add'),'trim|required');      
        $this->form_validation->set_rules('eventLatitude',lang('event_add_lat'),'trim|required');
        $this->form_validation->set_rules('eventLongitude',lang('event_add_long'),'trim|required');
        $this->form_validation->set_rules('privacy',lang('event_privacy'),'trim|required');
        $this->form_validation->set_rules('payment',lang('event_payment'),'trim|required');  
        $this->form_validation->set_rules('userLimit',lang('event_user_lmt'),'trim|required');  
        $this->form_validation->set_rules('eventUserType',lang('event_user_typ'),'trim|required');
        $this->form_validation->set_rules('inviteFriendId',lang('event_friend'),'trim|required');
        $this->form_validation->set_rules('groupChat',lang('event_group_chat'),'trim|required');

        if($paymentType == 1){
            $this->form_validation->set_rules('eventAmount',lang('event_amt'),'trim|required');
            $this->form_validation->set_rules('currencySymbol',lang('event_cur'),'trim|required');
        }
      
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId = $this->authData->userId;
            
            $date = date('Y-m-d H:i:s');
            
            $eventData = array();   

            $eventData['eventOrganizer']    = $userId;
            $eventData['business_id']       = $this->post('businessId');
            $eventData['eventName']         = $this->post('eventName');
            $eventData['eventStartDate']    = date("Y-m-d H:i:s", strtotime($this->post('eventStartDate')));
            $eventData['eventEndDate']      = date("Y-m-d H:i:s", strtotime($this->post('eventEndDate')));
            $eventData['eventPlace']        = $this->post('eventPlace');
            $eventData['eventLatitude']     = $this->post('eventLatitude');
            $eventData['eventLongitude']    = $this->post('eventLongitude');
            $eventData['privacy']           = $this->post('privacy');                         // 1 for Public, 2 for Private
            $eventData['payment']           = $paymentType;                                   // 1 for Paid, 2 for Free
            $eventData['eventAmount']       = $this->post('eventAmount');                 // when event's payment type is paid
            $eventData['currencySymbol']    = $this->post('currencySymbol');           // when event's payment type is paid
            $eventData['currencyCode']      = $this->post('currencyCode');               // when event's payment type is paid
            $eventData['userLimit']         = $this->post('userLimit');
            $eventData['eventUserType']     = $this->post('eventUserType');             //  1 for Male,2 for Female,3 for Both            
            $eventData['groupChat']         = $this->post('groupChat');             //  1 for Yes, 0 for No            
            $eventData['crd']               = $date;

            $ids                            = $this->post('inviteFriendId');

            $friendId                       = explode(',', $ids);

            $image = array();
            $folder = 'event';
            //$eduId = $workId = $interestId = '';
            if(!empty($_FILES['eventImage']['name'])){
                $filesCount = count($_FILES['eventImage']['name']);   //check image count
                $files = $_FILES;
                $alb_img_key = 'eventImage';         

                if($filesCount>5){
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(169));
                    $this->response($response);
                }

                $this->load->model('image_model');

                for($i = 0; $i < $filesCount; $i++){
                    
                    $_FILES['eventImage']['name'] = $files[$alb_img_key]['name'][$i];
                    $_FILES['eventImage']['type'] = $files[$alb_img_key]['type'][$i];
                    $_FILES['eventImage']['tmp_name'] = $files[$alb_img_key]['tmp_name'][$i];
                    $_FILES['eventImage']['error'] = $files[$alb_img_key]['error'][$i];
                    $_FILES['eventImage']['size'] = $files[$alb_img_key]['size'][$i];
                    
                    $event_image = $this->image_model->updateMedia('eventImage',$folder);
                   
                    if(!is_array($event_image)){
                        $image[] = $event_image;
                    }else{
                       
                        $response = array('status'=>FAIL,'message'=>$event_image['error']);
                        $this->response($response);
                        break;
                    }
                }
            }            
           
            $isCreated = $this->Event_model->createEvent($eventData,$friendId,$image);
            
            if($isCreated){
                $response_data =  array('eventId'=>$isCreated); //created event ID
                
                $where_detail = array('e.eventId' => $isCreated);
                $event_detail = $this->Event_model->getEventImageDetail($where_detail);
                $response_data['event'] =  $event_detail;
                $userName = $this->authData->fullName;

                // send notifications as background process
                shell_exec("php /var/www/html/index.php service event createEventNotification '".$isCreated."' '".$userId."' '".$eventData['eventName']."' '".$userName."' >> /var/www/html/bgNotification_log.txt &");
                 
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(138), 'data' => $response_data);

            } else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            }
            $this->response($response);
        }
    } // end of function


    // for sending background notifications of multiple users
    function createEventNotification_get($eventId,$userId,$eventName,$userName){
        
        $this->common_model->createEventBgNotification($eventId,$userId,$eventName,$userName);

    } // end of function
    
    //add single event image
    function addEventImage_post(){
        
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $event_id = $this->post('eventId');
        
        //check if event exist
        $where = array('eventId'=>$event_id);
        $eventExist = $this->common_model->is_data_exists(EVENTS, $where);
        if($eventExist === FALSE){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
            $this->response($response);
        }
        $this->load->model('image_model');
        //Get event image count and check if event image limit is reached (For event max 5 images can be uploaded)
        $where = array('event_id'=>$event_id);
        $image_count = $this->common_model->get_total_count(EVENT_IMAGE, $where);
        if($image_count >= 5){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(169));
            $this->response($response);
        }
        
        if(empty($_FILES['eventImage']['name'])){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(112));
            $this->response($response);
        }
        
        //all good here, we can proceed to upload image now
        $folder = 'event';
        $event_image = $this->image_model->updateMedia('eventImage',$folder);
        if(is_array($event_image) && array_key_exists("error",$event_image)){
            $response = array('status' => FAIL, 'message' => strip_tags($event_image['error']));
            $this->response($response);
        }
        
        $insert_id = $this->Event_model->addEventImage($event_id, $event_image);
        if(!$insert_id){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            $this->response($response);
        }
        
        $where_detail = array('eventImgId' => $insert_id);
        $event_detail = $this->Event_model->getEventImageDetail($where_detail);
        $response_arr =  array('event'=>$event_detail);
        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(170), 'data'=>$response_arr);
        $this->response($response);
        
    }
    
    //delete event image
    function removeEventImage_post(){
        
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $event_image_id = $this->post('eventImageId');
        $where = array('eventImgId'=>$event_image_id);
        $image_detail = $this->common_model->getsingle(EVENT_IMAGE, $where);
        
        if(empty($image_detail)){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
            $this->response($response);
        }
        
        //Check event image count and prevent deletion of last image (Atleast one image is required for an event)
        $where_count = array('event_id'=>$image_detail->event_id);
        $image_count = $this->common_model->get_total_count(EVENT_IMAGE, $where_count);
        if($image_count == 1){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(171));
            $this->response($response);
        }
        
        $this->load->model('image_model');
        $this->common_model->deleteData(EVENT_IMAGE, $where); //delete image record

        $file_name = $image_detail->eventImage;
        $img_path = FCPATH.'uploads/event/';
        $this->image_model->unlinkFile($img_path, $file_name); //unlink image from server directory        
        
        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(172));
        $this->response($response);
    }
    
    function getInvitationUserList_post(){
        
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $offset = $this->post('offset');
        if(!isset($offset) || empty($this->post('limit'))){
            $event_data['offset'] = 0; $event_data['limit']= 10; 
        }else{
            $event_data['offset']  = $this->post('offset'); 
            $event_data['limit']   = $this->post('limit');
        }
        
        //user params
        //userGender = 1: Male, 2:Female, 3:Transgender, 4:All
        $other_param = array('userGender', 'privacy');
        foreach ($other_param as $key => $val){

            $param_val = $this->post($val);
            if(!empty($param_val)){
                $event_data[$val] = $param_val;                 
            }
        }

        $event_data['eventId']   = $this->post('eventId');
        
        //search or filter params
        $search = array();
        $search_param = array('name', 'latitude', 'longitude', 'rating', 'city', 'state', 'country');
        
        foreach ($search_param as $key => $val){

            $param_val = $this->post($val);
            if(!empty($param_val)){
                $search[$val] = $param_val;                 
            }
        }

        $address = $city = $state = $country = '';

        if(isset($search['latitude']) && isset($search['longitude'])){

            $getFullAddress = getAddress($search['latitude'],$search['longitude']);
            
            if(!empty($getFullAddress)){

                $address    = isset($getFullAddress['formatted_address']) ? $getFullAddress['formatted_address'] : '';  // get full address
                $city       = isset($getFullAddress['city']) ? $getFullAddress['city'] : '';                            // get city name
                $state      = isset($getFullAddress['state']) ? $getFullAddress['state'] : '';                          // get state name 
                $country    = isset($getFullAddress['country']) ? $getFullAddress['country'] : '';                      // get country name

                $search['city']     = $city;       
                $search['state']    = $state;       
                $search['country']  = $country;
                $search['address']  = $address;
            }
        }       

        $user_list = $this->Event_model->getInvitationUserList($event_data, $search);
        $response_data['user'] = $user_list;
        $response = array('status' => SUCCESS, 'data' => $response_data);
        $this->response($response);        
    }
    
    /*function test_shell_get(){
        $isCreated = 1; $userId = json_encode(array(3,4,5,6));
       // echo $userId; exit;
        // send notifications as background process
        shell_exec("php /var/www/html/index.php service event test_message '".$isCreated."' '".$userId."' >> /var/www/html/bgNotification_log.txt &"); 
    }
    
    function test_message_get($isCreated,$userId){
        //$a = unserialize($userId);
        $myfile = fopen(FCPATH."bgNotification_log.txt", "a") or die("Unable to open file!");
        $txt = 'Test message ::'.$isCreated. '---'. $userId."=====";
        $fwrite = fwrite($myfile, "\n". $txt);
        fclose($myfile);
        var_dump($fwrite); 
        if ($fwrite === false) {
            echo 'fail';
        }
    }*/

    function getEventList_get(){
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $data['offset']  = $this->get('offset'); 
        $data['limit']   = $this->get('limit');
        $data['listType']   = $this->get('listType'); // myEvent or eventRequest
        $data['searchText'] = $this->get('searchText');
        $data['userId'] = $this->authData->userId;
       
        //get event list
        if(!isset($data['offset']) || empty($data['limit'])){
            $data['offset'] = 0; $data['limit']= 10; 
        }        
        
        if($data['listType'] == 'myEvent'){

           $eventData = $this->common_model->myEventListCount($data);
           
        }elseif($data['listType'] == 'eventRequest'){

            $eventData = $this->common_model->eventRequestListCount($data);            
        }
      
        if($eventData){
            $response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(200),'currentDate'=>date('Y-m-d H:i:s'),'List'=>$eventData);
            $this->response($response); 
        }
        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        $this->response($response);
    } // end of function


    // get myEvent and eventRequest detail by eventId
    function getEventDetail_get(){
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $profileUrl = '';
        $data['detailType']   = $this->get('detailType'); // myEvent or eventRequest
        $data['userId'] = $this->authData->userId;
        $data['eventId'] = $this->get('eventId');

        $where = array('eventId'=>$data['eventId']);
        //check data exist
        $eventExist = $this->common_model->is_data_exists(EVENTS, $where);

        if(empty($eventExist)){
            
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
            $this->response($response);
        }
       
        if($data['detailType'] == 'myEvent'){

           $eventData = $this->common_model->myEventDetail($data);
           $profileUrl = base_url('home/event/myEventDetail/').encoding($data['eventId']).'/?eventId='.base64_encode($data['eventId']);

        }elseif($data['detailType'] == 'eventRequest'){

            $data['id'] = $this->get('id');
            $isExist = $this->common_model->is_data_exists(EVENTS, array('eventId'=>$data['eventId']));

            if(!$isExist){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }
            
            $compId =  $eventMemId = '';

            if(($this->get('compId'))){

                $compId =  $this->get('compId');
                $where = array('compId'=>$compId);
                $eventExist = $this->common_model->is_data_exists(COMPANION_MEMBER, $where);

            }elseif (($this->get('eventMemId'))) {

                $eventMemId =  $this->get('eventMemId');
                $where = array('eventMemId'=>$eventMemId);
                $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);

            }else{

                $eventExist = false;
            }
            
            if(!$eventExist){

                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }
            
            if(!empty($compId)){

                $data['compId'] = $compId;
                $eventData = $this->common_model->sharedEventRequestDetail($data);

                $compId = encoding($data['compId']);
                $query_str = '/?compId='.$compId;

                $profileUrl = base_url('home/event/eventRequestDetail/').encoding($data['eventId']).$query_str.'/?eventId='.base64_encode($data['eventId']).'/?compId='.base64_encode($data['compId']);
                
            }else{

                $data['eventMemId'] = $eventMemId;
                $eventData = $this->common_model->eventRequestDetail($data);

                $eventMemId = encoding($data['eventMemId']);
                $query_str = '/?eventMemId='.$eventMemId;

                $profileUrl = base_url('home/event/eventRequestDetail/').encoding($data['eventId']).$query_str.'/?eventId='.base64_encode($data['eventId']).'/?eventMemId='.base64_encode($data['eventMemId']);
            }           
        }
      
        if($eventData){
            $response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(200),'currentDate'=>date('Y-m-d H:i:s'),'Detail'=>$eventData['detail'],'joinedMember'=>$eventData['joinedMember'],'invitedMember'=>$eventData['invitedMember'],'companionMember'=>$eventData['companionMember'],'companionMemberAccept'=>$eventData['companionMemberAccept'] ? $eventData['companionMemberAccept'] : new stdClass(),'eventReview'=>$eventData['eventReview'],'eventUrl'=>$profileUrl);
            $this->response($response); 
        }
        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
        $this->response($response);

    } // end of function


    // get joined and invited member list by eventId
    function getEventMemberList_get(){
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $data['offset']       = $this->get('offset'); 
        $data['limit']        = $this->get('limit');
        $data['memberType']   = $this->get('memberType'); // joined or invited or companion
        $data['type']         = $this->get('type'); // myEvent or request
        $data['userId']       = $this->authData->userId;
        $data['eventId']      = $this->get('eventId');

        if(!isset($data['offset']) || empty($data['limit'])){
            $data['offset'] = 0; $data['limit']= 10; 
        }  
       
        if($data['memberType'] == 'joined'){

           $eventData = $this->common_model->joinedMemberCount($data);

        }elseif($data['memberType'] == 'invited'){

            $eventData = $this->common_model->invitedMemberCount($data);
                       
        }elseif($data['memberType'] == 'companion'){

            $eventData = $this->common_model->companionMemberCount($data);            
        }        


        if($eventData){
            $response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(200),'currentDate'=>date('Y-m-d H:i:s'),'List'=>$eventData['list'],'eventCreaterDetail'=>$eventData['eventCreaterDetail']);
            $this->response($response);
        }
        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(200),'List'=>array(),'eventCreaterDetail'=>$this->common_model->eventCreaterData($data));
        $this->response($response);

    } // end of function

    // remove invited and joind member from list
    function removeMember_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
        $this->form_validation->set_rules('memberType',lang('event_mem_type'),'trim|required');
        $this->form_validation->set_rules('eventMemId',lang('event_members'),'trim|required');
       
        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {
            $userId = $this->authData->userId;
            $memberType   = $this->post('memberType'); // invited or joined
            $eventMemId   = $this->post('eventMemId');
            $eventId      = $this->post('eventId');

            $where = array('eventMemId'=>$eventMemId);

            //check data exist
            $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);
            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(141));
                $this->response($response);
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
                        $response = array('status' => SUCCESS, 'message' => 'Member removed.');
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                    }
                }else{
                    $response = array('status' => FAIL, 'message' => 'You can not remove companion member');
                }

            }elseif ($memberType == 'joined') {

                //get total count
                $getwhereMem = array('event_id'=>$eventId,'memberStatus' => 2,'memberType !=' =>1);
                $joinedCount = $this->common_model->get_total_count(EVENT_MEMBER, $getwhereMem);

                if($joinedCount){
                    
                    // to get member id for deleting companion member also
                    $joinWhere = array('eventMemId'=>$eventMemId,'memberStatus' => 2);
                   /* $getMemId = $this->common_model->getsingle(EVENT_MEMBER,$joinWhere);
                    if($getMemId){
                        // delete companion member
                        $compWhere = array('invitedBy'=>$getMemId->memberId);
                        $this->common_model->deleteData(EVENT_MEMBER, $compWhere);
                    }*/
                    // delete member
                    $joinedData = $this->common_model->deleteData(EVENT_MEMBER, $joinWhere);
                    if($joinedData){
                        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(173));
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                    }
                }else{
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(174));
                }

            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(175));
            }
        }        
        $this->response($response);

    } // End of function

    //delete event
    function deleteEvent_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
       
        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {

            $userId = $this->authData->userId;
            $eventId = $this->post('eventId');
            $where = array('eventId'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->is_data_exists(EVENTS, $where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }
            
            //delete event
            $isDelete = $this->common_model->deleteEvent($eventId,$userId);
            switch ($isDelete) {

                case 'ED': // event deleted
                   $response=array('status'=>SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(142));
                break;                 
                
                case 'NE': // somthing going wrong
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(144));
                break; 

                case 'JM': // joined member
                    $response=array('status'=>FAIL, 'message' => ResponseMessages::getStatusCodeMessage(143));
                break; 
              
                default:
                    $response=array('status'=>FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                break;                
            }
        }
        $this->response($response);

    } // End of function

    //join member or reject event request
    function joinMember_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
        $this->form_validation->set_rules('memberId',lang('event_members'),'trim|required');
        $this->form_validation->set_rules('status',lang('event_mem_status'),'trim|required'); 
       
        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {

            $userId = $this->authData->userId;
            $eventId = $this->post('eventId');
            $memberId = $this->post('memberId');
            $status = $this->post('status'); // 1 for accept and 2 for reject

            $where = array('memberId'=>$userId,'event_id'=>$eventId);
            //check data exist
            //$eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);
            $eventExist = $this->common_model->getsingle(EVENT_MEMBER,$where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }

            $checkWhere = array('eventId'=>$eventId);
            $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

            if($getEventData){

                if($status == 1){ // for accept request

                    $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                    if($checkUserLimit === TRUE){
                        //update status
                        if($getEventData->payment == 1){ // 1:Paid,2:Free
                            $updateData['memberStatus'] = 2;

                        }elseif ($getEventData->payment == 2) {
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


                                $body_send  = $this->authData->fullName.$showMsg.$getEventData->eventName.'.'; //body to be sent with current notification
                                $body_save  = '[UNAME]'.$showMsg.'[ENAME].'; //body to be saved in DB
                                $notif_type = 'join_event';
                                $notify_for = $user_info_for->userId; 
                                $eventMemId = $eventExist->eventMemId;
                               
                                //send notification to user
                                $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                //save notification

                                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
                                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                            }

                            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(145));
                        }else{
                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
                        }
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(153));
                    }
                }elseif($status == 2){ // for reject

                    $this->common_model->deleteData(EVENT_MEMBER,array('memberId'=>$userId,'event_id'=>$eventId));
                    $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(162));
                }else{
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                }
            }          
        }
        $this->response($response);

    } // End of function

    // if event's payment type is paid for member
    function eventPayment_post(){

        $this->load->model('Payment_model');
        $this->load->library('Stripe');
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');
        $this->form_validation->set_rules('memberId',lang('event_members'),'trim|required');
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');

        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {

            $userId = $this->authData->userId;
            $email = $this->authData->email;
            $eventId = $this->post('eventId');
            $memberId = $this->post('memberId');
            $token = $this->post('token');
            $where = array('memberId'=>$memberId,'event_id'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->getsingle(EVENT_MEMBER,$where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }

            $checkWhere = array('eventId'=>$eventId);
            $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

            if($getEventData){

                $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                if($checkUserLimit === TRUE){

                    $payment = $getEventData->eventAmount;

                    if($token){

                        $customer_id = $this->Payment_model->getStripeCustomerId();
        
                        if($customer_id === FALSE){
                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                            $this->response($response);
                        }
                        
                        if(empty($customer_id)){
                            //create customer if ID not found
                            $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                            
                            if($stripe_res['status'] == false){

                                $response = array('status' => FAIL, 'message' => $stripe_res['message']);
                                $this->response($response);
                            }
                            
                            $customer_id = $stripe_res['data']->id;  //customer ID
                            
                            //save customer ID in our DB for future use
                            $update = $this->Payment_model->saveCustomerId($customer_id);
                            
                            //some problem in updating customer ID
                            if(!$update){
                                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                                $this->response($response);
                            }
                        }
    
                        /*$result = $this->stripe->pay_by_card_id($payment,$customer_id);//pay
                       
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
                                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(163));
                                        $this->response($response);
                                    }

                                    /* to pay stripe and save detail in db*/
                                    /*$payData['transactionId'] = $result['data']->balance_transaction;
                                    $payData['chargeId'] =  $result['data']->id;
                                    $payData['paymentStatus'] =  $result['data']->status;
                                    $payData['amount'] = $payment;
                                    $payData['crd'] = date('Y-m-d H:i:s');
                                    $payData['paymentType'] = 3;
                                    $payData['user_id'] = $userId;
                                    $payData['referenceId'] = $eventId;

                                    //check data exist
                                    $wherePay = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>3,'referenceId'=>$eventId);
                                    $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);
                                    if(!empty($paymentExist)){
                                        $response = array('status' => FAIL, 'message' => 'Payment is already done');
                                        $this->response($response);
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

                                                $body_send  = $this->authData->fullName.$showMsg.$getEventData->eventName.'.'; //body to be sent with current notification
                                                $body_save  = '[UNAME]'.$showMsg.'[ENAME].'; //body to be saved in DB
                                                $notif_type = 'event_payment';
                                                $notify_for = $user_info_for->userId;
                                                $eventMemId = $eventExist->eventMemId;
                                               
                                                //send notification to user
                                                $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                                                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                                                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                                //save notification

                                                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                                                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
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
                        /*}else{

                            $response = array('status' => FAIL, 'message' => $result['message']);
                        } */                                     
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                    }                    
                }else{
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(153));
                }
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
            }
        }
        $this->response($response);

    } // End of function

    //share event to multiple members 
    function shareMember_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
        $this->form_validation->set_rules('memberId',lang('event_members'),'trim|required');
        $this->form_validation->set_rules('eventMemId',lang('event_mem_name'),'trim|required');
       
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId = $this->authData->userId;
            $eventId = $this->post('eventId');
            $eventMemId = $this->post('eventMemId');
            $memberId = $this->post('memberId');
            $friendId = explode(',', $memberId);
            
            $checkWhere = array('eventId'=>$eventId);
            $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

            if($getEventData){

                $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                if($checkUserLimit === TRUE){
                    // insert shared member as companion
                    $isInsert = $this->Event_model->shareEvent($userId,$eventId,$friendId);                    

                    if($isInsert){
                        $userName = $this->authData->fullName;
                        // send notifications as background process
                        shell_exec("php /var/www/html/index.php service event shareEventBgNotification '".$eventMemId."' '".$userId."' '".$getEventData->eventName."' '".$userName."' '".$getEventData->eventId."' >> /var/www/html/bgNotification_log.txt &");

                        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(147));
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
                    }
                }else{
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(153));
                }                   
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
            }          
        }
        $this->response($response);

    } // End of function

    // for sending background notifications of multiple users
    function shareEventBgNotification_get($eventMemId,$userId,$eventName,$userName,$eventId){
        
        $this->common_model->shareEventBgNotification($eventMemId,$userId,$eventName,$userName,$eventId);

    } // end of function


    //companion status accept/reject for event
    function companionMemberStatus_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventMemId',lang('event_mem_name'),'trim|required');
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
        $this->form_validation->set_rules('status',lang('event_comp_status'),'trim|required');
       
        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {

            $userId = $this->authData->userId;
            $eventId = $this->post('eventId');
            $status = $this->post('status');
            $eventMemId = $this->post('eventMemId');
            $where = array('companionMemId'=>$userId,'eventMem_Id'=>$eventMemId,'event_id'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->getsingle(COMPANION_MEMBER, $where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
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
                        if($getEventData->payment == 1){ // 1:Paid,2:Free

                            $updateData['companionMemberStatus'] = 2;

                        }elseif ($getEventData->payment == 2) {

                            $updateData['companionMemberStatus'] = 1;
                        }

                        //check status already exist or not
                        $compExist = $this->common_model->getsingle(COMPANION_MEMBER, array('eventMem_Id'=>$eventMemId,'event_id'=>$eventId,'companionMemberStatus'=>1));

                        if(!empty($compExist)){

                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(197));
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

                                $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                                $notif_type = 'companion_accept';
                                $notify_for = $getEventData->eventOrganizer;                                   
                               
                                //send notification to user
                                $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId='',$eventMemId,$notif_type,$notify_for);

                                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>'','eventMemId'=>$eventMemId,'createrId'=>$notify_for);

                                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                //save notification

                                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
                                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                            }

                            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(149));
                        }else{
                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
                        }
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(153));
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

                            $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                            $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                            $notif_type = 'companion_reject';
                            $notify_for = $getEventData->eventOrganizer;

                            $compId = $eventExist->compId;          
                           
                            //send notification to user
                            $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId,$eventMemId='',$notif_type,$notify_for);

                            $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$compId,'eventMemId'=>'','createrId'=>$notify_for);

                            $notif_msg['body'] = $body_save; //replace body text with placeholder text
                            //save notification

                            $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                            $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
                            $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
                        }

                        $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(150));
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));  
                    }
                }
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            } 
        }
        $this->response($response);

    } // End of function


    // if event's payment type is paid for companion
    function companionPayment_post(){

        $this->load->model('Payment_model');
        $this->load->library('Stripe');
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');
        $this->form_validation->set_rules('compId',lang('event_comp_user'),'trim|required');
        $this->form_validation->set_rules('eventMemId',lang('event_mem_name'),'trim|required');
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');        
       
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId     = $this->authData->userId;
            $email      = $this->authData->email;
            $eventId    = $this->post('eventId');
            $compId     = $this->post('compId');
            $eventMemId = $this->post('eventMemId');
            $token      = $this->post('token');

            $where = array('memberId'=>$userId,'event_id'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->is_data_exists(EVENT_MEMBER, $where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }

            $checkWhere = array('eventId'=>$eventId);
            $getEventData = $this->common_model->getsingle(EVENTS,$checkWhere);

            if($getEventData){

                $checkUserLimit = $this->common_model->getEventMemberCount($eventId);

                if($checkUserLimit === TRUE){

                    $payment = $getEventData->eventAmount;

                    $checkPaymentWhere = array('compId'=>$compId,'companionMemberStatus'=>1,'event_id'=>$eventId);

                    $memPayment = $this->common_model->getsingle(COMPANION_MEMBER, $checkPaymentWhere);

                    if(!empty($memPayment)){
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(193));
                        $this->response($response);
                    }

                    $customer_id = $this->Payment_model->getStripeCustomerId();
        
                    if($customer_id === FALSE){
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                        $this->response($response);
                    }
                    
                    if(empty($customer_id)){
                        //create customer if ID not found
                        $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                        
                        if($stripe_res['status'] == false){
                            $response = array('status' => FAIL, 'message' => $stripe_res['message']);
                            $this->response($response);
                        }
                        
                        $customer_id = $stripe_res['data']->id;  //customer ID
                        
                        //save customer ID in our DB for future use
                        $update = $this->Payment_model->saveCustomerId($customer_id);
                        
                        //some problem in updating customer ID
                        if(!$update){
                            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                            $this->response($response);
                        }
                    }

                    if($token){
                                                    
                       /* $result = $this->stripe->pay_by_card_id($payment,$customer_id);//pay
                       
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
                                        
                                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(163));
                                        $this->response($response);
                                    }

                                    /* to pay stripe and save detail in db*/
                                    /*$payData['transactionId'] = $result['data']->balance_transaction;
                                    $payData['chargeId'] =  $result['data']->id;
                                    $payData['paymentStatus'] =  $result['data']->status;
                                    $payData['amount'] = $payment;
                                    $payData['crd'] = date('Y-m-d H:i:s');
                                    $payData['paymentType'] = 4;
                                    $payData['user_id'] = $userId;
                                    $payData['referenceId'] = $eventId;

                                    //check data exist
                                    $wherePay = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>4,'referenceId'=>$eventId);
                                    $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $wherePay);
                                    
                                    if(!empty($paymentExist)){
                                        $response = array('status' => FAIL, 'message' => 'Payment is already done');
                                        $this->response($response);
                                    }
                                    $this->common_model->insertData(PAYMENT_TRANSACTIONS,$payData);*/
                                    /*to pay stripe and save detail in db*/

                                    // save final payment in db
                                    $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$finalData);

                                    if($insertId){

                                        //update status               
                                        $updateData['companionMemberStatus'] = 1;       
                                        $updateData['upd'] = date('Y-m-d H:i:s');          
                                        $updateWhere = array('compId'=>$compId);

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

                                                $body_send  = $this->authData->fullName.$showMsg; //body to be sent with current notification
                                                $body_save  = '[UNAME]'.$showMsg; //body to be saved in DB
                                                $notif_type = 'companion_payment';
                                                $notify_for = $user_info_for->userId;
                                                   
                                                //send notification to user
                                                $this->notification_model->send_push_notification_for_event($registrationIds, $title, $body_send,$eventId,$compId,$eventMemId='',$notif_type,$notify_for);

                                                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$eventId,'compId'=>$compId,'eventMemId'=>'','createrId'=>$notify_for);

                                                $notif_msg['body'] = $body_save; //replace body text with placeholder text
                                                //save notification

                                                $insertdata = array('notificationBy'=>$this->authData->userId, 'notificationFor'=>$user_info_for->userId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type,'referenceId'=>$eventId, 'crd'=>datetime());
                                                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$this->authData->userId,'notificationType'=>$notif_type);
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
                        /*}else{

                            $response = array('status' => FAIL, 'message' => $result['message']);
                        }*/
                    }else{
                        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                    }
                }else{
                    $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(153));
                }
            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
            }
        }
        $this->response($response);

    } // End of function

    // to update event
    function updateEvent_post(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $paymentType = $this->post('payment');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('eventName',lang('event_name_place'),'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('eventStartDate',lang('event_start_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventEndDate',lang('event_end_datetime_place'),'trim|required');      
        $this->form_validation->set_rules('eventPlace',lang('event_add'),'trim|required');
        $this->form_validation->set_rules('eventLatitude',lang('event_add_lat'),'trim|required');
        $this->form_validation->set_rules('eventLongitude',lang('event_add_long'),'trim|required');
        $this->form_validation->set_rules('privacy',lang('event_privacy'),'trim|required');
        $this->form_validation->set_rules('payment',lang('event_payment'),'trim|required');  
        $this->form_validation->set_rules('userLimit',lang('event_user_lmt'),'trim|required');  
        $this->form_validation->set_rules('eventUserType',lang('event_user_typ'),'trim|required');
        $this->form_validation->set_rules('inviteFriendId',lang('event_friend'),'trim|required'); 
        $this->form_validation->set_rules('eventId',lang('event_id'),'trim|required');  
        $this->form_validation->set_rules('groupChat',lang('event_group_chat'),'trim|required');

        if($paymentType == 1){
            $this->form_validation->set_rules('eventAmount',lang('event_amt'),'trim|required');
            $this->form_validation->set_rules('currencySymbol',lang('event_cur'),'trim|required');
        }

        if ($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);  
        } else {

            $userId = $this->authData->userId;
            $eventId = $this->post('eventId');
            $where = array('eventId'=>$eventId);
            //check data exist
            $eventExist = $this->common_model->is_data_exists(EVENTS, $where);

            if(empty($eventExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(140));
                $this->response($response);
            }
            
            $eventData['business_id']       = $this->post('businessId');
            $eventData['eventName']         = $this->post('eventName');
            $eventData['eventStartDate']    = date("Y-m-d H:i:s", strtotime($this->post('eventStartDate')));
            $eventData['eventEndDate']      = date("Y-m-d H:i:s", strtotime($this->post('eventEndDate')));
            $eventData['eventPlace']        = $this->post('eventPlace');
            $eventData['eventLatitude']     = $this->post('eventLatitude');
            $eventData['eventLongitude']    = $this->post('eventLongitude');
            $eventData['privacy']           = $this->post('privacy');              // 1 for Public, 2 for Private
            $eventData['payment']           = $paymentType;                        // 1 for Paid, 2 for Free
            $eventData['eventAmount']       = $this->post('eventAmount');          // when event's payment type is paid
            $eventData['currencySymbol']    = $this->post('currencySymbol');       // when event's payment type is paid
            $eventData['currencyCode']      = $this->post('currencyCode');         // when event's payment type is paid
            $eventData['userLimit']         = $this->post('userLimit');
            $eventData['eventUserType']     = $this->post('eventUserType');        // 1 for Male,2 for Female,3 for Both                            
            $eventData['groupChat']         = $this->post('groupChat');            // 1 for Yes, 0 for No
                                            
            $eventData['upd']               = date('Y-m-d H:i:s');

            $ids                            = $this->post('inviteFriendId');

            $friendId                       = explode(',', $ids);        
           
            $isUpdated = $this->Event_model->updateEvent($eventData,$eventId,$userId,$friendId);

            if($isUpdated == TRUE){

                $userName = $this->authData->fullName;

                // send notifications as background process
                shell_exec("php /var/www/html/index.php service event createEventNotification '".$eventId."' '".$userId."' '".$eventData['eventName']."' '".$userName."' >> /var/www/html/bgNotification_log.txt &");

                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(151));

            }  else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            }
        }
        $this->response($response);
        
    } // End of function

    // delete user's image
    function deleteEventImage_post(){

        //check auth token
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('eventImgId',lang('event_img'),'required');

        if($this->form_validation->run() == FALSE){
            $responseArray = array('status'=>FAIL,'message'=>validation_errors());
            $response = $this->generate_response($responseArray);
            $this->response($response);

        } else {

            $eventImgId   = $this->post('eventImgId');
            $where = array('user_id'=>$this->authData->userId,'eventImgId'=>$eventImgId);
            $res = $this->common_model->deleteData(EVENT_IMAGE,$where);
         
            if($res){
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(130));
            } else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
            }
        }
        $this->response($response);
    }

    // to get band aacount detail
    function getBankAccDetail_get(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId = $this->authData->userId;        
        //get data
        $accountDetail = $this->common_model->getBankAccountDetail($userId);
        if($accountDetail){
            $response = array('status' => SUCCESS, 'message'=>'OK','accountDetail' => $accountDetail);
        } else{
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        }
        $this->response($response);
    }

    // to get band aacount detail
    function getEventIdsDetail_get(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId      = $this->authData->userId;        
        $eventId     = $this->get('eventId');
        $eventDetail = array();        
        //get data
        $detail = $this->common_model->getsingle(EVENT_MEMBER,array('memberId'=>$userId,'event_id'=>$eventId));
        
        $eventDetail['eventId'] = $eventId;
        $eventDetail['eventMemId'] = $detail->eventMemId ? $detail->eventMemId : '';
        $eventDetail['compId'] = '';
        
        $compDetail = $this->common_model->getsingle(COMPANION_MEMBER,array('event_id'=>$eventId,'companionMemberStatus'=>1,'companionMemId'=>$userId));

        $eventDetail['eventId'] = $eventId;
        $eventDetail['eventMemId'] = $detail->eventMemId ? $detail->eventMemId : '';
        $eventDetail['compId'] = $compDetail->compId ? $compDetail->compId : '';
       
        $response = array('status' => SUCCESS, 'message'=>'OK','detail' => $eventDetail);        
        $this->response($response);
    }

    function test_post(){

        $this->load->library('Stripe');
        $name = "Monika";
        $cardNumber="4242424242424242";
        $expMonth = '11';
        $expYear='2021'; $cvv='123';$payment=100;
        $token = $this->stripe->addCardAccount($name,$cardNumber,$expMonth,$expYear,$cvv);

        if($token){
            $resu = $this->stripe->save_card_id($token);

            if(!empty($resu['data']) && $resu['status'] == true){
                
                $result = $this->stripe->pay_by_card_id($payment,$resu['data']);//pay
              // pr($result);
                if(!empty($result['data']) && $result['status'] === true){
                    
                    $data = array(
                        'amount'=>100,
                        'bankAccId'=>'acct_1Ca11JESYzteytPH',
                        "currency"=>"eur"
                    );
                    $isPaymentDone = $this->stripe->owner_pay_byBankId($data);
                }
            }
        }
    }

} // End of class
