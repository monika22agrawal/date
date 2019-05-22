<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tablelist extends CI_Model {

    var $column_order =array(null,'usr.fullName','usr.email','usr.contactNo','usr.status', 'usr.isVerifiedId', 'usr.idWithHand','usr.gender','uImg.image'); //set column field database for datatable orderable
    var $column_search = array('usr.fullName'); //set column field database for datatable searchable
    var $order = array('usr.userId' => 'DESC');  // default order

    var $group_by = 'usr.userId'; 

    public function __construct(){
        // Call the Model constructor
        parent::__construct();
    }

    function prepare_query(){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            usr.userId, usr.fullName, usr.email, usr.countryCode, usr.contactNo, usr.status, usr.isVerifiedId, usr.idWithHand,
            (
                case 
                    when( usr.gender = "" or usr.gender IS NULL) 
                        THEN "NA"
                    when( usr.gender !="" AND usr.gender = 1) 
                        THEN "Male"
                    when( usr.gender !="" AND usr.gender = 2) 
                        THEN "Female"
                    when( usr.gender !="" AND usr.gender = 3) 
                        THEN "Transgender"
                    ELSE 
                        "Both" 
                END ) as gender,
            (
                case
                    when( MIN(uImg.userImgId) IS NULL)
                        THEN "'.$defaultImg.'"
                    ELSE  /*userImgId not empty get image using userImgId */
                        (SELECT 
                            (
                                case 
                                    when( image = "" OR  image IS NULL)
                                        THEN "'.$defaultImg.'"
                                    when(  image !="" AND isSocial =1)
                                        THEN  image
                                    ELSE
                                        image
                                END 
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
                END 
            ) as image');

        $this->db->from(USERS.' as usr');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = usr.userId','left');    
    }

    //prepare post list query
    private function posts_get_query() {
        
        $this->prepare_query();
        $i = 0;
        foreach ($this->column_search as $emp) // loop column 
        {
            if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
                $_POST['search']['value'] = $_POST['search']['value'];
            } else
                $_POST['search']['value'] = '';

            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $this->db->group_start();
                    $this->db->like(($emp), $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like(($emp), $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
            }
            
            if(!empty($this->group_by)){
                $this->db->group_by($this->group_by);
            }

            if(isset($_POST['order'])) // here order processing
            {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } 
            else if(isset($this->order))
            {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
    }

    function get_list() {

        $this->posts_get_query();
        if(isset($_POST['length']) && $_POST['length'] < 1) {
            $_POST['length']= '10';
        } else
        $_POST['length']= $_POST['length'];
        
        if(isset($_POST['start']) && $_POST['start'] > 1) {
            $_POST['start']= $_POST['start'];
        }
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();  
        return $query->result();
    }

    function count_filtered() {

        $this->posts_get_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all() {

        $this->prepare_query();
        return $this->db->count_all_results();
    }


    /*id proof*/
    function id_prepare_query(){

        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_THUMB_IMG;

        $this->db->select('
            usr.userId, usr.fullName, usr.isVerifiedId, usr.idWithHand');

        $this->db->from(USERS.' as usr');
        $this->db->where(array('usr.idWithHand !='=>'','usr.isVerifiedId'=>0));
    }

    //prepare post list query
    private function id_get_query() {
        
        $this->id_prepare_query();
        $i = 0;
        foreach ($this->column_search as $emp) // loop column 
        {
            if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
                $_POST['search']['value'] = $_POST['search']['value'];
            } else
                $_POST['search']['value'] = '';

            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $this->db->group_start();
                    $this->db->like(($emp), $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like(($emp), $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
            }
            
            if(!empty($this->group_by)){
                $this->db->group_by($this->group_by);
            }

            if(isset($_POST['order'])) // here order processing
            {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } 
            else if(isset($this->order))
            {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
    }

    function get_id_list() {

        $this->id_get_query();
        if(isset($_POST['length']) && $_POST['length'] < 1) {
            $_POST['length']= '10';
        } else
        $_POST['length']= $_POST['length'];
        
        if(isset($_POST['start']) && $_POST['start'] > 1) {
            $_POST['start']= $_POST['start'];
        }
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();  
        return $query->result();
    }

    function count_id_filtered() {

        $this->id_get_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_id() {

        $this->id_prepare_query();
        return $this->db->count_all_results();
    }

}