<?php 
Class Interest_model extends CI_Model {

	function activeInactive($id){
        $this->db->select('*');  
        $this->db->where('interestId',$id);
        $sql = $this->db->get(INTERESTS)->row();
        if($sql->status == 0){
            $this->db->update(INTERESTS,array('status'=> '1'),array('interestId'=>$id));
            return array('message'=>'Active');
        }else{
            $this->db->update(INTERESTS,array('status'=> '0'),array('interestId'=>$id));
            return array('message'=>'Inactive');
        }
    }

    // Check  Contact is exist or not
    function checkRecord($interest){

        $isExist = $this->db->get_where(INTERESTS,array('interest'=>$interest))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function

    // Check  Contact is exist or not
    function checkEduRecord($education){

        $isExist = $this->db->get_where(EDUCATION,array('education'=>$education))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function

    // Check  Contact is exist or not
    function checkEduInSpRecord($education){

        $isExist = $this->db->get_where(EDUCATION,array('eduInSpanish'=>$education))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function

    // Check  Contact is exist or not
    function checkWorkRecord($work){

        $isExist = $this->db->get_where(WORKS,array('name'=>$work))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function

    // Check  Contact is exist or not
    function checkWorkSPRecord($work){

        $isExist = $this->db->get_where(WORKS,array('nameInSpanish'=>$work))->row();
        if(!empty($isExist)){
            return false;
        }else{
            return true;
        }

    } //Enf Function
	
}