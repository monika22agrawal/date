<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_model extends CI_Model {

    // Check  Contact is exist or not
	function checkCNO($contactNo){

        $isExist = $this->db->get_where(USERS,array('contactNo'=>$contactNo))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function

     // Verify contact number using OTP
	function verifyNo($data){

        $sql = $this->db->select('userId')->from(USERS)->where(array('contactNo'=>$data['contactNo']))->get();
     
        if($sql->num_rows()){
                
            return array('status'=>0,'msg'=>lang('number_already'));        
           
        }else{

           	$message = lang('sent_mobile_otp')' : '.$data['OTP'];

	        $this->load->library('twilio');

	        $from   = '+34931071610';
	        $to     = $data['countryCode'].$data['contactNo'];
	        
	        $response = $this->twilio->sms($from, $to, $message);
	        if($response->IsError){
                //return  array('status'=>0,'msg'=>$response->ErrorMessage);
	            return  array('status'=>0,'msg'=>lang('twilio_common_msg'));
	        }else{
	            return  array('status'=>1,'msg'=>lang('mobile_otp'),'otp'=>$message); 
	        }
	        
	        return  array('status'=>0,'msg'=>lang('something_wrong'));
        }

    } //Enf Function

} //End of Class