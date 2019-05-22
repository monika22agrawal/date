<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscription extends CommonFront {

	function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Subscription_model');
        $this->load->library('Stripe');
        $this->load_language_files(); 
    }

    //process stripe card payment- Create customer, charge customer and make subscription
    function subsPaymentProcess(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $email = $this->session->userdata('email');
        $userId = $this->session->userdata('userId');

        $where = array('userId'=>$userId,'subscriptionId !=' => '');
        $subsExist = $this->common_model->is_data_exists(USERS, $where);
        if(!empty($subsExist)){
            $res['status'] = 0; $res['msg'] = lang('already_subs');
            echo json_encode($res); exit;
        }

        $res=array();
        extract($_POST);
        $stripeToken;
        
        //create customer if ID not found
        $stripe_res = $this->stripe->save_card_id($email, $stripeToken); //create a customer
        
        if($stripe_res['status'] == false){
            $response = array('status' => '0', 'msg' => $stripe_res['message']);
            echo json_encode($res); exit;
        }
        
        $customer_id = $stripe_res['data']->id;  //customer ID
        
        //save customer ID in our DB for future use
        $update = $this->Subscription_model->saveCustomerId($customer_id);
        
        //some problem in updating customer ID
        if(!$update){
            $response = array('status' => '0', 'msg' => lang('something_wrong'));
            echo json_encode($res); exit;
        }        
        
        //to get plan details
        $planDetail = $this->stripe->get_plan(STRIPE_PLAN_ID);
       
        //subscribe customer to a plan
        $subscription = $this->stripe->create_subscription($customer_id, STRIPE_PLAN_ID);
      
        if($subscription['status'] == false){
            $res['status'] = 0; $res['msg'] = $subscription['message'];
            echo json_encode($res); exit;  //fail
        }
        
        $subscriptionId = $subscription['data']->id;  //customer ID
        
        // to save subscription data in user table
        $userData['subscriptionId'] = $subscriptionId;
        $userData['subscriptionStatus'] = ($subscription['data']['status'] == 'active') ? 1 : 0;
        $update_sub = $this->common_model->updateFields(USERS,$userData,array('userId'=>$userId));

        //some problem in updating customer ID
        if(!$update_sub){
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
        }
        
        //save payment data in DB
        $payment_data['chargeId'] = $subscriptionId;
        $payment_data['user_id'] = $userId;
        $payment_data['amount'] = $planDetail['data']['amount'];
        $payment_data['paymentStatus'] = $subscription['data']['status'];
        $payment_data['crd'] = datetime();

        $paymentId = $this->Subscription_model->savePaymentDetails($payment_data);
        
        //some problem in updating payment info
        if(!$paymentId){
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
        }
        
        $res['status'] = 1; 
        $res['msg'] = lang('biz_subs_payment');
        $res['url'] = base_url('home/user/userProfile');
        echo json_encode($res); exit;  //success
        
    } // End Of Function

    //Stripe webhook handler- For caputring behind the scenes events like subscription payment fail or subscription ended
    function stripe_web_hook(){

        $file_name = 'stripe_logs.txt';
        
        // Retrieve the request's body and parse it as JSON:
        $input = @file_get_contents('php://input');
        
        $event_json = json_decode($input);

        // Do something with $event_json
        $cutomer_id = $event_json->data->object->customer;
        $subscription_id = $event_json->data->object->id;

        switch ($event_json->type) {
            
            /* Occurs whenever a customer's subscription ends. */
            case "customer.subscription.deleted":
            
            $this->Subscription_model->cancelSubscription($subscription_id); //make relevant cancel actions in our DB
            log_event($cutomer_id, $file_name);
            break;
            
            /* Occurs whenever a subscription changes (e.g., switching from one plan to another, 
               or changing the status from trial to active). 
               Here we will only listen to status, if it is canceled or unpaid or past_due 
            */
            case "customer.subscription.updated":
            
            //usually canceled state will trigger subscription.delete event so we don't really need to check here
            //but we are putting this anyway just to be double sure
            //in past_due event the script could email the customer directly, asking them to update their payment details.
            //after past_due state Stripe will retry to charge again before changing state to unpaid or canceled
            //unpaid or canceled state occurs when Stripe has exhausted all payment retry attempts.
            //but for now we are not taking past_due state into consideration
            
            $sub_status = $event_json->data->object->status;
            if($sub_status == 'canceled' || $sub_status == 'unpaid'){
                $this->Subscription_model->cancelSubscription($subscription_id); //make relevant cancel actions in our DB
            }
            log_event($status, $file_name);
            break;
        }
        //return response to stripe
        http_response_code(200); // PHP 5.4 or greater
    }

    // to cancel subscription
    function cancelSubscription(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $subsId = $this->Subscription_model->getSubscriptionId();

        if($subsId === false){
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
        }

        $subsDetail = $this->stripe->cancel_subscription($subsId,TRUE);

        if(!empty($subsDetail['data']) && $subsDetail['data']['cancel_at_period_end'] == true){

            $isCancel = $this->common_model->updateFields(USERS,array('subscriptionStatus'=>0),array('userId'=>$userId));

            //some problem in updating payment info
            if(!$isCancel){
                $res['status'] = 0; $res['msg'] = lang('something_wrong');
                echo json_encode($res); exit;
            }
            
            $res['status'] = 1; 
            $res['msg'] = lang('biz_subs_canceled');
            $res['url'] = base_url('home/user/userProfile');
            echo json_encode($res); exit;  //success
        }
    }

} // End Of Class