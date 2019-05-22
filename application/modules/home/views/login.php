<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>

<!--================Banner Area =================-->
<section class="banner_area">
    <div class="container">
        <div class="banner_content">
            <h3 title="Login"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('login'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
        </div>
    </div>
</section>
<!--================End Banner Area =================-->

<!--================Contact From Area =================-->
<div class="wraper">
    <section class="contact_form_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3 col-sm-12 col-xs-12">
                    <div class="socil-icns text-center">
                        <ul class="list-inline">
                            <li>
                                <a id="loginBtn" href="javascript:void(0);" class="fb">
                                    <i class="fa fa-facebook"></i>
                                    <span><?php echo lang('facebook_login'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a id="customBtn" href="javascript:void(0);" class="gplus">
                                    <i class="fa fa-google"></i>
                                    <span><?php echo lang('google_login'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>

                    <div class="text-center mt-20">
                        <span><img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt=""></span> <?php echo lang('or_signup'); ?>
                        <span><img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border-rgt.png" alt=""></span>
                    </div>

                    <div class="clearfix"></div>

                    <div class="regfrm mt-20">
                        
                        <form id="loginForm" onsubmit="return false;" method="POST" action="<?php echo base_url('home/login/userLogin') ?>">

                            <div class="form-group regfld">
                                <input type="text" class="form-control" name="email" value="<?php echo $this->input->cookie('email', TRUE); ?>" required autocomplete="off" placeholder="<?php echo lang('email_placeholder'); ?>"/>
                            </div>
                            <div class="form-group regfld">
                                <input type="password" class="form-control" name="password" value="<?php echo $this->input->cookie('password', TRUE); ?>" placeholder="<?php echo lang('password_placeholder'); ?>" required />
                            </div>
                            <div class="dsply-in-blk">
                                <div class="dsply-blck-lft">
                                    <div class="keeplogin">
                                        <input type="checkbox" name="rem" value="1" <?php if($this->input->cookie('email',true)){ ?>checked <?php } ?> id="box-1" >
                                        <label for="box-1"><?php echo lang('remember_me'); ?></label>
                                    </div>
                                </div>
                                <div class="dsply-blck-rgt">
                                    <a class="keeplogin frgt-pass" href="javascript:void(0);" data-toggle="modal" data-target="#frgt-pswrd"><?php echo lang('forgot_password'); ?></a>
                                </div>
                            </div>
                            <div class="form-group text-right mt-30">
                                <button type="submit" value="LogIn" class="btn form-control login_btn spinner btn_focs_whte"><?php echo lang('login'); ?></button>
                            </div>
                            <div class="clearfix"></div>
                            <div class="text-center val-line mt-30">
                                <p><?php echo lang('dont_have_account'); ?> <a class="ancr-tg" href="<?php echo base_url('home/login/registration');?>"><?php echo lang('create_account'); ?></a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--================End Contact From Area =================-->

<!---Forgot Modal Start Here-->

<div class="modal fade" id="frgt-pswrd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('forgot_password'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="forgot_form" method="post" action="<?php echo base_url('home/login/forgotPassword');?>" class="form floating-label text-left" onsubmit="return false;">
                <div class="modal-body mdl-body">
                    <div class="regfrm mdl-pad mt-20">
                        <p class="para text-left mb-15"><?php echo lang('forgot_pwd_msg'); ?></p>
                        <div class="form-group regfld">
                            <input type="text" class="form-control" name="email" placeholder="Email" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="submit" value="LogIn" class="btn form-control login_btn forgot-password"><?php echo lang('submit'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!---Forgot Modal End Here-->