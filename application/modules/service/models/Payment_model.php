<?php
class Payment_model extends CI_Model {
	
    // to get customer id from users table 
    function getStripeCustomerId(){

        $getCusId = $this->common_model->getsingle(USERS,array('userId'=>$this->authData->userId));
        if($getCusId){
            return $getCusId->stripeCustomerId;
        }else{
            return FALSE;
        }

    } // End of function

    // to save customer id in db
    function saveCustomerId($customer_id){

        $where = array('userId'=>$this->authData->userId);
        $isUpdated = $this->common_model->updateFields(USERS, array('stripeCustomerId'=>$customer_id),$where);

        if($isUpdated){
            return TRUE;
        }

    } // End of function

    /*// to save subscription id in db
    function saveSubscriptionId($subscription_id){

        $where = array('userId'=>$this->authData->userId);
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
    function cancelSubscription($userId){

        $where = array('userId'=>$userId);
        $isUpdated = $this->common_model->updateFields(USERS, array('subscriptionId'=>'','subscriptionStatus'=>0),$where);

        if($isUpdated){
            return TRUE;
        }else{
            return FALSE;
        }
        
    } // End of function

    // if subdcription is cancelled then set blank value in subscription id and status cancel
    function getSubscriptionId(){

        $isSubsId = $this->common_model->getsingle(USERS,array('userId'=>$this->authData->userId));

        return !empty($isSubsId) ? $isSubsId->subscriptionId : FALSE;
        
    } // End of function

} //End of Class