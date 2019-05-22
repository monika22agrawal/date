<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommonServiceV2 extends REST_Controller{
    
    public function __construct(){
        
        parent::__construct();
        $this->load->model('service_model');
        
        //$this->load->model('notification_model'); //load push notification model
        $language_array = array('english','spanish');//language array
        $this->appLang = 'english'; //set default langauge
        $header = $this->input->request_headers();//get header values
        $lang_key = '';//set key
        //check for language key exist in header array or not
        if(array_key_exists ( 'language' , $header )){
            $lang_key = 'language';
        } elseif(array_key_exists ( 'Language' , $header )){
            $lang_key = 'Language';
        }

        if(!empty($lang_key)){//if language key not empty get language from header

            $lang_val = $header[$lang_key];//get header language 

            if(in_array($lang_val,$language_array )){//check if header langauge in array set in varaible
                $this->appLang = $lang_val;
            }
        }

        if($this->appLang == 'spanish'){
            $this->config->set_item('language', $this->appLang);
        }
       
        //load response language files for selected language
        $this->lang->load('response_messages_lang', $this->appLang);  
        $this->lang->load('home_message_lang', $this->appLang);
        $this->load->helper('responsemessages_helper');
    }
    
    //check auth token of request
    public function check_service_auth(){
        /*Authtoken*/
        $this->authData = '';
        $header = $this->input->request_headers();
        
        //check if key exist as different server may have different types of key (case sensitive) 
        if(array_key_exists ( 'authToken' , $header )){
            $key = 'authToken';
        }
        elseif(array_key_exists ( 'Authtoken' , $header )){
            $key = 'Authtoken';
        }
        elseif(array_key_exists ( 'AuthToken' , $header )){
            $key = 'AuthToken';
        }
        else{
            $this->response($this->token_error_msg(), SERVER_ERROR); //authetication failed 
        }
       
        $authToken = isset($header[$key]) ? $header[$key] : '';
        $userAuthData =  !empty($authToken) ? $this->service_model->isValidToken($authToken) : '';
        

        if(empty($userAuthData)){ 
            $this->response($this->token_error_msg(2), SERVER_ERROR); //authetication failed 
        }

        if($userAuthData->status != 1)
        {
            $this->response($this->token_error_msg(1), SERVER_ERROR); //authetication failed, user is inactive 
        } 

            $this->authData = $userAuthData; 
            return TRUE;
    }

    //show auth token error message
    public function token_error_msg($inactive_status=1){

        $ar = array('message'=>ResponseMessages::getStatusCodeMessage(101),'authToken'=>'','responseCode'=>300, 'isActive'=>1);

        if($inactive_status==1){
            $ar['isActive'] = 0;//user inactive
        }
        //return $ar;
        $this->response($ar, SERVER_ERROR); //authetication failed, user is inactive 
    }
    
    //send push notifications
    public function send_push_notification($token_arr, $title, $body, $reference_id, $type){
        if(empty($token_arr)){
            return false;
        }
        //prepare notification message array
        $notif_msg = array('title'=>$title, 'body'=> $body, 'reference_id'=>$reference_id, 'type'=> $type, 'click_action'=>'ChatActivity', 'sound'=>'default');
        $this->notification_model->send_notification($token_arr, $notif_msg);  //send andriod and ios push notification
        return $notif_msg;  //return message array
    }

    public function generate_response($data = array(), $header = array()){
      //header('X-Response-Code: ' . $code);        
        if(count($header) > 0){
            foreach($header as $key => $value){
            header($key. ' : ' . $value);
            }
        }
        $response = array();
        if(count($data)){
            $response = $data;
        }
        return $response;
    }
    
    /*//show auth token error message
    public function token_error_msg(){
        return array( 'message'=>ResponseMessages::getStatusCodeMessage(101),'authToken'=>'','responseCode'=>300);
    }*/

}//End Class