<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CommonFront {

	function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Payment_model');
        $this->load->library('Stripe');
        $this->load_language_files();
    }

    // user's payment for showing top on the list
    function paymentForShowTop(){
        
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $payment = 100;
        $token  = $this->input->post('stripeToken');
        $email = $this->session->userdata('email');
 
        if($token){

            $customer_id = $this->Payment_model->getStripeCustomerId();
        
            if($customer_id === FALSE){
                $res['status'] = 4; $res['msg'] = lang('something_wrong');
                echo json_encode($res); exit;
            }
            
            if(empty($customer_id)){
                //create customer if ID not found
                $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                
                if($stripe_res['status'] == false){
                    $res['status'] = 5; $res['msg'] = $stripe_res['message'];
                    echo json_encode($res); exit;
                }
                
                $customer_id = $stripe_res['data']->id;  //customer ID
                
                //save customer ID in our DB for future use
                $update = $this->Payment_model->saveCustomerId($customer_id);
                
                //some problem in updating customer ID
                if(!$update){
                    $res['status'] = 6; $res['msg'] = lang('something_wrong');
                    echo json_encode($res); exit;
                }
            }
            
            $result = $this->stripe->pay_by_card_id($payment,$customer_id); //pay

            if(!empty($result['data']) && $result['status'] === true){

                $data['transactionId'] = $result['data']->balance_transaction;
                $data['chargeId'] =  $result['data']->id;
                $data['paymentStatus'] =  $result['data']->status;
                $data['amount'] = $payment;
                $data['crd'] = date('Y-m-d H:i:s');
                $data['paymentType'] = 1;
                $data['user_id'] = $this->session->userdata('userId');

                //check data exist
                $where = array('user_id'=>$data['user_id'],'paymentStatus'=>'succeeded','paymentType'=>1);
                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $where);
                if(!empty($paymentExist)){
                    /*$this->session->set_flashdata('success', 'Payment is already done');
                    redirect('home/user/userProfile');*/
                    $res['status'] = 2; $res['msg'] = lang('payment_already_done');
                    echo json_encode($res); exit;
                }

                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$data);

                if($insertId){
                    $this->common_model->updateFields(USERS, array('showTopPayment'=>1),array('userId'=>$data['user_id']));
                    $this->load->model('Login_model');
                    $this->Login_model->session_create($data['user_id']);

                    $res['status'] = 1; 
                    $res['msg'] = lang('payment_done');
                    $res['url'] = base_url('home/user/userProfile');
                    echo json_encode($res); exit;
                }
                
            }else{

                $res['status'] = 3; $res['msg'] = $result['message'];
                echo json_encode($res); exit;  //fail
            }
        }else{
            
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
            //redirect('home/user/userProfile');
        }   
    }

    // user's payment for creating appointment 
    function viewOnMapPayment(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }
       
        $payment = 100;
        $token  = $this->input->post('stripeToken');
        $pageType  = $this->input->post('pageType');
        $email = $this->session->userdata('email');

        $URL = base_url('home');

        if($pageType == 1){
            $URL = base_url('home/user/userProfile');
        }elseif($pageType == 2){
            $URL = base_url('home/nearByYou/2');
        }

        $resu = $this->stripe->save_card_id($token);
 
        if($token){

            $customer_id = $this->Payment_model->getStripeCustomerId();
        
            if($customer_id === FALSE){
                $res['status'] = 4; $res['msg'] = lang('something_wrong');
                echo json_encode($res); exit;
            }
            
            if(empty($customer_id)){
                //create customer if ID not found
                $stripe_res = $this->stripe->save_card_id($email, $token); //create a customer
                
                if($stripe_res['status'] == false){
                    $res['status'] = 5; $res['msg'] = $stripe_res['message'];
                    echo json_encode($res); exit;
                }
                
                $customer_id = $stripe_res['data']->id;  //customer ID
                
                //save customer ID in our DB for future use
                $update = $this->Payment_model->saveCustomerId($customer_id);
                
                //some problem in updating customer ID
                if(!$update){
                    $res['status'] = 6; $res['msg'] = lang('something_wrong');
                    echo json_encode($res); exit;
                }
            }
            
            $result = $this->stripe->pay_by_card_id($payment,$customer_id); //pay
            
            if(!empty($result['data']) && $result['status'] === true){

                $data['transactionId'] = $result['data']->balance_transaction;
                $data['chargeId'] =  $result['data']->id;
                $data['paymentStatus'] =  $result['data']->status;
                $data['amount'] = $payment;
                $data['crd'] = date('Y-m-d H:i:s');
                $data['paymentType'] = 2;
                $data['user_id'] = $this->session->userdata('userId');

                //check data exist
                $where = array('user_id'=>$data['user_id'],'paymentStatus'=>'succeeded','paymentType'=>2);
                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $where);

                if(!empty($paymentExist)){
                    
                    $res['status'] = 2; $res['msg'] = lang('payment_already_done');
                    echo json_encode($res); exit;
                }

                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$data);

                if($insertId){
                    $this->common_model->updateFields(USERS, array('mapPayment'=>1),array('userId'=>$data['user_id']));
                    $this->load->model('Login_model');
                    $this->Login_model->session_create($data['user_id']);

                    $res['status'] = 1; 
                    $res['msg'] = lang('payment_done');
                    $res['url'] = $URL;
                    echo json_encode($res); exit;
                }
            
            }else{

                $res['status'] = 3; $res['msg'] = $result['message'];
                echo json_encode($res); exit;  //fail
            }
        }else{
            
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
            //redirect('home/user/userProfile');
        }   
    } // end of function

    function bankAccount(){

        $this->check_user_session();
        $userId = $this->session->userdata('userId');
        $data_val['front_scripts'] = array();
        $data_val['bankDetail'] = $this->common_model->getBankAccountDetail($userId);
        $this->load->front_render('bank_account',$data_val,'');
    }

    // add and update event organiser's bank account while creating paid event
    function addBankAccount(){

        //check for auth
        $this->check_user_session();

        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('firstName',lang('first_name'),'trim|required');
        $this->form_validation->set_rules('lastName',lang('last_name'),'trim|required');
        /*$this->form_validation->set_rules('routingNumber',lang('routing_number'),'trim|required');*/   
        /*$this->form_validation->set_rules('accountNumber',lang('acc_number'),'trim|required');*/     
        $this->form_validation->set_rules('accountNumber',lang('iban_number'),'trim|required');     
        /*$this->form_validation->set_rules('postalCode',lang('postal_code'),'trim|required');     
        $this->form_validation->set_rules('ssnLast',lang('ssn_last'),'trim|required'); */      
        $this->form_validation->set_rules('dob',lang('date_of_birth'),'trim|required');       
      
        if($this->form_validation->run() == FALSE){
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds , 'url' => base_url('home/event/updateEvent'));
        } else {
            $userId = $this->session->userdata('userId');
            
            $date = date('Y-m-d H:i:s');
            
            $eventData = array();

            $holderName = $this->input->post('firstName')." ".$this->input->post('lastName');
            //$routingNumber = $this->input->post('routingNumber');
            $accountNumber = $this->input->post('accountNumber');
            /*$postalCode = $this->input->post('postalCode');
            $ssnLast = $this->input->post('ssnLast');*/
            $dob = date('Y-m-d',strtotime($this->input->post('dob')));
            $country = 'ES';
            $currency = 'EUR'; 
            /*$country = 'US';
            $currency = 'USD'; */           
            $routingNumber = '';
            $ssnLast = '';
            $postalCode = '';
            $token = $this->stripe->create_custom_account($holderName,$dob,$country,$currency,$routingNumber,$accountNumber,$ssnLast,$postalCode);

            if(!empty($token['data']) && $token['status'] === true){

                $data['firstName'] = $this->input->post('firstName');
                $data['lastName'] = $this->input->post('lastName');
                //$data['routingNumber'] =  $routingNumber;
                $data['accountNumber'] =  $accountNumber;
                //$data['postalCode'] = $postalCode;
               // $data['ssnLast'] = $ssnLast;
                /*$data['country'] = $country;
                $data['currency'] = $currency;*/
                $data['crd'] = $date;
                $data['user_id'] = $userId;
                $data['accountId'] = $token['data']->id;
                $data['dob'] = $dob;
                $this->load->model('Login_model');
                //check data exist
                $where = array('user_id'=>$userId);
                $paymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, $where);
                
                if(!empty($paymentExist)){
                    $data['upd'] = $date;
                    $insertId = $this->common_model->updateFields(BANK_ACCOUNT_DETAILS,$data,array('user_id'=>$userId));
                    
                    $this->Login_model->session_create($data['user_id']);
                    $response = array('status' => 1, 'msg' => lang('bank_detail_updated'), 'url'=>base_url('home/user/userProfile'));
                }else{

                    $insertId = $this->common_model->insertData(BANK_ACCOUNT_DETAILS,$data);
                    $this->Login_model->session_create($data['user_id']);
                    $response = array('status' => 2, 'msg' => lang('bank_detail_added'), 'url'=>base_url('home/user/userProfile'));
                }

            } else{
                $response = array('status' => 3, 'msg' => $token['message']);
            }
        }
        echo json_encode($response);

    } // end of function

} // End Of Class