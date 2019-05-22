<?php 
    $frontend_assets =  base_url().'frontend_asset/';    
?>
<!--================Banner Area =================-->
<section class="banner_area">
    <div class="container">
        <div class="banner_content">
            <h3 title="New account"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('signup_title'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
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
                                    <span><?php echo lang('facebook_signup'); ?></span>
                                </a>
                            </li>
                            <li>
                                <a id="customBtn" href="javascript:void(0);" class="gplus">
                                    <i class="fa fa-google"></i>
                                    <span><?php echo lang('google_signup'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt-20 mb-20">
                    <span><img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt=""></span> <?php echo lang('or_signup'); ?>
                    <span><img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border-rgt.png" alt=""></span>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3 col-sm-12 col-xs-12">
                    <div class="form-wizard form-header-classic form-body-classic" id="msform">
           
                        <form method="POST" id="myform" action="<?php echo base_url('home/login/userRegister') ?>" autocomplete="off" enctype="multipart/form-data">

                            <input type="hidden" id="addr" name="address" value="<?php echo $this->session->userdata('address');?>">
                            <input type="hidden" id="latitude" name="latitude" value="<?php echo $this->session->userdata('lat');?>"> 
                            <input type="hidden" id="longitude" name="longitude" value="<?php echo $this->session->userdata('long');?>">
                            <input type="hidden" id="city" name="city" value="<?php echo $this->session->userdata('city');?>">
                            <input type="hidden" id="state" name="state" value="<?php echo $this->session->userdata('state');?>"> 
                            <input type="hidden" id="country" name="country" value="<?php echo $this->session->userdata('country');?>"> 

                            <input type="hidden" name="socialId" value="" id="socialId"> 

                            <input type="hidden" name="socialType" value="" id="socialType"> 

                            <input type="hidden" name="profileImage" value="" id="profileImage">

                            <?php if(!empty($msg)){?>

                                <div class="alert alert-success" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <span>
                                    <strong><?php echo lang('success'); ?></strong> <?php echo lang('your'); ?> <span id="showSocialType"></span> <?php echo lang('complete_signup_process'); ?></span>
                                </div>

                            <?php } ?>

                            <div style="display:none;" class="alert alert-danger " role="alert" id="err-invalid">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>Oh snap!</strong> <span id="error-invalid"></span>
                            </div>
                            <div style="display:none;" class="alert alert-success " role="alert" id="err-sucess">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>Sucess!</strong><span id="error-sucess"></span>
                            </div>
                            <fieldset data-step="1">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step1_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">Tell us something more about you</h3> -->
                                        <div class="boxed" >
                                            <div>
                                                <input type="radio" id="male" name="gender" value="1">
                                                <label for="male"><?php echo lang('male_gender'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="female" name="gender" value="2">
                                                <label for="female"><?php echo lang('female_gender'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="trnsgndr" name="gender" value="3">
                                                <label for="trnsgndr"><?php echo lang('transgender_gender'); ?></label>
                                            </div>
                                        </div>
                                        <!-- to show error using jquery error placement -->
                                        <div class="genderError"></div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?></button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="2">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step2_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">Tell us something more about you</h3> -->
                                        <div class="boxed">
                                            <div>
                                                <input type="radio" id="frnd" name="purpose" value="1">
                                                <label for="frnd"><?php echo lang('make_new_friends'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="cht" name="purpose" value="2">
                                                <label for="cht"><?php echo lang('chat'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="date" name="purpose" value="3">
                                                <label for="date"><?php echo lang('date'); ?></label>
                                            </div>
                                        </div>
                                        <!-- to show error using jquery error placement -->
                                        <div class="purposeError"></div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="3">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step3_title'); ?></h2>
                                       <!--  <h3 class="fs-subtitle">Tell us something more about you</h3> -->
                                        <div class="boxed">
                                            <div>
                                                <input type="radio" id="girls" name="dateWith" value="1">
                                                <label for="girls"><?php echo lang('date_with_girls'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="guys" name="dateWith" value="2">
                                                <label for="guys"><?php echo lang('date_with_guys'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="trns" name="dateWith" value="3">
                                                <label for="trns"><?php echo lang('date_with_transgender'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="all" name="dateWith" value="4">
                                                <label for="all"><?php echo lang('date_with_all'); ?></label>
                                            </div>
                                        </div>
                                        <!-- to show error using jquery error placement -->
                                        <div class="dateWithError"></div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="4">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step4_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">Tell us something more about you</h3> -->
                                        <div class="boxed">
                                            <div>
                                                <input type="radio" id="Publc" name="eventInvitation" value="1">
                                                <label for="Publc"><?php echo lang('public_invitation'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="prvet" name="eventInvitation" value="2">
                                                <label for="prvet"><?php echo lang('private_invitation'); ?></label>
                                            </div>
                                            <div>
                                                <input type="radio" id="bth" name="eventInvitation" value="3">
                                                <label for="bth"><?php echo lang('both_invitation'); ?></label>
                                            </div>
                                        </div>
                                        <!-- to show error using jquery error placement -->
                                        <div class="eventInvitationError"></div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="5">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step5_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">Enter your email address, we will send you confirmation code to verify email address...</h3> -->
                                        <div class="mt-40">
                                            
                                            <div class="regfrm mt-20">
                                                <div class="form-group regfld lbl-pos">
                                                    <input type="text" class="form-control" name="email" value="" id="email" autofocus required autocomplete="off" placeholder="<?php echo lang('email_placeholder'); ?>"/>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        <button type="button" name="next" class="btn-next btn form-control login_btn stepForm-1 btn_focs_whte"><?php echo lang('next_process'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="6">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step6_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">We have sent you a confirmation code to validate your email address...</h3> -->
                                        <div class="mt-40">
                                            
                                            <div class="regfrm mt-20">
                                                <div class="form-group regfld lbl-pos">
                                                    <input type="text" id="get_otp" onkeypress="return isNumberKey(event);" class="form-control" value="" name="otp" maxlength="4" pattern="\d{4}" autofocus autocomplete="new-otp" placeholder="<?php echo lang('email_confirmation_code_placeholder'); ?>"/>
                                                </div>

                                            </div>
                                            <!-- <button type="button" name="next" class="form-control login_btn stepForm-4">Resend Code
                                            </button> -->
                                            <div class="text-right">
                                                <a href="javascript:void(0);" class="rsnd-cde stepForm-4"><?php echo lang('resend_code'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        
                                        <button type="button" name="next" class="btn form-control login_btn stepForm-2"><?php echo lang('next_process'); ?>
                                        </button>
                                        <button type="button" style="display: none;" name="next" class="btn-next btn form-control login_btn btn_focs_whte"><?php echo lang('next_process'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset data-step="7">
                                <div class="animated">
                                    <div class="ansBlock">
                                        <h2 class="fs-title"><?php echo lang('step7_title'); ?></h2>
                                        <!-- <h3 class="fs-subtitle">Tell us something more about you</h3> -->
                                        <div class="mt-40">
                                            <div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center;">
                                                <div class="log_div">
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>user-acnt-icn.png" id="pImg">
                                                    <div class="text-center upload_pic_in_album">
                                                        <input accept="images/*" class="inputfile hideDiv" id="file-1" name="profileImage" onchange="document.getElementById('pImg').src = window.URL.createObjectURL(this.files[0])" style="opacity: 0;" type="file" value="">
                                                        <label for="file-1" class="upload_pic">
                                                            <span class="fa fa-camera"></span>
                                                        </label>
                                                    </div>
                                                    <div id="profileImage-err"></div>
                                                </div>
                                            </div>
                                            <p class="note-txt"><?php echo lang('upload_max_img_size'); ?></p>
                                            <div class="clearfix"></div>
                                            <div class="regfrm mt-20">
                                                <div class="form-group regfld lbl-pos">
                                                    <input type="text" class="form-control" id="fullName" name="fullName" value="" placeholder="<?php echo lang('fullname_placeholder'); ?>" required />
                                                </div>

                                                <!-- show only social registration -->
                                                <div class="form-group regfld lbl-pos showHide" id="socialEmail">
                                                    <input type="text" class="form-control" name="" value="" id="socialemail" autofocus readonly autocomplete="off" placeholder="<?php echo lang('email_placeholder'); ?>"/>
                                                </div>

                                                <div class="form-group regfld lbl-pos pwdhide">
                                                    <input type="password" minlength="8" class="form-control" id="password" name="password" value="" placeholder="<?php echo lang('password_placeholder'); ?>" required />
                                                </div>

                                                <div class="form-group regfld lbl-pos">
                                                    <!-- <input id="datetimepicker4" class="form-control datetimepicker4" name="" required="" type="text" placeholder="Birthday"> -->
                                                    <input type="text" class="form-control birth" name="birthday" value="" id='datepicker4' placeholder="<?php echo lang('birthday_placeholder');?>" required />
                                                </div>
                                            </div>
                                            <div class="tc-wrap">
                                                <p><?php echo lang('continue_signup_process'); ?> <a href="#"><?php echo lang('terms_conditions'); ?></a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wizard-buttons mt-30 text-center">
                                        <input type="button" name="previous" class="previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                        <button type="button" name="next" class="btn-submit btn form-control login_btn btn_focs_whte emove-stepForm-3 stepForm-3"><?php echo lang('sign_up'); ?>
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="text-center val-line mt-30">
                    <p><?php echo lang('already_have_account'); ?> <a class="ancr-tg" href="<?php echo base_url('home/login');?>"><?php echo lang(
                        'login_title'); ?></a></p>
                </div>
            </div>
        </div>
    </section>
</div>
<!--================End Contact From Area =================-->

<script type="text/javascript">

    var fullName = sessionStorage.getItem("fullName");
    var email = sessionStorage.getItem("email");
    var socialId = sessionStorage.getItem("socialId");
    var profileImage = sessionStorage.getItem("profileImage");
    var socialType = sessionStorage.getItem("socialType");
   
    if(socialId != '' && socialId != null){
        $(".pwdhide").hide();
        $("#pImg").attr("src",profileImage);
    }

    if(fullName != '' && fullName != null){

        $("#fullName").val(fullName);
    }

    if(email != '' && email != null){
        $("#socialemail").attr('readonly','readonly');
        $("#socialemail").val(email);
        $("#socialEmail").show();
        $("#email").val(email);
    }else{
        $("#socialEmail").hide();
    }

    $("#socialId").val(socialId);
    $("#profileImage").val(profileImage);
    $("#socialType").val(socialType);
    $("#showSocialType").text(socialType);

    sessionStorage.removeItem("fullName");  
    sessionStorage.removeItem("email");       
    sessionStorage.removeItem("socialId");       
    sessionStorage.removeItem("profileImage");       
    sessionStorage.removeItem("socialType");

    $(document).ready(function(){

        /*getLocation();
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else { 
                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {

            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            $('#latitude').val(lat);
            $('#longitude').val(lng);
            var google_map_position = new google.maps.LatLng( lat, lng );
            var google_maps_geocoder = new google.maps.Geocoder();
            google_maps_geocoder.geocode(
                { 'latLng': google_map_position },

                function( results, status ) {
                   var address = (results[0].formatted_address);
                   $('#addr').val(address);
                }
            );
        }
*/
        setTimeout(function(){
            $('.birth').val('');
            $('#password').val('');
        }, 5000);
    });
    
</script>