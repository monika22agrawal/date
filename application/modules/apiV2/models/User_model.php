<?php 
class User_model extends CI_Model {

	//Generate token for user
	function _generate_token()
	{
		$this->load->helper('security');
		$salt = do_hash(time().mt_rand());
		$new_key = substr($salt, 0, 20);
		return $new_key;
	}
	
	//Function for check provided token is valid or not
	function isValidToken($authToken)
	{
		$this->db->select('*');
		$this->db->where('authToken',$authToken);
		if($query = $this->db->get('users')){
			if($query->num_rows() > 0){
				return $query->row();
			}
		}		
		return FALSE;
	}

	function inviteAllFriends($limit,$start,$searchArray){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;
        $newData = array(); 
        //$userId = !empty($this->session->userdata('userId')) ? $this->session->userdata('userId') : '0';
        // for miles 6371 & for km 3959
        $km = 50;

        if(!empty($searchArray['latitude']) && !empty($searchArray['longitude'])){
            $lat = $searchArray['latitude'];
            $long = $searchArray['longitude'];
            $this->db->select('(case
             	when( MIN(uImg.userImgId) IS NULL)
             	THEN "'.$defaultImg.'"
             	ELSE 
                (SELECT 
                	(case 
                        when( image = "" OR  image IS NULL)
                        THEN "'.$defaultImg.'"
                        when(  image !="" AND isSocial =1)
                        THEN  image
                        ELSE
                        concat("'.$imgUrl.'", image)
                       END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as image,COALESCE(w.name,"NA") as work,u.userId,u.fullName,COALESCE(u.address,"NA") as address,u.latitude,u.longitude,u.showOnMap,u.gender,u.mapPayment,u.showTopPayment, ( 6371 * acos( cos( radians( '.$lat.'  ) ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$long.') ) + sin( radians('.$lat.') ) * sin( radians( latitude ) ) ) ) AS distance');  
	        $this->db->having('distance <= ' . $km);

        }else{

            $this->db->select('
            	(
            		case
             			when( MIN(uImg.userImgId) IS NULL)
             				THEN "'.$defaultImg.'"
             			ELSE 
                			(SELECT 
                				(
                					case 
	                        			when( image = "" OR  image IS NULL)
	                        				THEN "'.$defaultImg.'"
	                        			when(  image !="" AND isSocial =1)
	                        				THEN  image
	                        			ELSE
	                        				concat("'.$imgUrl.'", image)
                       				END 
                       			) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            		END 
            	) as image,
            	COALESCE(w.name,"NA") as work,
            	u.userId, u.fullName, u.address, u.latitude, u.longitude, u.showOnMap, u.gender, u.mapPayment, u.showTopPayment
            ');
	    }

	    $this->db->from(USERS.' as u');
        $this->db->join(USERS_IMAGE.' as uImg','u.userId = uImg.user_id','left');
        $this->db->join(USERS_WORK.' as uwm','u.userId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        if(!empty($searchArray['gender'])){
            ($searchArray['gender'] == '1' || $searchArray['gender'] == '2' || $searchArray['gender'] == '3') ? $this->db->where(array('u.gender'=>$searchArray['gender'])) : '';
        }
            
        //($val == '1') ? $this->db->where(array('u.showOnMap'=>'1')) : '';
        (!empty($searchArray['searchName'])) ? $this->db->like(array('u.fullName'=>trim($searchArray['searchName']))) : '';

        $this->db->where(array('u.status'=>1));

        if($this->session->userdata('front_login') == true && $this->session->userdata('userId') != ''){
            $this->db->where('u.userId !=',$this->session->userdata('userId'));
        }

        $this->db->group_by('u.userId');
        $this->db->limit($limit,$start);
        $this->db->order_by('u.showTopPayment','desc');
        $this->db->order_by('u.userId','desc');

        $req = $this->db->get();

        //echo $this->db->last_query();die;
        if($req->num_rows()){

            $this->load->model('User_model');

            $detail =  $req->result();

            foreach ($detail as $k => $value) {

                $newData[$k] = array(

                    'userId' 			=> $value->userId,
                    'fullName' 			=> $value->fullName,
                    'address' 			=> $value->address,
                    'latitude'	 		=> $value->latitude,
                    'longitude' 		=> $value->longitude,
                    'showOnMap' 		=> $value->showOnMap,
                    'work'				=> $value->work,
                    'gender' 			=> $value->gender,
                    'profileImage'		=> $value->image,
                    'showTopPayment'	=> $value->showTopPayment,
                    'mapPayment'		=> $value->mapPayment,
                    'isAppointment'		=> $this->User_model->checkAppointment($value->userId)
                );                                       
            }
        } 
        
        return $newData;
	}

	// number varification and otp send
	function verifyNo($data){
		
		$this->load->library('twilio');
		$from 		= '+34931071610';
		$to 		= $data['countryCode'].$data['contactNo'];
		$message 	= 'Your apoim verification code is : '.$data['OTP'];
		$response 	= $this->twilio->sms($from, $to, $message);
		
		if($response->IsError){
			return  array('status'=>0,'error'=>$response->ErrorMessage);
		}else{
			// update number to user table

			$this->common_model->updateFields(USERS, array('countryCode' =>$data['countryCode'],'contactNo' =>$data['contactNo']), array('userId'=>$this->authData->userId));

			return  array('status'=>1,'otp'=>$data['OTP'],'countryCode'=>$data['countryCode'],'contactNo'=>$data['contactNo']);
		}
	}

	function getUserVerificationStatus($userId){

		$imgUrl = AWS_CDN_IDPROOF_IMG_PATH;
		$FaceImgUrl = AWS_CDN_FACE_VERIFY_IMG_PATH;

		$req = $this->db->select('
			otpVerified, emailVerified, isVerifiedId,
			(
				case 
	            	when( idWithHand = "" OR idWithHand IS NULL) 
	            THEN ""
	            	ELSE
	            		concat("'.$imgUrl.'",idWithHand) 
	            END 
	        ) as idWithHand,
	        isFaceVerified, 
	        (
				case 
	            	when( faceImage = "" OR faceImage IS NULL) 
	            THEN ""
	            	ELSE
	            		concat("'.$FaceImgUrl.'",faceImage) 
	            END 
	        ) as faceImage
	    ')->where(array('userId'=>$userId))->get(USERS);
	    
		if($req->num_rows()){

			return $req->row();
		}
		return false;
	}

	function userInfo($id){

		$u_id = $this->authData->userId;
		$bizUserId = !empty($id) ? $id : $u_id;
		$res  = $this->db->select('*')->where(array('userId'=>$id))->get(USERS);

		if($res->num_rows()){
			
			$result = $res->row();

			if(!empty($id) && $u_id != $id){
	            //check data exist
	            $where = array('visit_by_id'=>$u_id,'visit_for_id'=>$id);
	            $isExist = $this->common_model->is_data_exists(VISITORS, $where);
	            if(empty($isExist)){
	                // to insert visitors record for showing total visitor
	               $this->common_model->insertData(VISITORS,array('visit_by_id'=>$u_id,'visit_for_id'=>$id)); 
	            }
	        }
			
			/*if (!empty($result->profileImage) && filter_var($result->profileImage, FILTER_VALIDATE_URL) === false) {
				$result->profileImage = base_url().UPLOAD_FOLDER.'/profile/'.$result->profileImage;
			}*/
			$imgUrl = AWS_CDN_IDPROOF_IMG_PATH;
			$FaceImgUrl = AWS_CDN_FACE_VERIFY_IMG_PATH;
			$result->address 	= !empty($result->address) ? $result->address : 'NA';
			$result->idWithHand = !empty($result->idWithHand) ? $imgUrl.$result->idWithHand : '';
			$result->faceImage = !empty($result->faceImage) ? $FaceImgUrl.$result->faceImage : '';
			$this->db->select('w.*,uw.*');
			$this->db->from(WORKS.' as w');
			$this->db->join(USERS_WORK.' as uw','uw.work_id = w.workId');
			$this->db->where(array('uw.user_id'=>$id));
			
			$userWork = $this->db->get()->row_array();

			if(!empty($userWork['name'])){
				
				$result->work = $userWork['name'];
				$result->nameInSpanish = $userWork['nameInSpanish'];
				$result->workId = $userWork['workId'];
			}else{
				
				$result->work = '';
				$result->nameInSpanish = '';
				$result->workId = '';
			}

			$this->db->select('e.*,ue.*');
			$this->db->from(EDUCATION.' as e');
			$this->db->join(USERS_EDUCATION.' as ue','ue.edu_id = e.eduId');
			$this->db->where(array('ue.user_id'=>$id));
			
			$userEdu = $this->db->get()->row_array();
			
			if(!empty($userEdu['education'])){
				
				$result->education = $userEdu['education'];
				$result->eduInSpanish = $userEdu['eduInSpanish'];
				$result->eduId = $userEdu['eduId'];
			}else{				
				$result->education = '';
				$result->eduInSpanish = '';
				$result->eduId = '';
			}
			
			$this->db->select('GROUP_CONCAT(i.interest),i.interestId,uim.*');
			$this->db->from(INTERESTS.' as i');
			$this->db->join(USERS_INTEREST_MAPPING.' as uim','uim.interest_id = i.interestId');
			$this->db->where(array('uim.user_id'=>$id));

			$userInterest = $this->db->get()->row_array();
			
			if(!empty($userInterest['GROUP_CONCAT(i.interest)'])){
				
				$result->interest = $userInterest['GROUP_CONCAT(i.interest)'];
			}else{				
				$result->interest = '';
			}
			
			//check for request send or recived
			$query = $this->db->query('SELECT * FROM '.REQUESTS.' WHERE requestBy = "'.$id.'" AND requestFor ="'.$u_id.'" OR requestBy = "'.$u_id.'" AND requestFor = "'.$id.'" ');
			$isRequest = $query->row();
			if($isRequest){
				// if isRequest = 1= send and 2= recieved and 0 = not send
				if($isRequest->requestBy == $u_id)
				{
					$result->isRequest = "1"; //request send
					$result->isFriend  = "0"; //not friend
				}else{
					$result->isRequest = "2"; //request receved
					$result->isFriend  = "0"; //not friend
				}
				
			}else{
				//check for friend
				$query = $this->db->query('SELECT * FROM '.FRIENDS.' WHERE byId = "'.$id.'" AND forId ="'.$u_id.'" OR byId = "'.$u_id.'" AND forId = "'.$id.'" ');
				$isFriend = $query->row();
				if($isFriend){
					//if isfriend = 1 for friend and 0 = not friend
					$result->isRequest = "0"; //request not send 
					$result->isFriend  = "1"; //  is friend 
				}else{
					$result->isRequest = "0"; //request not send 
					$result->isFriend  = "0"; //not friend
				}
			}

			//check for apointment
			$query = $this->db->query('SELECT * FROM '.APPOINTMENTS.' WHERE isFinish = 0 AND isDelete = 0 AND appointById = "'.$id.'" AND appointForId ="'.$u_id.'" OR isFinish = 0 AND isDelete = 0 AND appointById = "'.$u_id.'" AND appointForId = "'.$id.'" ');
			
			$isAppointment = $query->row();
			// print_r($isAppointment);die;
			if($isAppointment){
				// if isAppointment = 1 for send appointment and 2 for receved apointment 0 no appiontment
				if($isAppointment->appointById == $u_id){

					$result->isAppointment = "1" ;//send appointment
				}else{
					$result->isAppointment = "2"; //receved appointment
				}

			}else{
				$result->isAppointment = "0"; // no appointment
			}

			//check for isfavorite user 
			$this->db->select('favId');
			$this->db->from(FAVORITES);
			$this->db->where(array('user_id'=>$this->authData->userId,'favUserId'=>$id));
			$isFaverite = $this->db->get()->row();
			if($isFaverite){
				//isFaverite = 1 for feverite and 0 for not feveritr
				$result->isFavorite ="1";
			}else{
				$result->isFavorite ="0";
			}
			
			//check for islike user 
			$this->db->select('likeId');
			$this->db->from(LIKES);
			$this->db->where(array('user_id'=>$this->authData->userId,'LikeUserId'=>$id));
			$isLike = $this->db->get()->row();
			if($isLike){
				//isFaverite = 1 for like and 0 for not like
				$result->isLike ="1";
			}else{
				$result->isLike ="0";
			}

			$friendCount = $this->db->query('SELECT count(friendId) as totalFriends FROM '.FRIENDS.' WHERE byId = "'.$u_id.'" OR forId ="'.$u_id.'" ');
			$result->totalFriends = $friendCount->row()->totalFriends;
			
			// visit count
            $visitCount = $this->db->query('SELECT count(visitId) as totalVisits FROM '.VISITORS.' WHERE visit_for_id ="'.$u_id.'" ');
            $result->visit = $visitCount->row()->totalVisits;

			$likeCount = $this->db->query('SELECT count(likeId) as totalLikes FROM '.LIKES.' WHERE likeUserId ="'.$id.'" ');
			$result->totalLikes = $likeCount->row()->totalLikes;

			$ratingCount = $this->db->query('SELECT ROUND(AVG(rating)) as totalRating FROM '.REVIEW.' WHERE for_user_id ="'.$id.'" GROUP BY for_user_id ');
			
			$result->totalRating = isset($ratingCount->row()->totalRating) ? $ratingCount->row()->totalRating : '0';

			$images = $this->usersImage($id);
			$result->profileImage = $images;

			//check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BANK_ACCOUNT_DETAILS, array('user_id'=>$u_id));
            $result->bankAccountStatus = 0;
            if(!empty($userPaymentExist)){
                $result->bankAccountStatus = 1;
            }

            //check data exist
            $userPaymentExist = $this->common_model->is_data_exists(BUSINESS, array('user_id'=>$bizUserId));
            $result->isBusinessAdded = '0';
            if(!empty($userPaymentExist)){
                $result->isBusinessAdded = '1';
            }

			$result->profileUrl = base_url('home/user/userDetail/').encoding($id).'/?id='.base64_encode($id);
		
			// for notification isRead 1	
			$notType = array('add_like','add_favorite','friend_request','accept_request');

			$this->db->where('notificationBy',$id);
			$this->db->where_in('notificationType',$notType);
			$this->db->update(NOTIFICATIONS,array('isRead'=>1));
			
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
            ) as image, '.USERS_IMAGE.'.userImgId,
            (
                case
                    when( image = "" OR image IS NULL)
                        THEN "'.$defaultImg.'"
                    when( image !="" AND isSocial = 1)
                        THEN image
                    ELSE
                        image
                END 
            ) as imgName'

        )
        ->order_by(USERS_IMAGE.'.userImgId','DESC')
        ->get_where(USERS_IMAGE,array('user_id' => $userId))
        ->result();
//pr($image);
        return $image;
    }

	function updateProfile($userData,$eduId,$workId,$interestId){

		//array_shift($image);		
		$this->db->where('userId',$this->authData->userId);
		$this->db->update('users',$userData);

		/*$imgData = array();
		if(is_array($image) && !empty($image)){
			
			$i = 0;
			foreach($image as $val) {
				
				$imgData[$i]['image'] = $val;
				$imgData[$i]['user_id'] = $this->authData->userId;					

				$this->db->insert(USERS_IMAGE,$imgData[$i]);
				
				$i++;
			}
		}*/

		if(!empty($interestId)){

			$checkInt = $this->db->select('*')->where(array('user_id'=>$this->authData->userId))->get(USERS_INTEREST_MAPPING);
			$this->db->where('user_id',$this->authData->userId);
			$this->db->delete(USERS_INTEREST_MAPPING);
			
			$newdata = array();
			$i = 0;
			foreach (explode(',', $interestId) as $value) {

					$interest = $this->common_model->is_id_exist(INTERESTS,"interest",$value);

					if(!empty($interest)){
						
						$newdata[$i] = array(
						'interest_id'=>$interest->interestId,
						'user_id'=>$this->authData->userId
						);
					}else{
						
						$insert['interest'] = $value;
						$insert['type']		= 1;
						$insert['crd'] 		= date('Y-m-d H:i:s');
						$insert['upd'] 		= date('Y-m-d H:i:s');
						// $intrest_id 		= $this->common_model->insertData(INTERESTS,$insert);

						$this->db->insert(INTERESTS, $insert);
        				$interest_id = $this->db->insert_id();
        				//print_r($intrest_id);die;
						$newdata[$i] = array(
							'interest_id'=>$interest_id,
							'user_id'=>$this->authData->userId
							);
					}

				$i++;
			}
			$this->db->insert_batch(USERS_INTEREST_MAPPING,$newdata);			
		}

		if(!empty($eduId)){

			$this->db->where('user_id',$this->authData->userId);
			$this->db->delete(USERS_EDUCATION);
			$this->db->insert(USERS_EDUCATION,array('edu_id'=>$eduId,'user_id'=>$this->authData->userId));
		}

		if(!empty($workId)){

			$this->db->where('user_id',$this->authData->userId);
			$this->db->delete(USERS_WORK);
			$this->db->insert(USERS_WORK,array('work_id'=>$workId,'user_id'=>$this->authData->userId));
		}

		$reqResult = $this->userInfo($this->authData->userId);
			
		return $reqResult;

	}//end function

	function uploadUserImage($userData){

		$insertId = $this->common_model->insertData(USERS_IMAGE,$userData);

		if($insertId){

			$image = array();
	        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
	        $imgUrl = AWS_CDN_USER_IMG_PATH;         

	        $this->db->select('(case 
	            when( image = "" OR image IS NULL) 
	            THEN "'.$defaultImg.'"
	            when( image !="" AND isSocial = 1) 
	            THEN image
	            ELSE
	            concat("'.$imgUrl.'",image) 
	           END ) as image,userImgId');
	        $this->db->from(USERS_IMAGE);
	        $this->db->where(array('user_id'=>$userData['user_id'],'userImgId'=>$insertId));
	        $query  = $this->db->get();
	        $image = $query->row();

	        return $image;
		}
		return false;
	}

	function getPassword(){

        $res = $this->db->select('password')->get_where(USERS,array('userId'=>$this->authData->userId));
        return $res->row();
    }

	function changePassword($newPassword){
        
        $password['password'] = $newPassword ;
        $this->db->where('userId',$this->authData->userId);
        $checkUpdate = $this->db->update(USERS,$password);

        if($checkUpdate > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    function nearByUsers($data){

    	// for miles 6371 & for km 3959
        $km = 50;  

    	$defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH; 

        $whereNewUser = '';
        if(!empty($data['newUsers']) && $data['newUsers'] == 3){
            $whereNewUser = "( u.crd` >= curdate() - INTERVAL 7 DAY)";
        }
		
		$this->db->select('
			(
				case 
	            	when( a1.appointById = '.$data['userId'].')
	            		THEN 1
	            	when( a2.appointForId = '.$data['userId'].') 
	            		THEN 2
	            	ELSE
	            		0
	           	END 
	        ) as isAppointment,
           	u.userId, u.fullName, u.age, u.gender, u.address, u.latitude, u.longitude,
           	(
           		case
					when( MIN(uImg.userImgId) IS NULL)
						THEN "'.$defaultImg.'"
					ELSE  /*userImgId not empty get image using userImgId */
	                	(SELECT 
	                		(case 
	                            when( image = "" OR  image IS NULL)
	                            	THEN "'.$defaultImg.'"
	                            when(  image !="" AND isSocial =1)
	                            	THEN  image
	                            ELSE
	                            	concat("'.$imgUrl.'", image)
                           	END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
             	END 
            ) as profileImage,
            u.showOnMap, u.mapPayment, u.showTopPayment, ( 6371 * acos( cos( radians( '.$data['latitude'].'  ) ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('.$data['longitude'].') ) + sin( radians('.$data['latitude'].') ) * sin( radians( u.latitude ) ) ) ) AS distance');

		$this->db->from(USERS.' as u');
		$this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = u.userId ','left');

		$this->db->join(APPOINTMENTS.' as a1','a1.appointForId = u.userId AND a1.isFinish = 0 AND a1.isDelete = 0','left');
		$this->db->join(APPOINTMENTS.' as a2','a2.appointById = u.userId AND a2.isFinish = 0 AND a2.isDelete = 0','left');
		   
		if( $data['showMe']==1 || $data['showMe']==2 || $data['showMe']==3 ){
			$this->db->where('u.gender',$data['showMe']);
		}
		if(!empty($data['ageStart'])&&!empty($data['ageEnd']))
		{
			$this->db->where('u.age BETWEEN '.$data['ageStart'].' and '.$data['ageEnd']);
		}
		(!empty($whereNewUser)) ? $this->db->where($whereNewUser) : '';
		$this->db->where('userId!=',$data['userId']);
		$this->db->group_by('u.userId');
		$this->db->having('distance <= ' . $km);
		//$this->db->order_by('distance');
		$this->db->order_by('u.showTopPayment','desc');
        $this->db->order_by('u.userId','desc');
		$data = $this->db->get();
		//lq();
		$newData = array();
		
		if($data->num_rows()){
			$newData =  $data->result();
		}
		
		return $newData;
	}

	//get myFavorite user list
	function getMyFaoriteList($offset,$limit){

		$user_id = $this->authData->userId;	
		$defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;
		$this->db->select('f.*,u.fullName,
			(case
             	when( MIN(uImg.userImgId) IS NULL) 
             	THEN "'.$defaultUserImg.'" 
             	ELSE  
                (SELECT 
                	(case 
                        when( image = "" OR  image IS NULL) 
                        	THEN "'.$defaultUserImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            concat("'.$userImg.'", image) 
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage');
		$this->db->from(FAVORITES.' as f');
		$this->db->join(USERS.' as u','u.userId = f.favUserId','left');
		$this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = f.favUserId','left');
		$this->db->where(array('f.user_id'=>$user_id));
		$this->db->limit($limit, $offset);
        $this->db->order_by('favId','DESC');
        $this->db->group_by('u.userId');
        $query  = $this->db->get();
		$result = $query->result();
		foreach ($result as $rows) {
			//GET WORK NAME 
			$work = $this->getUserWork($rows->favUserId);
			//check for work exist
			if(!empty($work)){
				$rows->workName =  $work[0]->workName;
			}else{
				$rows->workName = '';
			}			
		}

		return $result;
	}
	//get work name
	function getUserWork($user_id){
		$where=array('user_id'=>$user_id);
		
		$id = $this->common_model->getsingle(USERS_WORK,$where);
		//check for work id exist 
		if(!empty($id)){
			
			$this->db->select('name as workName')->from(WORKS);
			$this->db->where('workId',$id->work_id);
			$query  = $this->db->get();
			$result = $query->result();
			return $result;
		}
		return FALSE;
	}

	// get friend list and request list
	function getFriendlist($data){

        if(!isset($data['offset']) || empty($data['limit'])){
            $data['offset'] = 0; $data['limit']= 10; 
        }        
        
        $count = $this->countAllRequest($data);
        if($data['listType'] == 'friend'){

           $userData = $this->friendListCount($data);

           
        }elseif($data['listType'] == 'request'){

        	$userData = $this->requestListCount($data);
            
        }else{
            return false;//invalid type
        }
            
        return array('list'=>$userData,'requestCount'=>$count);        
        
    }//End Function

    // get all friend's record
    function friendList($data){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            uf.friendId, IF(uf.byId = "'.$data['userId'].'",COALESCE(wf.name,""),COALESCE(w.name,"")) as work, 
            IF(uf.byId = "'.$data['userId'].'",uf.forId,uf.byId) as userId,
            IF(uf.byId = "'.$data['userId'].'",u2.fullName,u1.fullName) as fullName,
            IF(uf.byId = "'.$data['userId'].'",u2.gender,u1.gender) as gender,
            IF(uf.byId = "'.$data['userId'].'",u2.eventInvitation,u1.eventInvitation) as eventInvitation,            
            (
                case

                when (uf.byId = "'.$data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$data['userId'].'" && uImg.image = "") 
                    THEN "'.$defaultUserImg.'"
            
                when (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)

                when (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                    THEN IF(uf.byId = "'.$data['userId'].'",concat("'.$userImg.'",ufImg.image),concat("'.$userImg.'",uImg.image))
                ELSE
                    "'.$defaultUserImg.'"
                END
            ) as profileImage,
            (
                case

                when (uf.byId = "'.$data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$data['userId'].'" && uImg.image = "") 
                    THEN "'.$defaultUserImg.'"
            
                when (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)

                when (uf.forId = "'.$data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                    THEN IF(uf.byId = "'.$data['userId'].'",ufImg.image,uImg.image)
                ELSE
                    "'.$defaultUserImg.'"
                END
            ) as webProfileImage
        ');

        $this->db->from(FRIENDS.' as uf');

        $this->db->join(USERS.' as u1','uf.byId = u1.userId'); 
        $this->db->join(USERS.' as u2','uf.forId = u2.userId');

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = uf.byId','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = uf.forId','left');

        $this->db->join(USERS_WORK.' as uwm','uf.byId = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $this->db->join(USERS_WORK.' as uwf','uf.forId = uwf.user_id','left');
        $this->db->join(WORKS.' as wf','uwf.work_id = wf.workId','left');

        $this->db->group_start();
            $this->db->where('uf.forId',$data['userId']);
            $this->db->or_where('uf.byId',$data['userId']);
        $this->db->group_end();
        
        $where = array('u1.status'=>'1','u2.status'=>'1');

        $this->db->where($where);

        if(!empty($data['searchText'])){

            $this->db->group_start();
                $this->db->group_start();
                    $this->db->like('u1.fullName',$data['searchText'],'after');
                    $this->db->where('u1.userId !=',$data['userId']);
                $this->db->group_end();
                $this->db->or_group_start();
                    $this->db->like('u2.fullName',$data['searchText'],'after');
                    $this->db->where('u2.userId !=',$data['userId']);
                $this->db->group_end();
            $this->db->group_end();
        }           
        
        $this->db->order_by('u1.fullName','asc');
        $this->db->order_by('u2.fullName','asc');
        $this->db->group_by('u1.userId');
        $this->db->group_by('u2.userId');            
    }

    // get list for pagination
    function friendListCount($data){

        $this->friendList($data);
        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();

            // for notification isRead 1
            $notType = array('accept_request');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return $userData;
        }
        return array();
    }

    // get all record of requests
    function requestRecord($data){

        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;

        $this->db->select('
            COALESCE(w.name,"") as work, u.userId, u.fullName,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultUserImg.'"
                    ELSE  
                        (SELECT (case 
                            when( image = "" OR  image IS NULL)
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial =1)
                                THEN  image
                            ELSE
                                concat("'.$userImg.'", image)
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as profileImage,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultUserImg.'"
                    ELSE  
                        (SELECT (case 
                            when( image = "" OR  image IS NULL)
                                THEN "'.$defaultUserImg.'"
                            when(  image !="" AND isSocial =1)
                                THEN  image
                            ELSE
                                image
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
                END 
            ) as webProfileImage
        ');
            
        $this->db->from(USERS.' as u');

        $this->db->join(REQUESTS.' as reqf','reqf.requestBy = u.userId'); 

        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = reqf.requestBy','left');

        $this->db->join(USERS_WORK.' as uwm','reqf.requestBy = uwm.user_id','left');
        $this->db->join(WORKS.' as w','uwm.work_id = w.workId','left');

        $where = array('u.status'=>'1','reqf.requestStatus'=>1,'reqf.requestFor'=>$data['userId']);
        $this->db->where($where);
        !empty($data['searchText']) ? $this->db->like('u.fullName',$data['searchText'],'after') : '';
        
        $this->db->order_by('u.fullName','asc');
        $this->db->group_by('u.userId');
    }

    // get list for pagination
    function requestListCount($data){

        $this->requestRecord($data);

        $this->db->limit($data['limit'],$data['offset']);

        $query = $this->db->get();
        if($query->num_rows() >0){

            $userData = $query->result();
            // for notification isRead 1
            $notType = array('friend_request');
            $this->db->where('notificationFor',$data['userId']);
            $this->db->where_in('notificationType',$notType);
            $this->db->update(NOTIFICATIONS,array('isRead'=>1));
            return $userData;
        }
        return array();
    }

    // count of request
    function countAllRequest($data){

        $this->requestRecord($data);
        $query = $this->db->get();                    
                
        return $query->num_rows();       
    }

    
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

	//get myFavorite user list
	function getUserImgLikeList($offset,$limit,$userImgId,$forUserId){

		$user_id = $this->authData->userId;	
		$defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;
		$this->db->select('proImg.*,u.fullName,
			(case
             	when( MIN(uImg.userImgId) IS NULL) 
             	THEN "'.$defaultUserImg.'" 
             	ELSE  
                (SELECT 
                	(case 
                        when( image = "" OR  image IS NULL) 
                        	THEN "'.$defaultUserImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            concat("'.$userImg.'", image) 
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage');
		$this->db->from(PROFILE_IMAGE_LIKES.' as proImg');
		$this->db->join(USERS.' as u','u.userId = proImg.like_by_user_id','left');
		$this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = proImg.like_by_user_id','left');
		$this->db->where(array('proImg.like_for_user_id'=>$forUserId,'proImg.user_img_id'=>$userImgId));
		$this->db->limit($limit, $offset);
        $this->db->order_by('ImgLikeId','DESC');
        $this->db->group_by('u.userId');
        $query  = $this->db->get();
		$result = $query->result();
	
		return $result;
	}

	//get notification list on app side
    function getNotificationList($offset,$limit,$userId){
        
        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;
       
        $this->db->select('n.notId,n.isRead,n.notificationBy,n.notificationFor,n.referenceId,n.message,n.notificationType,n.crd,u.fullName');
        $this->db->from(NOTIFICATIONS.' as n');
        $this->db->join(USERS.' as u','u.userId = n.notificationBy','left');
        $this->db->where(array('n.notificationFor'=>$userId));
        $this->db->limit($limit,$offset);
        $this->db->order_by('n.notId','DESC');
        $req  = $this->db->get();
        
        //lq();
        $result = $req->result();
        if($req->num_rows()){           
            
            foreach($result as $k=>$v){
                $notif_payload = json_decode($v->message);
                
                //if notification is related to post then get event name
                if($v->notificationType == 'create_event' || $v->notificationType == 'join_event' || $v->notificationType=='event_payment' || $v->notificationType=='share_event' || $v->notificationType=='companion_accept' || $v->notificationType=='companion_reject' || $v->notificationType=='companion_payment'){
                    //replace placeholder name with real event name
                    $notif_payload->body = $this->common_model->replace_event_placeholder_name($notif_payload->referenceId, $notif_payload->body);
                }
                
                //get fullName of user
                $notif_payload->body = $this->common_model->replace_user_placeholder_name($v->notificationBy, $notif_payload->body);
                
                $result[$k]->message = $notif_payload;

                $img = $this->db->select('(case
                when( MIN(uImg.userImgId) IS NULL) 
                THEN "'.$defaultUserImg.'" 
                ELSE  
                (SELECT 
                    (case 
                        when( image = "" OR  image IS NULL) 
                            THEN "'.$defaultUserImg.'"
                        when(  image !="" AND isSocial =1)
                            THEN  image
                        ELSE
                            concat("'.$userImg.'", image) 
                        END ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId))
            END ) as profileImage')->where(array('user_id'=>$v->notificationBy))->get(USERS_IMAGE.' as uImg')->row();
                
                $v->image = isset($img->profileImage) ? $img->profileImage : '';

                $v->timeElapsed = time_elapsed_string($v->crd); //add time_elapsed key to show time elapsed in user friendly string
            }
        }        
        return $result;

    } // End Function
	
} // End Of Class

