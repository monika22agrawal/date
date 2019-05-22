
<?php
$activeTabs = $this->uri->segment('1');

if(!empty($this->uri->segment('2'))){

    $activeTabs = $this->uri->segment('2');
} 

if( !empty($this->uri->segment('2')) && !empty($this->uri->segment('3')) ){
    $activeTabs = $this->uri->segment('3');
}
$title = $Dashboard =  $UserList = $InterestList = $EventList = $PaymentList = $Appointmentlist = $about = $contact =$terms = $privacy = $EducationList = $WorkList =  '' ;

switch ($activeTabs) {

    case 'dashboard':
       $Dashboard = "active";
       $title = "Dashboard";
    break;  

    case 'userList':
       $UserList = "active";
       $title = "UserList";
    break; 

    case 'profile':
       $UserList = "active";
       $title = "Profile";
    break;

    case 'idProofList':
       $idProof = "active";
       $title = "Id Proof List";
    break;

    case 'eventDetail':
        $EventList = "active";
        $title = "Event Detail";
    break;

    case 'interestList':
       $InterestList = "active";
       $title = "Interest List";
    break;  

    case 'educationList':
       $EducationList = "active";
       $title = "Education List";
    break;   

    case 'workList':
       $WorkList = "active";
       $title = "Work List";
    break;  

    case 'eventList':
        $EventList = "active";
        $title = "Event List";
    break;   

    case 'paymentList':
        $PaymentList = "active";
        $title = "Payment List";
        break;
        
    case 'appointmentList':
        $Appointmentlist = "active";
        $title = "Appointment List";
        break;
    case 'aboutUs':
        $about = "active";
        $title = "About";
        break;

    case 'contactUs':
        $contact = "active";
        $title = "Contact Us";
        break;

    case 'termCondition':
        $terms = "active";
        $title = "Terms";
        break;

    case 'privacyPolicy':
        $privacy = "active";
        $title = "Privacy";
        break;

    default:
        $Dashboard = "active";
        $title = "Dashboard";
    break;
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admin | <?php echo $title;?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->

        <?php
            $backend_asset = base_url().'backend_asset/';
        ?>

        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>favicon-16x16.png">
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_BOOTSTRAP_CSS ?>bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_DIST_CSS ?>AdminLTE.min.css">
        <!-- Material Design -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_DIST_CSS ?>bootstrap-material-design.min.css">
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_DIST_CSS ?>ripples.min.css">
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_DIST_CSS ?>MaterialAdminLTE.min.css">
        <!-- MaterialAdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_DIST_CSS ?>skins/all-md-skins.min.css">
        <!-- Morris chart -->
        <!-- <link rel="stylesheet" href="<?php //echo AWS_CDN_BACK_PLUGINS ?>morris/morris.css"> -->
        <!-- jvectormap -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>jvectormap/jquery-jvectormap-1.2.2.css">
        <!-- Date Picker -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>datepicker/datepicker3.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>daterangepicker/daterangepicker.css">
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>toastr/toastr.min.css">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <link href="<?php echo base_url().APP_BACK_ASSETS;?>custom/css/admin_custom.css" rel="stylesheet">
        <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>jQuery/jquery-3.2.1.min.js"></script>
        <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>jQuery/jquery.validate.min.js"></script>
        <link href="<?php echo AWS_CDN_BACK_PLUGINS ?>light-gallery/css/lightgallery.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo AWS_CDN_BACK_PLUGINS ?>datatables/dataTables.bootstrap.css">

        <script type="text/javascript">
            var BASE_URL = '<?php echo base_url(); ?>';
        </script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body class="hold-transition skin-blue sidebar-mini" id="tl_admin_main_body" data-base-url="<?php echo base_url(); ?>">
        <div class="wrapper">
            <header class="main-header">
                <!-- Logo -->
                <a href="<?php echo base_url('admin/dashboard');?>" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini">A<b>P</b>M</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg">APOIM<!-- <b>Admin</b>LTE --></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">

                        <ul class="nav navbar-nav">
                            
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <?php 
                                        $imgUrl = AWS_CDN_USER_PLACEHOLDER_IMG;
                                        $image = $this->session->userdata('profileImage');

                                        if(!empty($image)){
                                            $imgUrl = AWS_CDN_USER_IMG_PATH.$image;
                                        } 
                                    ?>
                                  <img src="<?php echo $imgUrl; ?>" class="user-image" alt="User Image">
                                  <span class="hidden-xs">Admin</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header col">
                                        <img src="<?php echo $imgUrl; ?>" class="img-circle" alt="User Image">
                                        <p>
                                          <?php echo !empty($this->session->userdata('name')) ? ucfirst($this->session->userdata('name')) : 'Admin'; ?> <br> 
                                          <small><?php echo $this->session->userdata('email'); ?></small>
                                        </p>
                                    </li>
                                  
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?php echo base_url(); ?>admin/profile" class="btn btn-default"><span style="position:relative; right: 10px;">Profile</span></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="javascript:void(0);" class="btn btn-default" onclick="logout()"><span style="position:relative; right: 10px;">Log out</span></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="image" style="text-align:center">
                            <img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG; ?>logo.png" alt="User Image">
                        </div>
                    </div>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $Dashboard; ?>"><a href="<?php echo base_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="treeview <?php if(!empty($idProof)){
                        echo $idProof;

                        } else if(!empty($UserList)){
                            echo $UserList;
                        }?>">
                            <a href="javascript:void(0);">
                                <i class="fa fa-user"></i> <span>Users</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                <li class="<?php echo $UserList;?>"><a href="<?php echo base_url('admin/users/userList');?>"><i class="fa fa-circle-o"></i> <span>All User</span></a></li>
                                <li ><a href="<?php echo base_url('admin/users/idProofList'); ?>"><i class="fa fa-circle-o"></i> ID With Hand Requests</a></li>
                            </ul>

                        </li>
                    </ul>                  

                    <ul class="sidebar-menu">
                        <li class="<?php echo $InterestList;?>"><a href="<?php echo base_url('admin/interest/interestList');?>"><i class="fa fa-futbol-o"></i> <span>All Interest</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $EducationList ;?>"><a href="<?php echo base_url('admin/interest/educationList');?>"><i class="fa fa-graduation-cap"></i> <span>All Education</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $WorkList ;?>"><a href="<?php echo base_url('admin/interest/workList');?>"><i class="fa fa-briefcase"></i> <span>All Work</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $EventList;?>"><a href="<?php echo base_url('admin/users/eventList');?>"><i class="fa fa-calendar"></i> <span>All Events</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $PaymentList;?>"><a href="<?php echo base_url('admin/users/paymentList');?>"><i class="fa fa-cc-visa"></i> <span>Payment List</span></a></li>
                    </ul>

                    <ul class="sidebar-menu">
                        <li class="<?php echo $Appointmentlist;?>"><a href="<?php echo base_url('admin/users/appointmentList');?>"><i class="fa fa-calendar-check-o"></i> <span>Appointment List</span></a></li>
                    </ul>
                    <ul class="sidebar-menu">
                        <li class="treeview  <?php if(!empty($about)){
                        echo $about;

                        } else if(!empty($contact)){
                            echo $contact;
                        } else if(!empty($terms)){
                            echo $terms;
                        }else if(!empty($privacy)){
                            echo $privacy;
                        }?>">
                          <a href="javascript:void(0);">
                            <i class="fa fa-sticky-note"></i> <span>Content</span>
                            <span class="pull-right-container">
                              <i class="fa fa-angle-left pull-right"></i>
                            </span>
                          </a>
                   
                          <ul class="treeview-menu">
                            <li ><a href="<?php echo base_url('admin/aboutUs'); ?>"><i class="fa fa-circle-o"></i>About us</a></li>
                            <li ><a href="<?php echo base_url('admin/termCondition'); ?>"><i class="fa fa-circle-o"></i>Terms & conditions</a></li>
                             <li ><a href="<?php echo base_url('admin/privacyPolicy'); ?>"><i class="fa fa-circle-o"></i>Privacy policy</a></li>
                            <!-- <li ><a href="<?php echo base_url('admin/contactUs'); ?>"><i class="fa fa-circle-o"></i>Contact us</a></li> -->                           
                          </ul>
                        </li>
                    </ul>
                </section>
            <!-- /.sidebar -->
            </aside>

            <script type="text/javascript">
                var base_url = '<?php echo base_url();?>';
            </script>