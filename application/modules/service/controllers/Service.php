<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service extends CommonService {

	function __construct(){
		parent::__construct();
		$this->lang->load('header_footer_content_lang', $this->appLang);  //load response lang file
	}

	function test_post(){
		$this->load->library('face_plus');

		//$profileImage1 = $this->post('image_file1');		
		$profileImage2 = $this->post('image_url2');		

        $isSend = $this->face_plus->compareFace($_FILES['image_file1'],$profileImage2);
       // pr($isSend->confidence);
	}

	function userRegistration_post() {

		$this->load->library('form_validation');
		$this->load->model('image_model');
		$socialId = $this->post('socialId');

		if(empty($socialId)){

			$this->form_validation->set_rules('email',lang('email_placeholder'),'trim|required|valid_email');
			$this->form_validation->set_rules('password',lang('password_placeholder'),'trim|required|min_length[8]');

		}else{
			
			$this->form_validation->set_rules('socialType',lang('social_type'),'trim|required');
		}

		$this->form_validation->set_rules('fullName',lang('full_name'),'trim|required');
		//$this->form_validation->set_rules('address','Address','trim|required');
		/*$this->form_validation->set_rules('city','City','trim|required');
		$this->form_validation->set_rules('state','State','trim|required');
		$this->form_validation->set_rules('country','Country','trim|required');
		$this->form_validation->set_rules('latitude','Latitude','trim|required');
		$this->form_validation->set_rules('longitude','Longitude','trim|required');*/
		$this->form_validation->set_rules('gender',lang('gender'),'trim|required');
		$this->form_validation->set_rules('birthday',lang('birthday_placeholder'),'trim|required');
		$this->form_validation->set_rules('purpose',lang('purpose'),'trim|required');
		$this->form_validation->set_rules('dateWith',lang('date_with'),'trim|required');

		/*if(empty($_FILES['profileImage']['name'])){
			$this->form_validation->set_rules('profileImage','Profile image','required');
		}*/

		if($this->post('gender')==2){

			$this->form_validation->set_rules('eventInvitation',lang('event_invitation'),'required');	
		}

		if($this->form_validation->run() == FALSE){

			$response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$this->response($response);

		} else {

			$userData = $userImgData = array();
			$authToken = $this->service_model->_generate_token();
			$socialType = $this->post('socialType');

			$profileImage = $isSocial = '';
			$folder = 'profile';
			
			$isSocial = 0;
			if(!empty($_FILES['profileImage']['name'])){
				$folder = 'profile';
				$profileImage = $this->image_model->updateMedia('profileImage',$folder);
				$isSocial = 0;

				if(!empty($profileImage) && is_array($profileImage)){
					$response = array('status'=>FAIL,'message'=>$profileImage['error']);
					$this->response($response);
				}

			}elseif(!empty($socialId) && !empty($socialType)){ 

				$profileImage = $this->post('profileImage');
				$isSocial = 1;
			}

			$userImgData['image'] = $profileImage ? $profileImage : '';
			$userImgData['isSocial'] = $isSocial;

			$latitude 	= $this->post('latitude');
			$longitude 	= $this->post('longitude');

			$getFullAddress = getAddress($latitude,$longitude);

			$address = $city = $state = $country = '';
			if(!empty($getFullAddress)){

				$address 	= isset($getFullAddress['formatted_address']) ? $getFullAddress['formatted_address'] : '';	// get full address
				$city 		= isset($getFullAddress['city']) ? $getFullAddress['city'] : ''; 							// get city name
				$state 		= isset($getFullAddress['state']) ? $getFullAddress['state'] : ''; 							// get state name 
				$country 	= isset($getFullAddress['country']) ? $getFullAddress['country'] : ''; 						// get country name
			}		      
						
			$userData['fullName'] 			= $this->post('fullName');
			$userData['email'] 				= $this->post('email');
			$userData['password'] 			= $this->post('password') ? password_hash($this->post('password'), PASSWORD_DEFAULT) : '';
			$userData['latitude'] 			= $latitude;
			$userData['longitude'] 			= $longitude;
			$userData['address'] 		  	= $address;
			$userData['city'] 		  		= $city;
			$userData['state'] 		  		= $state;
			$userData['country'] 		  	= $country;
			$userData['gender'] 			= $this->post('gender');
			$userData['purpose'] 			= $this->post('purpose');
			$userData['dateWith'] 			= $this->post('dateWith');
			$userData['eventInvitation'] 	= $this->post('eventInvitation');
			$userData['birthday'] 			= date('Y-m-d',strtotime($this->post('birthday')));
			$userData['deviceToken'] 		= $this->post('deviceToken');
			$userData['deviceType'] 		= $this->post('deviceType');			
			$userData['registeredFrom'] 	= $this->post('deviceType');			
			$userData['setLanguage'] 		= $this->post('setLanguage') ? $this->post('setLanguage') : 'english';			
			$userData['emailVerified'] 		= 1;			
			$userData['socialId'] 			= $socialId;
			$userData['socialType'] 		= $socialType;
			$userData['authToken'] 			= $authToken;
			$userData['crd'] 				= date('Y-m-d H:i:s');
			
			$diff = (date('Y') - date('Y',strtotime($userData['birthday'])));
			$userData['age']	= $diff;	
				
			$isRegister = $this->service_model->userRegistration($userData,$userImgData);
			
			if(is_array($isRegister) && $isRegister['regType'] == 'NR'){

				$response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(128),'userDetail'=>$isRegister['data']);
			
			} elseif(is_string($isRegister) && $isRegister == 'AE'){

				$response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(116));
			
			} elseif(is_array($isRegister) && $isRegister['regType'] == 'SL'){

				$response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(106),'userDetail'=>$isRegister['data']);
			
			} elseif(is_array($isRegister) && $isRegister['regType'] == 'SR'){

				$response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(110),'userDetail'=>$isRegister['data']);
			
			} elseif(is_string($isRegister) && $isRegister == 'SGW'){

				$response = array('status'=>FAIL,'message'=> ResponseMessages::getStatusCodeMessage(118));
			
			} elseif(is_string($isRegister) && $isRegister == 'FT'){
				
				$response = array('status'=>SUCCESS,'message'=> ResponseMessages::getStatusCodeMessage(182));
			
			}elseif(is_string($isRegister) && $isRegister == 'NA'){
				
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(121), 'userDetail'=>$isLoggedIn['userDetail']);
			
			}else {
				$response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(118));
			}
			$this->response($response);
		}
    } //end function


    function userLogin_post(){

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email',lang('email_placeholder'),'required|valid_email');
		$this->form_validation->set_rules('password',lang('password_placeholder'),'required');

		if($this->form_validation->run() == FALSE){

			$responseArray = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$response = $this->generate_response($responseArray);
			$this->response($response);

		} else {
			
			$authToken = $this->service_model->_generate_token();

			$userData = array();
			$userData['email'] = $this->post('email');
			$userData['password'] = $this->post('password');
			$userData['deviceToken'] = $this->post('deviceToken');
			$userData['deviceType'] = $this->post('deviceType');
			$userData['authToken'] = $authToken;
			$userData['setLanguage'] 		= $this->post('setLanguage') ? $this->post('setLanguage') : 'english';
			
			$isLoggedIn = $this->service_model->userLogin($userData,$authToken);
			
			if(is_string($isLoggedIn['type']) && $isLoggedIn['type'] == 'NA'){
				
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(121), 'userDetail'=>$isLoggedIn['userDetail']);
			
			} elseif(is_string($isLoggedIn['type']) && $isLoggedIn['type'] == 'WP'){

				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(105), 'userDetail'=>$isLoggedIn['userDetail']);

			} elseif(is_string($isLoggedIn['type']) && $isLoggedIn['type'] == 'LS'){
				
				$responseArray = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(106),'userDetail'=>$isLoggedIn['userDetail']);
			}elseif(is_string($isLoggedIn['type']) && $isLoggedIn['type'] == 'SL'){
				
				$responseArray = array('status'=>FAIL,'message'=>"This email address is associated with ".$isLoggedIn['data']." account, but no password is associated with it yet, so it can't be used to log in. Forgot Password if you want to use this as a normal login.",'userDetail'=>$isLoggedIn['userDetail']);
			}else{
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(105));
			}
			$response = $this->generate_response($responseArray);
			$this->response($response);
		}
	} //end function


	function forgotPassword_post(){

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email',lang('email_placeholder'),'required|valid_email');
		if($this->form_validation->run() == FALSE){
			$responseArray = array('status'=>FAIL,'message'=>validation_errors());
			$response = $this->generate_response($responseArray);
			$this->response($response);
		} else {

	        $res = $this->service_model->forgotPassword($this->post('email'));
	     
	        if(is_array($res) && $res['type'] == 'SS'){ 
	     		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(120));

	     	} elseif(is_array($res) && $res['type'] == 'NE'){
	            $response = array('status' => FAIL, 'message' =>"Email does not exist");
	        }else if(is_array($res) && $res['type'] == 'SR') {
	            $response = array('status' => FAIL, 'message' => "This email is registerd with ".$res['socialType']." account ");
	        }else{
	        	$response = array('status'=>FAIL,'message'=> ResponseMessages::getStatusCodeMessage(118));
	        }
		}
        $this->response($response);

    } //End function

	
	// code for otp send using twillo
	function verifyEmail_post(){

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email',lang('email_placeholder'),'required|valid_email');

		if($this->form_validation->run() == FALSE){

			$responseArray = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$response = $this->generate_response($responseArray);
			$this->response($response);

		} else {

			$conform = (rand(10, 99)).(rand(11, 99));

			$data_val['email']		=	$this->post('email');
			$data_val['code']		=	$conform;
			
			$existContact = $this->common_model->get_records_by_id(USERS,true,array('email'=>$data_val['email'],'emailVerified'=>'1'),"*","","");
			
			if(empty($existContact)){

				$isVerify = $this->service_model->verifyEmail($data_val);

				if(is_array($isVerify)){

					switch ($isVerify['status']) {

						case "1":
							$responseArray = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(124),'code'=>$isVerify['code'],'email'=>$isVerify['email']);
							break;
						case "0":
							$responseArray = array('status'=>FAIL,'message'=>$isVerify['error']);
							break;
						default:
							$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(118));
					}
				}
			} else{
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(117));
			}
			$response = $this->generate_response($responseArray);
			$this->response($response);	
		}

	}//ENd Function

	//check social registration 
	function checkSocialRegistor_post(){

		$this->load->library('form_validation');
		$this->form_validation->set_rules('email',lang('email_placeholder'),'required|valid_email');

		if($this->form_validation->run() == FALSE){

			$responseArray = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$response = $this->generate_response($responseArray);
			$this->response($response);

		} else {

			$socialId   	= $this->post('socialId');
			$socialType 	= $this->post('socialType');
			$email 			= $this->post('email');
			$deviceType 	= $this->post('deviceType');
			$deviceToken 	= $this->post('deviceToken');

			//$where 		= array('socialId'=>$socialId,'socialType'=>$socialType);
			$where 			= array('email'=>$email);
			$userData 		= $this->common_model->getsingle(USERS, $where);
			
			$token = $this->service_model->_generate_token();
			
			if($userData){

				if($userData->status == 1){

					$this->common_model->updateFields(USERS, array('authToken' =>$token,'deviceToken'=>$deviceToken,'deviceType'=>$deviceType), array('userId'=>$userData->userId));

					$userData->totalFriends = $this->common_model->totalFriendCount($userData->userId);

					$images = $this->common_model->usersImage($userData->userId);
					
					$userData->profileImage = $images;

					//check data exist
		            $userPaymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, array('user_id'=>$userData->userId));
		            $userData->bankAccountStatus = 0;

		            if(!empty($userPaymentExist)){
		            	
		                $userData->bankAccountStatus = 1;
		            }

		            //check data exist
		            $userBizPaymentExist = $this->common_model->is_data_exists(BUSINESS, array('user_id'=>$userData->userId));
		            $userData->isBusinessAdded = '0';
		            if(!empty($userBizPaymentExist)){
		                $userData->isBusinessAdded = '1';
		            }

					$userData->authToken = $token;

					$response = array('status' => SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(135),'userDetail' => $userData);
					$this->response($response);
					
				}else{
					$response = array('status' => FAIL,'message' => ResponseMessages::getStatusCodeMessage(121));
					$this->response($response);
				}
			}else{
				$response = array('status' => FAIL,'message' => ResponseMessages::getStatusCodeMessage(134));
				$this->response($response);
			}
		}
	}

	//get education list
	function getEducationList_get(){

		$result = $this->service_model->getEducationList();

		if(!empty($result)){
			$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'educationList'=>$result);
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		}else {
			$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		} 
	}

	function getWorkList_get(){

		$result = $this->service_model->getWorkList();

		if(!empty($result)){
			$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'workList'=>$result);
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		}else {
			$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		} 
	}

	function getInterestList_get(){

		$result = $this->service_model->getInterestList();

		if(!empty($result)){
			$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'interestList'=>$result);
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		}else {
			$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		} 
	}

	//check age limit
	function _check_age(){
		$dob = $this->input->post("birthday");
    	$diff = (date('Y') - date('Y',strtotime($dob)));
    	if($diff<18){
    		$this->form_validation->set_message('_check_age','Age should be above 18 years.');
    		return FALSE;
    	}else{

    		return TRUE;
    	}
	}

	//delete user profile detail
	function deleteUserRecord_get(){
		
        $contactNo = $this->get('contactNo');
        if(!empty($contactNo)){
			$result = $this->common_model->deleteData(USERS,array('contactNo'=>$contactNo));

			if(!empty($result)){
				$responseArray = array('status'=>SUCCESS,'message' =>'User deleted.');
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			}else {
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(107));
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			} 
		}else {
			$responseArray = array('status'=>FAIL,'message'=>'Contact number is required.');
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		} 
	}

	function getContent_get(){
		
		$select = array('option_value');

        $where = array('option_name'=>'pp_page');

        $data['policy'] = $this->common_model->getsingle(OPTIONS,$where,$select);
        $return = '';
        if(!empty($data['policy'])){
        	$return['privacy_policy'] = base_url('home/privacy');
        }

        $select1 = array('option_value');
        $where1 = array('option_name'=>'tc_page');
        $data['terms'] = $this->common_model->getsingle(OPTIONS,$where1,$select1);
        if(!empty($data['terms'])){
        	$return['terms'] = base_url('home/terms');
        }

        $select2 = array('option_value');
        $where2 = array('option_name'=>'about_page');
        $data['aboutus'] = $this->common_model->getsingle(OPTIONS,$where2,$select2);
        if(!empty($data['aboutus'])){
        	$return['aboutus'] = base_url('home/about_us');
        }
        
        if(empty($return)){
            $response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(126));
            $this->response($response);
        }
        $response = array('status'=> SUCCESS,'message'=>"OK","Content"=>$return);
        $this->response($response);
    }// End


} //end of class
