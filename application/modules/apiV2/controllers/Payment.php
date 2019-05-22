<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CommonServiceV2 {

	function __construct() {
        parent::__construct();
        $this->load->library('Stripe');
        $this->load->model('Payment_model');
    }

    // user's payment for showing top on the list
    function paymentForShowTopList_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');        
      
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId = $this->authData->userId;
            $email = $this->authData->email;            
            $date = date('Y-m-d H:i:s');            
            $eventData = array();
            $token      = $this->post('token');
            $payment = 100;            
    
 
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
            }else{
                //update stripe token 
                $updataData['description'] = $email;
                $updataData['source']      = $token;
                $stripe_res_upd = $this->stripe->update_customer($customer_id,$updataData);
            }

            $result = $this->stripe->pay_by_card_id($payment,$customer_id);//pay
           
            if(!empty($result['data']) && $result['status'] === true){

                $data['transactionId'] = $result['data']->balance_transaction;
                $data['chargeId'] =  $result['data']->id;
                $data['paymentStatus'] =  $result['data']->status;
                $data['amount'] = $payment;
                $data['crd'] = date('Y-m-d H:i:s');
                $data['paymentType'] = 1;
                $data['user_id'] = $userId;

                //check data exist
                $where = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>1);
                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $where);
                if(!empty($paymentExist)){
                    $response = array('status' => FAIL, 'message' => 'Payment is already done');
                    $this->response($response);
                }
                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$data);

                if($insertId){
                    $this->common_model->updateFields(USERS, array('showTopPayment'=>1),array('userId'=>$data['user_id']));
                }
                
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(176));
            }else{

                $response = array('status' => FAIL, 'message' => $result['message']);
            }
                
            $this->response($response);
        }
    } // end of function

    // user's payment for creating appointment
    function viewOnMapPayment_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');     
      
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {
            
            $userId = $this->authData->userId;
            $email = $this->authData->email;
            
            $date = date('Y-m-d H:i:s');
            
            $eventData = array();

            $token = $this->post('token');
            $payment = 100;
                            
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
            }else{
                //update stripe token 
                $updataData['description'] = $email;
                $updataData['source']      = $token;
                $stripe_res_upd = $this->stripe->update_customer($customer_id,$updataData);
            } 
                
            $result = $this->stripe->pay_by_card_id($payment,$customer_id);//pay
           
            if(!empty($result['data']) && $result['status'] === true){

                $data['transactionId'] = $result['data']->balance_transaction;
                $data['chargeId'] =  $result['data']->id;
                $data['paymentStatus'] =  $result['data']->status;
                $data['amount'] = $payment;
                $data['crd'] = date('Y-m-d H:i:s');
                $data['paymentType'] = 2;
                $data['user_id'] = $userId;

                //check data exist
                $where = array('user_id'=>$userId,'paymentStatus'=>'succeeded','paymentType'=>2);
                $paymentExist = $this->common_model->is_data_exists(PAYMENT_TRANSACTIONS, $where);
                if(!empty($paymentExist)){
                    $response = array('status' => FAIL, 'message' => 'Payment is already done');
                    $this->response($response);
                }
                $insertId = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$data);

                if($insertId){
                    $this->common_model->updateFields(USERS,array('mapPayment'=>1),array('userId'=>$data['user_id']));
                }
                
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(176));
            }else{

                $response = array('status' => FAIL, 'message' => $result['message']);
            }

            $this->response($response);
        }
    } // end of function


    // add and update event organiser's bank account while creating paid event
    function addBankAccount_post(){
 
        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('firstName',lang('first_name'),'trim|required');
        $this->form_validation->set_rules('lastName',lang('last_name'),'trim|required');
        $this->form_validation->set_rules('routingNumber',lang('routing_number'),'trim|required');   
        $this->form_validation->set_rules('accountNumber',lang('acc_number'),'trim|required');     
        $this->form_validation->set_rules('postalCode',lang('postal_code'),'trim|required');     
        $this->form_validation->set_rules('ssnLast',lang('ssn_last'),'trim|required');       
        $this->form_validation->set_rules('dob',lang('date_of_birth'),'trim|required');       
      
        if($this->form_validation->run() == FALSE){
            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);
        } else {
            $userId = $this->authData->userId;
            
            $date = date('Y-m-d H:i:s');
            
            $eventData = array();

            $holderName = $this->post('firstName')." ".$this->post('lastName');
            $routingNumber = $this->post('routingNumber');
            $accountNumber = $this->post('accountNumber');
            $postalCode = $this->post('postalCode');
            $ssnLast = $this->post('ssnLast');
            $dob = date('Y-m-d',strtotime($this->post('dob')));
            $country = 'US';
            $currency = 'USD';
            
            $token = $this->stripe->create_custom_account($holderName,$dob,$country,$currency,$routingNumber,$accountNumber,$ssnLast,$postalCode);

            if(!empty($token['data']) && $token['status'] === true){

                $data['firstName'] = $this->input->post('firstName');
                $data['lastName'] = $this->input->post('lastName');
                $data['routingNumber'] =  $routingNumber;
                $data['accountNumber'] =  $accountNumber;
                $data['postalCode'] = $postalCode;
                $data['ssnLast'] = $ssnLast;
                /*$data['country'] = $country;
                $data['currency'] = $currency;*/
                $data['crd'] = $date;
                $data['user_id'] = $userId;
                $data['accountId'] = $token['data']->id;
                $data['dob'] = $dob;

                //check data exist
                $where = array('user_id'=>$userId);
                $paymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, $where);
                if(!empty($paymentExist)){
                    $data['upd'] = $date;
                    $insertId = $this->common_model->updateFields(BANK_ACCOUNT_DETAILS,$data,array('user_id'=>$userId));
                    $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(178));
                }else{
                    $insertId = $this->common_model->insertData(BANK_ACCOUNT_DETAILS,$data);
                    $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(177)); 
                }

            } else{
                $response = array('status' => FAIL, 'message' => $token['message']);
            }
            $this->response($response);
        }
    } // end of function

    //process stripe card payment- Create customer, charge customer and make subscription
    function subsPaymentProcess_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->load->library('form_validation');
                 
        $this->form_validation->set_rules('token',lang('stripe_token'),'trim|required');     
      
        if($this->form_validation->run() == FALSE){

            $response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
            $this->response($response);

        } else {

            $userId = $this->authData->userId;
            $email = $this->authData->email;
            $token = $this->post('token');

            $where = array('userId'=>$userId,'subscriptionId !=' => '');
            $subsExist = $this->common_model->is_data_exists(USERS, $where);
            if(!empty($subsExist)){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(179));
                $this->response($response);
            }
            
            //get user stripe customer ID
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
            }else{
                //update stripe token 
                $updataData['description'] = $email;
                $updataData['source']      = $token;
                $stripe_res_upd = $this->stripe->update_customer($customer_id,$updataData);
            }
            
            //to get plan details
            $planDetail = $this->stripe->get_plan(STRIPE_PLAN_ID);
           
            //subscribe customer to a plan
            $subscription = $this->stripe->create_subscription($customer_id, STRIPE_PLAN_ID);
          
            if($subscription['status'] == false){
                $response = array('status' => FAIL, 'message' => $subscription['message']);
                $this->response($response);
            }
            
            $subscriptionId = $subscription['data']->id;  //customer ID
            
            // to save data in user table
            $userData['subscriptionId'] = $subscriptionId;
            $userData['subscriptionStatus'] = ($subscription['data']['status'] == 'active') ? 1 : 0;
            $update_sub = $this->common_model->updateFields(USERS,$userData,array('userId'=>$userId));

            //some problem in updating customer ID
            if(!$update_sub){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }
            
            //save payment data in DB
            $payment_data['chargeId'] = $subscriptionId;
            $payment_data['user_id'] = $userId;
            $payment_data['amount'] = $planDetail['data']['amount'];
            $payment_data['paymentStatus'] = $subscription['data']['status'];
            $payment_data['crd'] = datetime();

            $paymentId = $this->Payment_model->savePaymentDetails($payment_data);
            
            //some problem in updating payment info
            if(!$paymentId){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }
            $subDetail['subscriptionId'] = $subscriptionId;
            $subDetail['subscriptionStatus'] = $userData['subscriptionStatus'];
            $subDetail['customerId'] = $customer_id;

            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(180),'subscriptionDetail'=>$subDetail);
            $this->response($response);  //success
        }
        
    } // End Of Function

    // to cancel subscription
    function cancelSubscription_post(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $userId = $this->authData->userId;
        $subsId = $this->Payment_model->getSubscriptionId();

        if($subsId === FALSE){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            $this->response($response);
        }     

        $subsDetail = $this->stripe->cancel_subscription($subsId,TRUE);
       
        if(!empty($subsDetail['data']) && $subsDetail['data']['cancel_at_period_end'] == true){

            $isCancel = $this->common_model->updateFields(USERS,array('subscriptionStatus'=>0),array('userId'=>$userId));

            //some problem in updating payment info
            if(!$isCancel){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }

            $getUserDetail = $this->common_model->getsingle(USERS,array('userId'=>$userId));

            $subDetail['subscriptionId'] = $getUserDetail->subscriptionId;
            $subDetail['subscriptionStatus'] = $getUserDetail->subscriptionStatus;
            $subDetail['customerId'] = $getUserDetail->stripeCustomerId;

            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(181),'subscriptionDetail'=>$subDetail);
            $this->response($response); //success
        }

    } // End Of Function

} // End Of Class