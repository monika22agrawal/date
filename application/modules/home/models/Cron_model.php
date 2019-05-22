<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_model extends CI_Model {

	function checkAppTimeExpire(){

		$date = date('Y-m-d H:i:s');
        
        $where = array('CONCAT(appointDate,SPACE(1),appointTime) < '=>$date);
        $this->db->limit(50);
        $this->db->update(APPOINTMENTS, array('isDelete'=>1,'isFinish'=>1), $where);            

        return TRUE;

    } //End of Function

} //End of Class