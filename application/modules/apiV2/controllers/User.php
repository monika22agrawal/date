<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CommonServiceV2 {

	function __construct(){

		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('image_model');
		$this->lang->load('user_message_lang', $this->appLang);  //load response lang file
	}

	function inviteAllFriends_get(){

		$allUsers = $this->user_model->inviteAllFriends($limit,$start,$searchArray);
	}

	// code for otp send using twillo
	function verifyNo_post(){

		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

    	// Created otp and send on mobile number
		$this->load->library('form_validation');
		$this->form_validation->set_rules('contactNo',lang('contact_no'),'trim|required|numeric');
		$this->form_validation->set_rules('countryCode',lang('country_code'),'trim|required');

		if($this->form_validation->run() == FALSE){

			$responseArray = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$response = $this->generate_response($responseArray);
			$this->response($response);
			
		} else {

			$userId  = $this->authData->userId;
			$conform = (rand(10, 99)).(rand(11, 99));
			$data_val['contactNo']		=	$this->post('contactNo');
			$data_val['countryCode']	=	$this->post('countryCode');
			$data_val['OTP']			=	$conform;
			$data_val['userId']			=	$userId;
			
			$existContact = $this->common_model->get_records_by_id(USERS,true,array('contactNo'=>$data_val['contactNo'],'countryCode'=>$data_val['countryCode'],'otpVerified'=>'1'),"*","","");
			
			if(empty($existContact)){

				$verifyNo = $this->user_model->verifyNo($data_val);

				if(is_array($verifyNo)){

					switch ($verifyNo['status']) {

						case "1":

							$responseArray = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(183),'otp'=>$verifyNo['otp'],'countryCode'=>$verifyNo['countryCode'],'contactNo'=>$verifyNo['contactNo']);
							break;

						case "0":

							$responseArray = array('status'=>FAIL,'message'=>$verifyNo['error']);
							break;

						default:
							$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(118));
					}
				}
			} else{
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(184));
			}
			$response = $this->generate_response($responseArray);
			$this->response($response);	
		}

	}//ENd Function


	// to verify id with hand
	function verifyIdWithHand_post() {

		//check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

		$this->load->library('form_validation');
		$this->load->model('image_model');

		$this->form_validation->set_rules('userId',lang('user_id'),'required');

		if(empty($_FILES['idWithHand']['name'])){
			$this->form_validation->set_rules('idWithHand',lang('user_id_proof'),'required');
		}

		if($this->form_validation->run() == FALSE){

			$response = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$this->response($response);

		} else {

			$idData = array();
			$idWithHand = '';
			$userId = $this->authData->userId;

			$folder = 'idProof';

			if(!empty($_FILES['idWithHand']['name'])){
				$idWithHand = $this->image_model->updateMedia('idWithHand',$folder);
			}

			if(!empty($idWithHand) && is_array($idWithHand)){
				$response = array('status'=>FAIL,'message'=>$idWithHand['error']);
				$this->response($response);
			}

			$idData['idWithHand'] 		= $idWithHand ? $idWithHand : '';
			$idData['isVerifiedId']     = 0;
				
            //check data exist
            $where = array('userId'=>$userId);
            $userExist = $this->common_model->is_data_exists(USERS, $where);
            if(!empty($userExist)){

                $this->common_model->updateFields(USERS,$idData,array('userId'=>$userId));
                $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(185));

            }else{
                $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118)); 
            }
			$this->response($response);
		}
    } //end function

    // to verify face
    function faceVerification_post() {

        //check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        } 

        $userId 	= $this->authData->userId;
        
        $this->load->model('image_model');

        if(empty($_FILES['faceImage']['name'])){
            $response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(186));
            $this->response($response);
        }

        $faceData = array();
        $faceImage = '';

        $folder = 'faceProof';

        if(!empty($_FILES['faceImage']['name'])){

            $faceImage = $this->image_model->updateMedia('faceImage',$folder);
        }

        if(!empty($faceImage) && is_array($faceImage)){
            $response = array('status'=>FAIL,'message'=>$faceImage['error']);
			$this->response($response);
        }

        $faceData['faceImage']        = $faceImage ? $faceImage : '';
        $faceData['isFaceVerified']   = '1';
            
        //check data exist
        $where = array('userId'=>$userId);
        $userExist = $this->common_model->is_data_exists(USERS, $where);

        if(!empty($userExist)){

            $this->common_model->updateFields(USERS,$faceData,array('userId'=>$userId));
            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(187));
            $this->response($response);

        }else{
            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118)); 
            $this->response($response);
        }
      
    } //end function

    //get user verification status
	function getUserVerificationStatus_get(){

		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId = $this->authData->userId;
       
		$result = $this->user_model->getUserVerificationStatus($userId);

		if(!empty($result)){
			$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'verificationStatus'=>$result);
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		}else {
			$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		}		
	}

	//update user profile
	function updateProfile_post(){
		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
		$eduId = $workId = $interestId = '';
		$userData = array();
		if(!empty($this->post('birthday'))){
			$userData['birthday'] = date('Y-m-d',strtotime($this->post('birthday')));
			$diff = (date('Y') - date('Y',strtotime($this->post('birthday'))));
			$userData['age']	= $diff;	
		}

		if(!empty($this->post('otpVerified')))
			$userData['otpVerified'] = $this->post('otpVerified');

		if(!empty($this->post('fullName')))
			$userData['fullName'] = $this->post('fullName');

		if(!empty($this->post('latitude')))
			$userData['latitude'] = $this->post('latitude');

		if(!empty($this->post('longitude')))
			$userData['longitude'] = $this->post('longitude');

		if(!empty($this->post('gender')))
			$userData['gender'] = $this->post('gender');

		if(!empty($this->post('height')))
			$userData['height'] = $this->post('height');

		if(!empty($this->post('weight')))
			$userData['weight'] = $this->post('weight');

		if(!empty($this->post('relationship')))
			$userData['relationship'] = $this->post('relationship');
		
		//if(!empty($this->post('about')))
			$userData['about'] = ($this->post('about') != 'NA') ? $this->post('about') : '';

		if(!empty($this->post('showOnMap')))
			$userData['showOnMap'] = $this->post('showOnMap');

		if(!empty($this->post('language')))
			$userData['language'] = $this->post('language');

		if(!empty($this->post('eventType')))
			$userData['eventType'] = $this->post('eventType');

		if(!empty($this->post('appointmentType')))
			$userData['appointmentType'] = $this->post('appointmentType');

		if(!empty($this->post('eduId')))
			$eduId = $this->post('eduId');

		if(!empty($this->post('workId')))
			$workId = $this->post('workId');

		if(!empty($this->post('interestId')))
			$interestId = $this->post('interestId');
		
		if(!empty($this->post('latitude')) && !empty($this->post('longitude'))){
			$getFullAddress = getAddress($userData['latitude'],$userData['longitude']);
		}
		

		$address = $city = $state = $country = '';
		if(!empty($getFullAddress)){

			$address    = isset($getFullAddress['formatted_address']) ? $getFullAddress['formatted_address'] : '';  // get full address
            $city       = isset($getFullAddress['city']) ? $getFullAddress['city'] : '';                            // get city name
            $state      = isset($getFullAddress['state']) ? $getFullAddress['state'] : '';                          // get state name 
            $country    = isset($getFullAddress['country']) ? $getFullAddress['country'] : '';                      // get country name
		}

		//if(!empty($this->post('address')))
			$userData['address'] 	= $address;

		//if(!empty($this->post('city')))
			$userData['city'] 		= $city;

		//if(!empty($this->post('state')))
			$userData['state'] 		= $state;

		//if(!empty($this->post('country')))
			$userData['country'] 	= $country;
		
		$userData['isProfileUpdate'] = $this->post('isProfileUpdate');;
		$userData['upd'] 			 = date('Y-m-d H:i:s');
		
		//update user profile				 
		$updateData = $this->user_model->updateProfile($userData,$eduId,$workId,$interestId);

		if(!empty($updateData)){

			$response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(108),'userDetail'=>$updateData);
		}else{
			$response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(118));
		}
		
		$this->response($response);

	}//ENd Function

	// upload user's image
	function uploadUserImage_post(){

		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

		$this->load->library('form_validation');
		$this->form_validation->set_rules('userId',lang('user_id'),'required');
		if(empty($_FILES['image']['name'])){
			$this->form_validation->set_rules('image',lang('user_profile_img'),'required');
		}

		if($this->form_validation->run() == FALSE){
			$responseArray = array('status'=>FAIL,'message'=>validation_errors());

			$response = $this->generate_response($responseArray);
			$this->response($response);

		} else {

			$image = '';
			$userId = $this->authData->userId;

			$folder = 'profile';

			if(!empty($_FILES['image']['name'])){
				$image = $this->image_model->updateMedia('image',$folder);
			}

			if(!empty($image) && is_array($image)){
				$response = array('status'=>FAIL,'message'=>$image['error']);
				$this->response($response);
			}

			$userData['user_id'] 	= $userId;
			if(!empty($image))
			    $userData['image'] 	= $image;

			$insertData = $this->user_model->uploadUserImage($userData);

	        if($insertData){
	      
	            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(200),'imageData'=>$insertData);
	        } else{
	            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	        }
		}
        $this->response($response);
	}

	// delete user's image
	function deleteUserImage_post(){

		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

		$this->load->library('form_validation');
		$this->form_validation->set_rules('userImgId','Image Id','required');

		if($this->form_validation->run() == FALSE){
			$responseArray = array('status'=>FAIL,'message'=>validation_errors());
			$response = $this->generate_response($responseArray);
			$this->response($response);

		} else {

			$userImgId   = $this->post('userImgId');
			$where = array('user_id'=>$this->authData->userId,'userImgId'=>$userImgId);
	        $res = $this->common_model->deleteData(USERS_IMAGE,$where);

	        if ($this->authData->isFaceVerified == 1) {
	        	
	        	$imgCount = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$userId));

		        if ($imgCount == 0) {
		            $checkWhere = array('userId'=>$this->authData->userId);
		            $this->db->update(USERS, array('faceImage'=>'','isFaceVerified'=>'0'), $checkWhere);
		        }
	        }
	        	     
	        if($res){
	            $response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(130));
	        } else{
	            $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	        }
		}
        $this->response($response);
	}

	//get user profile detail
	function getUserProfile_get(){

		//check auth token
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId = $this->get('userId');
        if(!empty($userId)){
			$result = $this->user_model->userInfo($userId);

			if(!empty($result)){
				$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'UserDetail'=>$result);
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			}else {
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(107));
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			} 
		}else {
			$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(125));
			$response = $this->generate_response($responseArray);
			$this->response($response,OK);
		} 
	}

	//change user password
	function changePassword_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $this->form_validation->set_rules('oldPassword', lang('current_pwd'), 'required');
        $this->form_validation->set_rules('newPassword', lang('new_pwd'), 'required');
       
        $userData = array();

        if($this->form_validation->run() == FALSE){
            $response = array('status' => FAIL, 'message' => validation_errors());
            $this->response($response);
        }else{

            $oldPassword = $this->post('oldPassword');
            $newPassword = password_hash($this->post('newPassword') , PASSWORD_DEFAULT);
            $user = $this->user_model->getPassword();            
            //password verify
            if(password_verify($oldPassword, $user->password)){
                
                $response = $this->user_model->changePassword($newPassword);

                if($response == TRUE){

                    $response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(189));
                }else{

                    $response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(190));
                }
                $this->response($response);

            }else{
                $response = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(191));
                $this->response($response);
            }
        }
    }
    
    //get user near by your location
    function nearByUsers_post(){
    	//check for auth
    	if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

		$this->load->library('form_validation');
		$this->form_validation->set_rules('latitude',lang('cur_lat'),'required');
		$this->form_validation->set_rules('longitude',lang('cur_long'),'required');

		if($this->form_validation->run() == FALSE){
			$responseArray = array('status'=>FAIL,'message'=>strip_tags(validation_errors()));
			$response = $this->generate_response($responseArray);
			$this->response($response);
		} else { 

			$data['showMe']		= $this->post('showMe'); // for gender
			$data['ageEnd'] 	= $this->post('ageEnd');
			$data['ageStart']	= $this->post('ageStart');
			$data['latitude'] 	= $this->post('latitude');
			$data['longitude'] 	= $this->post('longitude');  
			$data['newUsers'] 	= $this->post('newUsers');  
			$data['userId']	    = $this->authData->userId;
			
			//get data
			$result = $this->user_model->nearByUsers($data);
			if(!empty($result)){
				$responseArray = array('status'=>SUCCESS,'message' => ResponseMessages::getStatusCodeMessage(200),'nearByUsers'=>$result);
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			}else {
				$responseArray = array('status'=>FAIL,'message'=>ResponseMessages::getStatusCodeMessage(114));
				$response = $this->generate_response($responseArray);
				$this->response($response,OK);
			} 
		}		
	}
	// add and delete my favorite user
	function myFavorite_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $user_id 	= $this->authData->userId;
        $isFavorite = $this->post('isFavorite');
        $favUserId 	= $this->post('favUserId');
       	//check for delete
        if($isFavorite==0){
        	$where = array('user_id'=>$user_id,'favUserId'=>$favUserId);
        	//check for data exits
        	$apoimexist = $this->common_model->is_data_exists(FAVORITES, $where);
			if(empty($apoimexist)){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	        	$this->response($response);
	        }
        	$result = $this->common_model->deleteData(FAVORITES,$where);
        	//check for data delete yes or not
        	if($result){
        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(130));
        		$this->response($response);
        	}
       		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(118));
        	$this->response($response);

        }else{

        	$where = array('user_id'=>$user_id,'favUserId'=>$favUserId);
        	//check for data exits
        	$exist = $this->common_model->is_data_exists(FAVORITES, $where);
			if($exist){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(127));
	        	$this->response($response);
	    	}
        	$insertData['user_id']   = $user_id;
        	$insertData['favUserId'] = $favUserId;
        	$insertData['crd']	     = date('Y-m-d H:i:s');
       
        	$result = $this->common_model->insertData(FAVORITES,$insertData);
        	//check for data insert yes or not
        	if($result){

        		$where = array('userId'=>$favUserId,'isNotification'=>1);
	            $user_info_for = $this->common_model->getsingle(USERS,$where);
	            if($user_info_for){               
	                $registrationIds[] = $user_info_for->deviceToken; 
	                $title = lang('user_fav_noti');

	                $body_send  = $this->authData->fullName.' '.lang('user_fav_noti_msg'); //body to be sent with current notification
	                $body_save  = '[UNAME] '.lang('user_fav_noti_msg'); //body to be saved in DB
	                $notif_type = 'add_favorite';
	                $notify_for = $user_info_for->userId;                
	               
	                //send notification to user
	                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

	                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

	                $notif_msg['body'] = $body_save; //replace body text with placeholder text
	                //save notification

	                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$favUserId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
	                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
	                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
	            }

        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(126));
        		$this->response($response);
        	}
        		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
        		$this->response($response);
        }
       
	}
	//get my favorite user
	function getMyFavorite_get(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $offset  = $this->get('offset'); 
        $limit   = $this->get('limit');
        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit= 10;
        }
        
        //get favorite list
        $result = $this->user_model->getMyFaoriteList($offset,$limit);
        //check data exist
        if($result){
        	$response = array('status' => SUCCESS,'message'=>"OK",'favoriteList'=>$result);
        	$this->response($response);
        }else{
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        	$this->response($response);
        }
	}
	//friend request
	function friendRequest_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $this->form_validation->set_rules('requestFor', lang('req_for_user'), 'required');
        $this->form_validation->set_rules('type', lang('req_type'), 'required');
        $this->form_validation->set_rules('status', lang('req_status'), 'required');

        if($this->form_validation->run() == FALSE){ //validation fail

            $response = array('status' => FAIL, 'message' => validation_errors());
            $this->response($response);

        } else{
	        $user_id = $this->authData->userId;
	        $requestFor = $this->post('requestFor');
	        $type 		= $this->post('type');
	        $status 	= $this->post('status');

	        $where = array('requestBy'=>$user_id,'requestFor'=>$requestFor);
	       //type == add, for add new request type == edit, for edit existing request 
	        if($type=='add'){
	        	//check for data exist
	        	$exist = $this->common_model->is_data_exists(REQUESTS,$where);
		        if($exist){
		        	$response = array('status' => FAIL, 'message'=> ResponseMessages::getStatusCodeMessage(127));
			        $this->response($response);
		        }
		        $data['requestBy']  = $user_id;
		        $data['requestFor'] = $requestFor;
		        $data['crd'] 		= date('Y-m-d H:i:s');
		        //data insert in request table
		        $result = $this->common_model->insertData(REQUESTS,$data);
		        //check for data inserted yes or not
		     	if($result){

		     		$where = array('userId'=>$requestFor,'isNotification'=>1);
		            $user_info_for = $this->common_model->getsingle(USERS,$where);
		            if($user_info_for){               
		                $registrationIds[] = $user_info_for->deviceToken; 
		                $title = lang('frnd_req_noti');

		                $body_send  = $this->authData->fullName.lang('user_frnd_req_noti'); //body to be sent with current notification
		                $body_save  = '[UNAME]'.lang('user_frnd_req_noti'); //body to be saved in DB
		                $notif_type = 'friend_request';
		                $notify_for = $user_info_for->userId;                
		               
		                //send notification to user
		                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

		                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

		                $notif_msg['body'] = $body_save; //replace body text with placeholder text
		                //save notification

		                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$requestFor, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
		                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
		                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
		            }

	        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(133));
	        		$this->response($response);
	        	}
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
	        	$this->response($response);

	        }else{//for edit exist request
	        	
			    // status ==2 for accept request and status == 3 for reject/delete request
	        	if($status == 2){

	        		//check for data exist
		        	$where = array('requestBy'=>$requestFor,'requestFor'=>$user_id);
		        	$exist = $this->common_model->is_data_exists(REQUESTS,$where);
				    if(!$exist){
				        $response = array('status' => FAIL,'message' => ResponseMessages::getStatusCodeMessage(114));
					    $this->response($response);
				    }
	        		//FRIENDS
	       			$insertData['byId']  = $user_id;
	       			$insertData['forId'] = $requestFor;
	       			$insertData['crd']   = date('Y-m-d H:i:s');
	       			//delete request from request table
	       			$where  = array('requestBy'=>$requestFor,'requestFor'=>$user_id);
	       			$delete = $this->common_model->deleteData(REQUESTS,$where);
	       			//insert record in friend table
	       			$result = $this->common_model->insertData(FRIENDS,$insertData);
	       			//check data inserted in table
	       			if($result){

	       				$where = array('userId'=>$requestFor,'isNotification'=>1);
			            $user_info_for = $this->common_model->getsingle(USERS,$where);

			            if($user_info_for){   

			                $registrationIds[] = $user_info_for->deviceToken; 
			                $title = lang('req_acc_noti');

			                $body_send  = $this->authData->fullName.lang('user_frnd_acc_noti'); //body to be sent with current notification
			                $body_save  = '[UNAME]'.lang('user_frnd_acc_noti'); //body to be saved in DB
			                $notif_type = 'accept_request';
			                $notify_for = $user_info_for->userId;                
			               
			                //send notification to user
			                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

			                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

			                $notif_msg['body'] = $body_save; //replace body text with placeholder text
			                //save notification

			                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$requestFor, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
			                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
			                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
			            }

	       				$response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(132));
	        			$this->response($response);	
	       			}
	       			$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
	        		$this->response($response);
	 				
	        	}else{	// status == 3 for reject/delete request

	        		//check for data exist
		        	$where = array('requestBy'=>$user_id,'requestFor'=>$requestFor);
		        	$requestExist = $this->common_model->is_data_exists(REQUESTS,$where);

		        	if($requestExist){
		        		
		        		// cancle own sent request
		        		$delete = $this->common_model->deleteData(REQUESTS,$where);
		        		//check request delete yes or not
		       			if($delete){
			        		$response = array('status' => SUCCESS,'message' => ResponseMessages::getStatusCodeMessage(136));
					    	$this->response($response);
					    }
		        	}else{
		        		
			        	//delete request if rejected
		        		$where  = array('requestBy'=>$requestFor,'requestFor'=>$user_id);

		       			$delete = $this->common_model->deleteData(REQUESTS,$where);
		       			//check request delete yes or not
		       			if($delete){
			       			$response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(131));
			        		$this->response($response);	
		        		}
		        	}
	        		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(188));
	        		$this->response($response);
	        	}
	        }
        }
	}
	
	function getFriendList_get(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
            }

        $data['offset']  = $this->get('offset'); 
        $data['limit']   = $this->get('limit');
        $data['listType']   = $this->get('listType');
        $data['searchText'] = $this->get('searchText');
        $data['userId'] = $this->authData->userId;
        $data['eventId'] = !empty($this->get('eventId')) ? $this->get('eventId') : '';
       
        //get favorite list
        $result = $this->user_model->getFriendlist($data);
      
        if($result){
        	$response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(200),'requestCount'=>$result['requestCount'],'List'=>$result['list']);
		    $this->response($response);	
        }
        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	    $this->response($response);
	}

	//unfriend or delete friends
	function unfriend_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $user_id  = $this->authData->userId;
        $friendId = $this->post('friendId');
        $where    = array('friendId'=>$friendId);
        //check data exist 
        $exist    = $this->common_model->is_data_exists(FRIENDS,$where);
        if(!$exist){
		    $response = array('status' => FAIL, 'message'=> ResponseMessages::getStatusCodeMessage(114));
			$this->response($response);     
        }
        //delete record
        $delete = $this->common_model->deleteData(FRIENDS,$where);
        if($delete){
        	//check data deleted yes or not
        	$response = array('status'=> SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(130));
		    $this->response($response);	
        }
        $response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
	    $this->response($response);
	}

	// add and delete my like user
	function myLike_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $user_id 	= $this->authData->userId;
        $isLike = $this->post('isLike');
        $likeUserId 	= $this->post('likeUserId');
       	//check for delete
        if($isLike==0){
        	$where = array('user_id'=>$user_id,'likeUserId'=>$likeUserId);
        	//check for data exits
        	$apoimexist = $this->common_model->is_data_exists(LIKES, $where);
			if(empty($apoimexist)){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	        	$this->response($response);
	        }
        	$result = $this->common_model->deleteData(LIKES,$where);
        	//check for data delete yes or not
        	if($result){
        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(130));
        		$this->response($response);
        	}
       		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(118));
        	$this->response($response);

        }else{

        	$where = array('user_id'=>$user_id,'likeUserId'=>$likeUserId);
        	//check for data exits
        	$exist = $this->common_model->is_data_exists(LIKES, $where);
			if($exist){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(127));
	        	$this->response($response);
	    	}
        	$insertData['user_id']   = $user_id;
        	$insertData['likeUserId'] = $likeUserId;
        	$insertData['crd']	     = date('Y-m-d H:i:s');
       
        	$result = $this->common_model->insertData(LIKES,$insertData);
        	//check for data insert yes or not
        	if($result){

        		$where = array('userId'=>$likeUserId,'isNotification'=>1);
	            $user_info_for = $this->common_model->getsingle(USERS,$where);
	            if($user_info_for){              
	                $registrationIds[] = $user_info_for->deviceToken; 
	                $title = lang('like');

	                $body_send  = $this->authData->fullName.lang('user_like_profile_noti'); //body to be sent with current notification
	                $body_save  = '[UNAME]'.lang('user_like_profile_noti'); //body to be saved in DB
	                $notif_type = 'add_like';
	                $notify_for = $user_info_for->userId;                
	               
	                //send notification to user
	                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

	                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

	                $notif_msg['body'] = $body_save; //replace body text with placeholder text
	                //save notification

	                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$likeUserId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
	                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
	                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
	            }

        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(194));
        		$this->response($response);
        	}
    		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
    		$this->response($response);
        }       
	}

	//user logout
    function logout_get(){

        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR); //authentication failed
        }
        //empty device token on when user logged out
        $logout = $this->common_model->updateFields(USERS, array('deviceToken' =>''), array('userId'=>$this->authData->userId));
       
        $response = array('status'=>SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(137));
        
        $this->response($response);
    }

    //get my notification list
	function getNotificationList_get(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        
        $offset  = $this->get('offset'); 
        $limit   = $this->get('limit');
        $userId = $this->authData->userId;	
        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit= 50;
        }
        
        //get notification list
        $result = $this->user_model->getNotificationList($offset,$limit,$userId);
        //check data exist
        if($result){
        	$response = array('status' => SUCCESS,'message'=>ResponseMessages::getStatusCodeMessage(200),'notificationList'=>$result);
        	$this->response($response);
        }else{
        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        	$this->response($response);
        }
	}

	// update notification status for on / off
	function notificationStatus_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $user_id 	= $this->authData->userId;
        $status = $this->post('status');
        
       	//check for delete
        if($status==0){
        	$notiStatus = 0;

        }else{
        	$notiStatus = 1;        	
        } 
        $where = array('userId'=>$user_id);        	
        //update status
        $result = $this->common_model->updateFields(USERS, array('isNotification'=>$notiStatus),$where);
    	//check for data delete yes or not
    	if($result){
    		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(154));
    		$this->response($response);
    	}else{
    		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
			$this->response($response);
    	}		    
	}

    function getReviewList_get(){

    	//check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $userId     = $this->authData->userId;
        $offset     = $this->get('offset');
        $limit      = $this->get('limit');
        $listType   = $this->get('listType');  // review list type appointment, event

        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit = 10;
        }

        $where = array('by_user_id'=>$userId);
        $or_where = array('for_user_id'=>$userId);

        if($listType == 'appointment') {

			$whereType = array('reviewType'=>1);       // 1 for aapointment list	
        	$reviewList = $this->user_model->getReviewList($offset,$limit);

        }elseif ($listType == 'event') {

           	$whereType = array('reviewType'=>2);       // 2 for event list
            $reviewList = $this->user_model->getReviewList($offset,$limit);
        }

        if($reviewList){

        	$response = array('status' => SUCCESS, 'message'=>'OK','reviewList' => $reviewList);

        } else{

        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        }
        $this->response($response);
    }

    // update user's location
	function updateLocation_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }
        $userId 	= $this->authData->userId;
        $latitude 	= $this->post('latitude');
        $longitude 	= $this->post('longitude');
        $result = '';
        if(!empty($latitude) && !empty($longitude)){

        	$getFullAddress = getAddress($latitude,$longitude);

			$address = $city = $state = $country = '';
			if(!empty($getFullAddress)){

				$address 	= isset($getFullAddress['formatted_address']) ? $getFullAddress['formatted_address'] : '';	// get full address
				$city 		= isset($getFullAddress['city']) ? $getFullAddress['city'] : ''; 							// get city name
				$state 		= isset($getFullAddress['state']) ? $getFullAddress['state'] : ''; 							// get state name 
				$country 	= isset($getFullAddress['country']) ? $getFullAddress['country'] : ''; 						// get country name
			}

			$userData['latitude'] 			= $latitude;
			$userData['longitude'] 			= $longitude;
			$userData['address'] 		  	= $address;
			$userData['city'] 		  		= $city;
			$userData['state'] 		  		= $state;
			$userData['country'] 		  	= $country;
	     	
	        //update status
	        $result = $this->common_model->updateFields(USERS, $userData,array('userId'=>$userId));
        }
       	
    	//check for data delete yes or not
    	if($result){
    		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(154));
    		$this->response($response);
    	}else{
    		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
			$this->response($response);
    	}		    
	}

	//get education/work list
	function getAllListData_get(){

		$eduList 	= $this->user_model->getEducationList();
		$workList 	= $this->user_model->getWorkList();
		$intList 	= $this->user_model->getInterestList();
		
		$responseArray = array('status'=>SUCCESS,'message' =>ResponseMessages::getStatusCodeMessage(200),'educationList'=>$eduList,'workList'=>$workList,'interestList'=>$intList);
		$response = $this->generate_response($responseArray);
		$this->response($response,OK);
		
	}

	// add and delete my image like user
	function profileImgLike_post(){
		//check for auth
		if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $user_id 	= $this->authData->userId;
        $isLike 	= $this->post('isLike');
        $forUserId 	= $this->post('forUserId');
        $userImgId 	= $this->post('userImgId');

       	//check for delete
        if($isLike==0){

        	$where = array('like_by_user_id'=>$user_id,'like_for_user_id'=>$forUserId,'user_img_id'=>$userImgId);
        	//check for data exits
        	$apoimexist = $this->common_model->is_data_exists(PROFILE_IMAGE_LIKES, $where);
			if(empty($apoimexist)){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
	        	$this->response($response);
	        }
        	$result = $this->common_model->deleteData(PROFILE_IMAGE_LIKES,$where);
        	//check for data delete yes or not
        	if($result){
        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(130));
        		$this->response($response);
        	}
       		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
        	$this->response($response);

        }else{

        	$where = array('like_by_user_id'=>$user_id,'like_for_user_id'=>$forUserId,'user_img_id'=>$userImgId);
        	//check for data exits
        	$exist = $this->common_model->is_data_exists(PROFILE_IMAGE_LIKES, $where);
			if($exist){
	        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(127));
	        	$this->response($response);
	    	}
        	$insertData['like_by_user_id']   		= $user_id;
        	$insertData['like_for_user_id'] 		= $forUserId;
        	$insertData['user_img_id'] 				= $userImgId;
        	$insertData['crd']	     				= date('Y-m-d H:i:s');
       
        	$result = $this->common_model->insertData(PROFILE_IMAGE_LIKES,$insertData);
        	//check for data insert yes or not
        	if($result){

        		$where = array('userId'=>$forUserId,'isNotification'=>1);
	            $user_info_for = $this->common_model->getsingle(USERS,$where);
	            if($user_info_for){              
	                $registrationIds[] = $user_info_for->deviceToken; 
	                $title = "Profile Imgae Like";

	                $body_send  = $this->authData->fullName.' likes your profile image.'; //body to be sent with current notification
	                $body_save  = '[UNAME] likes your profile image.'; //body to be saved in DB
	                $notif_type = 'profile_img_like';
	                $notify_for = $user_info_for->userId;                
	               
	                //send notification to user
	                $this->notification_model->send_push_notification($registrationIds, $title, $body_send,$user_id,$notif_type);

	                $notif_msg = array('title'=>$title, 'body'=> $body_save,'type'=> $notif_type ,'sound'=>'default','referenceId'=>$user_id);

	                $notif_msg['body'] = $body_save; //replace body text with placeholder text
	                //save notification

	                $insertdata = array('notificationBy'=>$user_id, 'notificationFor'=>$forUserId, 'message'=>json_encode($notif_msg), 'notificationType'=>$notif_type, 'crd'=>datetime());
	                $notification_where = array('notificationFor'=>$user_info_for->userId,'notificationBy'=>$user_id,'notificationType'=>$notif_type);
	                $this->notification_model->save_notification(NOTIFICATIONS, $insertdata,$notification_where);
	            }

        		$response = array('status' => SUCCESS, 'message' => ResponseMessages::getStatusCodeMessage(194));
        		$this->response($response);
        	}
    		$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(118));
    		$this->response($response);
        }       
	}

	function getUserImgLikeList_get(){

    	//check for auth
        if(!$this->check_service_auth()){
            $this->response($this->token_error_msg(), SERVER_ERROR);  //authetication failed
        }

        $userId     	= $this->authData->userId;
        $offset     	= $this->get('offset');
        $limit      	= $this->get('limit');
        $userImgId   	= $this->get('userImgId'); 
        $forUserId   	= $this->get('forUserId'); 

        if(!isset($offset) || empty($limit)){
            $offset = 0; $limit = 100;
        }

        $list = $this->user_model->getUserImgLikeList($offset,$limit,$userImgId,$forUserId);

        if($list){

        	$response = array('status' => SUCCESS, 'message'=>'OK','list' => $list);

        } else{

        	$response = array('status' => FAIL, 'message' => ResponseMessages::getStatusCodeMessage(114));
        }
        $this->response($response);
    }

} //end of class
