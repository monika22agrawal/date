<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>APOIM | Admin</title>
        <?php
            $backend_assets =  base_url().'backend_asset/';
            ?>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Favicon -->
        <link rel="shortcut icon" href="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>favicon-16x16.png" />
        <!-- Bootstrap 3.3.6 -->
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
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="#"><img style="display:inline-block" src="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>logo.png" class="img-responsive" alt="" /></a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Admin Login</p>
                <form action="<?php echo site_url('admin/login') ?>" method="post">
                    <?php if (!empty($this->session->flashdata('login_err'))) { ?>
                    <div class="alert alert-danger">
                        <span style="text-align: center"><?php echo $this->session->flashdata('login_err'); ?></span>
                    </div>
                    <?php } ?>
                    <div class="form-group has-feedback">
                        <?php echo form_input($email); ?>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <?php echo form_input($password); ?>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div >
                            <button type="submit" class="btn btn-primary btn-raised btn-block btn-flat ">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <!-- /.social-auth-links -->
                <!-- <a href="<?php echo base_url('admin/forgot_password');?>">I forgot my password</a><br> -->
            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
        <!-- jQuery 2.2.3 -->
        <script src="<?php echo AWS_CDN_BACK_PLUGINS ?>jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo AWS_CDN_BACK_BOOTSTRAP_JS ?>bootstrap.min.js"></script>
        <!-- Material Design -->
        <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>material.min.js"></script>
        <script src="<?php echo AWS_CDN_BACK_DIST_JS ?>ripples.min.js"></script>
        <script>
            $.material.init();
        </script>
    </body>
</html>