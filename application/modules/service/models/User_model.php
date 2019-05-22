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
        $imgUrl = AWS_CDN_USER_THUMB_IMG;
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

			$images = $this->common_model->usersImage($id);
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
	        $imgUrl = AWS_CDN_USER_THUMB_IMG;         

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
        $imgUrl 	= AWS_CDN_USER_IMG_PATH; 

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
		$this->db->where(array('userId!='=>$data['userId'],'u.status'=>1));
		$this->db->group_by('u.userId');
		//$this->db->having('distance <= ' . $km);
		$this->db->order_by('distance');
		//$this->db->order_by('u.showTopPayment','desc');
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
        $userImg = AWS_CDN_USER_THUMB_IMG;
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
        
        $count = $this->common_model->countAllRequest($data);
        if($data['listType'] == 'friend'){

           $userData = $this->common_model->friendListCount($data);

           
        }elseif($data['listType'] == 'request'){

        	$userData = $this->common_model->requestListCount($data);
            
        }else{
            return false;//invalid type
        }
            
        return array('list'=>$userData,'requestCount'=>$count);        
        
    }//End Function

    
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
        $userImg = AWS_CDN_USER_THUMB_IMG;
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
	
} // End Of Class

