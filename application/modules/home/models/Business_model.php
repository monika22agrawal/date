<?php 
class Business_model extends CI_Model {


    function getBusinessDetail($userId){

        $where = array('user_id'=>$userId);
        $getRespose = $this->common_model->getsingle(BUSINESS,$where);       
        return $getRespose;
    }

	function addBusiness($busenessData){

		$insertId = $this->common_model->insertData(BUSINESS,$busenessData);
		return $insertId;
	}

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
    function getSubscriptionId(){

        $isSubsId = $this->common_model->getsingle(USERS,array('userId'=>$this->session->userdata('userId')));

        return !empty($isSubsId) ? $isSubsId->bizSubscriptionId : FALSE;
        
    } // End of function

    // to get customer id from users table 
    function bizSubsDetail($userId){

        $getSubsId = $this->common_model->getsingle(USERS,array('userId'=>$userId));
        if($getSubsId){
            return $getSubsId->bizSubscriptionId;
        }else{
            return FALSE;
        }

    } // End of function
       
} //end of class

       