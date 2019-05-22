<?php 
    $frontend_assets =  base_url().'frontend_asset/';
    //pr($userDetail);
?>
<!--================Banner Area =================-->
<section class="banner_area profile_banner">

    <div class="profiles_inners">
        <div class="container">
            <div class="profile_content">
                
                <!-- Start to make an appointment -->
    
                <?php if($this->session->userdata('front_login') == true){ ?>

                    <!-- already sent appointment -->
                    <?php //if($appStatus->isAppointment == 1){?>

                        <!-- <a href="javascript:void(0);" onclick="msg(this)" data-msg="You already sent appointment request to <?php echo ucfirst($userDetail->fullName);?>." class="btn login_btn mke-btn">Make an appointment <span class="fa fa-map-marker"></span></a>

                        <a href="javascript:void(0);" onclick="msg(this)" data-msg="You already sent appointment request to <?php echo ucfirst($userDetail->fullName);?>." class="mrcker-icn">
                            <span class="markr-postn"><i class="fa fa-map-marker"></i></span>
                        </a>  -->   

                    <!-- already have an appointment -->
                    <?php //}elseif($appStatus->isAppointment == 2){ ?>

                       <!--  <a href="javascript:void(0);" onclick="msg(this)"" data-msg="You already have an appointment with <?php echo ucfirst($userDetail->fullName);?>." class="btn login_btn mke-btn">Make an appointment <span class="fa fa-map-marker"></span></a>

                        <a href="javascript:void(0);" onclick="msg(this)"" data-msg="You already have an appointment with <?php echo ucfirst($userDetail->fullName);?>." class="mrcker-icn">
                            <span class="markr-postn"><i class="fa fa-map-marker"></i></span>
                        </a> -->

                    <!-- create appointment -->
                    <?php //}else{ ?>
                    
                        <a href="<?php echo base_url('home/appointment/createAppointment/').$this->uri->segment(4).'/';?>" class="btn login_btn mke-btn btn_focs_whte"><?php echo lang('make_appointment'); ?> <span class="fa fa-map-marker"></span></a>

                        <a href="<?php echo base_url('home/appointment/createAppointment/').$this->uri->segment(4).'/';?>" class="mrcker-icn">
                            <span class="markr-postn"><i class="fa fa-map-marker"></i></span>
                        </a>

                    <?php //} ?>


                <?php } else { ?>

                    <a href="javascript:void(0)" onclick="msg(this)" data-msg="<?php echo lang('login_make_appointment'); ?>" class="btn login_btn mke-btn"><?php echo lang('make_appointment'); ?> <span class="fa fa-map-marker"></span></a>

                    <a href="javascript:void(0)" onclick="msg(this)" data-msg="<?php echo lang('login_make_appointment'); ?>" class="mrcker-icn">
                        <span class="markr-postn"><i class="fa fa-map-marker"></i></span>
                    </a>

                <?php } ?>

                <!-- End to make an appointment -->

                <div class="user_img usr-img-like">
                    <?php  
                    if(!filter_var($userDetail->imgName, FILTER_VALIDATE_URL) === false) { 
                        $img = $userDetail->imgName;
                    }else if(!empty($userDetail->imgName)){ 
                        $img = AWS_CDN_USER_THUMB_IMG.$userDetail->imgName;
                    } else{                    
                        $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                    } ?>
                    <img class="img-circle" src="<?php echo $img;?>" alt="">
                </div>
                <div class="user_name">
                    <h3 class="text-transform-upr"><?php echo ucfirst($userDetail->fullName);?></h3>
                    <h4><?php if($userDetail->gender == 1) { echo lang('male_gender');}elseif($userDetail->gender == 2){echo lang('female_gender');}else{ echo lang('transgender_gender');}?> | <?php echo $userDetail->age;?></h4>
                    <ul>
                        <li class="adress mrkr-icn"><span class="fa fa-map-marker"></span><?php echo !empty($userDetail->address) ? $userDetail->address : "NA";?></li>

                        <?php if($this->session->userdata('front_login') == true){ ?>

                            <?php

                                $likeClass = 'unlikeCls';
                                $likeStatus = '1';

                                if($like->isLike =='1')
                                {
                                   $likeClass = 'likeCls';
                                   $likeStatus = '0';
                                }
                            ?> 
                            <!-- for like / unlike -->
                            <li class="action-icn text-center pt-8">
                                <ul>
                                    <li id="likeUser" class="lkes-cnt icn-fnt">

                                        <a id="lkUser" onclick="markLike('<?php echo $this->uri->segment(4);?>','<?php echo $likeStatus;?>')" href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('like'); ?>">

                                            <span class="fa fa-heart <?php echo $likeClass;?>"></span>

                                        </a>
                                    </li>

                                    <li class="lkes-cnt" id="getLike"><?php echo $userDetail->totalLikes;?></li>
                                </ul>
                            </li>

                            <?php

                                $favoriteClass = 'unlikeCls';
                                $favStatus = '1';

                                if($fav->isFavorite=='1')
                                {
                                   $favoriteClass = 'likeCls';
                                   $favStatus = '0';
                                }
                            ?> 
                            <!-- add / remove favorite -->
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <li id="favUser" class="lkes-cnt icn-fnt">

                                        <a id="fvUser" onclick="markFavorite('<?php echo $this->uri->segment(4);?>','<?php echo $favStatus;?>')" href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('favourite'); ?>">

                                            <span class="fa fa-star <?php echo $favoriteClass;?>"></span>
                                        </a>

                                    </li>
                                    <li class="lkes-cnt"></li>
                                </ul>
                            </li>
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <li class="shre-sec lkes-cnt icn-fnt dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false"><span class="fa fa-share-alt"></a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                            <li>
                                                <div data-href="<?php echo base_url('home/user/userDetail/').($this->uri->segment(4)).'/';?>" data-layout="button" data-size="large" data-mobile-iframe="true">
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fapoim.com%2Fhome%2Fuser%2FuserDetail%2F<?php echo ($this->uri->segment(4)).'/';?>%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore social" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook fb"></i>&nbsp;<?php echo lang('facebook'); ?></a>
                                                </div>
                                            </li>

                                            <!-- Twitter share -->
                                            <li>
                                                <a href="https://twitter.com/share?url=<?php echo base_url('home/user/userDetail/').($this->uri->segment(4)).'/';?>" class="social twitter popup"><i class="fa fa-twitter tw"></i>&nbsp;<?php echo lang('twitter'); ?></a>
                                            </li>

                                            <!-- Google Plus Share -->
                                            <!-- <li>
                                                <a href="https://plus.google.com/share?url=<?php echo base_url('home/user/userDetail/').($this->uri->segment(4)).'/';?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-google-plus gp"></i>&nbsp;<?php echo lang('google_plus'); ?></a>
                                            </li> -->

                                            <!-- Linkedin Share -->
                                            <li>
                                                <a href="https://www.linkedin.com/shareArticle?url=<?php echo base_url('home/user/userDetail/').($this->uri->segment(4)).'/';?>&title=<?php echo ucfirst($userDetail->fullName);?>&text=<?php echo ($userDetail->about == 'NA') ? "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!" : $userDetail->about;?>&submitted-image-url=<?php echo !empty($images) ? $images[0]->image : AWS_CDN_USER_PLACEHOLDER_IMG;?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-linkedin lk"></i>&nbsp;<?php echo lang('linkedin'); ?></a>
                                            </li>

                                            <!-- Pinterest Share -->
                                            <li>
                                                <a href="https://pinterest.com/pin/create/button/?url=<?php echo base_url('home/user/userDetail/').($this->uri->segment(4)).'/';?>&media=<?php echo !empty($images) ? $images[0]->image : AWS_CDN_USER_PLACEHOLDER_IMG;?>&text=<?php echo ($userDetail->about == 'NA') ? "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!" : $userDetail->about;?>&title=<?php echo ucfirst($userDetail->fullName);?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-pinterest pr"></i>&nbsp;<?php echo lang('pinterest'); ?></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="lkes-cnt"><span></span></li>
                                </ul>
                            </li>
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <!-- already sent appointment -->
                                    <?php //if($appStatus->isAppointment == 1){?>

                                        <!-- <li class="lkes-cnt icn-fnt"><a href="javascript:void(0);" onclick="msg(this)" data-msg="You already sent appointment request to <?php //echo ucfirst($userDetail->fullName);?>." data-toggle="tooltip" data-placement="bottom" title="Make Appointment"><span class="fa fa-map-marker"></a></li> -->

                                    <!-- already have an appointment -->
                                    <?php //}elseif($appStatus->isAppointment == 2){ ?>

                                        <!-- <li class="lkes-cnt icn-fnt"><a href="javascript:void(0);"  onclick="msg(this)"" data-msg="You already have an appointment with <?php //echo ucfirst($userDetail->fullName);?>." data-toggle="tooltip" data-placement="bottom" title="Make Appointment"><span class="fa fa-map-marker"></a></li> -->

                                    <!-- create appointment -->
                                    <?php //}else{ ?>
                                    
                                        <!-- <li class="lkes-cnt icn-fnt"><a href="<?php //echo base_url('home/appointment/createAppointment/').$this->uri->segment(4).'/';?>" data-toggle="tooltip" data-placement="bottom" title="Make Appointment"><span class="fa fa-map-marker"></a></li> -->

                                    <?php //} ?>
                                    <li class="lkes-cnt"><span></span></li>
                                </ul>
                            </li>

                        <?php } else{ ?>

                            <li class="action-icn text-center pt-8">
                                <ul>
                                    <li class="lkes-cnt icn-fnt"><a href="javascript:void(0)" onclick="msg(this)" data-msg="<?php echo lang('login_like'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('like');?>"><span class="fa fa-heart"></a></li>
                                    <li class="lkes-cnt"></span><?php echo $userDetail->totalLikes;?></li>
                                </ul>
                            </li>
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <li class="lkes-cnt icn-fnt"><a href="javascript:void(0)" onclick="msg(this)" data-msg="<?php echo lang('login_favorite'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('favourite');?>"><span class="fa fa-star"></a></li>
                                    <li class="lkes-cnt"></span></li>
                                </ul>
                            </li>
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <li class="shre-sec lkes-cnt icn-fnt dropdown">
                                        <a href="javascript:void(0)" onclick="msg(this)" data-msg="<?php echo lang('login_share_profile'); ?>" class="dropdown-toggle" id="dropdownMenuButton" data-placement="bottom" data-toggle="tooltip" aria-haspopup="true" title="<?php echo lang('share');?>" aria-expanded="false"><span class="fa fa-share-alt"></a>
                                    </li>
                                    <li class="lkes-cnt"><span></span></li>
                                </ul>
                            </li>
                            <li class="action-icn pl-25 text-center pt-8">
                                <ul>
                                    <!-- <li class="lkes-cnt icn-fnt"><a href="javascript:void(0)" onclick="msg(this)" data-msg="Please login first for creating appointment." data-toggle="tooltip" data-placement="bottom" title="Make Appointment"><span class="fa fa-map-marker"></a></li> -->
                                    <li class="lkes-cnt"><span></span></li>
                                </ul>
                            </li>

                        <?php } ?>
                    </ul>
                </div>
                <div class="right_side_content">
                    <span id="showCncl">
                        <?php if($this->session->userdata('front_login') == true){ ?>

                            <?php if($requestStatus->isRequest == '0' && $requestStatus->isFriend == '0'){ ?>

                                <!-- For sent friend request -->
                                <button id="hideAddIcon" data-requestfor="<?php echo decoding($this->uri->segment(4));?>" class="btn login_btn addFriend btn_focs_whte"><?php echo lang('add_friend'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>user.png" alt=""></button>

                            <?php } else if($requestStatus->isRequest == '1'){ ?>

                                <!-- For cancel request -->
                                <button data-status="3" data-requestfor="<?php echo decoding($this->uri->segment(4));?>" class="btn login_btn requestStatusFromDetail btn_focs_whte"><?php echo lang('cancel'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>reject.png" alt=""></button>

                            <?php } else if($requestStatus->isRequest == '2'){ ?>

                                <!-- For accept request to show an opponent user -->
                                <button data-status="2" data-requestfor="<?php echo decoding($this->uri->segment(4));?>" class="btn login_btn requestStatusFromDetail btn-green btn_focs_whte"><?php echo lang('accept'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>accept.png" alt=""></button>

                                <!-- For reject request to show an opponent user -->
                                <button data-status="3" data-requestfor="<?php echo decoding($this->uri->segment(4));?>" class="btn login_btn requestStatusFromDetail btn_focs_whte"><?php echo lang('reject'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>reject.png" alt=""></button>

                            <?php } ?> 

                            <!-- for chat -->
                            <!-- <?php //echo base_url('home/chat?userId=').$this->uri->segment(4).'/';?> -->
                            <a href="<?php echo base_url('home/chat?userId=').$this->uri->segment(4).'&'.'type=user';?>" class="btn login_btn btn_focs_whte"><?php echo lang('chat_now'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>comment.png" alt=""></a>

                        <?php } else{ ?> <!-- if session not created -->

                            <button onclick="msg(this)" data-msg="<?php echo lang('login_req_friend');?>" class="btn login_btn"><?php echo lang('add_friend'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>user.png" alt=""></button>

                            <button onclick="msg(this)"" data-msg="<?php echo lang('login_chat'); ?>" class="btn login_btn"><?php echo lang('chat_now'); ?> <img src="<?php echo AWS_CDN_FRONT_IMG;?>comment.png" alt=""></button>

                        <?php } ?>
                    </span>
                </div>
            </div>
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
                    <ul class="nav nav-tabs profile_menu sticky" role="tablist">

                        <li role="presentation" class="active"><a href="#more-info" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('overview'); ?></a></li>

                        <li role="presentation"><a href="#apoim-revws" onclick="appReviewList(0,'2','<?php echo $id;?>');" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('event_review'); ?><span class="trm-cnt-num"><?php echo $userDetail->totalEventReview;?></span></a></li>

                        <li role="presentation"><a href="#apoim-revws" onclick="appReviewList(0,'1','<?php echo $id;?>');" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('appoint_review'); ?><span class="trm-cnt-num"><?php echo $userDetail->totalAppReview;?></span></a></li>

                        <?php if(!empty($bizDetail)){ ?>

                            <li role="presentation"><a href="#busness" aria-controls="activity" role="tab" data-toggle="tab"><?php echo lang('business'); ?></a></li>

                        <?php } ?>

                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active fade in" id="more-info">
                            <div class="profile_list pro-detl">
                                <ul>
                                    <li><span class="min-wdth"><?php echo lang('work_as'); ?></span><a><?php echo !empty($userDetail->work) ? $userDetail->work : "NA";?></a></li>
                                    <li><span class="min-wdth"><?php echo lang('education'); ?></span><a><?php echo !empty($userDetail->education) ? $userDetail->education  : "NA";?></a></li>
                                    <li><span class="min-wdth"><?php echo lang('height'); ?></span><a><?php echo !empty($userDetail->height) ? $userDetail->height  : "NA";?></a></li>
                                    <li><span class="min-wdth"><?php echo lang('weight'); ?></span><a><?php echo !empty($userDetail->weight) ? $userDetail->weight  : "NA";?></a></li>
                                    <li><span class="min-wdth"></span><a></a></li>
                                </ul>
                        
                                <ul>                                    
                                    <li><span><?php echo lang('relationship'); ?></span><a><?php echo $userDetail->relationship;?></a></li>
                                    <li><span><?php echo lang('i_speak'); ?></span><a><?php  $var = explode(',', $userDetail->language); echo implode(', ', $var); ?></a></li>
                                    <!-- <li><span><?php echo lang('event_type'); ?></span><a><?php 
                                        if($userDetail->eventType == 1){ 
                                            echo 'Paid'; }elseif($userDetail->eventType == 2){ echo 'Free';}else { echo 'NA';}?></a></li> -->
                                    <li><span><?php echo lang('appointment_type'); ?></span><a><?php 
                                        if($userDetail->appointmentType == 1){ 
                                            echo 'Paid'; }elseif($userDetail->appointmentType == 2){ echo 'Free';}else { echo 'NA';}?></a></li>
                                    <li><a></a></li>
                                </ul>

                            </div>
                            <div class="intrst-prt-sec">
                                
                                <h2 class="pt-10"><?php echo lang('interest'); ?></h2>
                                
                                <div class="intrst-tag">

                                    <?php if(!empty($userDetail->game)){ 

                                        $var = explode(',', $userDetail->game); 

                                        foreach ($var as $k => $value) {  ?>

                                           <span><?php echo $value;?></span>

                                        <?php } 

                                        }else { ?>
                                
                                           <span><?php echo 'NA';?></span>   
                                           
                                    <?php } ?> 
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="members_about_box">
                                <h4><?php echo lang('about_me'); ?></h4>
                                <p><?php echo !empty($userDetail->about) ? ucfirst($userDetail->about).'.' :"NA";?></p>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade in" id="apoim-revws">
                            <div class="profile_list">
                                <div class="author_posts_inners">
                                    
                                    <div id="appointmentReviewList"></div> 
                                    <br>
                                    <center id="showLoader"></center>   
                                    
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($bizDetail)){ ?>
                            <div role="tabpanel" class="tab-pane fade in" id="busness">
                                <div class="profile_list">
                                    <div class="s_title">
                                        <h4 class="busness-head"><?php echo ucfirst($bizDetail->businessName); ?></h4>
                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div class="busness-img-tab">
                                                <img src="<?php echo $bizDetail->businessImage; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pl-0">
                                            <section class="map_area map-bsnes-area">

                                                <?php if($userDetail->gender == '1'){ 
                                                        
                                                        $mapIcon = MAP_ICON_MAIL;

                                                    }else{

                                                        $mapIcon = MAP_USER_FEMAIL;
                                                    }
                                                ?>
                                                <div frameborder="0" style="border:0" allowfullscreen id="mapId" data-icon="<?php echo $mapIcon;?>" data-img="<div class='map_loca_name_row'><div class='infoCnt'><div class='map_add3'><?php echo $bizDetail->businessAddress;?></div></div></div>" data-name="<?php echo $userDetail->fullName;?>" data-address="<?php echo $bizDetail->businessAddress;?>" data-lat="<?php echo $bizDetail->businesslat;?>" data-long="<?php echo $bizDetail->businesslong;?>" class="map-section map-sec">
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>                            
                </div>
            </div>
            <div class="col-md-3">
                <div class="right_sidebar_area">
                    <?php if(!empty($images)){ ?>
                    <aside class="s_widget photo_widget">
                        <div class="s_title">
                            <h4><?php echo lang('photo'); ?></h4>
                            <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
                        </div>
                        <div id="apoim-gal" class="lgt-gal-photo">
                            <?php foreach ($images as $value) { ?>  

                                <?php  

                                if(!filter_var($value->imgName, FILTER_VALIDATE_URL) === false) { 
                                    $imgslider = $value->imgName;
                                }else if(!empty($value->imgName)){ 
                                    $imgslider = AWS_CDN_USER_MEDIUM_IMG.$value->imgName;
                                } else{                    
                                    $imgslider = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                                if(!filter_var($value->imgName, FILTER_VALIDATE_URL) === false) { 
                                    $imgview = $value->imgName;
                                }else if(!empty($value->imgName)){ 
                                    $imgview = AWS_CDN_USER_THUMB_IMG.$value->imgName;
                                } else{                    
                                    $imgview = AWS_CDN_USER_PLACEHOLDER_IMG;
                                } 

                                ?>

                                <div class="pic" data-src="<?php echo $imgslider;?>"><img src="<?php echo $imgview;?>" alt=""></div>
                            <?php } ?>
                        </div>
                    </aside>
                    <?php } ?>
                    <aside class="s_widget recent_post_widget">
                        <div class="s_title">
                            <h4><?php echo lang('verification'); ?></h4>
                            <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
                        </div>
                        <div class="verifctn-blck list-hd">
                            <ul>
                                <li class="<?php echo ($userDetail->otpVerified == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('sms'); ?>">
                                        <div class="verphy-prt">

                                            <?php if($userDetail->otpVerified == 1){?>

                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_sms@3x.png" /> 

                                                <i class="fa fa-check"></i>  

                                            <?php }else{ ?>

                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_sms@3x.png" />

                                            <?php } ?>                                           
                                        </div>                                    
                                    </a>
                                </li>
                                <li class="<?php echo ($userDetail->isVerifiedId == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('id_with_hand'); ?>">
                                        <div class="verphy-prt">
                                            <?php if($userDetail->isVerifiedId == 1){?>

                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_id@3x.png" /> 
                                                <i class="fa fa-check"></i>  

                                            <?php }else{ ?>

                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>Veriphication/active_id@3x.png" />

                                            <?php } ?>
                                        </div>                                     
                                    </a>
                                </li>
                                <li class="<?php echo ($userDetail->isFaceVerified == 1) ? 'active' : 'inactive-veriphy'; ?> bor-top" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('face_detection'); ?>">
                                    <a href="javascript:void(0)">
                                        <div class="verphy-prt">
                                            <?php if($userDetail->isFaceVerified == 1){?>
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
<!--================End Blog grid Area =================-->
<input type="hidden" id="page-count" value="">
<input type="hidden" id="type" value="">

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('action'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15" id="showMsg"></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <?php if($this->session->userdata('front_login') == FALSE){ 
                        if($appStatus->isAppointment == 0){
                    ?>
                    <a href="<?php echo base_url('home/login');?>">
                        <button type="button" value="LogIn" class="btn form-control login_btn"><?php echo lang('ok'); ?></button>
                    </a>
                    <?php } }else{?>
                        <a href="javascript:void(0)" class="checkMap btn form-control login_btn" data-dismiss="modal"><?php echo lang('ok'); ?></a>
                    <?php } ?>
                    <a href="javascript:void(0)" class="checkMap" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    /*Twitter shaing*/    
    $('.popup').click(function(event) {
        var width  = 575,
        height = 400,
        left   = ($(window).width()  - width)  / 2,
        top    = ($(window).height() - height) / 2,
        url    = this.href,
        opts   = 'status=1' +
        ',width='  + width  +
        ',height=' + height +
        ',top='    + top    +
        ',left='   + left;

        window.open(url, 'twitter', opts);

        return false;
    });
    /*Twitter shaing*/
    
    /* for scroll using ajax pagination for event and appointment list*/
    
    $(window).scroll(function() {

        var totalUser = $('#totalAppReview-count').val();
        var page = $("#page-count").val();
        var type = $("#type").val();
        var id = $("#app_event_user_id").val();

        if($(window).scrollTop() == $(document).height() - $(window).height()) {

            if((totalUser != 0)){

                appReviewList(page,type,id);
            }           
        }
    });    
   
</script>