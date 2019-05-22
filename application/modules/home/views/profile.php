<?php 
    $frontend_assets =  base_url().'frontend_asset/';
    //pr($profile);
    //echo $this->uri->segment(4); die;
?>

<script type="text/javascript">
    
    var oldPwd          = '<?php echo lang('old_password_req');?>',
        newPwd          = '<?php echo lang('new_password_req');?>',
        newPwdLen       = '<?php echo lang('new_password_req_len');?>',
        conPwd          = '<?php echo lang('confirm_password_req');?>',
        equalPwd        = '<?php echo lang('equal_password_req');?>',
        updatePwd       = '<?php echo lang('password_updated');?>',
        incPwd          = '<?php echo lang('incorrect_password');?>',
        bizNameReq          = '<?php echo lang('biz_name_req');?>',
        bizNameMin          = '<?php echo lang('biz_name_min_len');?>',
        bizNameMax          = '<?php echo lang('biz_name_max_len');?>',
        bizAddReq          = '<?php echo lang('biz_add_req');?>',
        bizAddReq          = '<?php echo lang('biz_add_req');?>',
        mobNumReq          = '<?php echo lang('mob_num_req');?>',
        mobNumDigReq          = '<?php echo lang('mob_num_req_digit');?>',
        mobNumMinDig          = '<?php echo lang('mob_num_min_digit');?>',
        mobNumMaxDig          = '<?php echo lang('mob_num_max_digit');?>',
        mobNumAlready          = '<?php echo lang('mob_num_already');?>',
        weightReq          = '<?php echo lang('weight_req');?>',
        weightReqVal          = '<?php echo lang('weight_req_val');?>',
        intReq          = '<?php echo lang('interest_req');?>',
        lanReq          = '<?php echo lang('language_req');?>',
        fullNameReq          = '<?php echo lang('full_name_req');?>',
        fullNameMinLen          = '<?php echo lang('full_name_min_len');?>',
        fullNameMaxLen          = '<?php echo lang('full_name_max_len');?>',
        birthReq          = '<?php echo lang('birthday_req');?>',
        allFieldReq          = '<?php echo lang('fiil_all_field_req');?>',
        oneImgReq          = '<?php echo lang('select_atleast_one_img');?>',
        reverifyFaceReq          = '<?php echo lang('reverify_face_detection');?>',
        fiveImgOnlyReq          = '<?php echo lang('event_more_than_five');?>',
        addbankReq          = '<?php echo lang('add_bank_req');?>';
</script>
<!--================Banner Area =================-->
<div class="wraper">
    <section class="banner_area profile_banner">
        <div class="profiles_inners">
            <div class="container" id="uDetail">
            </div>
        </div>
    </section>
    <!--================End Banner Area =================-->

    <!--================Blog grid Area =================-->
    <section class="blog_grid_area pad-extra">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="members_profile_inners">

                        <ul class="nav nav-tabs profile_menu sticky" role="tablist">

                            <li role="presentation" class="<?php echo ($this->uri->segment(4) != 1) ? 'active' : '' ;?>"><a id="overview-clk" onclick="overView();" href="#more-info" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('overview'); ?></a></li>

                            <li role="presentation"><a href="#favrites" onclick="favoriteList(0);" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('my_favourites'); ?><span class="trm-cnt-num" id="favCnt" ><?php echo $profile->totalFavorites;?></span></a></li>

                            <li role="presentation"><a href="#frnds" onclick="$('#frnd-click').click();" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('friends'); ?><span class="trm-cnt-num"><?php echo $profile->totalFriends;?></span></a></li>                                                               
                            <li role="presentation"><a href="#revws" onclick="$('#rev-click').click();" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('reviews'); ?><span class="trm-cnt-num"><?php echo $profile->totalAppReview+$profile->totalEventReview;?></span></a></li>

                            <!-- <li role="presentation"><a href="#subscrptn" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('subscription'); ?></a></li> -->

                            <li role="presentation" class="<?php echo ($this->uri->segment(4) == 1) ? 'active' : '' ;?>"><a href="#setngs" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('settings'); ?></a></li>

                        </ul>
                        
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?php echo ($this->uri->segment(4) != 1) ? 'active' : '' ;?> fade in" id="more-info">
                                <div id="overView"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade in" id="favrites">
                                <div class="profile_list">
                                    <div id="favouriteList"></div>
                                    <br>
                                    <div id="showLoader" class="show_loader"></div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade in" id="frnds">
                                <div class="profile_list">
                                    <div class="an_tab_container frnds-tab">
                                        <ul class="tabs mb-10 frnds-tab">
                                            <li class="active" id="frnd-click" onclick="friendsList(0);" data-tab="#my-frnds"><?php echo lang('my_friends'); ?></li>
                                            <li data-tab="#frnd-reqst" onclick="friendsRequestList(0);" ><?php echo lang('friend_request'); ?></li>
                                        </ul>
                                        <div class="active displayed tab_content" id="my-frnds">
                                            <div id="friend-list" class=""></div>
                                            <br>
                                            <div id="showLoader" class="show_loader clearfix"></div>
                                        </div>
                                        <div class="tab_content" id="frnd-reqst">
                                            <div id="friend-request-list" class=""></div>
                                            <br>
                                            <div id="showLoader" class="show_loader clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <div role="tabpanel" class="tab-pane fade in" id="revws">
                                <div class="profile_list">
                                    <div class="an_tab_container frnds-tab">

                                        <?php

                                        $id = '';
                                        $segment = $this->uri->segment(3); 

                                        if($segment == 'userDetail'){

                                            $id = decoding($this->uri->segment(4));

                                        } elseif ($segment == 'userProfile') {
                                            
                                            $id = $this->session->userdata('userId');
                                        }

                                        ?>
                                        <input type="hidden" value="<?php echo $id;?>" id="app_event_user_id">
                                        <ul class="tabs mb-10 evnt-tab-wid">
                                            <li class="active" id="rev-click" data-tab="#apoin-rvw" onclick="appReviewList(0,'2','<?php echo $id;?>');"><?php echo lang('event'); ?></li>
                                            <li data-tab="#apoin-rvw" onclick="appReviewList(0,'1','<?php echo $id;?>');"><?php echo lang('appointment'); ?></li>
                                        </ul>
                                        <div class="tab_content" id="apoin-rvw">
                                            <div class="author_posts_inners">
                                                <div id="appointmentReviewList"></div> 
                                                <br>
                                                <div id="showLoader" class="show_loader clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade in" id="subscrptn">
                                <div class="profile_list subscrptn-sec-prt">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                            <div class="price_list">
                                                <div class="price_box active-price">
                                                    <h4><?php echo lang('pay_for_appointment'); ?></h4>
                                                    <p class="price mt-5 mb-15">
                                                        <?php echo lang('pay'); ?> <span> $50 </span> <?php echo lang('lifetime'); ?>
                                                    </p>
                                                    <div class="price_round">
                                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>payment.png" />
                                                    </div>
                                                    <p class="prce-pra min-hgt-pra mt-20"><?php echo lang('appointment_pay'); ?></p>
                                                    <div class="form-group mt-20 mb-0">

                                                        <?php if($profile->mapPayment == 0){?>
                                                                                   
                                                            <button type="button" onclick="openStripeModel(this)" data-pType="2" data-pagetype="1" data-title="<?php echo lang('view_user_map'); ?>" class="btn form-control login_btn btn_focs_whte"><?php echo lang('pay'); ?> $50</button>

                                                        <?php }else{ ?>

                                                            <button type="button" class="btn form-control login_btn btn_focs_whte"><?php echo lang('paid'); ?></button>

                                                        <?php } ?>
                                                        
                                                    </div>

                                                    <?php if($profile->mapPayment != 0){?>

                                                        <div class="prce-crcle-slct">
                                                            <i class="fa fa-check"></i>
                                                        </div>

                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                            <div class="price_list">
                                                <div class="price_box active-price">
                                                    <h4><?php echo lang('tobe_top'); ?></h4>
                                                    <p class="price mt-5 mb-15">
                                                        <?php echo lang('pay'); ?><span> $50</span> <?php echo lang('lifetime'); ?>
                                                    </p>
                                                    <div class="price_round">
                                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>top_user_list.png" />
                                                    </div>
                                                    <p class="prce-pra min-hgt-pra mt-20"><?php echo lang('pay_tobe_top'); ?></p>
                                                    <div class="form-group mt-20 mb-0">

                                                        <?php if($profile->showTopPayment == 0){?>

                                                            <button type="button" onclick="openStripeModel(this)" data-ptype="1" data-title="<?php echo lang('being_top'); ?>" class="btn form-control login_btn btn_focs_whte"><?php echo lang('pay'); ?> $50</button>

                                                        <?php }else{ ?>

                                                            <button type="button" class="btn form-control login_btn btn_focs_whte"><?php echo lang('paid'); ?></button>

                                                        <?php } ?>
                                                       
                                                    </div>
                                                    <?php if($profile->showTopPayment != 0){?>
                                                        <div class="prce-crcle-slct">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="price_list">
                                                <div class="price_box">
                                                    <h4>Apoim Premium account</h4>
                                                    <p class="price mt-5 mb-15">
                                                       Pay<span>$200</span>Monthly
                                                    </p>
                                                    <div class="price_round">
                                                        <img src="<?php echo $frontend_assets;?>img/premium.png" />
                                                    </div>
                                                    <?php if(empty($profile->subscriptionId)){?> 

                                                        <p class="prce-pra mt-20">You are on a free plan. Please upgrade plan to get full access.</p>

                                                        <div class="form-group mt-20 mb-0">
                                                            
                                                            <button type="button" onclick="openStripeModel(this)" data-ptype="3" data-title="Monthly Subscription" class="btn form-control login_btn">Subscribe</button>

                                                        </div>

                                                    <?php } else{ 

                                                        $p_currency = $p_amount = $p_interval = ''; $plan_name = 'Free';
                                                        
                                                        if(isset($planDetail) && $planDetail['status'] === true){

                                                            $plan_detail = $planDetail['data'];
                                                            $plan_name = $plan_detail->nickname;
                                                            $p_currency = $plan_detail->currency;
                                                            $p_amount = $plan_detail->amount;
                                                            $p_interval = $plan_detail->interval;
                                                        }
                                                        
                                                        $plan_text = '<p class="prce-pra mt-20">You are on a free plan. Please upgrade plan to get full access.</p>';

                                                        $upgrade = true; //show upgrade button only when plan is Free

                                                        $cancel_text = '';

                                                        if(isset($subsDetail['data']) && !empty($subsDetail['data'])){
                                                            
                                                            $end_time = date('F d, Y',$subsDetail['data']['current_period_end']);

                                                            $plan_text = '<p class="prce-pra mt-20">You are on a <b>'.$plan_name.'</b> plan ending on '.$end_time.'. Your Current plan is <span><sup>$</sup>'.$p_amount.'</span> per '.$p_interval.'.</p>';

                                                            $upgrade = false;

                                                            $cancel_text = '<button type="button" data-toggle="modal" onclick="stripeModel(this)" data-substype="1" class="btn form-control login_btn">Cancel Subscription</button>';

                                                            if($subsDetail['data']['cancel_at_period_end'] == true){

                                                                $cancel_text = '<p class="prce-pra mt-20">You have canceled your current subscription plan and you will be downgraded to <b>Free</b> plan at the end of current billing cycle.</p>';
                                                            }
                                                        }

                                                        echo '<div class="form-group mt-20 mb-0">'.$plan_text.'</div>';
                                                        echo '<div class="form-group mt-20 mb-0">'.$cancel_text.'</div>';                                            
                                                    } ?>

                                                </div>
                                            </div>
                                        </div> -->
                                        <?php if($profile->isBusinessAdded == 1){ ?>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                            <div class="price_list">
                                                <div class="price_box">
                                                    <h4><?php echo lang('promote_biz'); ?></h4>
                                                    <p class="price mt-5 mb-15">
                                                        <?php echo lang('pay'); ?> <span>$50</span> <?php echo lang('monthly'); ?>
                                                    </p>
                                                    <div class="price_round">
                                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>profits.png" />
                                                    </div>

                                                    <?php if(empty($profile->bizSubscriptionId)){?> 
                                                        
                                                        <p class="prce-pra min-hgt-pra mt-20"><?php echo lang('promote_biz_msg'); ?><p>

                                                        <div class="form-group mt-20 mb-0">

                                                            <button onclick="openStripeModel(this)" data-pType="6"  data-title="<?php echo lang('promote_biz_title'); ?>" class="btn form-control login_btn btn_focs_whte"><?php echo lang('subscribe'); ?></button>

                                                        </div>

                                                    <?php }else{

                                                        $p_currency = $p_amount = $p_interval = ''; $plan_name = lang('free');

                                                        if(isset($bizPlanDetail) && $bizPlanDetail['status'] === true){

                                                            $plan_detail = $bizPlanDetail['data'];
                                                            $plan_name = $plan_detail->nickname;
                                                            $p_currency = $plan_detail->currency;
                                                            $p_amount = $plan_detail->amount;
                                                            $p_interval = $plan_detail->interval;
                                                        }
                                                        
                                                        $biz_plan_text = '<p class="prce-pra min-hgt-pra mt-20">'.lang('upgrade_plan').'</p>';

                                                        $upgrade = true; //show upgrade button only when plan is Free

                                                        $biz_cancel_text = '';

                                                        if(isset($bizSubsDetail['data']) && !empty($bizSubsDetail['data'])){
                                                            
                                                            $end_time = date('F d, Y',$bizSubsDetail['data']['current_period_end']);

                                                            $biz_plan_text = '<p class="prce-pra min-hgt-pra mt-20">'.lang('you_are_on').' <b>'.$plan_name.'</b> '.lang('plan_ending_on').' '.$end_time.'. '.lang('your_current_plan').' <span><sup>$</sup>'.$p_amount.'</span> '.lang('per').' '.$p_interval.'.</p>';

                                                            $upgrade = false;

                                                            $biz_cancel_text = '<button type="button" data-toggle="modal" onclick="stripeModel(this)" data-substype="2" class="btn form-control login_btn">'.lang('cancel_subscription').'</button>';

                                                            if($bizSubsDetail['data']['cancel_at_period_end'] == true){

                                                                $biz_cancel_text = '<p class="prce-pra min-hgt-pra mt-20">'.lang('canceled_subscription_msg').'</p>';
                                                            }
                                                        }

                                                        echo '<div class="form-group mt-20 mb-0">'.$biz_plan_text.'</div>';
                                                        echo '<div class="form-group mt-20 mb-0">'.$biz_cancel_text.'</div>';                                        
                                                    } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>                           
                            <div role="tabpanel" class="tab-pane <?php echo ($this->uri->segment(4) == 1) ? 'active' : '' ;?> fade in" id="setngs">
                                <div class="profile_list subscrptn-sec-prt">
                                    <div  class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pl-0 pr-0">
                                            <ul class="nav nav-tabs tabs-left set-tab-sec">
                                                
                                                <li class="<?php echo ($this->uri->segment(4) != 1) ? 'active' : '' ;?>"><a href="#edit-basic-info" data-toggle="tab"><span class="fa fa-edit"></span><?php echo lang('basic_info');?></a></li>

                                                <li><a href="#edit-othr-info" data-toggle="tab"><span class="fa fa-edit"></span><?php echo lang('more_info'); ?></a></li>

                                                <li><a href="#chnge-pswrd" data-toggle="tab"><span class="fa fa-lock"></span><?php echo lang('change_password'); ?></a></li>

                                                <li><a href="#vrfictn" data-toggle="tab"><span class="fa fa-check"></span><?php echo lang('verification'); ?></a></li>

                                                <li><a href="#pymnt" data-toggle="tab"><span class="fa fa-money"></span><?php echo lang('manage_business'); ?></a></li>

                                                <li class="<?php echo ($this->uri->segment(4) == 1) ? 'active' : '' ;?>"><a href="#addAcc" data-toggle="tab" id="okPayBtn"><span class="fa fa-university"></span><?php echo lang('manage_bank_account'); ?></a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">

                                            <div class="tab-content">

                                                <!-- start basic info section -->
                                                <div class="tab-pane <?php echo ($this->uri->segment(4) != 1) ? 'active' : '' ;?>" id="edit-basic-info">

                                                    <form autocomplete="off" id="up_basic_info" method="post" action="<?php echo base_url('home/user/updateUserBasicInfo');?>" enctype="multipart/form-data" class="form floating-label text-left">

                                                        <input type="hidden" name="latitude" value="<?php echo $profile->latitude;?>" id="usrlat">
                                                        <input type="hidden" name="longitude" value="<?php echo $profile->longitude;?>" id="usrlong">

                                                        <input type="hidden" name="city" value="<?php echo $profile->city;?>" id="usrproCity">
                                                        <input type="hidden" name="state" value="<?php echo $profile->state;?>" id="usrproState">
                                                        <input type="hidden" name="country" value="<?php echo $profile->country;?>" id="usrproCountry">

                                                        <input type="hidden" name="bankAccountStatus" value="<?php echo $this->session->userdata('bankAccountStatus');?>" id="getAccStatus">

                                                        <div class="edt-bsc-inf">
                                                            <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('Add_profile_img'); ?></h2>

                                                            <div id="dImg1111" class="uplod-sec text-center mb-20">
                                                                <div class="upload-btn-wrapper addMore">
                                                                    <button class="btn upld-butn"><span><img src="<?php echo AWS_CDN_FRONT_IMG; ?>upload.png" /></span><?php echo lang('upload_profile_img'); ?></button>
                                                                    <input accept="image/*" type="file" id="newImgMy" name="profileImage" onchange="addMoreProfileImg(this)">    
                                                                </div>
                                                                <input type="hidden" id="imgCount" value="<?php echo $imgCount;?>" >
                                                                <input type="hidden" id="faceVerifyStatus" value="<?php echo $profile->isFaceVerified;?>" >
                                                            </div>
                                                            <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 updateSliderImg" style="text-align:center;">
                                                            <?php $countImg = count($images); if(!empty($images)){ 
                                                            $a=1;
                                                            foreach ($images as $k => $get){                          
                                                            ?>
                                                                <div id="dImg<?php echo $get->userImgId; ?>" class="item log_div log_rel ml-10 img_count_no">

                                                                    <?php
                                                                        if(!filter_var($get->imgName, FILTER_VALIDATE_URL) === false) {
                                                                            $img = $get->imgName;
                                                                        }else if(!empty($get->imgName)){ 
                                                                            $img = AWS_CDN_USER_THUMB_IMG.$get->imgName;
                                                                        } else{
                                                                            $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                                        } 
                                                                    ?>
                                                    
                                                                    <img src="<?php echo $img;?>" id="pImg<?php echo $k; ?>" class="smll-sze">           
                                                                    <div class="text-center upload_pic_in_album">           
                                                                        <input class="inputfile hideDiv" type="file" id="imgMy<?php echo $get->userImgId; ?>" name="profileImage" onchange="userProfileImages('<?php echo $get->userImgId; ?>'); document.getElementById('pImg'+<?php echo $k;?>).src = window.URL.createObjectURL(this.files[0])" style="display: none;">
                                                                    </div>

                                                                    <div class="crcle">
                                                                        <a href="javascript:void(0)" onclick="deleteProfileImages('<?php echo $get->userImgId; ?>'); "><span class="fa fa-close"></span></a>
                                                                    </div>

                                                                </div>

                                                                <?php $a++;} }else{ ?>

                                                                    <div class="item log_div log_rel ml-10"></div>

                                                                <?php } ?>
                                                            </div>

                                                            <div class="clearfix"></div>
                                                            <div class="fde-line"></div>

                                                            <div class="othr-bsc-info mt-20 edt-otr-inf">
                                                                <div class="regfrm mt-20">

                                                                    <div class="form-group regfld">
                                                                        <input class="form-control" maxlength="25" value="<?php echo $profile->fullName;?>" name="fullName" required="" type="text" placeholder="<?php echo lang('fullname_placeholder'); ?>">
                                                                    </div>

                                                                    <div class="form-group regfld datePic">
                                                                        <input id="sandbox-container" class="form-control datetimepicker4" value="<?php echo date('Y/m/d',strtotime($profile->birthday));?>" name="birthday" readonly required="" type="text" placeholder="<?php echo lang('birthday_placeholder'); ?>">
                                                                    </div>

                                                                    <div class="form-group">

                                                                        <select class="form-control" name="gender" class="searchpicker" id="exampleFormControlSelect14">

                                                                            <option value=""><?php echo lang('select_gender'); ?></option>

                                                                            <option value="1" <?php if($profile->gender == '1'){ echo "selected='selected'";}?>><?php echo lang('male_gender'); ?></option>

                                                                            <option value="2" <?php if($profile->gender == '2'){ echo "selected='selected'";}?>><?php echo lang('female_gender'); ?></option>

                                                                            <option value="3" <?php if($profile->gender == '3'){ echo "selected='selected'";}?>><?php echo lang('transgender_gender'); ?></option>
                                                                        </select>

                                                                    </div>
                                                                    <?php //if($profile->gender == '2'){ ?>

                                                                        <div class="form-group <?php echo ($profile->gender == '2') ? 'showDiv' : 'showHide';?>" id="gender-hide-show">

                                                                            <select class="form-control" name="eventInvitation" class="searchpicker" id="exampleFormControlSelect144">

                                                                                <option value=""><?php echo lang('event_invitation'); ?></option>

                                                                                <option value="1" <?php if($profile->eventInvitation == '1'){ echo "selected='selected'";}?>><?php echo lang('public_invitation'); ?></option>

                                                                                <option value="2" <?php if($profile->eventInvitation == '2'){ echo "selected='selected'";}?>><?php echo lang('private_invitation'); ?></option>

                                                                                <option value="3" <?php if($profile->eventInvitation == '3'){ echo "selected='selected'";}?>><?php echo lang('both_invitation'); ?></option>
                                                                                
                                                                            </select>

                                                                        </div>

                                                                    <?php //} ?>

                                                                    <div class="form-group regfld">
                                                                        <input class="form-control" id="usraddress" onkeyup="checkAddress();" name="address" value="<?php echo $profile->address;?>" required="" type="text" placeholder="<?php echo lang('current_location'); ?>">
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                        <div class="radio-btns-apoim">
                                                                            <div class="radio__container">
                                                                                <p><?php echo lang('show_me_on_map'); ?></p>
                                                                                <div class="radio-inline">
                                                                                    <input class="radio" id="awesome-item-1" name="showOnMap" type="radio" value="1" <?php if($profile->showOnMap == '1'){ echo 'checked';}?>>
                                                                                    <label class="radio__label" for="awesome-item-1"><?php echo lang('yes'); ?></label>    
                                                                                </div>
                                                                                <div class="radio-inline">
                                                                                    <input class="radio" id="awesome-item-2" name="showOnMap" type="radio" value="2" <?php if($profile->showOnMap == '0'){ echo 'checked';}?>>
                                                                                    <label class="radio__label" for="awesome-item-2"><?php echo lang('no'); ?></label>    
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                        <div class="radio-btns-apoim">
                                                                            <div class="radio__container getAppPay">
                                                                                <p><?php echo lang('appointment_type'); ?></p>
                                                                                <div class="radio-inline">
                                                                                    <input class="radio" id="awesome-item-11" name="appointmentType" type="radio" value="1" <?php if($profile->appointmentType == '1'){ echo 'checked';}?>>
                                                                                    <label class="radio__label" for="awesome-item-11"><?php echo lang('paid'); ?></label>    
                                                                                </div>
                                                                                <div class="radio-inline">
                                                                                    <input id="freeApp" class="radio"  name="appointmentType" type="radio" value="2" <?php if($profile->appointmentType == '2'){ echo 'checked';}?>>
                                                                                    <label class="radio__label" for="freeApp"><?php echo lang('free_event'); ?></label>    
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group text-right mt-30">
                                                                        <button type="button" id="updateBasicInfo" class="btn form-control login_btn btn_focs_whte"><?php echo lang('done'); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- end basic info section -->

                                                <!-- start more info section -->
                                                <div class="tab-pane" id="edit-othr-info">

                                                    <form autocomplete="off" id="up-profile" method="post" action="<?php echo base_url('home/user/updateUserProfileData');?>">

                                                        <div class="edt-otr-inf">
                                                            <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('add_more_info'); ?></h2>
                                                            <div class="regfrm">
                                                                <div class="row">
                                                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                        <div class="form-group">
                                                                        <select class="form-control" name="work_id" class="searchpicker" id="exampleFormControlSelect1">
                                                                            <option value=""><?php echo lang('select_work'); ?></option>
                                                                            <?php  foreach($works as $name):  ?>
                                                                                <option value="<?php echo $name->workId; ?>" <?php if($name->workId == $profile->work_id){ echo "selected='selected'";}?>><?php echo ($this->session->userdata('language') != 'spanish' ) ? $name->name : $name->nameInSpanish; ?></option>
                                                                            <?php endforeach;?>
                                                                        </select>
                                                                    </div>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                        <div class="form-group">
                                                                        <select class="form-control" name="edu_id" class="searchpicker" id="exampleFormControlSelect12">

                                                                            <option value=""><?php echo lang('select_education'); ?></option>
                                                                            <?php  foreach($education as $education):  ?>
                                                                                <option value="<?php echo $education->eduId; ?>" <?php if($education->eduId == $profile->edu_id){ echo "selected='selected'";}?>><?php echo ($this->session->userdata('language') != 'spanish' ) ? $education->education : $education->eduInSpanish; ?></option>
                                                                            <?php endforeach;?>
                                                                        </select>
                                                                    </div>  
                                                                    </div>  
                                                                </div>
                                                                     
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 pr-0 pl-0">
                                                                                <?php $var = explode(' ', $profile->weight); ?>
                                                                                <div class="form-group regfld">
                                                                                    
                                                                                    <input type="text" min="1" class="form-control brdr-unt hgt-rad" onkeypress="return isNumberKey(event);" value="<?php echo $var[0];?>" name="weight" required="" placeholder="<?php echo lang('weight'); ?>">

                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 pl-0 pr-0">
                                                                                <?php $Units = array('Kg','Lbs','Pounds');?>
                                                                                <div class="form-group">
                                                                                    <select class="form-control frm-brdr" name="unit" id="selct-unt1">
                                                                                        <option value=""><?php echo lang('unit'); ?></option>
                                                                                        <?php  foreach($Units as $unit): ?>
                                                                                            <option value="<?php echo $unit; ?>" <?php if((!empty($var[1])) && $unit == $var[1]){ echo "selected='selected'";}?>><?php echo $unit; ?></option>
                                                                                        <?php endforeach;?>  
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                                          
                                                                       
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                            <?php $relation = array('1'=>lang('rel_single'),'2'=>lang('rel_married'),'3'=>lang('rel_divorced'),'4'=>lang('rel_widowed'));?>
                                                                            <div class="form-group">
                                                                                <select class="form-control" name="relationship" id="selct-unt">
                                                                                    <option value=""><?php echo lang('relationship'); ?></option>
                                                                                    <?php  foreach($relation as $key => $val): ?>
                                                                                        <option value="<?php echo $key; ?>" <?php if($val == $profile->relationship){ echo "selected='selected'";}?>><?php echo $val; ?></option>
                                                                                    <?php endforeach;?> 
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <?php $heights = getHeight();?>
                                                                       
                                                                            <select class="form-control" name="height" class="searchpicker" id="exampleFormControlSelect11" data-live-search="true">
                                                                                <option value=""><?php echo lang('select_height'); ?></option>
                                                                                <?php  foreach($heights as $height):  ?>
                                                                                    <option value="<?php echo $height; ?>" <?php if($height == $profile->height){ echo "selected='selected'";}?>><?php echo $height; ?></option>
                                                                                <?php endforeach;?>
                                                                            </select>
                                                                        
                                                                    </div> 
                                                                    <div class="form-group">

                                                                        <?php $language = array('English','Spanish','French');
                                                                            $category = explode(',',$profile->language);
                                                                        ?>
                                                                        <div class="form-group intrst-tgs">

                                                                            <select id="languageBox" name="language[]" class="ui fluid search dropdownLanguage" multiple="">

                                                                                <option value=""><?php echo lang('i_speak'); ?></option>

                                                                                <?php  foreach($language as $value): ?>

                                                                                    <option value="<?php echo $value; ?>" <?php echo (in_array($value,$category))? '' : ''; ?> ><?php echo $value; ?></option>

                                                                                <?php endforeach;?>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                <?php $uIntId = explode(',', $profile->uIntId);
                                                                $uIntName = explode(',', $profile->game);?>
                                                                <div class="form-group intrst-tgs">
                                                                    <div class="tag">
                                                                        <select id="interestBox" name="interest_id[]" class="ui fluid search dropdownInt" multiple="">
                                                                            <option value=""><?php echo lang('interest'); ?></option>
                                                                            <?php foreach($interest as $int): ?>

                                                                                <option value="<?php echo $int->interest; ?>" <?php echo (in_array($int->interest,$uIntName))? '' : ''; ?> ><?php echo $int->interest; ?></option>

                                                                            <?php endforeach;?>  
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <p class="lower-text mb-15"><?php echo lang('enter_type_int');?></p> 
                                                                <div class="clearfix"></div>
                                                                <div class="form-group">
                                                                    <textarea id="comment" placeholder="<?php echo lang('about_you'); ?>" maxlength="50" name="about" rows="1"><?php echo ucfirst($profile->about).'.'; ?></textarea>
                                                                </div>
                                                                <div class="tgle-grp-cht tgle-grp-cht1">
                                                                    <p class="mb-15"><?php echo lang('notification'); ?></p>
                                                                    <div class="tgle">
                                                                        <div class="toggle-group">
                                                                            <input onclick="notiStatus();" type="checkbox" name="isNotification" value="1" id="on-off-switch" tabindex="1" <?php if($profile->isNotification == '1'){ echo 'checked';}?>>
                                                                            <label for="on-off-switch"></label>
                                                                            <div class="onoffswitch pull-right" aria-hidden="true">
                                                                                <div class="onoffswitch-label">
                                                                                    <div class="onoffswitch-inner"></div>
                                                                                    <div class="onoffswitch-switch"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <div class="form-group text-right mt-30">
                                                                    <button type="button" id="updateUserProfile" class="btn form-control login_btn btn_focs_whte"><?php echo lang('done'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- end more info section -->

                                                <!-- start change password section -->
                                                <div class="tab-pane" id="chnge-pswrd">
                                                    <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('change_your_Password'); ?></h2>
                                                    <div class="regfrm">
                                                        <form method="post" id="chngPassword" action="javascript:void(0);">
                                                            <div class="form-group regfld">
                                                                <input type="password" class="form-control" name="oldpassword" id="oldpassword" value="" required title="Please enter old password" placeholder="<?php echo lang('old_password'); ?>"/>
                                                            </div>
                                                            <div class="form-group regfld">
                                                                <input type="password" class="form-control" name="newpassword" id="newpassword" value="" required title="Please enter new password" placeholder="<?php echo lang('new_password'); ?>"/>
                                                            </div>
                                                            <div class="form-group regfld">
                                                                <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" class="form-controller" value="" placeholder="<?php echo lang('confirm_password'); ?>" required />
                                                            </div>
                                                            <div class="form-group text-right mt-30">
                                                                <button type="button" value="LogIn" class="btn form-control login_btn chngPwd btn_focs_whte"><?php echo lang('update'); ?></button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- end change password section -->

<!-- start verification section -->
<div class="tab-pane" id="vrfictn" class="vrfctn-sec">

    <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('verification'); ?></h2>

    <div class="accordion js-accordion">

        <!--  Start Mobile Verification Section  -->
        <div class="accordion__item js-accordion-item">
            <div class="accordion-header js-accordion-header"><span><img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/veriphy1.png" /></span><?php echo lang('sms'); ?> <?php if($profile->otpVerified == 1){?><span class="crcle-veri ml-20"><i class="fa fa-check"></i></span> <?php } ?></div> 
            <div class="accordion-body js-accordion-body">
                <div class="accordion-body__contents">
                    <div class="vrphy-block-stps">
                        <form method="POST" name="otpForm" id="myform" action="<?php echo base_url('home/verification/matchVerificationCode/') ?>" > 
                            <div class="row">
                                <div class="flex-div">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="vrphy-gif">
                                            <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/125.gif">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">

                                        <?php if($profile->otpVerified != 1){?>

                                            <div class="vrfy-txt-prt">
                                                <h3><?php echo lang('mobile_verification'); ?></h3>
                                                <p class="para mt-15"><?php echo lang('mobile_verification_msg'); ?></p>
                                                <!-- <p class="para mt-15">Under Development.</p> -->
                                                <div class="form-group text-left mt-20">
                                                    <button type="button" class="btn form-control login_btn hide-btn mobileView btn_focs_whte"><?php echo lang('proceed'); ?></button>
                                                </div>
                                            </div>

                                        <?php }else{ ?>

                                            <div class="vrfy-txt-prt">
                                                <h3><?php echo lang('mobile_verification'); ?></h3>
                                                <p class="para mt-15 text-center thnk-you"><span class="fa fa-check"></span><?php echo lang('success_mobile_veri'); ?></p>
                                                <div class="form-group text-left mt-20">
                                                    <button type="button" class="btn form-control login_btn btn_focs_whte"><?php echo lang('verified'); ?></button>
                                                </div>
                                            </div>

                                        <?php } ?>                
                                    </div>
                                </div>
                                <div class="mobile-form showHide" id="step-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="mobl-form mt-10">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-5 pr-0">
                                                            <div class="conty-cd">
                                                                <select id="countryCode" name="countryCode" class="form-control brdr-rads-none select2-list dirty searchpicker" data-live-search="true" required>
                                                                <?php foreach($result['result'] as $codes):  ?>
                                                                    <option value="<?php echo '+'.$codes->phonecode; ?>" <?php if( $codes->phonecode.'IN' == "91".$codes->iso){ echo "selected='selected'";}?> ><?php echo '+'.$codes->phonecode. ' ('.$codes->iso.')';?></option>
                                                                <?php endforeach;?>
                                                            </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 col-md-8 col-sm-7 col-xs-7 pl-0">
                                                            <div class="mobile-num">
                                                                <input type="text" class="form-control brdr-rads-none" name="contactNo" value="" id="contactNo" placeholder="<?php echo lang('mobile_no_placeholder'); ?>" onkeypress="return isNumberKey(event);" autofocus required />
                                                            </div>
                                                        </div>
                                                    </div>                                                           
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="lod-mre-btn">
                                        <button type="button" class="btn form-control login_btn stepForm-1 btn_focs_whte"><?php echo lang('continue'); ?></button>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 showHide" id="step-2">
                                    <div class="row mt-30">
                                        <div class="regfrm mt-20">
                                            <div class="form-group regfld lbl-pos">
                                                <input type="text" id="get_otp" onkeypress="return isNumberKey(event);" class="form-control" value="" name="otpcode" maxlength="4" pattern="\d{4}" autofocus autocomplete="new-otp" placeholder="<?php echo lang('mobile_confirmation_code'); ?>"/>
                                            </div>

                                        </div>
                                        <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="form-group regfld">
                                                <input name="code1" type="text" maxlength="1" size='1' onKeyup="autotab(this, document.otpForm.code2);" onkeypress="return isNumberKey1(event);" autofocus class="form-control text-center">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="form-group regfld">
                                                <input name="code2" id="code2" type="text" maxlength="1" size='1' onKeyup="autotab(this, document.otpForm.code3);" onkeypress="return isNumberKey1(event);" autofocus" class="form-control text-center"> 
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="form-group regfld">
                                                <input name="code3" id="code3" type="text" maxlength="1" size='1' onKeyup="autotab(this, document.otpForm.code4);" onkeypress="return isNumberKey1(event);" autofocus" class="form-control text-center"> 
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                            <div class="form-group regfld">
                                                <input name="code4" id="code4" type="text" maxlength="1" size='1' onKeyup="autotab(this, document.otpForm.sendcode);" onkeypress="return isNumberKey1(event);" autofocus" class="form-control text-center">
                                            </div>
                                        </div> -->
                                    </div>
                                    <div class="skp-anchr text-right">
                                        <a href="javascript:void(0)" class="resend-code"><?php echo lang('resend_code'); ?></a>
                                    </div> 
                                    <div class="form-group text-center mt-10">
                                        <button type="button" id="sendcode" class="btn form-control login_btn stepForm-2 btn_focs_whte"><?php echo lang('verification'); ?></button>
                                    </div>    
                                </div>
                            </div>
                        </form>
                        <div class="mobile_veri_form" id='step-3'>
                            <div class="form-group" >
                                <p class="text-center thnk-you"><span class="fa fa-check"></span><?php echo lang('success_mobile_veri'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  End Mobile Verification Section  -->

        <!--  Start Id Verification Section  -->
        <div class="accordion__item js-accordion-item mt-20">
            <div class="accordion-header js-accordion-header"><span><img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/veriphy2.png" /></span><?php echo lang('id_with_hand'); ?> <?php if($profile->isVerifiedId == 1){?> <span class="crcle-veri ml-20"><i class="fa fa-check"></i></span> <?php }elseif ($profile->isVerifiedId == 2) { ?>
                <span class="crcle-veri ml-20 bck-rd"><i class="fa fa-times"></i></span>
            <?php }elseif ($profile->isVerifiedId == 0 && !empty($profile->idWithHand)){ ?> <span class="crcle-veri ml-20 bck-yllw"><i class="fa fa-clock-o"></i></span><?php } ?></div> 
            <div class="accordion-body js-accordion-body">
                <div class="accordion-body__contents">
                    <div class="vrphy-block-stps">
                        <div class="row">
                            <div class="flex-div">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <div class="vrphy-gif">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/face_detection.gif">
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                    <div class="vrfy-txt-prt">
                                        <h3><?php echo lang('id_with_hand'); ?></h3>
                                        <p class="para mt-15"><?php echo lang('id_doc_msg'); ?></p>
                                        <div class="form-group text-left mt-20 hide-ver-btn">
                                            <button type="button" class="btn form-control login_btn idVeriView btn_focs_whte"><?php echo lang('proceed'); ?></button>
                                        </div>
                                    </div>                                            
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt-15 showHide" id="veri-step-1">

                                <form method="POST" action="<?php echo base_url('home/verification/verifyIdWithHand/') ?>">

                                    <?php $status = $class = $faclass =''; if($profile->isVerifiedId == 1){ $status=lang('approved'); $class="fnt-wgt-700 color-grn"; $faclass = 'fa-check';?>

                                    <!-- Approved -->
                                    <div class="log_div">
                                        
                                        <img src="<?php echo AWS_CDN_IDPROOF_THUMB_IMG.$profile->idWithHand; ?>">
                                            
                                    </div>

                                    <?php } elseif ($profile->isVerifiedId == 0 && !empty($profile->idWithHand)) { $status=lang('in_review'); $class="fnt-wgt-700 color-yllw"; $faclass = 'fa-clock-o'; ?>

                                    <!-- In Review -->
                                    <div class="log_div">
                                        
                                        <img src="<?php echo AWS_CDN_IDPROOF_THUMB_IMG.$profile->idWithHand; ?>">
                                       
                                    </div>

                                    <?php }elseif ($profile->isVerifiedId == 2) { $status="Rejected"; $class="fnt-wgt-700 color-rd"; $faclass = 'fa-close';?>

                                    <!-- Rejected -->
                                    <div class="log_div">
                                        
                                        <img src="<?php echo AWS_CDN_IDPROOF_THUMB_IMG.$profile->idWithHand; ?>" id="pImg2">

                                        <div class="text-center upload_pic_in_album"> 
                                            <input accept="images/*" class="inputfile hideDiv" id="file-12" name="idWithHand" onchange="document.getElementById('pImg2').src = window.URL.createObjectURL(this.files[0])" style="display: none;" type="file" />
                                            <label for="file-12" class="upload_pic">
                                            <span class="fa fa-camera"></span></label>
                                        </div>
                                        
                                    </div>
                                    <div id="idWithHand-err" class="frmat"> </div>
                                    <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                    <div class="form-group text-center">
                                        <div class="lod-mre-btn">
                                            <button type="button" class="btn form-control login_btn mt-15 rem-clss submitIdVerificationData btn_focs_whte"><?php echo lang('submit'); ?></button>    
                                        </div>
                                    </div>

                                <?php } else{ ?>

                                    <!-- Not Uploaded -->
                                    <div class="log_div">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>user-acnt-icn.png" id="pImg2">
                                        <div class="text-center upload_pic_in_album">
                                            <input accept="images/*" class="inputfile hideDiv" id="file-12" name="idWithHand" onchange="document.getElementById('pImg2').src = window.URL.createObjectURL(this.files[0])" style="display: none;" type="file">
                                            <label for="file-12" class="upload_pic">
                                                <span class="fa fa-camera"></span>
                                            </label>
                                        </div>
                                        
                                    </div>
                                    <div id="idWithHand-err" class="frmat"> </div>
                                    <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                    <div class="form-group text-center">
                                        <button type="button" value="LogIn" class="btn form-control login_btn mt-15 rem-cls submitIdVerificationData btn_focs_whte"><?php echo lang('verification'); ?></button>
                                    </div>

                                <?php } ?>
                                     
                                </form>
                            </div>
                            <div class="form-group showHide showMsg">
                                <p class="text-center"><?php echo !empty($status) ? lang('status') : '';?></p>   
                                <p class="text-center veri_txt <?php echo $class;?>"><span><i class="fa <?php echo $faclass;?>"></i></span> <?php echo $status;?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  End ID Verification Section  -->

        <!--  Start face Verification Section  -->
        <div class="accordion__item js-accordion-item mt-20">
            <div class="accordion-header js-accordion-header"><span><img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_face@3x.png" /></span><?php echo lang('face_detection'); ?> <?php if($profile->isFaceVerified == 1){?> <span class="crcle-veri ml-20"><i class="fa fa-check"></i></span> <?php } ?></div> 
            <div class="accordion-body js-accordion-body">
                <div class="accordion-body__contents">
                    <div class="panel-body">
                        <div class="vrphy-block-stps">
                            <div class="row">
                                <div class="flex-div">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <div class="vrphy-gif">
                                            <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/face_detection.gif">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                        <div class="vrfy-txt-prt">
                                            <h3><?php echo lang('face_detection'); ?></h3>
                                            <p class="para mt-15"><?php echo lang('face_detect_msg'); ?></p>
                                            <div class="form-group text-left mt-20 hide-face-ver-btn">
                                                <button type="button" value="LogIn" class="btn form-control login_btn faceVeriView btn_focs_whte"><?php echo lang('proceed'); ?></button>
                                            </div> 
                                        </div>                                            
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt-15 showHide" id="face-step-1">

                                    <form method="POST" action="<?php echo base_url('home/verification/faceVerification/') ?>">

                                        <?php $status = $class = $faclass =''; if($profile->isFaceVerified == 1 && !empty($profile->faceImage)){ $status="Verified"; $class="color-grn"; $faclass = 'fa-check';?>

                                            <!-- Verified -->
                                            <div class="log_div">
                                                
                                                <img src="<?php echo AWS_CDN_FACE_VERIFY_IMG_PATH.$profile->faceImage; ?>">
                                                    
                                            </div>

                                        <?php } else { ?>

                                            <!-- Not verified -->
                                            <div class="log_div">
                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>user-acnt-icn.png" id="facePImg">
                                                <div class="text-center upload_pic_in_album">
                                                    <input accept="images/*" class="inputfile hideDiv" id="face-file-12" name="faceImage" style="display: none;" type="file">
                                                    <label for="face-file-12" class="upload_pic">
                                                        <span class="fa fa-camera"></span>
                                                    </label>
                                                </div>                                           
                                            </div>
                                            <div id="faceImage-err"></div>
                                            <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                            <div class="form-group text-center">
                                                <button type="button" class="btn form-control login_btn mt-15 face-rem-clss submitFaceVerificationData btn_focs_whte"><?php echo lang('verification'); ?></button>
                                            </div>
                                            <!-- Not verified -->
                                        <?php } ?>
                                        
                                    </form>

                                </div>

                                <div class="form-group showHide showFaceMsg">
                                    <p class="text-center"><?php echo !empty($status) ? lang('status') : '';?></p>   
                                    <p class="text-center thnk-you veri_txt <?php echo $class;?>"><span><i class="fa <?php echo $faclass;?>"></i></span> <?php echo $status;?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  End face Verification Section  -->
    </div>
</div>
<!-- start verification section --> 
                                                <!-- start business section -->
                                                <div class="tab-pane" id="pymnt">
                                                    <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('manage_biz'); ?></h2>
                                                    <!-- <div class="rgster-bsness text-center">
                                                        <img class="bsnes-image" src="<?php echo $frontend_assets;?>img/busness-img.png">
                                                    </div> -->
                                                    <div class="fde-line"></div>
                                                    <div class="regfrm">

                                                        <form id="businessForm" method="POST" action="<?php echo base_url('home/business/addBusinessData');?>" enctype="multipart/form-data">

                                                            <input type="hidden" value="<?php echo !empty($bizDetail) ? '1' : '2'; ?>" name="businessType">
                                                            <input type="hidden" id="lat" name="businesslat" value="<?php echo (!empty($bizDetail->businesslat) && $bizDetail->businesslat) ? $bizDetail->businesslat : '';?>">
                                                            <input type="hidden" id="long" name="businesslong" value="<?php echo (!empty($bizDetail->businesslong) && $bizDetail->businesslong) ? $bizDetail->businesslong : '';?>">

                                                            <div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center;">
                                                                <div class="log_div bsnes-img text-center mt-30">
                                                                    
                                                                    <img src="<?php echo (!empty($bizDetail->businessImage)) ? $bizDetail->businessImage : AWS_CDN_FRONT_IMG.'placeholder-image.png'; ?>" id="pImg11">
                                                                    <div class="text-center upload_pic_in_album bsnes-cam">
                                                                        <input accept="images/*" class="inputfile hideDiv" id="file-11" name="businessImage" onchange="document.getElementById('pImg11').src = window.URL.createObjectURL(this.files[0])" style="opacity: 0;" type="file">
                                                                        <label for="file-11" class="upload_pic">
                                                                            <span class="fa fa-camera"></span>
                                                                        </label>
                                                                    </div>
                                                                    <div id="businessImage-err"></div>
                                                                </div>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <div class="form-group regfld mt-20">
                                                                <input class="form-control" name="businessName" value="<?php if(!empty($bizDetail->businessName)){ echo $bizDetail->businessName; }else{ echo ($this->input->post('businessName') != '') ? $this->input->post('businessName') : ''; } ?>" required type="text" placeholder="<?php echo lang('biz_name_place'); ?>">
                                                            </div>
                                                            <div class="form-group regfld">
                                                                <input class="form-control" id="address" name="businessAddress" value="<?php if(!empty($bizDetail->businessAddress)){ echo $bizDetail->businessAddress; }else{ echo ($this->input->post('businessAddress') != '') ? $this->input->post('businessAddress') : ''; } ?>" required type="text" placeholder="<?php echo lang('add_location'); ?>">
                                                            </div>
                                                            <div class="form-group text-right mt-30">
                                                                <button type="button" name="next" class="btn form-control login_btn rem-cls submitBusinessData btn_focs_whte" value="Next"><?php echo lang('done'); ?></button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>  
                                                <!-- start business section -->  

                                                <!-- start manage bank account -->
                                                <div class="tab-pane <?php echo ($this->uri->segment(4) == 1) ? 'active' : '' ;?>" id="addAcc">
                                                    <h2 class="subsrbe-hedr text-center mb-20"><?php echo lang('manage_bank_account'); ?></h2>
                                                    <div class="regfrm">
                                                        <form method="post" id="add_band_accP" action="<?php echo base_url('home/payment/addBankAccount');?>">
                                                            <div class="row">
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" name="firstName" value="<?php if(!empty($bankDetail->firstName)){ echo $bankDetail->firstName; }else{ echo ($this->input->post('firstName') != '') ? $this->input->post('firstName') : ''; } ?>" required placeholder="<?php echo lang('first_name'); ?>"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" name="lastName" value="<?php if(!empty($bankDetail->lastName)){ echo $bankDetail->lastName; }else{ echo ($this->input->post('lastName') != '') ? $this->input->post('lastName') : ''; } ?>"  required placeholder="<?php echo lang('last_name'); ?>"/>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input id="date22" class="form-control datetimepicker4" name="dob" value="<?php if(!empty($bankDetail->dob)){ echo $bankDetail->dob; }else{ echo ($this->input->post('dob') != '') ? $this->input->post('dob') : ''; } ?>" readonly required="" type="text" placeholder="<?php echo lang('date_of_birth'); ?>">
                                                                    </div>
                                                                </div>
                                                                <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" onkeypress="return isNumberKey1(event);" name="routingNumber" value="<?php if(!empty($bankDetail->routingNumber)){ echo $bankDetail->routingNumber; }else{ echo ($this->input->post('routingNumber') != '') ? $this->input->post('routingNumber') : ''; } ?>" class="form-controller" placeholder="<?php echo lang('routing_number'); ?>" required />
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" onkeypress="return isNumberKey1(event);" name="postalCode" value="<?php if(!empty($bankDetail->postalCode)){ echo $bankDetail->postalCode; }else{ echo ($this->input->post('postalCode') != '') ? $this->input->post('postalCode') : ''; } ?>" placeholder="Postal code" required />
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" onkeypress="return isNumberKey1(event);" name="ssnLast" value="<?php if(!empty($bankDetail->ssnLast)){ echo $bankDetail->ssnLast; }else{ echo ($this->input->post('ssnLast') != '') ? $this->input->post('ssnLast') : ''; } ?>" placeholder="<?php echo lang('ssn_last'); ?>" required />
                                                                    </div>
                                                                </div> -->
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                    <div class="form-group regfld">
                                                                        <input type="text" class="form-control" onkeypress="return isNumberKey1(event);" name="accountNumber" value="<?php if(!empty($bankDetail->accountNumber)){ echo $bankDetail->accountNumber; }else{ echo ($this->input->post('accountNumber') != '') ? $this->input->post('accountNumber') : ''; } ?>" placeholder="<?php echo lang('iban_number'); ?>" required />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group text-right mt-30">
                                                                <button type="button" class="btn form-control login_btn addBank addBankAccountP btn_focs_whte"><?php echo ($this->session->userdata('bankAccountStatus') == 1) ? lang('update_acc') : lang('add_acc'); ?></button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- end manage bank account -->                                                
                                            </div>
                                        </div>                                            
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>                            
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="right_sidebar_area">
                            <div id="loadImgSlider"></div>
                        <aside class="s_widget recent_post_widget">
                            <div class="s_title">
                                <h4><?php echo lang('verification'); ?></h4>
                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
                            </div>
                            <div class="verifctn-blck list-hd">
                                <ul>
                                    <li class="<?php echo ($profile->otpVerified == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top">
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('sms'); ?>">
                                            <div class="verphy-prt">
                                                <?php if($profile->otpVerified == 1){?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_sms@3x.png" /> 
                                                    <i class="fa fa-check"></i>
                                                <?php }else{ ?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_sms@3x.png" /> 
                                                <?php } ?>                                        
                                            </div>                                    
                                        </a>
                                    </li>
                                    <li class="<?php echo ($profile->isVerifiedId == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top">
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('id_with_hand'); ?>">
                                            <div class="verphy-prt">
                                                <?php if($profile->isVerifiedId == 1){?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_id@3x.png" />    
                                                    <i class="fa fa-check"></i>        
                                                <?php }else{ ?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_id@3x.png" /> 
                                                <?php } ?>                                   
                                            </div>                                     
                                        </a>
                                    </li>
                                    <li class="<?php echo ($profile->isFaceVerified == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('face_detection'); ?>">
                                        <a href="javascript:void(0)">
                                            <div class="verphy-prt">
                                                <?php if($profile->isFaceVerified == 1){?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_face@3x.png" />    
                                                    <i class="fa fa-check"></i>        
                                                <?php }else{ ?>
                                                    <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_face@3x.png" /> 
                                                <?php } ?>        
                                            </div>
                                        </a>
                                    </li>             
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--================End Blog grid Area =================-->
<input type="hidden" id="page-count" value="">
<input type="hidden" id="type" value="">
<input type="hidden" id="tabType" value="">
<!-- The Modal for unfriend -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('unfriend'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('sure_msg_unfriend'); ?> <span id="fName"></span>.</p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">

                <form method="post" action="<?php echo base_url('home/user/unfriend');?>" class="form floating-label text-left">

                    <input type="hidden" id="fId" name="friendId">

                    <div class="form-group text-right pay-btn">

                        <button type="button" value="LogIn" class="btn form-control login_btn unfriend-user btn_focs_whte"><?php echo lang('ok'); ?></button>

                        <a href="javascript:void(0)" class="checkMap" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- end unfriend model -->

<!-- The Modal for cancel subscription -->
<div class="modal fade" id="myModalCheckFriend" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('cancel_subscription'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('cancel_subs_popupmsg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" value="LogIn" class="btn form-control login_btn cancelSubscription btn_focs_whte"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" class="checkMap" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End The Modal for cancel subscription -->

<!-- The Modal For delete profile image -->
<div class="modal fade" id="myModaldelImg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('delete_img_title'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15" id="chngtxt"><?php echo lang('delete_img_msg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn set_del_btn btn_focs_whte"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" class="checkMap" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End The Modal For delete profile image -->

<!-- The Modal for update bank account -->
<div class="modal fade" id="myModalShowPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('bank_acc'); ?></h5>
                <button type="button" class="close makeFree" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><span id="bank-msg"></span></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn makeFreePay btn_focs_whte" data-dismiss="modal"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" class="makeFree" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">    

    var imgCount    = '<?php echo $imgCount;?>';
    var w           = '<?php echo $this->uri->segment(4);?>';

    $(document).ready(function(){
        
        $('.searchpicker').selectpicker();
        if(w != 1){
            $('#overview-clk').click(); // to click on overview tab
        }        

        var todayDatea = new Date().getYear();
        var dateNow = '<?php echo date('Y/m/d',strtotime($profile->birthday));?>';

        $('#sandbox-container').datetimepicker({

            format          : 'YYYY-MM-DD',
            minDate         : '1919-01-01',
            ignoreReadonly  : true,
            useCurrent      : false,
            defaultDate     : dateNow,
            maxDate: new Date(new Date().setYear(todayDatea + 1882))

        });

        var todayDate = new Date().getYear();

        $('#date22').datetimepicker({

            format          : 'YYYY-MM-DD',
            ignoreReadonly  : true,
            maxDate         : new Date(new Date().setYear(todayDate + 1887))
        });

        var userIntNames = <?php echo json_encode($uIntName);?>;        
        
        //semantic js for showing selected interet
        $('.dropdownInt').dropdown('set selected',userIntNames);
        $('.dropdownInt').dropdown({allowAdditions: true});

        //semantic js for showing selected language
        var category = <?php echo json_encode($category);?>;
        $('.dropdownLanguage').dropdown('set selected',category);
    });
 
     /* for scroll using ajax pagination for event and appointment list*/
    
    $(window).scroll(function() {

        var totalUser   = $('#total-count').val();
        var page        = $("#page-count").val();
        var type        = $("#type").val(); // type for appointment and event review
        var tabType     = $("#tabType").val(); // type for appointment and event review
        var id          = $("#app_event_user_id").val();

        if($(window).scrollTop() == $(document).height() - $(window).height()) {

            if((totalUser != 0)){
                
                switch (tabType){

                    case 'favTab':
                        favoriteList(page);
                    break;

                    case 'reviewTab':
                        appReviewList(page,type,id);
                    break;

                    case 'friendTab':
                        friendsList(page);
                    break;

                    case 'friendReqTab':
                        friendsRequestList(page);
                    break;

                }
            }           
        }
    });
</script>