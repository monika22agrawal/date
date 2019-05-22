<?php
    $frontend_assets =  base_url().'frontend_asset/';

    $activeTabs = $this->uri->segment('1');

    if(!empty($this->uri->segment('2'))){

        $activeTabs = $this->uri->segment('2');
    }

    $title = $Home = $AboutUs  =  $privacyPolicy = $termsCondition = '';

switch ($activeTabs) {

    case 'about_us':
       $AboutUs = "active";
       $title = lang('about');
    break; 

    case 'terms':
       $termsCondition = "active";
       $title = lang('terms_conditions');
    break; 

    case 'privacy':
        $privacyPolicy = "active";
        $title = lang('privacy_policy');
    break; 
  
    default:
        $Home = "active";
        $title = lang('home_title');
    break;    
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

        <!-- for FB common meta tags -->
        <meta property="fb:app_id" content="<?php echo FACEBOOK_APP_ID;?>" />
        <meta property="og:title" content="APOIM" />
        <meta property="og:url" content="https://www.apoim.com/" />
        <meta property="og:description" content="<?php echo lang('home_title');?>"> 
        <meta property="og:image" content="<?php echo AWS_CDN_FRONT_IMG; ?>apoim_email_logo.png">
        <meta property="og:image:width" content="640" />
        <meta property="og:image:height" content="640" />
        <meta property="og:type" content="article" />

        <link rel="icon" href="<?php echo AWS_CDN_FRONT_IMG; ?>favicon-16x16.png" type="image/x-icon" />
        <title>Apoim | <?php echo $title;?></title>

        <!-- Icon css link -->
        <link href="<?php echo AWS_CDN_FRONT_CSS; ?>font-awesome.min.css" rel="stylesheet">

        <!-- Bootstrap -->
        <link href="<?php echo AWS_CDN_FRONT_CSS; ?>bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/common-style.css" rel="stylesheet">
        <link href="<?php echo base_url().APP_FRONT_ASSETS; ?>css/responsive.css" rel="stylesheet">       
    </head>
    <body> 
    <!--================Frist Main hader Area =================-->
    <header class="header_menu_area white_menu lnk-footer-hed">
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
                    <li class="dropdown submenu">
                        <h4><?php echo $title;?></h4>
                    </li>
                </ul>
                </div>
            </div>
        </nav>
    </header>