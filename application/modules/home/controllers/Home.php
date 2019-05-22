<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CommonFront {

	function __construct() {
        parent::__construct();        
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Home_model');
        $this->load_language_files();
    }

    function index(){

        // $getFullAddress = getAddress('40.05897515814939','-2.1388766528824497');
        // pr($getFullAddress);
        $data['guysCount']          = $this->common_model->get_total_count(USERS,array('gender'=>1));
        $data['girlsCount']         = $this->common_model->get_total_count(USERS,array('gender'=>2));
        $data['transgenderCount']   = $this->common_model->get_total_count(USERS,array('gender'=>3));
        $data['nearUsers']          = $this->Home_model->getTopUsers();
        $data['eventList']          = $this->Home_model->getLatestEvent();

        $vendor_revolution_asset_css = AWS_CDN_FRONT_VENDORS.'revolution/css/';

        $data['front_styles']           = array($vendor_revolution_asset_css.'layers.css',$vendor_revolution_asset_css.'navigation.css',$vendor_revolution_asset_css.'settings.css');  //load css

        $vendor_revolution_asset        = AWS_CDN_FRONT_VENDORS.'revolution/js/';
        $vendor_revolution_asset_ext    = AWS_CDN_FRONT_VENDORS.'revolution/js/extensions/';

        $data['front_scripts']    = array($vendor_revolution_asset.'jquery.themepunch.tools.min.js',$vendor_revolution_asset.'jquery.themepunch.revolution.min.js',$vendor_revolution_asset_ext.'revolution.extension.actions.min.js',$vendor_revolution_asset_ext.'revolution.extension.carousel.min.js',$vendor_revolution_asset_ext.'revolution.extension.kenburn.min.js',$vendor_revolution_asset_ext.'revolution.extension.layeranimation.min.js',$vendor_revolution_asset_ext.'revolution.extension.migration.min.js',$vendor_revolution_asset_ext.'revolution.extension.navigation.min.js',$vendor_revolution_asset_ext.'revolution.extension.parallax.min.js',$vendor_revolution_asset_ext.'revolution.extension.slideanims.min.js',$vendor_revolution_asset_ext.'revolution.extension.video.min.js'); //load js

        $this->load->front_render('home',$data,'');
    }

    function updateLanguage(){

        $language    = $this->input->post('language') ? $this->input->post('language') : 'english';

        $this->session->set_userdata('language', $language);
        if($this->session->userdata('userId') != ''){
      
            $where = array('userId'=>$this->session->userdata('userId'));         
            //update status
            $result = $this->common_model->updateFields(USERS, array('setLanguage'=>$language),$where);
        }
        
        $this->session->set_userdata('language', $language);

        echo true;exit();
    }

    function about_us(){

        $lang = $this->session->userdata('language');

        $type = 'about_page_english';
        if($lang == 'spanish'){
            $type = 'about_page_spanish';
        }

        $data['aboutUs'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>$type));

        $this->load->front_render_minimal('about_us',$data,'');
    }

    function privacy(){

        $lang = $this->session->userdata('language');

        $type = 'pp_page_english';
        if($lang == 'spanish'){
            $type = 'pp_page_spanish';
        }

        $data['privacy'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>$type));

        $this->load->front_render_minimal('privacy_policy',$data,'');
    }

    function terms(){

        $lang = $this->session->userdata('language');
        
        $type = 'tc_page_english';
        if($lang == 'spanish'){
            $type = 'tc_page_spanish';
        }

        $data['tc'] = $this->common_model->optionDataRetrive(OPTIONS,array('option_name'=>$type));

        $this->load->front_render_minimal('terms_and_condition',$data,'');
    }

    function recordNotFound(){

        $this->load->front_render('page_not_found','','');
    }

    function development(){

        $this->load->front_render('under_development','','');
    }

    function checklogin(){

        $uId = $this->session->userdata('userId');

        $currentlatitude    = $this->input->post('latitude');
        $currentlongitude   = $this->input->post('longitude');
        $currentAddress     = $this->input->post('address');
        $currentCity        = $this->input->post('city');
        $currentState       = $this->input->post('state');
        $currentCountry     = $this->input->post('country');

        $lat        = $this->session->userdata('lat');
        $long       = $this->session->userdata('long');
        $city       = $this->session->userdata('city');
        $ipstate    = $this->session->userdata('state');
        $ipcountry  = $this->session->userdata('country');
        $ipaddress  = $this->session->userdata('address');

        if(!empty($currentlatitude) && !empty($currentlongitude)){

            $this->session->set_userdata('lat', $currentlatitude);
            $this->session->set_userdata('long', $currentlongitude);
            $this->session->set_userdata('address', $currentAddress);
            $this->session->set_userdata('city', $currentCity);
            $this->session->set_userdata('state', $currentState);
            $this->session->set_userdata('country', $currentCountry);

        }elseif(!empty($lat) && !empty($long) && !empty($ipcountry)){
 
            $this->session->set_userdata('lat', $lat);
            $this->session->set_userdata('long', $long);
            $this->session->set_userdata('address', $ipaddress);
            $this->session->set_userdata('city', $city);
            $this->session->set_userdata('state', $ipstate);
            $this->session->set_userdata('country', $ipcountry);

        }else{
            
            if($this->session->userdata('userId')){

                $detail = $this->common_model->usersDetail($this->session->userdata('userId'));
               
                $this->session->set_userdata('lat', $detail->latitude);
                $this->session->set_userdata('long', $detail->longitude);
                $this->session->set_userdata('address', $detail->address);
                $this->session->set_userdata('city', $detail->city);
                $this->session->set_userdata('state', $detail->state);
                $this->session->set_userdata('country', $detail->country);
            }            
        }
      
        if($uId){
            
            $req = $this->common_model->GetJoinRecord(NOTIFICATIONS, 'notificationBy', USERS, 'userId', 'users.userId,users.fullName,notifications.*', array('notificationFor'=>$uId,'webShow'=>0,'isRead'=>0) , '', '', 'DESC', 1, 0);
            
            if($req){

                $this->common_model->updateFields(NOTIFICATIONS,array('webShow' => 1), array( 'notId' => $req[0]->notId));

                $v = $req[0];
                $notif_payload = json_decode($v->message);
               
                $type = $v->notificationType;
                $referenceId = $notif_payload->referenceId;

                //if notification is related to post then get event name
                if($type == 'create_event' || $type == 'join_event' || $type == 'event_payment' || $type == 'share_event' || $type == 'companion_accept' || $type == 'companion_reject' || $type == 'companion_payment'){
                    //replace placeholder name with real event name
                    $notif_payload->body = $this->common_model->replace_event_placeholder_name($referenceId, $notif_payload->body);
                }          

                //get fullName of user
                $notif_payload->body = $this->common_model->replace_user_placeholder_name($v->notificationBy, $notif_payload->body);

                $req[0]->message = $notif_payload;                
                
                $url = base_url();
                $showmsg = $compId = $eventMemId = '';

                if($type == 'add_like' || $type == 'add_favorite' || $type == 'friend_request' || $type == 'accept_request' ){
                    // redirect userdetail page using userId
                    $url = base_url('home/user/userDetail/').encoding($referenceId).'/';

                }elseif($type == 'create_appointment' || $type == 'delete_appointment' ){
                    // redirect userdetail page using userId
                    $url = base_url('home/appointment/');
                    $showmsg = 'This appointment is not available.';

                }elseif($type == 'confirmed_appointment' || $type == 'update_appointment' || $type == 'finish_appointment' || $type == 'apply_counter' || $type == 'appointment_payment' || $type == 'update_counter' || $type == 'review_appointment'){
                    // redirect userdetail page using userId
                    $url = base_url('home/appointment/viewAppOnMap/').encoding($referenceId).'/';

                }elseif($type == 'create_event'|| $type == "companion_payment" ||
                    $type == "join_event" || $type == "event_payment" ||
                    $type == "share_event" || $type == "companion_accept" || $type == "companion_reject"){
                    // redirect userdetail page using userId
                    if($uId == $notif_payload->createrId){

                        $url = base_url('home/event/myEventDetail/').encoding($referenceId).'/'; // for my event detail
                    }else {
                          
                        if(!empty($notif_payload->eventMemId)){
                            $eventMemId = encoding($notif_payload->eventMemId);
                            $query_str = '/?eventMemId='.$eventMemId;
                        }elseif(!empty($notif_payload->compId)){
                            $compId = encoding($notif_payload->compId);
                            $query_str = '/?compId='.$compId;
                        }
                        $url = base_url('home/event/eventRequestDetail/').encoding($referenceId).$query_str.'/'; // event request detail
                    }
                }
                echo json_encode(array('status'=>1,'html'=>$req,'url'=>$url,'popupmsg'=>$showmsg)); exit;
            }
        }else{
            echo json_encode(array('status'=>0)); exit;
        }
    }

    // to get all notification
    function getNotificationList(){

        $offset  = 0; 
        $limit   = 50;
        $userId  = $this->session->userdata('userId');     
        $result['notiList'] = $this->common_model->getNotificationListWeb($offset,$limit,$userId);
        $this->load->view('notification_list',$result);
    }

    function nearByYou(){

        $this->load->model('Login_model');

        $ip = $this->Login_model->get_client_ip();

        $city_name = $this->Login_model->getCityNameByIpAddress($ip);

        if(!empty($city_name['city']) && !empty($city_name['latitude']) && !empty($city_name['longitude'])){

            $this->session->set_userdata('city', $city_name['city']);
            $this->session->set_userdata('state', $city_name['region_name']);
            $this->session->set_userdata('country', $city_name['country_name']);
            $this->session->set_userdata('latitude', $city_name['latitude']);
            $this->session->set_userdata('longitude', $city_name['longitude']);
        }

        $load_custom_js = base_url().APP_FRONT_ASSETS.'custom/js/';
        $data_val['front_scripts'] = array($load_custom_js.'filter.js');
        $this->load->front_render('near_you_user_tab',$data_val,'');
    }

   
    function searchResult(){

        $params = array('latitude','longitude','gender','minAge','maxAge','searchName','userOnlineStatus');

        foreach ($params as $value) {
            $searchArray[$value] = $this->input->post($value);
        }
        
        $result['page']     = $this->input->post('page');
        $result['viewType'] = $this->input->post('viewType');

        $limit = 18;
        //$start = $result['page']*$limit;
        $start = $result['page'];
        
        $result['nearUsers']        = $this->Home_model->getAllResult($limit,$start,$searchArray);
        $result['totalNearUsers']   = $this->Home_model->countAllResult($searchArray);
        
        $result['nearUsersOnMap']   = $this->Home_model->getAllResult('','',$searchArray);
        
        /* is_next: var to check we have records available in next set or not
         * 0: NO, 1: YES
         */
        $is_next = 0;
        $new_offset = $result['page'] + $limit;
        if($new_offset<$result['totalNearUsers']){
            $is_next = 1;
        }
        
        $nearByHtml = $this->load->view('near_you_list',$result, true);
        echo json_encode( array('status'=>1, 'html'=>$nearByHtml, 'isNext'=>$is_next, 'newOffset'=>$new_offset) ); exit;
    }

    function logout()
    {
        //$this->session->sess_destroy();
        /*$array_items = array('email' => '', 'fullName' => '', 'status' => '', 'userId' => '', 'mapPayment' => '', 'showTopPayment' => '', 'authToken' => '', 'front_login' => FALSE);*/

        $this->session->unset_userdata('front_login');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('fullName');
        $this->session->unset_userdata('status');
        $this->session->unset_userdata('userId');
        $this->session->unset_userdata('mapPayment');
        $this->session->unset_userdata('showTopPayment');
        $this->session->unset_userdata('authToken');
        
        redirect(site_url('home/login'));
    }
}