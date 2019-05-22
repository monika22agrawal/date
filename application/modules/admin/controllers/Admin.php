<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CommonBack {

    public $data = "";

    function __construct() {
        parent::__construct();        
    }

    public function index() {
        if(!empty($this->session->userdata('id')))
            redirect('admin/dashboard');
        
        $data['title'] = "Login | Register";
       
        $data['email'] = array('name' => 'email',
            'id' => 'email',
            'class'=> 'form-control',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email'),
            'placeholder' => 'Email Id',
        );
        $data['password'] = array('name' => 'password',
            'id' => 'password',
            'class'=> 'form-control',
            'type' => 'password',
            'placeholder' => 'Password',
        );
        $this->load->view('Login', $data);
        //$this->load->admin_render('login', $data, 'login_process');

    } //End Function

    /**
     * @method login
     * @description login authentication
     * @return array
     */
    public function login() {
        //$this->data['title'] = $this->lang->line('login_heading');
        if(!isset($_POST['email']) || !isset($_POST['password'])){
            redirect('admin/');
        }
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        
        if ($this->form_validation->run() == FALSE){

            $errors = validation_errors();
            $this->session->set_flashdata('login_err', $errors);
            redirect('admin','refresh');

        } else { 
            $data_val['email']      = $this->input->post('email');
            $data_val['password']   = $this->input->post('password'); 
           
            $isLogin = $this->common_model->isLogin($data_val, ADMIN);
            if($isLogin){
                $this->session->set_flashdata('success', 'User authentication successfully done!. ');
                redirect('admin/dashboard');
            }
            else{
                $error = 'Invalid email or password';
                $this->session->set_flashdata('login_err', $error);
                redirect('admin','refresh');
            }
        }

    } //End Function

    /**
     * @method logout
     * @description logout
     * @return array
     */
    public function logout() {

        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'Sign out successfully done! ');
        $response = array('status' => 1);
        echo json_encode($response);
        die;

    } //End Function
    
    public function dashboard() {

        if(empty($this->session->userdata('id')))
            redirect(site_url().'admin');
        
        $data['parent'] = "Dashboard";
        $data['title']  = "Dashboard";

        $table          = USERS;
        $data['count']  = $this->common_model->get_total_count($table);

        $data['payment_count']      = $this->common_model->get_total_count(PAYMENT_TRANSACTIONS);
        $data['event_count']        = $this->common_model->get_total_count(EVENTS);
        $data['apoinment_count']    = $this->common_model->get_total_count(APPOINTMENTS,array('isFinish'=>0,'isDelete'=>0));

        $this->load->admin_render('dashboard', $data, '');

    } //End Function

    public function updateContent(){

        $this->form_validation->set_rules('content', 'content', 'required');
        
        if ($this->form_validation->run() == FALSE){

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url('admin/about_us'));

        } else { 
             
            $additional_data = array(
                'content' => $this->input->post('content')
            );
      
            $table  = CONTENT;
            $where  = array('content_type' => 'about_us'); 
            $update = $this->common_model->updateData($table, $additional_data, $where);
            //pr($update);
            if($update > 0):
                $response = array('status' => 1, 'message' => 'updated successfully', 'url' => base_url('admin/about_us')); //success msg
            endif;

        }//End if

        echo json_encode($response);

    } //End Function

    /**
     * @method profile
     * @description profile display
     * @return array
     */
    //view admin profile
    function profile(){

        if(empty($this->session->userdata('id')))
            redirect(site_url().'admin');
        $data['parent'] = "Profile";
        $where = array('id' => $this->session->userdata('id')); 
        $table = ADMIN;

        $data['admin'] = $this->common_model->getsingle($table,$where); 
        $data['title'] = "Profile";
        $this->load->admin_render('adminProfile', $data, '');

    } //End function

    function aboutUs(){

        if(empty($this->session->userdata('id'))){
            redirect(site_url().'admin');
        }

        $data['parent'] = "About";
        $where = array('id' => $this->session->userdata('id'));
        $data['contentEng'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>'about_page_english'));
        $data['contentSp'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>'about_page_spanish'));
        $data['title'] = "About us";
        $this->load->admin_render('aboutus', $data, '');

    } //End function

    function privacyPolicy(){

        if(empty($this->session->userdata('id'))){
            redirect(site_url().'admin');
        }
        $data['title']   = "Privacy";
        $data['parent']  = "Privacy";
        $data['contentEng'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>'pp_page_english'));
        $data['contentSp'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>'pp_page_spanish'));
        $this->load->admin_render('privacy_policy',$data,'');

    } //End Function

    function termCondition(){

        if(empty($this->session->userdata('id'))){
            redirect(site_url().'admin');
        }

        $data['parent'] = "Terms";
        $where = array('id' => $this->session->userdata('id'));
        $data['contentEng'] = $this->common_model->getsingle(OPTIONS,array('option_name'=>'tc_page_english'));
        $data['contentSp'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>'tc_page_spanish'));
        $data['title'] = "Terms";
        $this->load->admin_render('terms_condition', $data, '');

    } //End function

    function contactUs(){

        if(empty($this->session->userdata('id')))
            redirect(site_url().'admin');
        $data['parent'] = "Contact us";
        $where = array('id' => $this->session->userdata('id')); 
        $data['content'] = $this->common_model->getsingle(OPTIONS,array('option_name'=>'contact_type'));
        $data['title'] = "Contact us";
        $this->load->admin_render('contactUs', $data, '');

    }//End function

    //change admin password
    function changePassword(){

        $this->load->library('form_validation');
        $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]',array('required'=>'Please enter current password','min_length'=>'Password Should be atleast 6 Character Long'));
        $this->form_validation->set_rules('npassword', 'new password', 'trim|required|matches[rnpassword]|min_length[6]',array('required'=>'Please enter new password','min_length'=>'Password Should be atleast 6 Character Long','matches'=>"Confirm password and new password doesn't match"));
        $this->form_validation->set_rules('rnpassword', 'retype new password ', 'trim|required',array('required'=>'Please retype new password'));

        $this->form_validation->set_error_delimiters('<div class="err_msg">', '</div>');
        if ($this->form_validation->run() == FALSE){ 

            $error = validation_errors(); 
            $res['status']=0; $res['message']= $error; 
            echo json_encode($res);      
        }else {
            
            $password =$this->input->post('password');
            $npassword =$this->input->post('npassword');
            $table  = ADMIN;
            $select = "password";
            $where = array('id' => $this->session->userdata('id')); 
            $admin = $this->common_model->getsingle($table,$where,$select);
            
            if(password_verify($password, $admin->password)){

                $set =array('password'=> password_hash($this->input->post('npassword') , PASSWORD_DEFAULT)); 
                $update = $this->common_model->updateFields($table, $set, $where);
                $res = array(); 
                $res['url']= base_url('admin/profile'); $res['status']=1; $res['message']='Password Updated successfully'; 
                echo json_encode($res); die;
                
            }else{
                $res['message']= "Your Current Password is Wrong !";
                echo json_encode($res); die;    
            }
        }

    } //End Function

    //update profile
    function updateProfile(){

        $this->form_validation->set_rules('name', 'Name', 'required|callback__alpha_dash_space');
        if ($this->form_validation->run() == FALSE){ 

            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url('admin/profile'));    
        } else { 
            
            $additional_data = array(
                'name' => $this->input->post('name')
            );
            $image = '';
            if(!empty($_FILES['image']['name'])):

                $this->load->model('image_model');
                $folder  = 'profile';
                $image = $this->image_model->updateMedia('image',$folder);
                if(isset($image['error']) && !empty($image['error'])){
                    $response = array('status' => 0, 'message' => $image['error']);
                    echo json_encode($response); die;
                }
            endif;
            if(!empty($image)){
                $additional_data['profileImage'] = $image;
            }
            $table = ADMIN;
            $where = array('id' => $this->session->userdata('id'));
            $update =  $this->common_model->updateFields($table, $additional_data, $where);
            $where_in = array('id' => $this->session->userdata('id'));
            $updated_session = $this->common_model->getsingle($table,$where_in);
            $session_data['name']   = $updated_session->name ;
            $session_data['profileImage']      = $updated_session->profileImage ;
            $this->session->set_userdata($session_data);
            $response = array('status' => 1, 'message' => 'Your profile updated successfully', 'url' => base_url('admin/profile')); //success msg
            
        }
        echo json_encode($response);

    } //End Function

    function _alpha_dash_space($str){

        if($str!=''){

            $res = ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? 0 : 1;

            if($res == '0'){

                $this->form_validation->set_message('_alpha_dash_space',"The Name accept Only alphabets ");
                return FALSE;

            }else{
                return TRUE;
            }

        }else{
            $this->form_validation->set_message('_alpha_dash_space',"The Name field is required");
            return FALSE;
        }

    } //End Function

} //End Class
