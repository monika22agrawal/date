<?php
 
$frontend_assets =  base_url().'frontend_asset/';

$activeTabs = $this->uri->segment('1');

if(!empty($this->uri->segment('2'))){

    $activeTabs = $this->uri->segment('2');
} 

if( !empty($this->uri->segment('2')) && !empty($this->uri->segment('3')) ){
    $activeTabs = $this->uri->segment('3');
}

$title = $Home = $AboutUs  =  $Login = $nearByYou = $AboutUs = $Appointment = $Event = $Chat = $UserProfile = $Registration = '';

switch ($activeTabs) {

    case 'home':
       $Home = "active";
       $title = lang('home_title');
    break; 

    case 'login':
       $Login = "active";
       $title = lang('sign_up');
    break; 

    case 'registration':
        $Registration = "active";
        $title = lang('sign_up');
    break; 

    case 'nearByYou':
        $nearByYou = "active";
        $title = lang('near_you');
    break;

    case 'createAppointment':
        $nearByYou = "active";
        $title = lang('near_you');;
    break;

    case 'userDetail':
        $nearByYou = "active";
        $title = lang('near_you');;
    break;

    case 'userOnMap':
        $nearByYou = "active";
        $title = lang('near_you');;
    break; 

    case 'aboutUs':
        $AboutUs = "active";
        $title = lang('about');
    break;

    case 'appointment':
        $Appointment = "active";
        $title = lang('appointment');
    break;

    case 'updateAppointment':
        $Appointment = "active";
        $title = lang('appointment');
    break;

    case 'viewAppOnMap':
        $Appointment = "active";
        $title = lang('appointment');
    break; 

    case 'event':
        $Event = "active";
        $title = lang('event');
    break; 

    case 'createEvent':
        $Event = "active";
        $title = lang('event');
    break; 

    case 'eventRequestDetail':
        $Event = "active";
        $title = lang('event');
    break;

    case 'myEventDetail':
        $Event = "active";
        $title = lang('event');
    break; 

    case 'updateEvent':
        $Event = "active";
        $title = lang('event');
    break; 

    case 'chat':
        $Chat = "active";
        $title = lang('chat');
    break;

    case 'userProfile':
        $UserProfile = "active";
        $title = "User Profile";
    break; 
    
    case 'updateProfile':
        $UserProfile = "active";
        $title = "User Profile";
    break;

    case 'verification':
        $UserProfile = "active";
        $title = "User Profile";
    break; 

    case 'addBusiness':
        $UserProfile = "active";
        $title = "User Profile";
    break; 

    case 'bankAccount':
        $UserProfile = "active";
        $title = "User Profile";
    break; 
  
    default:
        $Home = "active";
        $title = lang('home_title');
    break;    
}

$uriSegmemt = $this->uri->segment(3);
$id = $eventId = $details = $fullName = $url = $about = $image = '';
$height=768;$width=1024;
//list($width, $height) = getimagesize($details->imgName);

if($uriSegmemt == 'userDetail'){
    $id = decoding($this->uri->segment(4));    
}

if($uriSegmemt == 'userProfile'){
    $id = $this->session->userdata('userId');
}

if ($uriSegmemt == 'myEventDetail') {
    $eventId = decoding($this->uri->segment(4));
    $url     = base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';
}

if ($uriSegmemt == 'eventRequestDetail') {

    $eventId = decoding($this->uri->segment(4));

    $compId =  $eventMemId = '';
    if(isset($_GET['compId'])){
       
        $compId =  ($_GET['compId']); 
        $query_str = '/?compId='.$compId; 

    }elseif (isset($_GET['eventMemId'])) {
        
        $eventMemId =  ($_GET['eventMemId']);
        $query_str = '/?eventMemId='.$eventMemId;                                                    
    }

    $url = base_url('home/event/eventRequestDetail/').($this->uri->segment(4)).$query_str;

}

if(!empty($eventId)){

    $checkWhere = array('eventId'=>$eventId);
    $details = $this->common_model->getsingle(EVENTS,$checkWhere);

    $getEventImg = $this->common_model->eventsImage($eventId);

    $eventImg = $getEventImg[0]->eventImageName;

    if(!empty($eventImg)){ 
        $img = AWS_CDN_EVENT_MEDIUM_IMG.$eventImg;
    } else{
        $img = AWS_CDN_EVENT_PLACEHOLDER_IMG;
    }

    $fullName   = ucfirst($details->eventName);
    $about      = $details->eventPlace;
    $image      = $img;
}

if(!empty($id)){

    $details    = $this->common_model->usersDetail($id);

    $fullName   = ucfirst($details->fullName);
    $url        = base_url('home/user/userDetail/').encoding($id).'/';
    $about      = ($details->about == 'NA' || $details->about == '') ? "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!" : $details->about;
    $image      = !empty($details->profileImage) ? $details->profileImage : AWS_CDN_USER_PLACEHOLDER_IMG;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="<?php echo lang('home_title');?>">
    <meta name="keywords" content="<?php echo lang('keywords');?>">
    <meta name="author" content="APOIM">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="google-site-verification" content="<?php echo GOOGLE_API_KEY;?>" />
    <link rel="icon" href="<?php echo AWS_CDN_FRONT_IMG; ?>favicon-16x16.png" type="image/x-icon" />

    <?php if(!empty($details)){ ?>

        <!-- For facebook -->
        <meta property="og:url" content="<?php echo $url;?>" />
        <meta property="fb:app_id" content="<?php echo FACEBOOK_APP_ID;?>" />
        <meta property="og:type" content="Website" />
        <meta property="og:title" content="APOIM | <?php echo $fullName;?>" />
        <meta property="og:description" content="<?php echo $about;?>" />
        <meta property="og:image" content="<?php echo $image;?>" />
        <meta property="og:image:alt" content="APOIM | <?php echo ucfirst($fullName);?>" />
        <meta property="og:image:secure_url" content="<?php echo $image;?>">
        <meta property="og:image:width" content="<?php echo $width;?>">
        <meta property="og:image:height" content="<?php echo $height;?>">
        <!-- Ent facebook -->

        <!-- For twitter -->
        <meta name="twitter:card" content="summary" />  
        <meta name="twitter:title" content="APOIM | <?php echo $fullName;?>" />  
        <meta name="twitter:description" content="<?php echo $about;?>" />  
        <meta name="twitter:site" content="<?php echo $url;?>" />  
        <meta name="twitter:image" content="<?php echo $image;?>" />
        <!-- For twitter -->  

        <!-- Google Plus markup -->
        <meta itemprop="name" content="APOIM | <?php echo $fullName;?>"/>
        <meta itemprop="description" content="<?php echo $about;?>"/>
        <meta content="<?php echo $url;?>" itemprop="url"/>
        <meta itemprop="image" content="<?php echo $image;?>"/>
        <!-- End Google Plus markup -->

    <?php }else{ ?>

        <!-- for FB common meta tags -->
        <meta property="fb:app_id" content="<?php echo FACEBOOK_APP_ID;?>" />
        <meta property="og:title" content="APOIM" />
        <meta property="og:url" content="https://www.apoim.com/" />
        <meta property="og:description" content="<?php echo lang('home_title');?>"> 
        <meta property="og:image" content="<?php echo AWS_CDN_FRONT_IMG; ?>apoim_email_logo.png">
        <meta property="og:image:width" content="640" />
        <meta property="og:image:height" content="640" />
        <meta property="og:type" content="article" />

    <?php } ?>

    <title>APOIM | <?php echo $title;?></title>

    <!-- Icon css link -->
    <!-- <link href="<?php //echo AWS_CDN_FRONT_VENDORS; ?>material-icon/css/materialdesignicons.min.css" rel="stylesheet"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.4.93/css/materialdesignicons.css" rel="stylesheet">
    <!-- <link href="<?php //echo AWS_CDN_FRONT_CSS; ?>font-awesome.min.css" rel="stylesheet"> -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>linears-icon/style.css" rel="stylesheet">
      
    <!-- Bootstrap -->
    <link href="<?php echo AWS_CDN_FRONT_CSS; ?>bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>image-dropdown/dd.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>image-dropdown/skin2.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>bootstrap-selector/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>bootstrap-datepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>animate-css/animate.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_VENDORS; ?>jquery-ui/jquery-ui.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/1.6.11/css/lightgallery.min.css">
    
    <link href="<?php echo AWS_CDN_FRONT_CSS; ?>form-wizard-blue.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_CSS; ?>owl.carousel.min.css" rel="stylesheet">
    <link href="<?php echo AWS_CDN_FRONT_CSS; ?>owl.theme.default.min.css" rel="stylesheet">

    <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/style.css" rel="stylesheet">
    <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/common-style.css" rel="stylesheet"> 

    <link href="<?php echo AWS_CDN_FRONT_CSS; ?>toastr.min.css" rel="stylesheet">
    <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>custom/css/front_custom.css" rel="stylesheet">
    
    <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/responsive.css" rel="stylesheet">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo AWS_CDN_FRONT_JS; ?>jquery-3.3.1.min.js"></script>
    <script src="<?php echo AWS_CDN_FRONT_JS; ?>jquery.validate.min.js"></script>

    <?php 

    if($this->session->userdata('language') == 'spanish'){ ?>

        <script src="<?php echo AWS_CDN_FRONT_JS; ?>validate_message_es.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=es&key=<?php echo GOOGLE_API_KEY;?>"></script>

    <?php }else { ?> 

        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo GOOGLE_API_KEY;?>"></script>

    <?php } ?>

    
    <?php if(!empty($front_styles)) { load_css($front_styles); } //load required page styles ?> 
            
    <script type="text/javascript">

        var BASE_URL        = '<?php echo base_url(); ?>';
            IMG_BASE_URL    = '<?php echo AWS_CDN_URL; ?>';
            publish_key     = '<?php echo STRIPE_PUBLISH_KEY ?>';
            getLat          = '<?php echo $this->session->userdata('lat'); ?>',
            getLong         = '<?php echo $this->session->userdata('long'); ?>',
            getAddress      = '<?php echo $this->session->userdata('address'); ?>',
            getCity         = '<?php echo $this->session->userdata('city'); ?>',
            getCity         = '<?php echo $this->session->userdata('city'); ?>',
            commonMsg       = '<?php echo lang('try_again_msg'); ?>',
            commonMsgReq    = '<?php echo lang('require_all_field'); ?>',
            imgFormateAlwd  = '<?php echo lang('format_allowed'); ?>',
            imgMaxFive      = '<?php echo lang('max_five_img'); ?>',
            defaultImg      = '<?php echo AWS_CDN_EVENT_PLACEHOLDER_IMG; ?>',
            wrongMsg        = '<?php echo lang('something_wrong'); ?>',
            eBankFN         = '<?php echo lang('bank_first_name'); ?>',
            eBankFNMin      = '<?php echo lang('bank_first_name_minlen'); ?>',
            eBankFNMax      = '<?php echo lang('bank_first_name_maxlen'); ?>',
            eBankLN         = '<?php echo lang('bank_last_name'); ?>',
            eBankLNMin      = '<?php echo lang('bank_last_name_minlen'); ?>',
            eBankLNMax      = '<?php echo lang('bank_last_name_maxlen'); ?>',
            eBankRN         = '<?php echo lang('bank_routing_number'); ?>',
            eBankBD         = '<?php echo lang('bank_birth_date'); ?>',
            eBankACN        = '<?php echo lang('bank_account_number'); ?>',
            eBankPC         = '<?php echo lang('bank_postal_code'); ?>',
            eBankSSN        = '<?php echo lang('bank_ssn_last'); ?>',

            emailReq        = '<?php echo lang('register_email_req'); ?>',
            emailValid      = '<?php echo lang('register_email_valid'); ?>',
            pwdReq          = '<?php echo lang('register_pwd_req'); ?>',
            pwdMinLen       = '<?php echo lang('register_pwd_minlen'); ?>',
            emailCode       = '<?php echo lang('verify_email_code'); ?>',
            genderReq       = '<?php echo lang('register_gender_req'); ?>',
            purposeReq      = '<?php echo lang('register_purpose_req'); ?>',
            dateWReq        = '<?php echo lang('register_datewith_req'); ?>',
            eventtypeReq    = '<?php echo lang('register_event_invi_req'); ?>',
            resSuccess      = '<?php echo lang('registered_success'); ?>',
            emailAlready    = '<?php echo lang('register_already_email'); ?>',
            userInactive    = '<?php echo lang('inactive_user_error'); ?>',
            fullNReq        = '<?php echo lang('full_name_req'); ?>',
            fullNMinLen     = '<?php echo lang('full_name_min_len'); ?>',
            fullNMaxLen     = '<?php echo lang('full_name_max_len'); ?>',
            birthReq        = '<?php echo lang('birthday_req'); ?>';


            frontImgPath        = '<?php echo AWS_CDN_FRONT_IMG; ?>';
            
    </script>    

    <?php
        
        if($activeTabs == 'login' || $activeTabs == 'registration'){ ?>

            <script src="https://apis.google.com/js/api:client.js"></script>

    <?php } 

        if($activeTabs == 'nearByYou' || $activeTabs == 'viewAppOnMap' || $activeTabs == 'userProfile' || $activeTabs == 'eventRequestDetail' || $activeTabs == 'myEventDetail' || $activeTabs == 'addBusiness'){ ?>

            <script src="https://js.stripe.com/v3/"></script>

    <?php } ?>
   

    <!-- start to connect firebase -->
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-storage.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-messaging.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.0.4/firebase-functions.js"></script>

    <script>
        // Initialize Firebase for live
        /*var config = {
            apiKey: "AIzaSyADUxrQxnkWGAACA66sX1VsZKfibnUHkWI",
            authDomain: "appointment-1518611631450.firebaseapp.com",
            databaseURL: "https://appointment-1518611631450.firebaseio.com",
            projectId: "appointment-1518611631450",
            storageBucket: "appointment-1518611631450.appspot.com",
            messagingSenderId: "839320525088"
        };*/

        // Initialize Firebase for dev
        var config = {
            
            apiKey              : "<?php echo FIREBASE_API_KEY; ?>",
            authDomain          : "<?php echo FIREBASE_AUTH_DOMAIN; ?>",
            databaseURL         : "<?php echo FIREBASE_DB_URL; ?>",
            projectId           : "<?php echo FIREBASE_PROJECT_ID; ?>",
            storageBucket       : "<?php echo FIREBASE_STORAGE_BUCKET; ?>",
            messagingSenderId   : "<?php echo FIREBASE_MESSAGING_SENDER_ID; ?>"
        };

        // Initialize Firebase for local
        /*var config = {
            apiKey: "AIzaSyC55fzVWcs1yF8QvLtDkQj5ENXUvuVKTVI",
            authDomain: "apoimlocal.firebaseapp.com",
            databaseURL: "https://apoimlocal.firebaseio.com",
            projectId: "apoimlocal",
            storageBucket: "apoimlocal.appspot.com",
            messagingSenderId: "659616662103"
        };*/
        firebase.initializeApp(config);
    </script>
    <!-- end to connect firebase -->

</head>
<body>   
    <div id="tl_admin_loader" class="loaderIcon">
        <div class="LoaderInner">
            <img src="<?php echo AWS_CDN_FRONT_IMG;?>loader.apng">
        </div>
    </div>
    <script type="text/javascript">
        <?php if($activeTabs == 'chat'){ ?>
            $('#tl_admin_loader').show();        
        <?php } ?>
    </script>   
    <!--================Frist Main hader Area =================-->
    <header class="header_menu_area white_menu">
        <nav class="navbar navbar-default">
            <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo base_url();?>"><img src="<?php echo AWS_CDN_FRONT_IMG; ?>logo-2.png" alt=""></a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                
                <ul class="nav navbar-nav">
                    <li class="dropdown submenu <?php echo $Home;?>">
                        <a href="<?php echo base_url('home');?>"><?php echo lang('home'); ?></a>
                    </li>
                    <li class="dropdown submenu <?php echo $nearByYou;?>">
                        <a href="<?php echo base_url('home/nearByYou');?>"><?php echo lang('near_you'); ?></a>
                    </li>
                </ul>

                <?php if($this->session->userdata('front_login') == true && $this->session->userdata('userId') != ''){?>

                    <ul class="nav navbar-nav">
                        <li class="dropdown submenu <?php echo $Appointment;?>">
                            <a href="<?php echo base_url('home/appointment');?>"><?php echo lang('appointment'); ?></a>
                        </li>
                        <li class="dropdown submenu <?php echo $Event;?>">
                            <a href="<?php echo base_url('home/event');?>"><?php echo lang('event'); ?></a>
                        </li>
                        <li class="dropdown submenu <?php echo $Chat;?>">
                            <a href="<?php echo base_url('home/chat');?>"><?php echo lang('chat'); ?></a>
                        </li>
                        <li class="dropdown submenu <?php echo $UserProfile;?>">
                            <a href="<?php echo base_url('home/user/userProfile');?>"><?php echo lang('profile'); ?></a>
                        </li>
                    </ul>

                <?php } ?> 

                <ul class="nav navbar-nav">
                    <li class="dropdown submenu <?php echo $AboutUs;?>">
                        <!-- <a href="<?php //echo base_url('home/aboutUs');?>">About Us</a> -->
                        <a href="<?php echo base_url('home/about_us');?>" target="_blank"><?php echo lang('about'); ?></a>
                    </li>
                </ul>

                <?php if($this->session->userdata('front_login') != true && $this->session->userdata('userId') == ''){ ?>

                    <ul class="nav navbar-nav register">
                        <li class="<?php echo $Login;?>"><a href="<?php echo base_url('home/login');?>"><i class="mdi mdi-key-variant"></i><?php echo lang('login_title'); ?></a></li>
                        <li class="pad-lft mrgin-rgt-gap <?php echo $Registration;?>"><a href="<?php echo base_url('home/login/registration');?>"><i class="fa fa-user-plus"></i><?php echo lang('sign_up'); ?></a></li>
                    </ul>
                    
                <?php } else{ ?>

                    <ul class="nav navbar-nav">
                        
                         <li class="dropdown submenu"><a href="<?php echo base_url('home/logout');?>"><i class="fa fa-sign-out"></i> <?php echo lang('logout'); ?> </a> </li>   
                    </ul>

                <?php } ?>

                    <div class="nav navbar-nav navbar-right">
                        <div class="dropdown cstm_lnguage">
                            <button class="btn dropdown-toggle lngugr_btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php 
                                    $langImg = AWS_CDN_FRONT_IMG.'english_flag.jpg';
                                    if($this->session->userdata('language') == 'spanish'){
                                        $langImg = AWS_CDN_FRONT_IMG.'spanish_flag.png';
                                    }else{
                                        $langImg = AWS_CDN_FRONT_IMG.'english_flag.jpg';
                                    }
                                ?>
                                <span><img id="langImg" src="<?php echo $langImg; ?>" /></span> <span id="langText"> <?php echo $this->session->userdata('language') ? $this->session->userdata('language') : 'English'; ?> </span> <i class="fa fa-angle-down"></i>
                            </button>
                            <div class="dropdown-menu cstm_drpdwn_menu" aria-labelledby="dropdownMenuButton" >
                                <a onclick="language('English')" class="dropdown-item" href="javascript:void(0);"><span><img src="<?php echo AWS_CDN_FRONT_IMG; ?>english_flag.jpg" /></span> English - English (US)</a>
                                <a onclick="language('Spanish')" class="dropdown-item" href="javascript:void(0);"><span><img src="<?php echo AWS_CDN_FRONT_IMG; ?>spanish_flag.png" /></span> Spanish - Española</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </nav>
    </header>
    <!--================Frist Main hader Area =================-->
    <script type="text/javascript">
        
        function language(e){

            var lang = 'english';

            if(e == 'English'){

                var lang = 'english';
                
                $('#langImg').attr('src','<?php echo AWS_CDN_FRONT_IMG; ?>english_flag.jpg');
                $('#langText').text('English');

            }else{

                var lang = 'spanish';

                $('#langImg').attr('src','<?php echo AWS_CDN_FRONT_IMG; ?>spanish_flag.png');
                $('#langText').text('Spanish');
            }

            $.ajax({
                url: '<?php echo base_url(); ?>home/updateLanguage',
                type: "POST",
                data: {language:lang},
                cache: false,
                dataType: "JSON", 
                success: function(result) {

                    window.location.reload();
                }
            });            
            
        }

    </script>