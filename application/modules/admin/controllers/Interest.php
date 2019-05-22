<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Interest extends CommonBack {


    function __construct() {
        parent::__construct();
        $this->load->model('Interest_model');
    }

    function interestList(){
        $data['interest'] = $this->common_model->get_total_count(INTERESTS,array('type'=>0));
        $this->load->admin_render('interestList',$data,'');
    }

    function get_interest_list_ajax(){

        $this->load->model('Interest_list_model');
        $this->Interest_list_model->set_data(INTERESTS,array(null,'interestId','interest','type','status'),array('interest'),array('interestId'=>'DESC'),array('type'=>0));
        $list = $this->Interest_list_model->get_list();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $interest) {
       
            $action ='';
        $no++;
        $row = array();
        $row[] = $no;
        $row[] = display_placeholder_text(wordwrap($interest->interest,37,"<br>\n")); 
        //$row[] = display_placeholder_text(wordwrap($user->email,37,"<br>\n"));

        
        if($interest->status == 1) { $row[] =  '<p class="text-success">Active</p>'; } else { $row[] =  '<p  class="text-danger">Inactive</p>'; }        

        $clk_event = "statusFn('".INTERESTS."','interestId','".$interest->interestId."','".$interest->status."')";

        if($interest->status == 1){ $title = 'Inactive'; $icon = INACTIVE_ICON; } else{ $title = 'Active'; $icon = ACTIVE_ICON; }

        $action = '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$clk_event.'"  title="'.$title.'">'.$icon.'</a>';
        
        $update_event = "editFn('interest','updateInterest','".$interest->interestId."')";
        $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$update_event.'"  title="Update">'.EDIT_ICON.'</a>';
        //$link = base_url('admin/users/caretakerProfile/'.$user->userId);
        
        // $link = base_url('admin/users/userProfile/'.$user->userId);
        //$action .= '<a href="#"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
        $row[] = $action;
        $data[] = $row;

        //$_POST['draw']='';
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Interest_list_model->count_all(),
            "recordsFiltered" => $this->Interest_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);

    }

    function updateInterest(){
        //load view
        $id = $this->input->post('id');
        $getReord['record'] = $this->common_model->getsingle(INTERESTS,array('interestId'=>$id),'interest,interestId');
        $this->load->view('update_interest',$getReord);
    }

    function updateInterestData(){

        $this->form_validation->set_rules('interest', 'Interest', 'required');

        if ($this->form_validation->run() == FALSE){ 
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url()); 
            echo json_encode($response); 
        } else { 
            $id = $this->input->post('id');

             //check for unique name skipping current category name
            $interest = $this->input->post('interest');

            $where = array('interestId !=' => $id , 'interest' => $interest); 

            $result = $this->common_model->getsingle(INTERESTS, $where);

            if(!empty($result)){
                $response = array('status' => 0, 'message' => 'Interest already exist');  //category name already exists
                echo json_encode($response); die;
            }

            $update_where = array('interestId'=>$id);
            $cat_id = $this->common_model->updateFields(INTERESTS, array('interest'=>$interest), $update_where);  //update category data
            $response = array('status' => 1, 'message' =>'Interest updated successfully.', 'url'=>base_url('admin/interest/interestList')); //success msg
            echo json_encode($response);
        }
    }


    function activeInactive(){

        $id = $this->input->post('id');   
        $status = $this->Interest_model->activeInactive($id);

        if($status['message'] == 'active'){
            $data = array('status'=>1,'message'=>'Interest activated successfully');
        }else{
            $data = array('status'=>0,'message'=>'Interest inactivated ');
        }
        echo json_encode($data);
    }

    function checkRecord(){
        $isCheck = $this->Interest_model->checkRecord($this->input->get('interest'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function addInterest(){
        
        $interest =  ltrim($this->input->post('interest'));

        $is_exist = $this->common_model->is_data_exists(INTERESTS, array('interest'=>$interest));
        if($is_exist){
            $response = array('status' => 0, 'message' => 'Interest already exist'); //fail- already exist
            echo json_encode($response); exit;
        }
    
        $isAdd = $this->common_model->insertData(INTERESTS,array('interest'=>$interest));
        $response = array('status' => 1, 'message' => 'Interest added successfully'); //interest added successfully
        echo json_encode($response);
    }

    // education module

    function checkEduRecord(){
        $isCheck = $this->Interest_model->checkEduRecord($this->input->get('education'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function checkEduInSpRecord(){
        $isCheck = $this->Interest_model->checkEduInSpRecord($this->input->get('education'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function addEducation(){
        
        $education =  ltrim($this->input->post('education'));
        $eduInSpanish =  ltrim($this->input->post('eduInSpanish'));

        $is_exist = $this->common_model->is_data_exists(EDUCATION, array('education'=>$education,'eduInSpanish'=>$eduInSpanish));
        if($is_exist){
            $response = array('status' => 0, 'message' => 'Education already exist'); //fail- already exist
            echo json_encode($response); exit;
        }
    
        $isAdd = $this->common_model->insertData(EDUCATION,array('education'=>$education,'eduInSpanish'=>$eduInSpanish));
        $response = array('status' => 1, 'message' => 'Education added successfully'); //interest added successfully
        echo json_encode($response);
    }

    function educationList(){
        $data['education'] = $this->common_model->get_total_count(EDUCATION);
        $this->load->admin_render('educationList',$data,'');
    }

    function get_education_list_ajax(){

        $this->load->model('Interest_list_model');
        $this->Interest_list_model->set_data(EDUCATION,array(null,'eduId','eduInSpanish','education','status'),array('education','eduInSpanish'),array('eduId'=>'DESC'));
        $list = $this->Interest_list_model->get_list();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $education) {
       
        $action ='';
        $no++;
        $row = array();
        $row[] = $no;
        $row[] = display_placeholder_text(wordwrap($education->education,37,"<br>\n")); 
        $row[] = $education->eduInSpanish ? display_placeholder_text(wordwrap($education->eduInSpanish,37,"<br>\n")) : 'NA';        
        
        
        $update_event = "editFn('interest','updateEducation','".$education->eduId."')";
        $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$update_event.'"  title="Update">'.EDIT_ICON.'</a>';
        //$link = base_url('admin/users/caretakerProfile/'.$user->userId);
        
        // $link = base_url('admin/users/userProfile/'.$user->userId);
        //$action .= '<a href="#"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
        $row[] = $action;
        $data[] = $row;

        //$_POST['draw']='';
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Interest_list_model->count_all(),
            "recordsFiltered" => $this->Interest_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);

    }

    function updateEducation(){
        //load view
        $id = $this->input->post('id');
        $getReord['record'] = $this->common_model->getsingle(EDUCATION,array('eduId'=>$id),'education,eduInSpanish,eduId');
        $this->load->view('update_education',$getReord);
    }

    function updateEducationData(){

        $this->form_validation->set_rules('education', 'Education', 'required');
        $this->form_validation->set_rules('eduInSpanish', 'Education in spanish', 'required');

        if ($this->form_validation->run() == FALSE){ 
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url()); 
            echo json_encode($response); 
        } else { 
            $id = $this->input->post('id');

            //check for unique name skipping current category name
            $education = $this->input->post('education');
            $eduInSpanish = $this->input->post('eduInSpanish');

            $where = array('eduId !=' => $id , 'education' => $education, 'eduInSpanish' => $eduInSpanish, ); 

            $result = $this->common_model->getsingle(EDUCATION, $where);

            if(!empty($result)){
                $response = array('status' => 0, 'message' => 'Education already exist');  //category name already exists
                echo json_encode($response); die;
            }

            $update_where = array('eduId'=>$id);
            $cat_id = $this->common_model->updateFields(EDUCATION, array('education'=>$education,'eduInSpanish'=>$eduInSpanish), $update_where);  //update category data
            $response = array('status' => 1, 'message' =>'Education updated successfully.', 'url'=>base_url('admin/interest/educationList')); //success msg
            echo json_encode($response);
        }
    }

    // work module

    function workList(){
        $data['work'] = $this->common_model->get_total_count(WORKS);
        $this->load->admin_render('workList',$data,'');
    }

    function get_work_list_ajax(){

        $this->load->model('Interest_list_model');
        $this->Interest_list_model->set_data(WORKS,array(null,'workId','name','nameInSpanish','status'),array('name'),array('workId'=>'DESC'));
        $list = $this->Interest_list_model->get_list();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $work) {
       
            $action ='';
        $no++;
        $row = array();
        $row[] = $no;
        $row[] = display_placeholder_text(wordwrap($work->name,37,"<br>\n")); 
        $row[] = $work->nameInSpanish ? display_placeholder_text(wordwrap($work->nameInSpanish,37,"<br>\n")) : 'NA';
        
        $update_event = "editFn('interest','updateWork','".$work->workId."')";
        $action .= '<a href="javascript:void(0)" class="on-default edit-row table_action" onclick="'.$update_event.'"  title="Update">'.EDIT_ICON.'</a>';
        //$link = base_url('admin/users/caretakerProfile/'.$user->userId);
        
        // $link = base_url('admin/users/userProfile/'.$user->userId);
        //$action .= '<a href="#"  class="on-default edit-row table_action" title="View user">'.VIEW_ICON.'</a>';
        $row[] = $action;
        $data[] = $row;

        //$_POST['draw']='';
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Interest_list_model->count_all(),
            "recordsFiltered" => $this->Interest_list_model->count_filtered(),
            "data" => $data,
        );
        //output to json format
       echo json_encode($output);

    }

    function updateWork(){
        //load view
        $id = $this->input->post('id');
        $getReord['record'] = $this->common_model->getsingle(WORKS,array('workId'=>$id),'name,workId,nameInSpanish');
        $this->load->view('update_work',$getReord);
    }

    function updateWorkData(){

        $this->form_validation->set_rules('work', 'Work in english', 'required');
        $this->form_validation->set_rules('nameInSpanish', 'Work in spanish', 'required');

        if ($this->form_validation->run() == FALSE){ 
            $requireds = strip_tags($this->form_validation->error_string()) ? strip_tags($this->form_validation->error_string()) : ''; //validation error
            $response = array('status' => 0, 'message' => $requireds , 'url' => base_url()); 
            echo json_encode($response); 
        } else { 
            $id = $this->input->post('id');

             //check for unique name skipping current category name
            $work = $this->input->post('work');
            $nameInSpanish = $this->input->post('nameInSpanish');

            $where = array('workId !=' => $id , 'name' => $work, 'nameInSpanish' => $nameInSpanish ); 

            $result = $this->common_model->getsingle(WORKS, $where);

            if(!empty($result)){
                $response = array('status' => 0, 'message' => 'Work already exist');  //category name already exists
                echo json_encode($response); die;
            }

            $update_where = array('workId'=>$id);
            $cat_id = $this->common_model->updateFields(WORKS, array('name'=>$work, 'nameInSpanish' => $nameInSpanish), $update_where);  //update category data
            $response = array('status' => 1, 'message' =>'Work updated successfully.', 'url'=>base_url('admin/interest/interestList')); //success msg
            echo json_encode($response);
        }
    }

    function checkWorkRecord(){
        $isCheck = $this->Interest_model->checkWorkRecord($this->input->get('work'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function checkWorkSPRecord(){
        $isCheck = $this->Interest_model->checkWorkSPRecord($this->input->get('work'));
        if($isCheck == FALSE){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    function addWork(){
        
        $work =  ltrim($this->input->post('work'));
        $nameInSpanish =  ltrim($this->input->post('nameInSpanish'));

        $is_exist = $this->common_model->is_data_exists(WORKS, array('name'=>$work, 'nameInSpanish' => $nameInSpanish));
        if($is_exist){
            $response = array('status' => 0, 'message' => 'Work already exist'); //fail- already exist
            echo json_encode($response); exit;
        }
    
        $isAdd = $this->common_model->insertData(WORKS,array('name'=>$work, 'nameInSpanish' => $nameInSpanish));
        $response = array('status' => 1, 'message' => 'Work added successfully'); //interest added successfully
        echo json_encode($response);
    }
  
}
