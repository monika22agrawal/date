<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    //var $table , $column_order, $column_search , $order =  '';
    var $table = PAYMENT_TRANSACTIONS;
    var $column_order =array('id','fullName','transactionId','amount','paymentStatus','paymentType'); //set column field database for datatable orderable
    var $column_search = array('fullName','transactionId','amount','paymentStatus','paymentType'); //set column field database for datatable searchable
    var $order = array('fullName' => 'desc'); // default order
    var $where = '';
    var $group_by = ''; 

 
    public function __construct(){
        parent::__construct();
    }
    
    public function set_data($where=''){
        $this->where = $where;
    } 


    /*function prepare_query(){ 

        $defaultImg = base_url().DEFAULT_USER;
        $imgUrl = base_url().USER_IMG_PATH;

        $this->db->select('pay.*,u.fullName,(case 
            when( uImg.image = "" OR uImg.image IS NULL) 
            THEN "'.$defaultImg.'"
            when( uImg.image !="" AND uImg.isSocial = 1) 
            THEN uImg.image
            ELSE
            concat("'.$imgUrl.'",uImg.image) 
            END ) as userImg');;

        $this->db->from(PAYMENT_TRANSACTIONS.' as pay');

        $this->db->join(USERS.' as u','pay.user_id = u.userId'); 
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = pay.user_id','left');
        
        $where = array('u.status'=>'1');
        $this->db->where($where);
    }
*/
    //prepare post list query
    private function posts_get_query($userId='')
    {

        $sel_fields = array_filter($this->column_order);
        $this->db->select(PAYMENT_TRANSACTIONS.'.id,transactionId,amount,paymentStatus,paymentType');
        $this->db->select(USERS.'.fullName');
        $this->db->join(USERS,"payment_transactions.user_id = users.userId");
        $this->db->from($this->table);
        !empty($userId) ? $this->db->where('payment_transactions.user_id',$userId) : '';
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

    function get_list($userId='')
    {
        $this->posts_get_query($userId);
		if(isset($_POST['length']) && $_POST['length'] < 1) {
			$_POST['length']= '10';
		} else
		$_POST['length']= $_POST['length'];
		
		if(isset($_POST['start']) && $_POST['start'] > 1) {
			$_POST['start']= $_POST['start'];
		}
        $this->db->limit($_POST['length'], $_POST['start']);
		//print_r($_POST);die;
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($userId='')
    {
        $this->posts_get_query($userId);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($userId='')
    {
        $this->db->from($this->table);
        !empty($userId) ? $this->db->where('payment_transactions.user_id',$userId) : '';
        return $this->db->count_all_results();
    }

}