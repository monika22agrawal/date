<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment_list_model extends CI_Model {

    //var $table , $column_order, $column_search , $order =  '';

    var $column_order   = array(null,'u.fullName','appointDateTime','u.address','a.appointDate','a.appointmentStatus'); //set column field database for datatable orderable
    var $column_search  = array('u.fullName'); //set column field database for datatable searchable
    var $col_filter     = array('a.appointDate','a.appointmentStatus');
    var $order          = array('appId'=>'DESC');  // default order
    var $group_by       = 'uImg.user_id,ufImg.user_id,appId';
    
    public function __construct(){
        parent::__construct();
    }    
  
    function prepare_query($isCount=FALSE){
       
        $date = date('Y-m-d H:i:s');
        $defaultImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        $imgUrl = AWS_CDN_USER_IMG_PATH;
        
        $select = '
            a.*, CONCAT( a.appointDate,SPACE(1),appointTime ) as appointDateTime, u.fullName as ByName, uf.fullName as ForName, u.gender as ByGender, uf.gender as ForGender, u.latitude as ByLatitude, uf.latitude as ForLatitude, u.longitude as ByLongitude, uf.longitude as ForLongitude,
            IF( u.address IS NULL or u.address ="" or u.address ="0","NA",u.address ) as ByAddress,
            IF( uf.address IS NULL or uf.address ="" or uf.address ="0","NA",uf.address ) as ForAddress,
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
                                        image
                                END 
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(uImg.userImgId)
                        )
                END 
            ) as byImage,
            (
                case
                    when( MIN(ufImg.userImgId) IS NULL)
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
                                        image
                                END 
                            ) as image FROM '.USERS_IMAGE.' WHERE userImgId = MAX(ufImg.userImgId)
                        )
                END
            ) as forImage
        ';

        if($isCount===TRUE){
            $select = 'COUNT(a.appId) as totalRecords';
        }
        $this->db->select($select);

        $this->db->from(APPOINTMENTS.' as a');

        $this->db->join(USERS.' as u','u.userId = a.appointById','left');
        $this->db->join(USERS.' as uf','uf.userId = a.appointForId','left');
        $this->db->join(USERS_IMAGE.' as uImg','uImg.user_id = a.appointById','left');
        $this->db->join(USERS_IMAGE.' as ufImg','ufImg.user_id = a.appointForId','left');

        $this->db->where(array('a.isDelete'=>0));
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

             //for category filter
            $count_val = count($_POST['columns']);
            //print_r($_POST['columns']);
            for($i=0;$i<=$count_val-1;$i++){
            //print_r($_POST['columns'][$i]);
                if(!empty($_POST['columns'][$i]['search']['value']) && $_POST['columns'][$i]['search']['value'] != 'isFinish' && $_POST['columns'][$i]['search']['value'] != 'counter_rej' && $_POST['columns'][$i]['search']['value'] != 'new_app' && $_POST['columns'][$i]['search']['value'] != 'payment_pen' && $_POST['columns'][$i]['search']['value'] != 'app_con' && $_POST['columns'][$i]['search']['value'] != 'waiting' && $_POST['columns'][$i]['search']['value'] != 'app_rej'&& $_POST['columns'][$i]['search']['value'] != 'req_cncl'){ 

                    $this->db->where(array($this->col_filter[$i] => $_POST['columns'][$i]['search']['value']));

                }elseif($_POST['columns'][$i]['search']['value'] === 'isFinish'){

                    $this->db->where(array('isFinish' => 1));

                }elseif($_POST['columns'][$i]['search']['value'] === 'counter_rej'){

                    $this->db->where(array('isFinish' => 0,'isCounterApply' => 1,'appointmentStatus'=> 5));
                    $this->db->or_group_start();
                        $this->db->where(array('isFinish' => 0,'isCounterApply' => 1,'counterStatus'=> 2));
                    $this->db->group_end();

                }elseif($_POST['columns'][$i]['search']['value'] === 'new_app'){

                    $this->db->where(array('isFinish' => 0,'isCounterApply' => 1,'counterStatus' => 0));
                    
                }elseif($_POST['columns'][$i]['search']['value'] === 'payment_pen'){

                    $this->db->where(array('isFinish' => 0,'isCounterApply' => 0,'counterStatus'=> 1));
                    $this->db->or_group_start();
                        $this->db->where(array('isFinish' => 0,'appointmentStatus' => 2,'offerType' => 1));
                    $this->db->group_end();

                }elseif($_POST['columns'][$i]['search']['value'] === 'app_con'){

                    $this->db->where(array('isFinish' => 0,'isCounterApply' => 1,'counterStatus'=> 3));
                    $this->db->or_group_start();
                        $this->db->where(array('isFinish' => 0,'appointmentStatus' => 2,'offerType !=' => 1));
                    $this->db->group_end();
                    $this->db->or_group_start();
                        $this->db->where(array('isFinish' => 0,'appointmentStatus' => 4));
                    $this->db->group_end();

                }elseif($_POST['columns'][$i]['search']['value'] === 'waiting'){

                    $this->db->where(array('isFinish' => 0,'appointmentStatus'=>1));

                }elseif($_POST['columns'][$i]['search']['value'] === 'app_rej'){

                    $this->db->where(array('isFinish' => 0,'appointmentStatus'=>3));

                }elseif($_POST['columns'][$i]['search']['value'] === 'req_cncl'){

                    $this->db->where(array('isFinish' => 0,'appointmentStatus'=>5));
                }
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
        //lq();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->prepare_query(TRUE);
        $query = $this->db->get();
        $row = $query->row();
        return $row->totalRecords;
    }

}