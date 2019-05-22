<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Interest_list_model extends CI_Model {


   
    var $table , $column_order, $column_search , $order =  '';
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function set_data($data_table, $col_order, $col_search, $order_by ,$where=''){
        $this->column_order = $col_order;
        $this->column_search = $col_search;
        $this->table = $data_table;
        $this->where = $where;
        $this->order = $order_by;
    }
    private function _get_query()
    {
        $this->db->from($this->table);
        $i = 0;
        foreach ($this->column_search as $emp) // loop column 
        {
            if(isset($_POST['search']['value']) && !empty($_POST['search']['value'])){
            $_POST['search']['value'] = $_POST['search']['value'];
            } else{
                $_POST['search']['value'] = '';
            }
            

            if($_POST['search']['value']){ // if datatable send POST for search
            
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
        if(!empty($this->where))
            $this->db->where($this->where);
            
                
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
        $order = $this->order;
        // pr($order);
        $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    
    function get_list()
    {
        $this->_get_query();
        if(isset($_POST['length']) && $_POST['length'] < 1) {
            $_POST['length'] = '10';
        } else
        $_POST['length'] = $_POST['length'];
        
        if(isset($_POST['start']) && $_POST['start'] > 1) {
            $_POST['start']= $_POST['start'];
        }
        $this->db->limit($_POST['length'], $_POST['start']);
        //print_r($_POST);die;
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }


}