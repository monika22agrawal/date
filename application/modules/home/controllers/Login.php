<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CommonFront {

    function __construct() {
        parent::__construct(); 
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Login_model');   
        $this->load_language_files();    
    }

    function index(){    

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $detail['front_scripts'] = array($load_custom_js.'login_registration.js');
        $this->load->front_render('login',$detail,'');
    }

    function registration(){ 

        $data_val = $this->common_model->getAll('country','iso','ASC','phonecode,iso');

        $ip = $this->Login_model->get_client_ip();

        $city_name = $this->Login_model->getCityNameByIpAddress($ip);

        if(!empty($city_name['city']) && !empty($city_name['latitude']) && !empty($city_name['longitude'])){
            
            $fullAddr = $city_name['city'].','.$city_name['region_name'].','.$city_name['country_name'];

            $this->session->set_userdata('address', $fullAddr);
            $this->session->set_userdata('city', $city_name['city']);
            $this->session->set_userdata('state', $city_name['region_name']);
            $this->session->set_userdata('country', $city_name['country_name']);
            $this->session->set_userdata('lat', $city_name['latitude']);
            $this->session->set_userdata('long', $city_name['longitude']);
        }

        $data_val['msg'] = $this->input->post('page');
        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $data_val['front_scripts'] = array($load_custom_js.'login_registration.js');
        $this->load->front_render('sign_up',$data_val);
    }

    function userRegister(){     
        
        $this->load->library('form_validation');
        $this->load->model('image_model');

        $socialId = $this->input->post('socialId');

        if(empty($socialId)){

            $this->form_validation->set_rules('password',lang('password_placeholder'),'trim|required|min_length[8]');
            $this->form_validation->set_rules('email', lang('email_placeholder'), 'trim|required|valid_email');
        }

        $this->form_validation->set_rules('fullName', lang('full_name'), 'trim|required');
        $this->form_validation->set_rules('gender', lang('gender'), 'required');
        $this->form_validation->set_rules('purpose', lang('purpose'), 'required');
        $this->form_validation->set_rules('dateWith', lang('date_with'), 'required');
        $this->form_validation->set_rules('birthday', lang('birthday_placeholder'), 'required');

        $gender = $this->input->post('gender');
        if($gender == '2'){
            $this->form_validation->set_rules('eventInvitation', lang('event_invitation'), 'required');
        }

        /* if(empty($_FILES['profileImage']['name'])){
            $this->form_validation->set_rules('profileImage','Profile image','required');
        }*/

        $socialId = $this->input->post('socialId');
        $socialType = $this->input->post('socialType');
        
        if($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url()); 
            echo json_encode($response);

        } else{

            $profileImage = $isSocial = $img_url = '';

            if(empty($socialId) && empty($socialType)){

                if(!empty($_FILES['profileImage']['name'])){
                   
                    $imgFile['name']       = $_FILES['profileImage']['name'];
                    $imgFile['type']       = $_FILES['profileImage']['type'];
                    $imgFile['tmp_name']   = $_FILES['profileImage']['tmp_name'];
                    $imgFile['size']       = $_FILES['profileImage']['size'];

                    $megabytes = $this->common_model->convertByteToMb($imgFile['size']); // get image size in mb
            
                    $imageUrl = $compressImage ='';
                    if($megabytes > 2){
                        $folder = 'faceProof';

                        $compressImage = $this->image_model->upload_n_compress('profileImage',$folder);                

                        if(!empty($compressImage) && is_array($compressImage)){
                            //$response = array('status'=>0,'msg'=>$compressImage['error']);
                            $response = array('status'=>0,'msg'=>lang('face_detect_common_msg'));
                            echo json_encode($response);exit;
                            return; 
                        }

                        $imageUrl = base_url().'uploads/faceProof/'.$compressImage;
                    }

                    if($imgFile['tmp_name'] != ''){

                        $this->load->library('face_plus');  
               
                        //$isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                        if(!empty($imageUrl)){

                            $isDetected     = $this->face_plus->detectFaceImageUrl($imageUrl);

                        }else{

                            $isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                        }

                        $detectedData   =  '';
                        //$detectedData   = isset($isDetected->faces) ? $isDetected->faces : '';
                        
                        if (isset($isDetected->faces)&&empty($isDetected->faces)){
                            $detectedData = lang('face_detect_msg');
                        }

                        if (isset($isDetected) && is_string($isDetected)){
                            $detectedData = lang($isDetected);
                        }

                        if(!empty($compressImage)){

                            $file_name = $compressImage;
                            $img_path = FCPATH.'uploads/faceProof/';
                            $this->image_model->unlinkFile($img_path, $file_name); //unlink image from server directory  
                        }                         

                        if(!empty($detectedData)){

                            //$response = array('status'=>0,'msg'=>$detectedData);
                            $response = array('status'=>0,'msg'=>lang('face_detect_common_msg'));
                            echo json_encode($response);exit();
                            return;
                        }

                        $folder = 'profile';
                        $profileImage = $this->image_model->updateMedia('profileImage',$folder);
                        $isSocial = 0;
                    }                    
                }

                if(!empty($profileImage) && is_array($profileImage)){
                    
                    $response = array('status'=>0,'msg'=>$profileImage['error']);
                    echo json_encode($response);exit();
                    return;
                }

            } else{
                
                $profileImage = $this->input->post('profileImage');
      
                if($profileImage && empty($_FILES['profileImage']['name'])){

                    $this->load->library('face_plus');  
               
                    $isDetected     = $this->face_plus->detectFaceImageUrl($profileImage);

                    $detectedData   =  '';
                    //$detectedData   = isset($isDetected->faces) ? $isDetected->faces : '';
                    
                    if (isset($isDetected->faces)&&empty($isDetected->faces)){
                        $detectedData = 'face_detect_msg';
                    }

                    if (isset($isDetected) && is_string($isDetected)){
                        $detectedData = $isDetected;
                    }

                    if(!empty($detectedData)){

                        $response = array('status'=>0,'msg'=>lang($detectedData));
                        echo json_encode($response);exit();
                        return;
                    }
                    $isSocial = 1;
                }

                if(!empty($_FILES['profileImage']['name'])){

                    $imgFile['name']       = $_FILES['profileImage']['name'];
                    $imgFile['type']       = $_FILES['profileImage']['type'];
                    $imgFile['tmp_name']   = $_FILES['profileImage']['tmp_name'];
                    $imgFile['size']       = $_FILES['profileImage']['size'];

                    $megabytes = $this->common_model->convertByteToMb($imgFile['size']); // get image size in mb
            
                    $imageUrl = '';

                    if($megabytes > 2){

                        $folder = 'faceProof';

                        $compressImage = $this->image_model->upload_n_compress('profileImage',$folder);                

                        if(!empty($compressImage) && is_array($compressImage)){

                            $response = array('status'=>0,'msg'=>$compressImage['error']);
                            echo json_encode($response);exit;
                            return; 
                        }

                        $imageUrl = base_url().'uploads/faceProof/'.$compressImage;
                    }

                    if($imgFile['tmp_name'] != ''){

                        $this->load->library('face_plus');
               
                        //$isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                        if(!empty($imageUrl)){

                            $isDetected     = $this->face_plus->detectFaceImageUrl($imageUrl);

                        }else{

                            $isDetected     = $this->face_plus->detectFaceImageFile($imgFile);
                        }

                        $detectedData   =  '';
                        //$detectedData   = isset($isDetected->faces) ? $isDetected->faces : '';
                        
                        if (isset($isDetected->faces)&&empty($isDetected->faces)){
                            $detectedData = 'face_detect_msg';
                        }

                        if (isset($isDetected) && is_string($isDetected)){
                            $detectedData = $isDetected;
                        }

                        $file_name = $compressImage;
                        $img_path = FCPATH.'uploads/faceProof/';
                        $this->image_model->unlinkFile($img_path, $file_name); //unlink image from server directory 

                        if(!empty($detectedData)){

                            $response = array('status'=>0,'msg'=>lang($detectedData));
                            echo json_encode($response);exit();
                            return;
                        }

                        $folder = 'profile';
                        $profileImage = $this->image_model->updateMedia('profileImage',$folder);
                        $isSocial = 0;
                    }                    
                }

                if(!empty($profileImage) && is_array($profileImage)){
                    
                    $response = array('status'=>0,'msg'=>$profileImage['error']);
                    echo json_encode($response);exit();
                    return;
                }                
            }

            $userImgData['image'] = $profileImage ? $profileImage : '';
            $userImgData['isSocial'] = $isSocial;
             
            $dataInsert = array(

                'fullName'      => $this->input->post('fullName'),
                'email'         => $this->input->post('email'),
                'password'      => ($this->input->post('password') && empty($socialId)) ? password_hash($this->input->post('password'), PASSWORD_DEFAULT) : '',
                'address'       => !empty($this->input->post('address')) ? $this->input->post('address') : '',
                'city'          => !empty($this->input->post('city')) ? $this->input->post('city') : '',
                'state'         => !empty($this->input->post('state')) ? $this->input->post('state') : '',
                'country'       => !empty($this->input->post('country')) ? $this->input->post('country') : '',
                'latitude'      => !empty($this->input->post('latitude')) ? $this->input->post('latitude') : '',
                'longitude'     => !empty($this->input->post('longitude')) ? $this->input->post('longitude') : '',
                'setLanguage'   => $this->session->userdata('language') ? $this->session->userdata('language') : 'english',
                'gender'        => $gender,
                'purpose'       => $this->input->post('purpose'),
                'dateWith'      => $this->input->post('dateWith'),
                'birthday'      => date('Y-m-d',strtotime($this->input->post('birthday'))),
                'socialId'      => !empty($socialId) ? $socialId : '',
                'socialType'    => !empty($socialType) ? $socialType : '',
                'emailVerified' => '1',
                'crd'           => date('Y-m-d H:i:s')
            );

            if($gender == '2'){
                $dataInsert['eventInvitation'] = $this->input->post('eventInvitation');
            }

            $diff = (date('Y') - date('Y',strtotime($this->input->post('birthday'))));
            $dataInsert['age']    = $diff;

            $dataInsert     = sanitize_input_text($dataInsert);
            $userImgData    = sanitize_input_text($userImgData);

            $isRegister     = $this->Login_model->userRegister($dataInsert,$userImgData);
           
            echo json_encode($isRegister);                
        }        
        
    } // End of function

    // to check user already register on not
    function checkSocial(){

        $data = array(
            'socialId'=>$this->input->post('socialId'),
            'socialType'=>$this->input->post('socialType'),
            'email'=>$this->input->post('email')
        );

        $isCheck = $this->Login_model->checkSocial($data);
        
        echo $isCheck;        
    }

    // to check email already register or not
    function checkEmail(){

        $isCheck = $this->Login_model->checkEmail($this->input->get('email'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    // generated verification code and sent into email
    function emailVerification(){

        $data = array(

            'email' =>  $this->input->post('email'),
            'code' => (rand(10, 99)).(rand(11, 99))
        );

        $this->session->set_userdata("verificationCode",$data['code']);
        $this->session->set_userdata("verificationEmail",$data['email']);

        $isAdded = $this->Login_model->verifyEmail($data);
        
        echo json_encode($isAdded);
    }

    // to match verification code
    function matchVerificationCode(){

        $otp = $this->input->post('otp');
        $email = $this->input->post('email');
        $generatedCode = $this->session->userdata("verificationCode");
        $userEmail = $this->session->userdata("verificationEmail");

        if(!empty($otp) && !empty($email) && !empty($generatedCode) && !empty($userEmail)){

            if(($otp == $generatedCode) && ($userEmail == $email)){
                $response = array('status' => 1, 'msg' => 'success.');
                echo json_encode($response);exit;
            }else{
                $response = array('status' => 0, 'msg' => lang('code_not_match'));
                echo json_encode($response);exit;
            }

        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response);exit;
        }        
    }

    function userLogin(){

        $data = array();
        $this->load->library('form_validation');
       
        $this->form_validation->set_rules('email', lang('email_placeholder'), 'required');
        $this->form_validation->set_rules('password', lang('password_placeholder'), 'required');
        
        if ($this->form_validation->run() == FALSE){
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url()); 
            echo json_encode($response);
        } else {
            
            $userData['email'] = $this->input->post('email');
            $userData['password'] = $this->input->post('password');
            
            $isLoggedIn = $this->Login_model->login($userData);

            if(is_string($isLoggedIn) && $isLoggedIn=="LS"){
                /*remember me*/
                $remember = $this->input->post('rem');

                if($remember == 1) {    // if user check the remember me checkbox

                    setcookie('email', $userData['email'], time()+60*60*24*100, "/");
                    setcookie('password', $userData['password'], time()+60*60*24*100, "/");

                } else {   // if user not check the remember me checkbox

                    setcookie('email', ' ', time()-60*60*24*100, "/");
                    setcookie('password', ' ', time()-60*60*24*100, "/");           
                }

                $response = array('status' => 1, 'message' => lang('login_success'), 'url' => base_url('home/nearByYou'));
                               
            } elseif(is_string($isLoggedIn) && $isLoggedIn == "IP"){
                
                $response = array('status' => 0, 'message' => lang('wrong_pwd'));

            }elseif(is_string($isLoggedIn) && $isLoggedIn == "IU"){

                $response = array('status' => 0, 'message' => lang('inactive_user_error'));

            }elseif(is_string($isLoggedIn) && $isLoggedIn == "IC"){

                $message = "Invalid Credential";
                $response = array('status' => 0, 'message' => lang('wrong_email_pwd'));

            }elseif(is_array($isLoggedIn) && $isLoggedIn['type'] == "SL"){

                $message = "Invalid Credential";
                $response = array('status' => 0, 'message' => lang('social_forgot_msg1').$isLoggedIn['data'].lang('social_forgot_msg2'));

            } else {
                
                $response = array('status' => 0, 'message' => lang('something_wrong'));
            }   
        }
        echo json_encode($response);
    }


    function forgotPassword(){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email',lang('email_placeholder'),'required|valid_email');

        if($this->form_validation->run() == FALSE){

            $data=array('status'=>0,'message'=>strip_tags(validation_errors()));
            echo json_encode($data);

        }else{

            $email = $this->Login_model->forgotPassword($this->input->post('email'));

            if(is_array($email) && $email['type'] == 'SS'){

                $data = array('status' => 1, 'message' => lang('pwd_sent_success'), 'url'=>base_url('home/login'));

            } elseif(is_array($email) && $email['type'] == 'NE'){

                $data = array('status' => 3, 'message' => lang('email_not_exist'), 'url'=>base_url('home/login'));

            }else if(is_array($email) && $email['type'] == 'SR') {

                $data = array('status' => 4, 'message' => lang('register_social_email').$email['socialType'].lang('account'), 'url'=>base_url('home/login'));

            }else{

                $data = array('status'=>2,'message'=> lang('something_wrong'), 'url'=>base_url('home/login'));
            }

            echo json_encode($data);
        }
    }    
}