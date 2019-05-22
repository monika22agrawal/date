<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CommonFront {

	function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load_language_files();
    }

    function index(){

        if(isset($_GET['uId'])){
            redirect('home/Chat?userId='.encoding($_GET['uId']));
        }

        $this->check_user_session();
        $userId = $this->session->userdata('userId');

        /*$isSuscribe = $this->common_model->checkSuscription($userId);

        if($isSuscribe === TRUE){*/
            $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
            $detail['front_scripts'] = array($load_custom_js.'chat.js',$load_custom_js.'event_chat.js');
            $detail['myDetail'] = $this->common_model->usersDetail($userId);
            $this->load->front_render('chat',$detail,'');
            
        /*}else{
            $this->load->front_render('suscribe','');
        }*/
    } 

    function chatList(){
        
        $opId['opId'] = $this->input->post('opId');
        $this->load->view('chatMessage',$opId);
    }

    function chat_notification(){

        $id  = $this->input->post('id');
        $info = $this->common_model->usersDetail($id);

        if(!empty($info->deviceToken) && $info->isNotification == 1 ){

            $msg = $this->input->post('msg');
            $time = $this->input->post('time');
            $title = $this->session->userdata('fullName');            

            $body  = $msg;

            $notif_msg = array('title'=>$title, 'body'=> $body,'type'=> 'chat',

                'sender_name'=> $info->fullName,
                'message'=> $msg,
                'time'=> $time,
                'opponentChatId'=>$this->session->userdata('userId'),
                'click_action'=>'ChatActivity',
                'sound'=>'default'

            );
            $v = $this->notification_model->send_notification(array($info->deviceToken), $notif_msg);  //send andriod and ios push notification
            print_r($v);
           // return $notif_msg;  //return message array
        }        
    }

} // End Of Class