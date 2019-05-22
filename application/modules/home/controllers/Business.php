<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business extends CommonFront {

	function __construct(){

		parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
		$this->load->model('Business_model');
		$this->load->library('Stripe');
        $this->load_language_files(); 
	}

    function addBusiness(){

        $this->check_user_session();
        $userId = $this->session->userdata('userId');
        $detail['businessDetail'] = $this->Business_model->getBusinessDetail($userId);

        $detail['bizSubsDetail'] = $this->Business_model->bizSubsDetail($userId);

        $detail['planDetail'] = $this->stripe->get_plan(BUSINESS_PLAN_ID);
        
        if($detail['bizSubsDetail']){
            $detail['subsDetail'] = $this->stripe->get_subscription($detail['bizSubsDetail']);    
        }
        $this->load->front_render('add_business',$detail,'');
    }

	function addBusinessData() {
		
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

		$this->load->library('form_validation');
		$this->load->model('image_model');

		$this->form_validation->set_rules('businessName',lang('biz_name_place'),'trim|required');
		$this->form_validation->set_rules('businessAddress',lang('biz_add'),'trim|required|callback__check_lat_long',array('_check_lat_long'=>'Please select valid address'));

        $businessType = $this->input->post('businessType');
		if(empty($_FILES['businessImage']['name']) && $businessType == '2'){
			$this->form_validation->set_rules('businessImage',lang('biz_img'),'required');
		}

		if($this->form_validation->run() == FALSE){

			$requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'msg' => $requireds);  
            echo json_encode($response); exit;

		} else {

			$busenessData = array();
			$businessImage = '';
			$userId = $this->session->userdata('userId');

			$folder = 'business';

			if(!empty($_FILES['businessImage']['name'])){
				$folder = 'business';
				$businessImage = $this->image_model->updateMedia('businessImage',$folder);
			}

			if(!empty($businessImage) && is_array($businessImage)){
				$response = array('status'=>0,'msg'=>$businessImage['error']);
				echo json_encode($response);exit();
                return;
			}

			$busenessData['businessName'] 		= $this->input->post('businessName');
			$busenessData['businessAddress'] 	= $this->input->post('businessAddress');
			$busenessData['businesslat'] 		= $this->input->post('businesslat');
			$busenessData['businesslong'] 		= $this->input->post('businesslong');
            
            if($businessImage)
                $busenessData['businessImage'] 	= $businessImage;

			$busenessData['user_id'] 			= $userId;

            $this->load->model('Login_model');
            //check data exist
            $where = array('user_id'=>$userId);
            $businessExist = $this->common_model->is_data_exists(BUSINESS, $where);
            
            if(!empty($businessExist)){
                $insertId = $this->common_model->updateFields(BUSINESS,$busenessData,array('user_id'=>$userId));
                
                $this->Login_model->session_create($busenessData['user_id']);
                $response = array('status' => 1, 'msg' => lang('biz_updated'), 'url'=>base_url('home/nearByYou'));
                echo json_encode($response);exit();

            }else{

                $insertId = $this->common_model->insertData(BUSINESS,$busenessData);
                $this->Login_model->session_create($busenessData['user_id']);
                $response = array('status' => 2, 'msg' => lang('biz_added'), 'url'=>base_url('home/nearByYou'));
                echo json_encode($response);exit();
            }
		}

    } //end function

    function _check_lat_long(){
        
        $businesslat = $this->input->post('businesslat');
        $businesslong = $this->input->post('businesslong');        
        if(empty($businesslat) && empty($businesslong)){
            return FALSE;
        }
        return True;        
    }

    /*function businessSubscription(){

        $this->check_user_session();
        $userId = $this->session->userdata('userId');

        $detail['bizSubsDetail'] = $this->Business_model->bizSubsDetail($userId);

        $detail['planDetail'] = $this->stripe->get_plan(BUSINESS_PLAN_ID);
        
        
        if($detail['bizSubsDetail']){
            $detail['subsDetail'] = $this->stripe->get_subscription($detail['bizSubsDetail']);    
        }

        $this->load->front_render('business_subscription',$detail,'');
    }*/


    //process stripe card payment- Create customer, charge customer and make subscription
    function businessSubscriptionData(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $email = $this->session->userdata('email');
        $userId = $this->session->userdata('userId');
        $stripeToken = $this->input->post('stripeToken');

        $where = array('userId'=>$userId,'bizSubscriptionId !=' => '');
        $subsExist = $this->common_model->is_data_exists(USERS, $where);
        if(!empty($subsExist)){
            $response = array('status' => 0, 'msg' => lang('already_subs'));
            echo json_encode($response); exit;
        }
        
        //get user stripe customer ID
        $customer_id = $this->Business_model->getStripeCustomerId();
        
        if($customer_id === FALSE){
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response); exit;
        }
        
        if(empty($customer_id)){
            //create customer if ID not found
            $stripe_res = $this->stripe->save_card_id($email, $stripeToken); //create a customer
            
            if($stripe_res['status'] == false){
                $response = array('status' => 0, 'msg' => $stripe_res['message']);
                echo json_encode($response); exit;
            }
            
            $customer_id = $stripe_res['data']->id;  //customer ID
            
            //save customer ID in our DB for future use
            $update = $this->Business_model->saveCustomerId($customer_id);
            
            //some problem in updating customer ID
            if(!$update){
                $response = array('status' => 0, 'msg' => lang('something_wrong'));
                echo json_encode($response); exit;
            }
        }
           
        //to get plan details
        $planDetail = $this->stripe->get_plan(BUSINESS_PLAN_ID);
      
        //subscribe customer to a plan
        $subscription = $this->stripe->create_subscription($customer_id, BUSINESS_PLAN_ID);
       
        if($subscription['status'] == false){
            $response = array('status' => 0, 'msg' => $subscription['message']);
            echo json_encode($response); exit;
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
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response); exit;
        }
        $res['status'] = 1; 
        $res['msg'] = lang('biz_subs_payment');
        $res['url'] = base_url('home/user/userProfile');
        echo json_encode($res); exit;  //success
    
        
    } // End Of Function

     // to cancel subscription
    function cancelBizSubscription(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $subsId = $this->Business_model->bizSubsDetail($userId);

        if($subsId === false){
            $res['status'] = 0; $res['msg'] = lang('something_wrong');
            echo json_encode($res); exit;
        }

        $subsDetail = $this->stripe->cancel_subscription($subsId,TRUE);

        if(!empty($subsDetail['data']) && $subsDetail['data']['cancel_at_period_end'] == true){

            $isCancel = $this->common_model->updateFields(USERS,array('bizSubscriptionStatus'=>0),array('userId'=>$userId));

            //some problem in updating payment info
            if(!$isCancel){
                $res['status'] = 0; $res['msg'] = lang('something_wrong');
                echo json_encode($res); exit;
            }
            
            $res['status'] = 1; 
            $res['msg'] = lang('biz_subs_canceled');
            $res['url'] = base_url('home');
            echo json_encode($res); exit;  //success
        }
    }
	
} //end of class
