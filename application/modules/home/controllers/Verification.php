<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verification extends CommonFront {

	function __construct() {
        parent::__construct();
        $this->load->model('Verification_model');
        date_default_timezone_set('Asia/Kolkata');
        $this->load_language_files();
    }

    function index(){
        
        $this->check_user_session();   
        $data_val = $this->common_model->getAll('country','iso','ASC','phonecode,iso');
        $userId = $this->session->userdata('userId');

        $data_val['profile'] = $this->common_model->usersDetail($userId);
        $this->load->front_render('verification',$data_val,'');  
    }

    function checkCNO(){

        $isCheck = $this->Verification_model->checkCNO($this->input->get('contactNo'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function contactVerification(){

        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $data = array(
            'contactNo' =>  $this->input->post('contactNo'),
            'countryCode' => $this->input->post('countryCode'),
            'OTP' => (rand(10, 99)).(rand(11, 99))
        );
        $this->session->set_userdata("otp",$data['OTP']);
        $isAdded = $this->Verification_model->verifyNo($data);
        
        echo json_encode($isAdded);
    }

    // to match verification code
    function matchVerificationCode(){

       // $otp = $this->input->post('code1').$this->input->post('code2').$this->input->post('code3').$this->input->post('code4');
        $otp = $this->input->post('otpcode');

        if(empty($otp)){
            $response = array('status'=>0,'msg'=> lang('mobile_verify_code'));
            echo json_encode($response);exit;
        }

        $data['contactNo']     = $this->input->post('contactNo');
        $data['countryCode']   = $this->input->post('countryCode');
        $data['otpVerified']   = 1;

        $generatedCode = $this->session->userdata("otp");

        if(!empty($otp) && !empty($generatedCode)){

            if($otp == $generatedCode){

                //update data
                $appointData = $this->common_model->updateFields(USERS, $data,array('userId'=>$this->session->userdata('userId')));
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

    // to verify id with hand
    function verifyIdWithHand(){

        //check for auth
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }   

        $this->load->model('image_model');

        if(empty($_FILES['idWithHand']['name'])){
            $response = array('status'=>0,'msg'=> lang('face_id_verify_require'));
            echo json_encode($response);exit;
        }

        $idData = array();
        $idWithHand = '';
        $userId = $this->session->userdata('userId');

        $folder = 'idProof';

        if(!empty($_FILES['idWithHand']['name'])){
            $idWithHand = $this->image_model->updateMedia('idWithHand',$folder);
        }

        if(!empty($idWithHand) && is_array($idWithHand)){
            $response = array('status'=>0,'msg'=>$idWithHand['error']);
            echo json_encode($response);exit;
        }

        $idData['idWithHand']       = $idWithHand ? $idWithHand : '';
        $idData['isVerifiedId']     = 0;
            
        //check data exist
        $where = array('userId'=>$userId);
        $userExist = $this->common_model->is_data_exists(USERS, $where);
        if(!empty($userExist)){

            $this->common_model->updateFields(USERS,$idData,array('userId'=>$userId));
            $response = array('status' => 1, 'msg' => lang('id_proof_sent'));
            echo json_encode($response);exit; 
        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response);exit; 
        }
      
    } //end function

    // to verify face
    function faceVerification() {

        //check for auth
        $auth_res = $this->check_ajax_auth();
        if($auth_res!==TRUE){
            echo $auth_res;  //auth failed redirect user to home/login
            exit;
        }

        $userId = $this->session->userdata('userId');
        $imgCount = $this->common_model->get_total_count(USERS_IMAGE,array('user_id'=>$userId));
        if($imgCount == 0){

            $res['status']  = 0;
            $res['msg']     = lang('img_req_to_verify');
            echo json_encode($res); exit;
            return;
        }

        $this->load->model('image_model');

        if(empty($_FILES['faceImage']['name'])){
            $response = array('status' => 0, 'msg' => lang('face_id_verify_require'));
            echo json_encode($response);exit;
            return; 
        }

        $faceData = array();
        $faceImage = '';

        $folder = 'faceProof';

        if(!empty($_FILES['faceImage']['name'])){

            $this->load->library('face_plus');
            // start detect image
            $imgFile1['name']       = $_FILES['faceImage']['name'];
            $imgFile1['type']       = $_FILES['faceImage']['type'];
            $imgFile1['tmp_name']   = $_FILES['faceImage']['tmp_name'];
            $imgFile1['size']       = $_FILES['faceImage']['size'];

            $megabytes = $this->common_model->convertByteToMb($imgFile1['size']); // get image size in mb
            $imageUrl = $compressImage ='';

            if($megabytes > 2){

                $compressImage = $this->image_model->upload_n_compress('faceImage',$folder);

                if(!empty($compressImage) && is_array($compressImage)){
                    //$response = array('status'=>0,'msg'=>$compressImage['error']);
                    $response = array('status'=>0,'msg'=>lang('face_detect_common_msg'));
                    echo json_encode($response);exit;
                    return; 
                }

                $imageUrl = base_url().'uploads/faceProof/'.$compressImage;
            }

            if($imgFile1['tmp_name'] != ''){
                
                if(!empty($imageUrl)){

                    $isDetected     = $this->face_plus->detectFaceImageUrl($imageUrl);

                }else{

                    $isDetected     = $this->face_plus->detectFaceImageFile($imgFile1);
                }  

                $detectedData   =  '';
                
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
                    $response = array('status'=>0,'msg'=>$detectedData);
                    echo json_encode($response);exit();
                    return;
                }

                $detail = $this->common_model->usersImage($userId);  

                if(!empty($detail)){

                    $isCompared = '';
                    $i = 1;

                    foreach ($detail as $key => $value) {

                        sleep(5);
                        $imgFile2     = $value->image; 

                        if(!empty($imageUrl)){

                            $iscompare      = $this->face_plus->compareFaceUrl($imageUrl,$imgFile2);

                        }else{

                            $iscompare      = $this->face_plus->compareFace($imgFile1,$imgFile2);
                        }                        

                        if(isset($iscompare->confidence) && !empty($iscompare->confidence)){

                            if($iscompare->confidence >= 80){
                                
                                $isCompared = 1;

                                break;                                
                            }                            
                        }
                        $i++;
                    }

                    if($isCompared == ''){

                        $response = array('status' => 0, 'msg' => lang('trusted_face_img_require'));
                        echo json_encode($response);exit();
                        return;
                    }                    
                }
            } 
            // end detect image

            $faceImage = $this->image_model->updateMedia('faceImage',$folder);
        }

        if(!empty($faceImage) && is_array($faceImage)){
            $response = array('status'=>0,'msg'=>$faceImage['error']);
            echo json_encode($response);exit;
            return; 
        }

        $faceData['faceImage']        = $faceImage ? $faceImage : '';
        $faceData['isFaceVerified']   = '1';
            
        //check data exist
        $where = array('userId'=>$userId);

        $userExist = $this->common_model->is_data_exists(USERS, $where);

        if(!empty($userExist)){

            $this->common_model->updateFields(USERS,$faceData,array('userId'=>$userId));
            $response = array('status' => 1, 'msg' => lang('face_verify_success'));
            echo json_encode($response);exit;
            return; 

        }else{
            $response = array('status' => 0, 'msg' => lang('something_wrong'));
            echo json_encode($response);exit; 
            return; 
        }
      
    } //end function

} // End Of Class