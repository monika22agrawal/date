<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Friend_list_model extends CI_Model {

    //var $table , $column_order, $column_search , $order =  '';

    var $column_order =array(null,'u1.fullNam','u2.fullName'); //set column field database for datatable orderable
    var $column_search = array('u1.fullName','u2.fullName'); //set column field database for datatable searchable
    var $order = array('u1.fullName' => 'asc','u2.fullName'=>'asc'); // default order
    var $where = '';
    var $group_by = 'u1.userId,u2.userId'; 

 
    public function __construct(){
        parent::__construct();
    }
    
    public function set_data($data){
      $this->data = $data;
    }  


    function prepare_query(){ 

        $defaultUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $userImg = AWS_CDN_USER_IMG_PATH;

        $this->db->select('uf.friendId,IF(uf.byId = "'.$this->data['userId'].'",COALESCE(wf.name,""),COALESCE(w.name,"")) as work,IF(uf.byId = "'.$this->data['userId'].'",uf.forId,uf.byId) as userId,IF(uf.byId = "'.$this->data['userId'].'",u2.fullName,u1.fullName) as fullName,IF(uf.byId = "'.$this->data['userId'].'",u2.gender,u1.gender) as gender,IF(uf.byId = "'.$this->data['userId'].'",u2.eventInvitation,u1.eventInvitation) as eventInvitation,
                (case

                when (uf.byId = "'.$this->data['userId'].'" && ufImg.image = "") || (uf.forId = "'.$this->data['userId'].'" && uImg.image = "") 
                    THEN "'.$defaultUserImg.'"
            
                when (uf.byId = "'.$this->data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 1) || (uf.forId = "'.$this->data['userId'].'" && uImg.image != "" && uImg.isSocial = 1)

                    THEN IF(uf.byId = "'.$this->data['userId'].'",ufImg.image,uImg.image)

                when (uf.forId = "'.$this->data['userId'].'" && uImg.image != "" && uImg.isSocial = 0) || (uf.byId = "'.$this->data['userId'].'" && ufImg.image != "" && ufImg.isSocial = 0)

                    THEN IF(uf.byId = "'.$this->data['userId'].'", ufImg.image, uImg.image)
                ELSE
                    "'.$defaultUserImg.'"
            END) as profileImage');

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
                $this->db->where('uf.forId',$this->data['userId']);
                $this->db->or_where('uf.byId',$this->data['userId']);
            $this->db->group_end();

            $where = array('u1.status'=>'1','u2.status'=>'1');
            $this->db->where($where);
    }

    //prepare post list query
    private function posts_get_query()
    {
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

    function get_list()
    {
        $this->posts_get_query();
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

    function count_filtered()
    {
        $this->posts_get_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->prepare_query();
        return $this->db->count_all_results();
    }

}