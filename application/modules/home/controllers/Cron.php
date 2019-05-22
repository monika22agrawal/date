<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CommonFront {

	function __construct() {
        parent::__construct();
        $this->load->model('Cron_model');
        date_default_timezone_set('Asia/Kolkata');
    }

    function checkAppTimeExpire(){
        
        $data = $this->Cron_model->checkAppTimeExpire();
        if($data):
            echo "success";
        else :
            echo "Fail";
        endif;
    }

} // End Of Class