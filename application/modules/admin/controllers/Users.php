<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CommonBack {

    function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    function userList(){
        $data['users'] = $this->common_model->get_total_count(USERS);
        $this->load->admin_render('userList',$data,'');
    }

    function idProofList(){
        $data['users'] = $this->common_model->get_total_count(USERS);
        $this->load->admin_render('id_proof_list',$data,'');
    }

    function get_id_proof_list_ajax(){

        $this->load->model('Tablelist');

        $list = $this->Tablelist->get_id_list();

        $data = array();
        $no = $_POST['start'];

        foreach ($list as $user) {

            $action = '';
            $no++;
            $row    = array();
            $row[]  = $no;
            $isVerifiedId = ($user->isVerifiedId==1) ? '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>' : '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i>';

            $isVerifiedIdTitle = ($user->isVerifiedId==1) ? 'ID Verified' : 'ID Not Verify';

            $row[] = '<span title="'.$isVerifiedIdTitle.'">'.display_placeholder_text(wordwrap($user->fullName,37,"<br>\n")).' '.$isVerifiedId.'</span>';         
            if(!empty($user->idWithHand)){ if($user->isVerifiedId == 2){ 

                $row[] =  '<p class="text-danger">Unverified</p>';

            } elseif($user->isVerifiedId == 1){

                $row[] =  '<p class="text-success">Verified</p>';

            } else{
                $row[] =  '<p class="text-warning">Pending</p>';
            } }

            if(!filter_var($user->idWithHand, FILTER_VALIDATE_URL) === false) {
                $img = $user->idWithHand;
            }else if(!empty($user->idWithHand)){
                $img = AWS_CDN_IDPROOF_THUMB_IMG.$user->idWithHand;
            } else{
                $img = AWS_CDN_USER_PLACEHOLDER_IMG;
            }

            $row[] = '<img class="img-square" height="100px" width="100px" src="'.$img.'" />';

            if(!empty($user->idWithHand)){ 

                if($user->isVerifiedId == 1){

                    $action = '<button style="cursor: not-allowed;" class="btns-new apprve-btn btn-list-pad btn btn-success" title="Verified">'.ACTIVE_ICON.'</button>&nbsp;';

                } else{
                    $v = base_url()."admin/users/indentityProofStatusList/1/".$user->userId;
                    $action = '<a href="'.$v. '"  class="btns-new apprve-btn btn-list-pad btn btn-success" title="Verify">'.ACTIVE_ICON.'</a>&nbsp;';
                } 

                if($user->isVerifiedId == 2){

                    $action .= '<button style="cursor: not-allowed;" class="btns-new dsaprve-btn btn-list-pad btn btn-danger" title="Unverified">'.INACTIVE_ICON.'</button>&nbsp;';

                } else{ 
                    $v = base_url()."admin/users/indentityProofStatusList/2/".$user->userId;
                    $action .= '<a href="' .$v .'"  class="btns-new dsaprve-btn btn-list-pad btn btn-danger" title="Unverify">'.INACTIVE_ICON.'</a>&nbsp;';
                } 
                $link = base_url('admin/users/profile/').encoding($user->userId).'/';
                $action .= '<a href="'.$link.'"  class="btns-new dsaprve-btn btn-list-pad btn btn-danger" title="View user">'.VIEW_ICON.'</a>&nbsp;';

            } 

            $row[] = $action;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Tablelist->count_all_id(),
            "recordsFiltered" => $this->Tablelist->count_id_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }

    function get_user_list_ajax(){

        $this->load->model('Tablelist');

        $list = $this->Tablelist->get_list();

        $data = array();
        $no = $_POST['start'];

        foreach ($list as $user) {
        
            $action = '';
            $no++;
            $row    = array();
            $row[]  = $no;
            $isVerifiedId = ($user->isVerifiedId==1) ? '<i class="fa fa-check-circle text-success" aria-hidden="true"></i>' : '<i class="fa fa-times-circle text-danger" aria-hidden="true"></i>';

            $isVerifiedIdTitle = ($user->isVerifiedId==1) ? 'ID Verified' : 'ID Not Verify';

            $row[] = '<span title="'.$isVerifiedIdTitle.'">'.display_placeholder_text(wordwrap($user->fullName,37,"<br>\n")).' '.$isVerifiedId.'</span>'; 
            $row[] = display_placeholder_text(wordwrap($user->email,37,"<br>\n")); 
            //$row[] = display_placeholder_text($user->countryCode.'-'.$user->contactNo); 
            $row[] = display_placeholder_text($user->gender); 
            
            if($user->status == 1) { $row[] =  '<p class="text-success">Active</p>'; } else { $row[] =  '<p  class="text-danger">Inactive</p>'; }

            if(!filter_var($user->image, FILTER_VALIDATE_URL) === false) {
                $img = $user->image;
            }else if(!empty($user->image)){
                $img = AWS_CDN_USER_THUMB_IMG.$user->image;
            } else{
                $img = AWS_CDN_USER_PLACEHOLDER_IMG;
            }

            $row[] = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

            $clk_event = "statusFnu('".'users'."','userId','".$user->userId."','".$user->status."')";

            if($user->status == 1){ $title = 'Inactive user'; $icon = INACTIVE_ICON; } else{ $title = 'Active user'; $icon = ACTIVE_ICON; }

            $action = '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$clk_event.'"  title="'.$title.'">'.$icon.'</a>';

            $link = base_url('admin/users/profile/').encoding($user->userId).'/';
                $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
                  
            $row[] = $action;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Tablelist->count_all(),
            "recordsFiltered" => $this->Tablelist->count_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }

    function activeInactive(){

        $id = $this->input->post('id');
        $status = $this->User_model->activeInactive($id);

        if($status['message'] == 'active'){
            $data = array('status'=>1,'message'=>'User activated successfully');
        }else{
            $data = array('status'=>0,'message'=>'User inactivated');
        }
        echo json_encode($data);
    }

    function profile(){
  
        $userId = decoding($this->uri->segment(4));
        $data['userId']     = $userId;
        $data['detail']     = $this->User_model->usersDetail($userId);
        $data['payment']    = $this->User_model->getPaymentStatusDetail($userId);
        $data['bizDetail']  = $this->User_model->getBusinessDetail($userId);
        
        $this->load->admin_render('userDetail',$data);
    }

    function indentityProofStatus(){

        $status = $this->uri->segment(4);
        $userId = $this->uri->segment(5);
        $data = $this->User_model->indentityProofStatus($userId,$status);

        if($data == 1){
            redirect('admin/users/profile/'.encoding($userId).'/');
        }else{
            redirect('admin/users/profile/'.encoding($userId).'/');
        }
    }

    function indentityProofStatusList(){

        $status = $this->uri->segment(4);
        $userId = $this->uri->segment(5);
        $data = $this->User_model->indentityProofStatus($userId,$status);

        if($data == 1){
            redirect('/admin/users/idProofList');
        }else{
            redirect('/admin/users/idProofList');
        }
    }

    function my_favourite_list_ajax(){

        $userId = $_POST['user_id']; 

        $this->load->model('favourite_list_model');
        $this->favourite_list_model->set_data(array('f.user_id'=>$userId));
        $list = $this->favourite_list_model->get_list(); 
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $user) {
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($user->fullName));
            $row[] = display_placeholder_text($user->workName); 
            
            if(!filter_var($user->userImg, FILTER_VALIDATE_URL) === false) {
                $img = $user->userImg;
            }else if(!empty($user->userImg)){
                $img = AWS_CDN_USER_THUMB_IMG.$user->userImg;
            } else{
                $img = AWS_CDN_USER_PLACEHOLDER_IMG;
            }

            $row[] = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

            $link = base_url('admin/users/profile/').encoding($user->favUserId).'/';
            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
            $row[] = $action;
            $data[] = $row;          
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->favourite_list_model->count_all(),
            "recordsFiltered" => $this->favourite_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    function my_events_list_ajax(){

        $userId = $_POST['user_id']; 

        $this->load->model('event_list_model');
        $this->event_list_model->set_data(array('e.eventOrganizer'=>$userId));
        $list = $this->event_list_model->get_list();  
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $event) {
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($event->eventName));
            //$row[] = display_placeholder_text(wordwrap($event->eventPlace,20,"<br>\n")); 
            $row[] = display_placeholder_text(substr($event->eventPlace,0,15));
            
            if($event->payment == 1){
                $req = get_order_status_color('Paid');
                $requirment = '<span style="color:'.$req.'">'.'<b>Paid</b>'.' </span>' ;
                $row[] = $requirment ; 
            }else{
                $req = get_order_status_color('Free');
                $requirment = '<span style="color:'.$req.'">'.'<b>Free</b>'.' </span>' ;
                $row[] = $requirment ;
            }

            $enURL = encoding(base_url('admin/users/profile/').encoding($userId));
             
            $link = base_url('admin/users/eventDetail/').encoding($event->eventId).'/'.encoding($event->eventOrganizer).'/'.$enURL;
            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View event">'.VIEW_ICON.'</a>';
            $row[] = $action;
            $data[] = $row;         
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->event_list_model->count_all(),
            "recordsFiltered" => $this->event_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    function eventDetail(){

        $this->load->model('allevents_model');

        $redirectUrl = ($this->uri->segment(6)) ? decoding($this->uri->segment(6)) : '';
        if($redirectUrl){
            $this->session->set_userdata('redirectUrl',$redirectUrl);
        }
        $data['eventId'] = decoding($this->uri->segment(4));
        $data['userId'] = decoding($this->uri->segment(5));
        $data['eventDetail'] = $this->allevents_model->myEventDetail($data);

        $load_custom_js = base_url().APP_BACK_ASSETS.'custom/js/';
        $data_val['front_scripts'] = array($load_custom_js.'login_registration.js');

        $data['admin_scripts'] = array($load_custom_js.'joinedMember.js',$load_custom_js.'invitedMember.js');
        $this->load->admin_render('eventDetail',$data);
    }
 
    function joinedMemberList(){

        $this->load->model('allevents_model');
       
        $data['userId'] = $this->input->post('userId');
        $data['eventId'] = $this->input->post('eventId');
        $data['eventOrgId'] = $this->input->post('eventOrgId');
        $data['type'] = $this->input->post('type');
        $data['offset'] = $this->input->post('offset');
        $data['limit'] = $this->input->post('limit');

        $data['total_count'] = $this->allevents_model->countAllJoinedMember($data);
       
        $data['joined_member'] = $this->allevents_model->joinedMemberCount($data);

        $this->load->view('joinedMemeber_list',$data);
    }


    function invitedMemberList(){

        $this->load->model('allevents_model');
       
        $data['userId'] = $this->input->post('userId') ; 
        $data['eventId'] = $this->input->post('eventId') ; 
        $data['offset'] = $this->input->post('offset');
        $data['limit'] = $this->input->post('limit');

        if($data['offset']  == 0){
            $data['total_count'] = $this->allevents_model->countAllInvitedMember($data);
        }
       
        $data['invited_member'] = $this->allevents_model->invitedMemberCount($data); 
      
        $this->load->view('invitedMember',$data);
    }

    function appointment_list_ajax(){

        $data['offset'] = $this->input->post('offset');
        $data['limit'] = $this->input->post('limit');
        $userId = $_POST['user_id']; 

        $data['where']    = array('a.appointById'=>$userId);
        $data['or_where'] = array('a.appointForId'=>$userId);

        if($data['offset']  == 0){
            $data['total_count'] = $this->User_model->countAllApp($data);
        }

        $data['appointment'] = $this->User_model->appList($data);
        
        $this->load->view('appointment_list',$data);
    }


    function friend_list_ajax(){

        $data['userId'] = $_POST['user_id']; 
        $data['eventId'] = '';
        $this->load->model('friend_list_model');
        $this->friend_list_model->set_data($data);
        $list = $this->friend_list_model->get_list();  
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $frnd) {
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($frnd->fullName));
            $row[] = display_placeholder_text(ucfirst($frnd->work)); 
            
            if(!filter_var($frnd->profileImage, FILTER_VALIDATE_URL) === false) {

                $img = $frnd->profileImage;
                
            }else if(!empty($frnd->profileImage)){

                $img = AWS_CDN_USER_THUMB_IMG.$frnd->profileImage;

            } else{
                $img = AWS_CDN_USER_PLACEHOLDER_IMG;
            }

            $row[] = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

            $link = base_url('admin/users/profile/').encoding($frnd->userId).'/';
            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';

            $row[] = $action;
            $data[] = $row;           
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->friend_list_model->count_all(),
            "recordsFiltered" => $this->friend_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    function eventList(){
        $data['events'] = $this->common_model->get_total_count(EVENTS);
        $this->load->admin_render('eventList',$data,'');
    }

    function get_event_list_ajax(){

        $this->load->model('allevents_model');
        $list = $this->allevents_model->get_list();  
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $event) {
            
            $action ='';
            $no++;
            $row    = array();
            $row[]  = $no;
            
            if (strlen($event->eventName)>20) {
               $eventName= display_placeholder_text(wordwrap(substr(ucfirst($event->eventName), 0, 25), 20)).'..';
            }else {
               $eventName= ucfirst($event->eventName);
            }
            $row[]  = $eventName;
            if (strlen($event->eventPlace)>20) {
               $eventPlace= display_placeholder_text(wordwrap(substr($event->eventPlace, 0, 25), 20)).'..';
            }else {
               $eventPlace= ucfirst($event->eventPlace);
            }
            $row[]  = $eventPlace;
            $row[]  = display_placeholder_text(ucfirst($event->fullName));
            $row[]  = ($event->privacy == 1) ? 'Public' : 'Private';
            $row[]  = ($event->payment == 1) ? 'Paid' : 'Free';
            if($event->status == 1) { $row[] =  '<p class="text-success">Unblock</p>'; } else { $row[] =  '<p  class="text-danger">Block</p>'; }
            $img = (!empty($event->profileImage))? (filter_var($event->profileImage, FILTER_VALIDATE_URL))? $event->profileImage : base_url().UPLOAD_FOLDER.'/profile/'.$event->profileImage : AWS_CDN_USER_PLACEHOLDER_IMG;

            $row[]  = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

            $clk_event = "eventStatusFnu('".'events'."','eventId','".$event->eventId."','".$event->status."')";

            if($event->status == 1){ $title = 'Block Event'; $icon = INACTIVE_ICON; } else{ $title = 'Unblock Event'; $icon = ACTIVE_ICON; }

            $action = '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$clk_event.'"  title="'.$title.'">'.$icon.'</a>';

            $link   = base_url('admin/users/eventDetail/').encoding($event->eventId).'/'.encoding($event->eventOrganizer).'/'.encoding(base_url('admin/users/eventList/'));

            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View event detail">'.VIEW_ICON.'</a>';

            $row[]  = $action;
            $data[] = $row;           
        }

        $output = array(

            "draw" => $_POST['draw'],
            "recordsTotal" => $this->allevents_model->count_all(),
            "recordsFiltered" => $this->allevents_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }

    function eventBlockUnblock(){

        $this->load->model('allevents_model');
        $id = $this->input->post('id');
        $status = $this->allevents_model->eventBlockUnblock($id);

        if($status['message'] == 'active'){
            $data = array('status'=>1,'message'=>'Event unblocked successfully');
        }else{
            $data = array('status'=>0,'message'=>'Event blocked');
        }
        echo json_encode($data);
    }

    function blockUnblockMem(){

        $this->load->model('allevents_model');

        $eventMemId = decoding($this->uri->segment(4));
        $eventId = $this->uri->segment(5);
        $eventOrgId = $this->uri->segment(6);
        $data = $this->allevents_model->blockUnblockMem(array('eventMemId'=>$eventMemId));
        if($data == 1){
            redirect('admin/users/eventDetail/'.$eventId.'/'.$eventOrgId.'/');
        }else{
            redirect('admin/users/eventDetail/'.$eventId.'/'.$eventOrgId.'/');
        }
    }

    function blockUnblockComp(){

        $this->load->model('allevents_model');

        $compEventMemId = decoding($this->uri->segment(4));
        $eventId = $this->uri->segment(5);
        $eventOrgId = $this->uri->segment(6);
        $data = $this->allevents_model->blockUnblockComp(array('compId'=>$compEventMemId));
        if($data == 1){
            redirect('admin/users/eventDetail/'.$eventId.'/'.$eventOrgId.'/');
        }else{
            redirect('admin/users/eventDetail/'.$eventId.'/'.$eventOrgId.'/');
        }
    }

    function userImages(){

        $userId = $this->input->post('user_id'); 
        $data['media'] = $this->User_model->usersImage($userId); 
        $this->load->view('userMedia',$data);
    }

    function paymentList(){

        $data['payment'] = $this->common_model->get_total_count(PAYMENT_TRANSACTIONS);
        $this->load->admin_render('paymentList',$data,'');
    }

    function get_payment_list_ajax(){

        $this->load->model('payment_model');
        $userId = isset($_POST['user_id']) ? $_POST['user_id']: '';

        $list = $this->payment_model->get_list($userId);  
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $payment) { 
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($payment->fullName)); 
            $row[] = display_placeholder_text($payment->transactionId); 
            $row[] = display_placeholder_text('$'.$payment->amount);
            if($payment->paymentStatus == 'succeeded'){
                $row[] = '<span class="statusSuccess stus">Success</span>';
            }else if($payment->paymentStatus == 'pending'){
                $row[] = '<span class="statusWaiting stus">Pending</span>';
            }else if($payment->paymentStatus == 'active'){
                $row[] = '<span class="statusSuccess stus">Subscribed</span>';
            }else{
                $row[] = '<span class="statusDanger stus">Cancel</span>';
            }
        
            if($payment->paymentType == 1){
                $row[] = 'Top user';
            }elseif($payment->paymentType == 2){
                $row[] = 'View map';
            }elseif($payment->paymentType == 3){
                $row[] = 'Event Join Payment';
            }elseif($payment->paymentType == 4){
                $row[] = 'Companion Payment';
            }elseif($payment->paymentType == 0){
                $row[] = 'MemeberShip Subscription';
            }elseif($payment->paymentType == 6){
                $row[] = 'Business Subscribed';
            }elseif($payment->paymentType == 7){
                $row[] = 'Appointment Payment';
            }
           
            $app_detail = "viewModel('admin/users','paymentDetail','".$payment->id."')";
            $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$app_detail.'"  title="View detail">'.VIEW_ICON.'</a>';

            $row[] = $action;
            $data[] = $row;           
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->payment_model->count_all($userId),
            "recordsFiltered" => $this->payment_model->count_filtered($userId),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }


    function appointmentList(){

        $data['appoinment'] = $this->common_model->get_total_count(APPOINTMENTS,array('isDelete'=>0));
        $this->load->admin_render('appointment',$data,'');
    }

    function allAppointment(){

        $this->load->model('appointment_list_model');

        $list = $this->appointment_list_model->get_list();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $app) {
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($app->ByName));

            $row[] = display_placeholder_text(date('d M Y',strtotime($app->appointDate)).', '.date('h:i A',strtotime($app->appointTime)));
            $row[] = display_placeholder_text(substr($app->appointAddress,0,20).'...'); 

            if($app->isFinish == '0'){                            

                if ($app->isCounterApply == 1) {

                    if($app->appointmentStatus == '5'){

                        $row[] = '<p class="defaultStatus">Counter rejected</p>';

                    }else {
                        
                        if ($app->counterStatus == 0) {

                            $row[] = '<p class="waitingStatus">New appointment request</p>';                                        

                        }elseif($app->counterStatus == 1){

                            $row[] = '<p class="waitingStatus">Payment is pending</p>';

                        }elseif($app->counterStatus == 2){

                            $row[] = '<p class="defaultStatus">Counter rejected</p>';

                        }elseif($app->counterStatus == 3){

                            $row[] = '<p class="confirmStatus">Appointment confirmed</p>';
                        }
                    }
                    
                }elseif($app->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete

                    $row[] = '<p class="waitingStatus">Waiting for approval</p>';

                }elseif($app->appointmentStatus == '2'){

                    if ($app->offerType == 1 ) { // 1:Paid,2:Free

                        $row[] = '<p class="waitingStatus">Payment is pending</p>';

                    }else{

                        $row[] = '<p class="confirmStatus">Appointment confirmed</p>';
                    }                

                }elseif($app->appointmentStatus == '3'){

                    $row[] = '<p class="defaultStatus">Request rejected</p>';

                }elseif($app->appointmentStatus == '4'){

                    $row[] = '<p class="confirmStatus">Appointment confirmed</p>';

                }elseif($app->appointmentStatus == '5'){

                    $row[] = '<p class="defaultStatus">Request cancelled</p>';
                }
            } else{

                $row[] = '<p class="defaultStatus">Finished appointment</p>';
            }
            
            $app_detail = "viewFn('admin/users','appDetail','".$app->appId."')";
            $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$app_detail.'"  title="View detail">'.VIEW_ICON.'</a>';

            $row[] = $action;
            $data[] = $row;
        }

        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->appointment_list_model->count_all(),
                "recordsFiltered" => $this->appointment_list_model->count_filtered(),
                "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }

    function appDetail(){

        $appId = $this->input->post('id');
        $data['appDetail'] = $this->User_model->getAppDetail($appId); 
        $this->load->view('appointment_detail',$data);
    }

    function paymentDetail(){

        $paymentId = $this->input->post('id');
        $data['payment'] = $this->User_model->getPaymentDetail($paymentId);
        $this->load->view('payment_detail',$data);
    }
}
