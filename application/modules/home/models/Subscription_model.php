<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_model extends CI_Model {

	// to get customer id from users table 
	function getStripeCustomerId(){

		$getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));
		if($getCusId){
			return $getCusId->stripeCustomerId;
		}else{
			return FALSE;
		}

	} // End of function

	// to save customer id in db
	function saveCustomerId($customer_id){

		$where = array('userId'=>$this->session->userdata('userId'));
		$isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

		if($isUpdated){
			return TRUE;
		}

	} // End of function

	/*// to save subscription id in db
	function saveSubscriptionId($subscription_id){

		$where = array('userId'=>$this->session->userdata('userId'));
		$isUpdated = $this->common_model->updateFields(USERS, array('subscriptionId'=>$subscription_id),$where);

		if($isUpdated){
			return TRUE;
		}

	} // End of function*/

	// to save subscription payment detail in db 
	function savePaymentDetails($payment_data){

		$isInsert = $this->common_model->insertData(PAYMENT_TRANSACTIONS,$payment_data);
		if($isInsert){
			return TRUE;
		}else{
			return FALSE;
		}

	} // End of function

	// if subdcription is cancelled then set blank value in subscription id and status cancel
	function cancelSubscription($subscription_id){

		$this->db->select('userId,subscriptionId,bizSubscriptionId');
		$this->db->where(array('subscriptionId'=>$subscription_id));
		$this->db->or_where(array('bizSubscriptionId'=>$subscription_id));
		$req = $this->db->get(USERS);

		/*$this->db->select('userId,bizSubscriptionId');
		$this->db->where(array('bizSubscriptionId'=>$subscription_id));
		$req2 = $this->db->get(USERS);*/
		$isUpdated = '';
        if($req->num_rows()){ 

        	$res = $req->row();
        	if($res->subscriptionId == $subscription_id){

        		$where = array('userId'=>$res->userId);
				$isUpdated = $this->common_model->updateFields(USERS, array('subscriptionId'=>'','subscriptionStatus'=>0),$where);

        	}elseif($res->bizSubscriptionId == $subscription_id){

        		$where = array('userId'=>$res->userId);
				$isUpdated = $this->common_model->updateFields(USERS, array('bizSubscriptionId'=>'','bizSubscriptionStatus'=>0),$where);

        	}
        }

		if($isUpdated){
			return TRUE;
		}else{
			return FALSE;
		}
		
	} // End of function

	// if subdcription is cancelled then set blank value in subscription id and status cancel
	function getSubscriptionId(){

		$isSubsId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));

		return !empty($isSubsId) ? $isSubsId->subscriptionId : FALSE;
		
	} // End of function


	function getBizSubscriptionId(){

		$isSubsId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));

		return !empty($isSubsId) ? $isSubsId->bizSubscriptionId : FALSE;
		
	} // End of function

} //End of Class