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

    function get_user_list_ajax(){

        $this->load->model('Tablelist');
        $list = $this->Tablelist->get_list();
        $data = array();
        $no = $_POST['start']; 
        foreach ($list as $user) { 
       
            $action ='';
        $no++;
        $row = array();
        $row[] = $no;
        $row[] = display_placeholder_text(wordwrap($user->fullName,37,"<br>\n")); 
        $row[] = display_placeholder_text(wordwrap($user->email,37,"<br>\n")); 
        $row[] = display_placeholder_text($user->countryCode.'-'.$user->contactNo); 
        
        if($user->status == 1) { $row[] =  '<p class="text-success">Active</p>'; } else { $row[] =  '<p  class="text-danger">Inactive</p>'; }

        $img = (!empty($user->image))? (filter_var($user->image, FILTER_VALIDATE_URL))? $user->image : AWS_CDN_USER_IMG_PATH.$user->image : AWS_CDN_USER_PLACEHOLDER_IMG;

        $row[] = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

        $clk_event = "statusFnu('".'users'."','userId','".$user->userId."','".$user->status."')";

        if($user->status == 1){ $title = 'Inactive user'; $icon = INACTIVE_ICON; } else{ $title = 'Active user'; $icon = ACTIVE_ICON; }

        $action = '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$clk_event.'"  title="'.$title.'">'.$icon.'</a>';
    


        $link = base_url('admin/users/profile/').encoding($user->userId).'/';
                $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
              
        $row[] = $action;
        $data[] = $row;

        //$_POST['draw']='';
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
            $data = array('status'=>0,'message'=>'User inactivated ');
        }
        echo json_encode($data);
    }


    function profile(){
  
        $userId = decoding($this->uri->segment(4));
        $data['userId'] = $userId;
        $data['detail'] = $this->common_model->usersDetail($userId);
        $data['payment'] =  $this->common_model->paymentDetail($userId);
        $this->load->admin_render('userDetail',$data);
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
            $img = (!empty($user->userImg))? (filter_var($user->userImg, FILTER_VALIDATE_URL))? $user->userImg : AWS_CDN_USER_IMG_PATH.$user->userImg : AWS_CDN_USER_PLACEHOLDER_IMG;

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
            
        
            $link = base_url('admin/users/eventDetail/').encoding($event->eventId).'/'.encoding($event->eventOrganizer).'/';
            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
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
  
        $data['eventId'] = decoding($this->uri->segment(4)); 
        $data['userId'] = decoding($this->uri->segment(5));
        $data['eventDetail'] = $this->common_model->myEventDetail($data); 
        $data['admin_scripts'] = array('/joinedMember.js','/invitedMember.js');
        $this->load->admin_render('eventDetail',$data);
    }
 
    function joinedMemberList(){
       
        $data['userId'] = $this->input->post('userId') ; 
        $data['eventId'] = $this->input->post('eventId') ; 
        $data['type'] = $this->input->post('type') ; 
        $data['offset'] = $this->input->post('offset');
        $data['limit'] = $this->input->post('limit');

        $data['total_count'] = $this->common_model->countAllJoinedMember($data);
       
        $data['joined_member'] = $this->common_model->joinedMemberCount($data);

        $this->load->view('joinedMemeber_list',$data);
    }


    function invitedMemberList(){
       
        $data['userId'] = $this->input->post('userId') ; 
        $data['eventId'] = $this->input->post('eventId') ; 
        $data['offset'] = $this->input->post('offset');
        $data['limit'] = $this->input->post('limit'); 

        if($data['offset']  == 0){
            $data['total_count'] = $this->common_model->countAllInvitedMember($data); 
        }
       
        $data['invited_member'] = $this->common_model->invitedMemberCount($data); 
      
        $this->load->view('invitedMember',$data);
    }

 /*   function appointment_list_ajax(){

        $userId = $_POST['user_id']; 

        $where    = array('a.appointById'=>$userId);
        $or_where = array('a.appointForId'=>$userId);
        $this->load->model('appointment_list_model');
        $this->appointment_list_model->set_data($where,$or_where);
        $list = $this->appointment_list_model->get_list();  
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $app) {
            
            $action ='';
            $no++;
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($app->ForName));
            $row[] = display_placeholder_text(substr($app->appointAddress,0,20)); 
            //$row[] = display_placeholder_text(substr($app->ForAddress,0,15)); 
            $img = (!empty($app->forImage))? (filter_var($app->forImage, FILTER_VALIDATE_URL))? $app->forImage : base_url().AWS_CDN_USER_IMG_PATH.$app->forImage : AWS_CDN_USER_PLACEHOLDER_IMG;

            $row[] = '<img class="img-circle" style="height:70px; width:70px;" src="'.$img.'" />';


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
    }*/


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
            
            $img = (!empty($frnd->profileImage))? (filter_var($frnd->profileImage, FILTER_VALIDATE_URL))? $frnd->profileImage : base_url().AWS_CDN_USER_IMG_PATH.$frnd->profileImage : AWS_CDN_USER_PLACEHOLDER_IMG;

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
            $row = array();
            $row[] = $no;
            
            $row[] = display_placeholder_text(ucfirst($event->eventName)); 
            $row[] = display_placeholder_text(substr($event->eventPlace,0,15)); 
            $row[] = display_placeholder_text(ucfirst($event->fullName));

            $img = (!empty($event->profileImage))? (filter_var($event->profileImage, FILTER_VALIDATE_URL))? $event->profileImage : base_url().AWS_CDN_USER_IMG_PATH.$event->profileImage : AWS_CDN_USER_PLACEHOLDER_IMG;

            $row[] = '<img class="img-circle" height="60px" width="60px" src="'.$img.'" />';

            $link = base_url('admin/users/eventDetail/').encoding($event->eventId).'/'.encoding($event->eventOrganizer).'/';


           //$link = base_url('admin/users/profile/').encoding($event->eventOrganizer).'/';
            $action .= '<a href="'.$link.'"  class="on-default edit-row table_action" title="View event detail">'.VIEW_ICON.'</a>';

            $row[] = $action;
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

    function userImages(){

        $userId = $this->input->post('user_id'); 
        $data['media'] = $this->common_model->usersImage($userId); 
        $this->load->view('userMedia',$data);

    }

    function paymentList(){
        $data['payment'] = $this->common_model->get_total_count(PAYMENT_TRANSACTIONS);
        $this->load->admin_render('paymentList',$data,'');
    }

    function get_payment_list_ajax(){

        $this->load->model('payment_model');
        $list = $this->payment_model->get_list();  
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
            }
           
           $app_detail = "viewModel('admin/users','paymentDetail','".$payment->id."')";
            $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$app_detail.'"  title="View detail">'.VIEW_ICON.'</a>';

            $row[] = $action;
            $data[] = $row;
           
        }

        $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->payment_model->count_all(),
                    "recordsFiltered" => $this->payment_model->count_filtered(),
                    "data" => $data,
        );
        //output to json format
       echo json_encode($output);
    }


    function appointmentList(){
         $data['appoinment'] = $this->common_model->get_total_count(APPOINTMENTS);
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
                if($app->appointByStatus == '1'){ 
                    $row[] =  '<p class="waitingStatus">Waiting for approval</p>';
                }elseif($app->appointByStatus == '2'){
                    $row[] =  '<p class="confirmStatus">Confirmed appointment</p>';
                }
            }else{
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
