<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business extends CommonServiceV2 {

	function __construct(){

		parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
		$this->load->model('Business_model');
		$this->load->library('Stripe');
	}

    // to add bussiness for promoting
	function addBusiness_post() {

		//check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

		$this->load->library('form_validation');
		$this->load->model('image_model');

		$this->form_validation->set_rules('businessName',lang('biz_name_place'),'trim|required');
		$this->form_validation->set_rules('businessAddress',lang('biz_add'),'trim|required');
		$this->form_validation->set_rules('businesslat',lang('biz_add_lat'),'trim|required');
		$this->form_validation->set_rules('businesslong',lang('biz_add_long'),'trim|required');

		if(empty($_FILES['businessImage']['name'])){
			$this->form_validation->set_rules('businessImage',lang('biz_img'),'required');
		}

		if($this->form_validation->run() == FALSE){

			$response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$this->response($response);

		} else {

			$busenessData = array();
			$businessImage = '';
			$userId = $this->authData->userId;

			$folder = 'business';

			if(!empty($_FILES['businessImage']['name'])){
				$folder = 'business';
				$businessImage = $this->image_model->updateMedia('businessImage',$folder);
			}

			if(!empty($businessImage) && is_array($businessImage)){
				$response = array('status'=>FAIL,'message'=>$businessImage['error']);
				$this->response($response);
			}

            if(!empty($this->post('businessName')))
			    $busenessData['businessName'] 		= $this->post('businessName');

            if(!empty($this->post('businessAddress')))
			    $busenessData['businessAddress'] 	= $this->post('businessAddress');

            if(!empty($this->post('businesslat')))
			    $busenessData['businesslat'] 		= $this->post('businesslat');

            if(!empty($this->post('businesslong')))
			    $busenessData['businesslong'] 		= $this->post('businesslong');

            if(!empty($businessImage))
			    $busenessData['businessImage'] 		= $businessImage;

			$busenessData['user_id'] 			    = $userId;
				
            //check data exist
            $where = array('user_id'=>$userId);
            $businessExist = $this->common_model->is_data_exists(BUSINESS, $where);
            if(!empty($businessExist)){
                $insertId = $this->common_model->updateFields(BUSINESS,$busenessData,array('user_id'=>$userId));
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(165));
            }else{
                $insertId = $this->common_model->insertData(BUSINESS,$busenessData);
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(156)); 
            }
			$this->response($response);
		}
    } //end function


    function getBusinessDetail_get(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $id = $this->get('userId');
        $userId = !empty($id) ? $id : $this->authData->userId;
        $result = $this->Business_model->getBusinessDetail($userId);

        if(!empty($result)){
            $responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'businessDetail'=>$result);
            $response = $this->generate_response($responseArray);
            $this->response($response,OK);
        }else {
            $responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
            $response = $this->generate_response($responseArray);
            $this->response($response,OK);
        }
    }

    // to get all user's business list
    function getBusinessList_get(){

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $data['latitude']   = $this->get('latitude');
        $data['longitude']  = $this->get('longitude'); 

        if(!empty($data['latitude']) && !empty($data['latitude'])){

            $result = $this->Business_model->getBusinessList($data);

            if(!empty($result)){
                $responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'businessList'=>$result);
                $response = $this->generate_response($responseArray);
                $this->response($response,OK);
            }else {
                $responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
                $response = $this->generate_response($responseArray);
                $this->response($response,OK);
            }

        }else {
            $responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(166));
            $response = $this->generate_response($responseArray);
            $this->response($response,OK);
        }
    }    

    //process stripe card payment- Create customer, charge customer and make subscription
    function businessSubscription_post(){

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

            $where = array('userId'=>$userId,'bizSubscriptionId !=' => '');
            $subsExist = $this->common_model->is_data_exists(USERS, $where);
            if(!empty($subsExist)){
                $response = array('status' => FAIL, 'message' => 'You already subscribed');
                $this->response($response);
            }
            
            //get user stripe customer ID
            $customer_id = $this->Business_model->getStripeCustomerId();
        
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
                $update = $this->Business_model->saveCustomerId($customer_id);
                
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
            $planDetail = $this->stripe->get_plan(BUSINESS_PLAN_ID);
           
            //subscribe customer to a plan
            $subscription = $this->stripe->create_subscription($customer_id, BUSINESS_PLAN_ID);
          
            if($subscription['status'] == false){
                $response = array('status' => FAIL, 'message' => $subscription['message']);
                $this->response($response);
            }
            
            $subscriptionId = $subscription['data']->id;  //customer ID

            // to save data in user table
            $userData['bizSubscriptionId'] = $subscriptionId;
            $userData['bizSubscriptionStatus'] = ($subscription['data']['status'] == 'active') ? 1 : 0;
            $update_sub = $this->common_model->updateFields(USERS,$userData,array('userId'=>$userId));
            
            //save payment data in DB
            $payment_data['chargeId'] = $subscriptionId;
            $payment_data['user_id'] = $userId;
            $payment_data['amount'] = $planDetail['data']['amount'];
            $payment_data['paymentStatus'] = $subscription['data']['status'];
            $payment_data['paymentType'] = 6;
            $payment_data['crd'] = datetime();

            $paymentId = $this->Business_model->savePaymentDetails($payment_data);
            
            //some problem in updating payment info
            if(!$paymentId){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }
            $subDetail['bizSubscriptionId'] = $subscriptionId;
            $subDetail['bizSubscriptionStatus'] = $userData['bizSubscriptionStatus'];
            $subDetail['customerId'] = $customer_id;

            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(167),'subscriptionDetail'=>$subDetail);
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
        $subsId = $this->Business_model->getSubscriptionId();

        if($subsId === FALSE){
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
            $this->response($response);
        }     

        $subsDetail = $this->stripe->cancel_subscription($subsId,TRUE);
       
        if(!empty($subsDetail['data']) && $subsDetail['data']['cancel_at_period_end'] == true){

            $isCancel = $this->common_model->updateFields(USERS,array('bizSubscriptionStatus'=>0),array('userId'=>$userId));

            //some problem in updating payment info
            if(!$isCancel){
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
                $this->response($response);
            }

            $getUserDetail = $this->common_model->getsingle(USERS,array('userId'=>$userId));

            $subDetail['bizSubscriptionId'] = $getUserDetail->bizSubscriptionId;
            $subDetail['bizSubscriptionStatus'] = $getUserDetail->bizSubscriptionStatus;
            $subDetail['customerId'] = $getUserDetail->stripeCustomerId;

            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(168),'subscriptionDetail'=>$subDetail);
            $this->response($response); //success
        }

    } // End Of Function
	
} //end of class
