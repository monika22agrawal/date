<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {
	
	function getStripeCustomerId(){

		$getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));
		if($getCusId){
			return $getCusId->stripeCustomerId;
		}else{
			return FALSE;
		}
	}

	function saveCustomerId($customer_id){

		$where = array('userId'=>$this->session->userdata('userId'));
		$isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

		if($isUpdated){
			return TRUE;
		}
	}

} //End of Class