<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stripe_test extends CommonFront {

    function __construct() {
        parent::__construct();
    }

    function test(){

        $this->load->library('Stripe');

        $data = array(
            'amount'=>100,
            'bankAccId'=>"acct_1CaiqVGgBAQ55eeT",
            "currency"=>"eur"
        );
        $isPaymentDone = $this->stripe->owner_pay_byBankId($data);
        pr($isPaymentDone);
    }
}