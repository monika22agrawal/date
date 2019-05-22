<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

//Handles notifications for ios and andriod devices
class Notification_model extends CI_Model {

    public function __construct() {
        parent::__construct();  
        $this->notify_log = FCPATH.'notification_log.txt';    //notifcation file path
    }
    
    /* Firebase notification for Andriod and ios */
    function send_notification($registrationIds, $notificationMsg){   

        $msg = $notificationMsg;
        $fields = array(
            'registration_ids' 	=> $registrationIds,  //firebase token array
            'data'		=> $msg ,  //msg for andriod
            'notification'      => $msg   //msg for ios
        );

        $headers = array(
            'Authorization: key=' . NOTIFICATION_KEY, //firebase API key
            'Content-Type: application/json'
        );
        
        //curl request
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );  //firebase end url
        //curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        log_event($result, $this->notify_log);  //create log of notifcation
        return $result;
    }
    
    //store notifcation in DB
    function save_notification($table, $data, $where){

        if($where['notificationType'] == 'add_favorite' || $where['notificationType'] == 'add_like' || $where['notificationType'] == 'friend_request' || $where['notificationType'] == 'accept_request' || $where['notificationType'] == 'profile_img_like' ){
            $this->common_model->deleteData($table, $where);
        }
        //check for order id is exist
        /*$exist = $this->common_model->is_data_exists($table, $where);
        if($exist){

            $this->common_model->deleteData($table, $where);
            return $this->common_model->insertData($table, $data);
        }else{

            return $this->common_model->insertData($table, $data);
        }*/
        return $this->common_model->insertData($table, $data);
    }
    //send push notifications
    public function send_push_notification($token_arr, $title, $body, $reference_id, $type, $orgniserId = ''){
        if(empty($token_arr)){
            return false;
        }
        //prepare notification message array
        $notif_msg = array('title'=>$title, 'body'=> $body, 'reference_id'=>$reference_id, 'type'=> $type, 'click_action'=>'ChatActivity', 'sound'=>'default', 'createrId'=>$orgniserId);
        
        $this->send_notification($token_arr, $notif_msg); //send andriod and ios push notification
        return $notif_msg; //return message array
    }

    //send push notifications
    public function send_push_notification_for_event($token_arr, $title, $body, $reference_id, $compId='',$eventMemId='', $type, $orgniserId = ''){
        if(empty($token_arr)){
            return false;
        }
        //prepare notification message array
        $notif_msg = array('title'=>$title, 'body'=> $body, 'reference_id'=>$reference_id, 'type'=> $type, 'click_action'=>'ChatActivity', 'sound'=>'default', 'createrId'=>$orgniserId,'compId'=>$compId,'eventMemId'=>$eventMemId);
        
        $this->send_notification($token_arr, $notif_msg); //send andriod and ios push notification
        return $notif_msg; //return message array
    }
    
}
