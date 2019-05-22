<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {


    // Generate random password
    function random_password( $length = 8 ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;

    } //Enf Function

    // Function to get the client IP address
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    // Get current city name from IP address
    function getCityNameByIpAddress($ipaddress){
           
        $url = "http://api.ipstack.com/".$ipaddress.'?access_key=9db2579bfcd6ae007379db8459b8c0e4';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ipdata = curl_exec($ch);
        $result = json_decode($ipdata,true);       
        if(!empty($result)){           
            return $result;
        }else{
            return 0;
        }
    
    } //Enf Function

    // Get latlong from address using curl
    function getLatLong($address){

        if(!empty($address)){
           
            $formattedAddr = str_replace(' ','+',$address);

            $url = 'http://maps.google.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($response);

            if(isset($output->results[0]->geometry->location->lat)){

                $data['latitude']  = $output->results[0]->geometry->location->lat; 
                $data['longitude'] = $output->results[0]->geometry->location->lng;

                if(!empty($data)){

                    return $data;

                }else{
                    return false;
                }
            }else{
                return false;   
            }
        }else{
            return false;   
        }

    } //Enf Function

    // Check email is exist or not
    function checkEmail($email){

        $isExist = $this->db->get_where(USERS,array('email'=>$email))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }
    } //Enf Function


    // Verify contact number using OTP
	function verifyEmail($data){

        $sql = $this->db->select('userId')->from(USERS)->where(array('email'=>$data['email']))->get();
     
        if($sql->num_rows()){
                
            return array('status'=>0,'error'=>'Email is already exist.');        
           
        }else{

            // load email library
            $this->load->library('smtp_email');

            $code = $data['code'];
            
            //set masssage and subject for mail
            
            $subject = "Apoim - New account email verification";  

            $userData['code'] = $code;
            
            $message  = $this->load->view('email/email_verification',$userData,TRUE);                   
            
            //send mail
            $isSend = $this->smtp_email->send_mail($data['email'],$subject,$message);

            if($isSend == TRUE){
            
                return  array('status'=>1,'otp'=>$code);

            }else{
                return  array('status'=>2,'error'=>'Something went wrong,email is not sent');
            }
            
            return  array('status'=>0,'error'=>'Somting going wrong');
        }

    } //Enf Function

    /*function checkSocial($data){

        $res = $this->db->select('userId')->where(array('email'=>$data['email']))->get(USERS);

        if($res->num_rows() == 0)
        {
            $check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);

            if($check->num_rows() > 0)
            {
                $userId = $check->row();

                if($userId->status == 1){
                   
                    $this->session_create($userId->userId); 
                    return "SL";
                    
                }else{
                    return "NA"; // not active
                }

            } else{
                           
                return "SR";                
            }

        }else{
            if(!empty($data['socialId']) && !empty($data['socialType']))
            {
                $check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);

                if($check->num_rows() > 0)
                {
                    $userId = $check->row();  
                    if($userId->status == 1){          
                               
                        $this->session_create($userId->userId); 
                        return "SL";     
                    }else{
                        return "NA"; // not active
                    }                
                } else{
                    return 'AE';
                }
            }
        }
    }*/

    function checkSocial($data){

        $res = $this->db->select('userId')->where(array('email'=>$data['email']))->get(USERS);

        if($res->num_rows() == 0)
        {
            $check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);

            if($check->num_rows() > 0)
            {
                $userId = $check->row();

                if($userId->status == 1){
                   
                    $this->session_create($userId->userId); 
                    return "SL";  // social login
                    
                }else{
                    return "NA"; // not active
                }

            } else{
                           
                return "SR";     // social registration
            }

        }else{

            if(!empty($data['socialId']) && !empty($data['socialType']))
            {
                $check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);

                if($check->num_rows() > 0)
                {
                    $userId = $check->row();
                    if($userId->status == 1){          
                               
                        $this->session_create($userId->userId); 
                        return "SL";     // social login

                    }else{
                        return "NA";     // not active
                    }

                } else{
                    $this->session_create($res->row()->userId); 
                    return "SL";     // social login
                }
            }
        }
    }

    // Insert user's data in database
    function userRegister($data,$userImgData){

        //inser data in user table
        $this->db->insert(USERS,$data);
        $userId = $this->db->insert_id();

        //check data inserted yes or not
        if(empty($userId)){

            return array('status'=>0,'msg'=>"Something went wrong,Please try again.");
        }

        if(!empty($userImgData['image'])){
            $userImgData['user_id'] = $userId;
            $this->db->insert(USERS_IMAGE,$userImgData);
        }     

        if(empty($data['socialId']) && empty($data['socialType'])){
            $this->session_create($userId);
            return array('status'=>1,'msg'=>"NR");

        }elseif(!empty($data['socialId']) && !empty($data['socialType'])){

            $this->session_create($userId); 
            return array('status'=>1,'msg'=>"SR");
        }            
        
    } //Enf Function


    function login($userData) {

        $sql = $this->db->select('userId,password,status,socialType')->where(array('email' =>$userData['email']))->get('users');

        if($sql->num_rows() > 0){
            
            $user = $sql->row(); 
            $status = $user->status;

            if($status == '1'){

                if(!empty($user->password)){

                    if(password_verify($userData['password'],$user->password)){

                        $this->common_model->updateFields(USERS, array('deviceType'=>3),array('userId'=>$user->userId));
                        $this->session_create($user->userId);
                        return 'LS'; // Login successfully

                    }else{
                        return "IP"; // Invalid password
                    }

                }else{

                    return array('data'=>$user->socialType, 'type' => 'SL'); // social login
                }

            }else{

                return 'IU'; // Inactive User
            }
            
        } else{

            return "IC"; // Invalid credential
        }

    } //End Function


    // Create session for checking user login or not
    function session_create($lastId){

        $sql = $this->db->select('*')->where(array('userId'=>$lastId))->get(USERS);
        if($sql->num_rows()):
            $user= $sql->row();
            $sessionData = array(
                'email'           => $user->email,
                'fullName'        => $user->fullName,
                'status'          => $user->status,
                'userId'          => $user->userId,
                'mapPayment'      => $user->mapPayment,
                'showTopPayment'  => $user->showTopPayment,
                'subscriptionId'  => $user->subscriptionId,
                'authToken'       => $user->authToken,
                'front_login'     => true
            );

            //check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, array('user_id'=>$user->userId));
            $sessionData['bankAccountStatus'] = 0;
            if(!empty($userPaymentExist)){
                $sessionData['bankAccountStatus'] = 1;
            }

            $this->session->set_userdata($sessionData);
            return true;
        endif;
        return false;

    }//ENdFunction

    //forgot password
    function forgotPassword($email){
         
        $this->load->library('smtp_email');
        $sql = $this->db->select('userId,fullName,email,password,socialType')->where(array('email'=>$email))->get(USERS);
        if($sql->num_rows()){
            $result = $sql->row();
             //if(empty($result->socialType)){
                $to= $result->email;

                //$random = substr(md5(mt_rand()), 0, 10);
                $random = '12345678';

                $new_password = password_hash($random, PASSWORD_DEFAULT);

                $this->db->set('password',$new_password)->where('userId',$result->userId)->update(USERS);

                $data['fullName'] = $result->fullName;
                $data['password'] = $random;

                $subject = " Forgot Password";

                $message  = $this->load->view('email/forgot_password',$data,TRUE);
                
                $check =    $this->smtp_email->send_mail($to,$subject,$message);
                if($check){
                    return array('type'=>'SS','socialType'=>''); // successfull send
                }
            /*}else{
                return array('type'=>'SR','socialType'=>$result->socialType); // registerd with social
            }*/

        }else{
            return array('type'=>'NE','socialType'=>''); // Email not exist 
        }
    } //end function  
}