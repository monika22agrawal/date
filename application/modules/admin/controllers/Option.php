<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Option extends CommonBack {

    function __construct() {

        parent::__construct();
        
        if(!$this->session->userdata('id')) {
            redirect('admin'); 
        }
        $this->form_validation->CI =& $this;  //required for form validation callbacks in HMVC
        $this->validation_rules = array(); 
    }

    public function index() {

        if(!empty($this->session->userdata('id')))
            redirect('admin/dashboard');
  
        $this->load->view('login');
    }

    public function about_page(){

        $data['title'] = "About Page";
        $data['parent'] = "About Page";
        $data['content'] = $this->Common_model->optionDataRetrive(OPTIONS,array('option_name'=>'about_page'));
        $this->load->admin_render('about_page', $data, '');
    }    

    public function update_about_page(){

        $this->load->model('Common_model');

    	$v_rules = $this->validation_rules;

        $v_rules[] = array(
            'field' => 'contentsEng',
            'label' => 'Contents',
            'rules' => 'required|trim'
        );

        $this->form_validation->set_rules($v_rules);

        if ($this->form_validation->run() == FALSE){

            $messages = (validation_errors()) ? validation_errors() : ''; //validation error
            $response = array('status' => 0, 'message' => $messages);

        } else{

            $content = $this->input->post('content');
            $lang = $this->input->post('lang_type');
            
            if($content == 'about_page'){
                if($lang == 'spanish'){
                    $content = 'about_page_spanish';
                    $contentText = $this->input->post('contentsSp');
                }else{
                    $content = 'about_page_english';
                    $contentText = $this->input->post('contentsEng');
                }
            }

            if($content == 'pp_page'){
                if($lang == 'spanish'){
                    $content = 'pp_page_spanish';
                    $contentText = $this->input->post('contentsSp');
                }else{
                    $content = 'pp_page_english';
                    $contentText = $this->input->post('contentsEng');
                }
            }

            if($content == 'tc_page'){
                if($lang == 'spanish'){
                    $content = 'tc_page_spanish';
                    $contentText = $this->input->post('contentsSp');
                }else{
                    $content = 'tc_page_english';
                    $contentText = $this->input->post('contentsEng');
                }
            }

            $dataUpdate = array(
                'option_name' => $content,
                'option_value' => $contentText,
            );
            $response = $this->Common_model->optionDataUpdate(OPTIONS, $dataUpdate);  //insert category data

            if($response){
                
                $response = array('status' => 1, 'message' => 'Successfully Added'); //success msg

            } else{
                $response = array('status' => 0, 'message' => 'Something went wrong'); //Cat ID not found- error msg
            }  
        }  
        echo json_encode($response);
    }

    public function update_tc_page(){

        $this->load->model('Common_model');

        if (empty($_FILES['tc_file']['name'])){

            $response = array('status' => 0, 'message' => 'Please select pdf file for upload');
            echo json_encode($response); die();

        } else{
            // set path to store uploaded files
            $config['upload_path'] = TC_PDF;
            // set allowed file types
            $config['allowed_types'] = 'pdf';
            // set upload limit, set 0 for no limit
            $config['max_size']    = 0;
     
            // load upload library with custom config settings
            $this->load->library('upload', $config);
            
            // if upload failed , display errors
            if (!$this->upload->do_upload('tc_file'))
            {
                $response = array('status' => 0, 'message' => $this->upload->display_errors());
                echo json_encode($response); die;
            }

            if(!empty($this->input->post('exist_file'))){

                $path = TC_PDF.$this->input->post('exist_file');

                if(file_exists($path)){

                    unlink($path);
                }
            }
            
            $content = $this->input->post('content');
            $dataUpdate = array(
                'option_name' => $content ,
                'option_value' => $this->upload->data()['file_name'],
            );
            $response = $this->Common_model->optionDataUpdate(OPTIONS, $dataUpdate);  //insert category data

            if($response){

                $response = array('status' => 1, 'message' => 'Successfully Added', 'url' => $content); //success msg

            } else{
                $response = array('status' => 0, 'message' => 'Something went wrong'); //Cat ID not found- error msg
            }
        }  
        echo json_encode($response);
    }

}
