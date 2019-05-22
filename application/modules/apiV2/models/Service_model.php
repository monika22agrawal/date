<?php 
class Service_model extends CI_Model {

	//Generate token for user
	function _generate_token(){

		$this->load->helper('security');
		$salt = do_hash(time().mt_rand());
		$new_key = substr($salt, 0, 20);
		return $new_key;
	}
	
	//Function for check provided token is valid or not
	function isValidToken($authToken){

		$this->db->select('*');
		$this->db->where('authToken',$authToken);
		if($query = $this->db->get('users')){
			if($query->num_rows() > 0){
				return $query->row();
			}
		}		
		return FALSE;
	}

	function totalFriendCount($id){
        
        $friendCount = $this->db->query('SELECT count(friendId) as totalFriends FROM '.FRIENDS.' WHERE byId = "'.$id.'" OR forId ="'.$id.'" ');
        return $friendCount->row()->totalFriends;
    }

	function random_password( $length = 8 ) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,?";
	    $password = substr( str_shuffle( $chars ), 0, $length );
	    return $password;
	}

	function userInfo($id){

		$uId = $id['userId'];
		$res = $this->db->select('userId,fullName,birthday,gender,email,countryCode,contactNo,emailVerified,socialId,socialType,authToken,address,latitude,longitude,isProfileUpdate,mapPayment,showTopPayment,isNotification,stripeCustomerId,subscriptionId,subscriptionStatus,eventType,appointmentType')->where($id)->get(USERS);

		if($res->num_rows()){

			$result = $res->row();

			//$images = $this->common_model->usersImage($uId);
			
			//$result->profileImage = !empty($images) ? $images[0]->image : base_url().AWS_CDN_USER_PLACEHOLDER_IMG;

			$images = $this->usersImage($uId);
			$result->profileImage = $images;

			$friendCount = $this->db->query('SELECT count(friendId) as totalFriends FROM '.FRIENDS.' WHERE byId = "'.$uId.'" OR forId ="'.$uId.'" ');
			$result->totalFriends = $friendCount->row()->totalFriends;

			//check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, array('user_id'=>$id['userId']));
            $result->bankAccountStatus = "0";
            if(!empty($userPaymentExist)){
                $result->bankAccountStatus = "1";
            }

            //check data exist
            $userBizPaymentExist = $this->common_model->is_data_exists(BUSINESS, array('user_id'=>$id['userId']));
            $result->isBusinessAdded = '0';
            if(!empty($userBizPaymentExist)){
                $result->isBusinessAdded = '1';
            }

			return $result;
		} else {
			return false;
		}
	}

	 //get all user image from user_image table
    function usersImage($userId){
        
        $image = array();
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;

        $image = $this->db->select(

        USERS_IMAGE.'.image,userImgId,
            (
            SELECT
                COUNT(user_img_id) as totalLikes
                FROM '.PROFILE_IMAGE_LIKES.'
                WHERE user_img_id = userImgId
            ) as totalImglikes,

            (
            SELECT
                IF(like_by_user_id = "'.$userId.'","1","0")
                FROM '.PROFILE_IMAGE_LIKES.'
                WHERE user_img_id = userImgId
            ) as isLike,
            
            (
                case
                    when( '.USERS_IMAGE.'.image = "" OR '.USERS_IMAGE.'.image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( '.USERS_IMAGE.'.image !="" AND '.USERS_IMAGE.'.isSocial = 1)
                        THEN image
                    ELSE
                        concat("'.$imgUrl.'",image)
                END 
            ) as image, '.USERS_IMAGE.'.userImgId'
        )
        ->order_by(USERS_IMAGE.'.userImgId','DESC')
        ->get_where(USERS_IMAGE,array('user_id' => $userId))
        
        ->result();

        return $image;
    }
	
	//user registration
	function userRegistration($data,$userImgData) {

		$res = $this->db->select('userId')->where(array('email'=>$data['email'],'email !='=>''))->get(USERS);
		//check data exist or not
		if($res->num_rows() == 0) {

			if(!empty($data['socialId']) && !empty($data['socialType'])) {

				$check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);

				//check data exist using social id
				if($check->num_rows() == 1) {

					$id = $check->row();

					if($id->status == 1){

						//update divice token and type
						$this->db->where(array('userId'=>$id->userId));
						$this->db->update(USERS,array('authToken'=>$data['authToken'],'deviceToken'=>$data['deviceToken'],'deviceType'=>$data['deviceType']));
						$userDetail['data'] = $this->userInfo(array('userId'=>$id->userId));					
						$userDetail['regType'] = 'SL';

						return $userDetail;

					}else{
						return "NA"; // user iactive by admin
					}
					
				} else{
					
					//insert data into user table in social registration
					$this->db->insert(USERS,$data);
					$userId = $this->db->insert_id();

					if(!empty($userImgData['image'])){
						//insert user's image into user_image table in social registration
						$userImgData['user_id'] = $userId;
						$this->db->insert(USERS_IMAGE,$userImgData);
					}

					//get user detail
					$userDetail['data'] = $this->userInfo(array('userId'=>$userId));
					$userDetail['regType'] = 'SR';
					return $userDetail;			
				}
			}else{
				 
				//inser data in user table
				$this->db->insert(USERS,$data);
				$userId = $this->db->insert_id();

				//check data inserted yes or not
				if(empty($userId)){
					return "SGW";
				}
				
				if(!empty($userImgData['image'])){
					//insert user's image into user_image table in social registration
					$userImgData['user_id'] = $userId;
					$this->db->insert(USERS_IMAGE,$userImgData);
				}

				//get user detail from table
				$userDetail['data'] = $this->userInfo(array('userId'=>$userId));
	            								
				$userDetail['regType'] = 'NR';
				return $userDetail;
			}

		}else {
			
			//check social id or type
			if(!empty($data['socialId']) && !empty($data['socialType'])) {
				//get user info using socialid
				$check = $this->db->select('userId,status')->where(array('socialId'=>$data['socialId'],'socialType'=>$data['socialType']))->get(USERS);
				
				if($check->num_rows() == 1){
					//check data is exist 

					$id = $check->row();
					
					if($id->status == 1){
						
						$this->db->where(array('userId'=>$id->userId));
						$this->db->update(USERS,array('authToken'=>$data['authToken'],'deviceToken'=>$data['deviceToken'],'deviceType'=>$data['deviceType']));

						$userDetail['data'] = $this->userInfo(array('userId'=>$id->userId));
						$userDetail['regType'] = 'SL';
						return $userDetail;

					}else{
						return "NA";
					}
					
				} else{
					$this->db->where(array('userId'=>$res->row()->userId));
					$this->db->update(USERS,array('authToken'=>$data['authToken'],'deviceToken'=>$data['deviceToken'],'deviceType'=>$data['deviceType']));

					$userDetail['data'] = $this->userInfo(array('userId'=>$res->row()->userId));
					$userDetail['regType'] = 'SL';
					return $userDetail;
				}
			} else{
				return 'AE';
			}
		}
		return false;
	}

	//get user detail
	function userLogin($data,$authToken){

		$res = $this->db->select('userId,password,status,socialType')->where(array('email'=>$data['email']))->get(USERS);
		//print_r($data);die;
		if($res->num_rows() > 0){

			$result = $res->row();

			if(password_verify($data['password'],$result->password)){

				if($result->status == 1){//if user is active

					$update_data = array();
					$update_data['deviceToken'] = $data['deviceToken'];
					$update_data['deviceType'] = $data['deviceType'];
					$update_data['setLanguage'] = $data['setLanguage'];
					$update_data['authToken'] = $authToken;

					if(!empty($update_data['deviceToken'])){

						$this->db->update(USERS,array('deviceToken' => ''),array('deviceToken'=>$update_data['deviceToken']));

						$this->db->update(USERS,$update_data,array('userId'=>$result->userId));

						$userDetail = $this->userInfo(array('userId'=>$result->userId));

						return array('type'=>'LS','userDetail'=>$userDetail); //login successfull

					} else{

						$this->db->update(USERS,array('authToken'=>$data['authToken']),array('userId'=>$result->userId));
						$userDetail = $this->userInfo(array('userId'=>$result->userId));

						return array('type'=>'LS','userDetail'=>$userDetail); //login successfull
					}
				}else {
					return array('type'=>'NA','userDetail'=>array()); // not active
				}
			} elseif(empty($user->password)) {

				return array('type'=>'SL','userDetail'=>array(),'data'=>$result->socialType); //social login

			}else {
				return array('type'=>'WP','userDetail'=>array()); //wrong password
			}
		} 
		return false;
	}

    // email varification and random number sent to valid email send
	function verifyEmail($data){
		
		// load email library
		$this->load->library('smtp_email');

		$code = $data['code'];
		
		//set masssage and subject for mail
		
		$subject = "Welcome to Apoim";

        $userData['code'] = $code;
        
        $message  = $this->load->view('email/email_verification',$userData,TRUE);		            
		
        //send mail
        $isSend = $this->smtp_email->send_mail($data['email'],$subject,$message);
        								
		if($isSend == TRUE){
			
			return  array('status'=>1,'code'=>$code,'email'=>$data['email']);

		}else{
			return  array('status'=>0,'error'=>'Something went wrong,email is not sent');
		}
	}

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

	            $subject  = "Forgot Password";
	            
	            $message  = $this->load->view('email/forgot_password',$data,TRUE);

	            $check	  = $this->smtp_email->send_mail($to,$subject,$message);

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


    function getEducationList(){

		$getEdu = $this->db->select('eduId,education,eduInSpanish')->where(array('status'=>1))->order_by('education','ASC')->get(EDUCATION);
		if($getEdu->num_rows()){

			return $getEdu->result();
		}
		return false;	
	}

	function getWorkList(){

		$getWork = $this->db->select('workId,name,nameInSpanish')->where(array('status'=>1))->order_by('name','ASC')->get(WORKS);
		if($getWork->num_rows()){

			return $getWork->result();
		}
		return false;	
	}

	function getInterestList(){

		$getInt = $this->db->select('interestId,interest')->where(array('type'=>0,'status'=>1))->order_by('interest','ASC')->get(INTERESTS);
		if($getInt->num_rows()){

			return $getInt->result();
		}
		return false;	
	}
		
}

