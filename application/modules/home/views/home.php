<?php
    $frontend_assets =  base_url().'frontend_asset/';
?>
<!--================Slider Reg Area =================-->
<section class="slider_area slider_bg">
    <div class=slider_inner>
        <div class="rev_slider fullwidthabanner"  data-version="5.3.0.2" id="home-slider3">
            <ul> 
                <li data-slotamount="7" data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="600" data-rotate="0" data-saveperformance="off">
                    <!-- MAIN IMAGE -->
                    <!-- LAYERS -->
                    <div class="tp-caption left_img"
                        data-whitespace="nowrap"
                        data-voffset="['0']"
                        data-hoffset="['80','20']"
                        data-x="left"
                        data-y="bottom"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"  
                        data-start="1800" >
                        <img src="<?php echo AWS_CDN_FRONT_IMG ?>slider-item-4.png" alt="">
                    </div>
                    <div class="tp-caption first_text" 
                        data-width="500"
                        data-height="none"
                        data-whitespace="nowrap"
                        data-voffset="['300','180','180','200']"
                        data-hoffset="['255','0']"
                        data-x="center" 
                        data-y="top"
                        data-fontsize="['48','48','48','28']" 
                        data-lineheight="['55','55','55','35']" 
                        data-transform_idle="o:1;"
                        data-frames='[{"from":"x:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","speed":1500,"to":"o:1;","delay":500,"ease":"Power3.easeInOut"},{"delay":"wait","speed":1000,"to":"x:[-100%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power3.easeInOut"}]'
                        data-textAlign="['left','left','left','left']"
                        data-paddingtop="[0,0,0,0]"
                        data-paddingright="[0,0,0,0]"
                        data-paddingbottom="[0,0,0,0]"
                        data-paddingleft="[0,0,0,0]"
                        data-start="800" 
                        data-splitin="none" 
                        data-splitout="none" 
                        data-responsive_offset="on"><?php echo lang('home_slider_text');?>
                    </div>
                    <div class="tp-caption secand_text"
                        data-width="500"
                        data-height="none"
                        data-whitespace="nowrap"
                        data-voffset="['430','300','300','280']"
                        data-hoffset="['255','0']"
                        data-x="center"
                        data-transform_idle="o:1;"
                        data-fontsize="['22','22','22','22','18']" 
                        data-lineheight="['30','30','30','30','27']" 
                         data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
                        data-y="top"
                        data-start="1800" >
                        <?php echo lang('home_slider_para');?>
                    </div>
                    <div class="tp-caption third_text"
                        data-width="500"
                        data-height="none"
                        data-voffset="['470','340','340','320']"
                        data-hoffset="['255','0']"
                        data-whitespace="['nowrap','nowrap','nowrap','normal']"
                        data-width="['','','','100%']"
                        data-x="center"
                        data-fontsize="['16','16','16','20']" 
                        data-lineheight="['26','26','26','30']" 
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"  
                        data-y="top"
                        data-start="1800">
                        <?php  echo lang('home_slider_para2'); ?>
                    </div>
                    <div class="tp-caption download_btn"
                        data-width="500"
                        data-height="none"
                        data-whitespace="nowrap"
                        data-voffset="['540','410']"
                        data-hoffset="['255','0']"
                        data-x="center"
                        data-transform_idle="o:1;"
                        data-frames='[{"from":"x:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","speed":1500,"to":"o:1;","delay":500,"ease":"Power3.easeInOut"},{"delay":"wait","speed":1000,"to":"x:[-100%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power3.easeInOut"}]'
                        data-textAlign="['left','left','left','left']"
                        data-paddingtop="[0,0,0,0]"
                        data-paddingright="[0,0,0,0]"
                        data-paddingbottom="[0,0,0,0]"
                        data-paddingleft="[0,0,0,0]"
                        data-y="top"
                        data-start="1800" >
                        <?php if($this->session->userdata('front_login') != true && $this->session->userdata('userId') == ''){?>
                            <a class="register_angkar_btn" href="<?php echo base_url('home/login/registration');?>"><?php echo lang('join_now'); ?></a>
                        <?php } ?>
                    </div>
                </li>
                <li data-slotamount="7" data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="600" data-rotate="0" data-saveperformance="off">
                    <!-- MAIN IMAGE -->
                    <!-- LAYERS -->
                    <div class="tp-caption left_img"
                        data-whitespace="nowrap"
                        data-voffset="['0']"
                        data-hoffset="['80','20']"
                        data-x="left"
                        data-y="bottom"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"  
                        data-start="1800" >
                        <img src="<?php echo AWS_CDN_FRONT_IMG ?>slider-item-1.png" alt="">
                    </div>
                    <div class="tp-caption first_text" 
                        data-width="500"
                        data-height="none"
                        data-voffset="['300','180','180','200']"
                        data-hoffset="['255','0']"
                        data-x="center" 
                        data-y="top"
                        data-fontsize="['48','48','48','28']" 
                        data-lineheight="['55','55','55','35']" 
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
                        data-start="800" 
                        data-splitin="none" 
                        data-splitout="none" 
                        data-responsive_offset="on"><?php echo lang('home_slider_text');?>
                    </div>
                    <div class="tp-caption secand_text"
                        data-width="500"
                        data-voffset="['430','300','300','280']"
                        data-hoffset="['255','0']"
                        data-x="center"
                        data-transform_idle="o:1;"
                        data-fontsize="['22','22','22','22','18']" 
                        data-lineheight="['30','30','30','30','27']" 
                         data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
                        data-y="top"
                        data-start="1800" >
                        <?php echo lang('home_slider_para');?>
                    </div>
                    <div class="tp-caption third_text"
                        data-width="500"
                        data-height="none"
                        data-voffset="['470','340','340','320']"
                        data-hoffset="['255','0']"
                        data-whitespace="['nowrap','nowrap','nowrap','normal']"
                        data-width="['','','','100%']"
                        data-x="center"
                        data-fontsize="['16','16','16','20']" 
                        data-lineheight="['26','26','26','30']" 
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"  
                        data-y="top"
                        data-start="1800">
                        <?php  echo lang('home_slider_para2'); ?>
                    </div>
                    <div class="tp-caption download_btn"
                        data-width="500"
                        data-height="none"
                        data-whitespace="nowrap"
                        data-voffset="['540','410']"
                        data-hoffset="['255','0']"
                        data-x="center"
                        data-transform_idle="o:1;"
                        data-frames='[{"from":"x:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","speed":1500,"to":"o:1;","delay":500,"ease":"Power3.easeInOut"},{"delay":"wait","speed":1000,"to":"x:[-100%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power3.easeInOut"}]'
                        data-textAlign="['left','left','left','left']"
                        data-paddingtop="[0,0,0,0]"
                        data-paddingright="[0,0,0,0]"
                        data-paddingbottom="[0,0,0,0]"
                        data-paddingleft="[0,0,0,0]"
                        data-y="top"
                        data-start="1800" >
                        <?php if($this->session->userdata('front_login') != true && $this->session->userdata('userId') == ''){?>
                            <a class="register_angkar_btn" href="<?php echo base_url('home/login/registration');?>"><?php echo lang('join_now'); ?></a>
                        <?php } ?>
                    </div>
                </li>
            </ul> 
        </div><!-- END REVOLUTION SLIDER -->
    </div>
</section>
<!--================End Slider Reg Area =================-->

<!--================Welcome Area =================-->
<section class="welcome_area">
    <div class="container">
        <div class="welcome_title">
            <h3><?php echo lang('welcome_to_apoim'); ?></h3>
            <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-title-b.png" alt="">
        </div>
        <div class="row">
            <div class="col-md-3 col-sm-3 col-xs-6">
                <div class="welcome_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-icon-1.png" alt="">
                    <h4 class="counter"><?php echo ($girlsCount+$guysCount+$transgenderCount);?></h4>
                    <h6><?php echo lang('total_members');?></h6>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6">
                <div class="welcome_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-icon-4.png" alt="">
                    <h4 class="counter"><?php echo $girlsCount;?></h4>
                    <h6><?php echo lang('girls');?></h6>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6">
                <div class="welcome_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-icon-3.png" alt="">
                    <h4 class="counter"><?php echo $guysCount;?></h4>
                    <h6><?php echo lang('guys'); ?></h6>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6">
                <div class="welcome_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>transgender.png" alt="">
                    <h4 class="counter"><?php echo $transgenderCount;?></h4>
                    <h6><?php echo lang('transgender'); ?></h6>
                </div>
            </div>                                        
        </div>
    </div>
</section>
<!--================End Welcome Area =================-->

<!--================Download Area =================-->
<section class="download_area">
    <div class="download_full_slider">
        <div class="container">
            <div class="row">
                <div class="item">
                    <div class="col-md-7">
                        <div class="download_app_icon">
                            <h3><?php echo lang('download_apoim'); ?></h3>
                            <h5><?php echo lang('app_store'); ?></h5>
                            <ul>
                                <li><a href="#"><i class="fa fa-android"></i></a></li>
                                <li><a href="#"><i class="fa fa-apple"></i></a></li>
                            </ul>
                        </div>
                        <div class="download_content str-sec owl-carousel owl-theme">
                            <div class="item">
                                <p><?php echo lang('user2_highest_rating'); ?></p>
                                <h4><?php echo lang('user2_name'); ?></h4>
                                <p class="str-sec-pra pl-10">
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </p>
                            </div>
                            <div class="item">
                                <p><?php echo lang('user1_highest_rating'); ?></p>
                                <h4><?php echo lang('user1_name'); ?></h4>
                                <p class="str-sec-pra pl-10">
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </p>
                            </div>
                            <div class="item">
                                <p><?php echo lang('user3_highest_rating'); ?></p>
                                <h4><?php echo lang('user3_name'); ?></h4>
                                <p class="str-sec-pra pl-10">
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 pad-lft-rgt">
                        <div class="download_moblie">
                            <div class="download_m_slider">
                                <img src="<?php echo AWS_CDN_FRONT_IMG ?>mobile-1.png" alt="">
                                <div class="download_moblile_slider owl-carousel owl-theme">
                                    <div class="item">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG ?>0111.png" alt="">
                                    </div>
                                    <div class="item">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG ?>0222.png" alt="">
                                    </div>
                                    <div class="item">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG ?>0333.png" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Download Area =================-->

<!--================Find Your Soul Area =================-->
<section class="find_soul_area">
    <div class="container">
        <div class="welcome_title">
            <h3><?php echo lang('user_soul_mate'); ?></h3>
            <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-title-b.png" alt="">
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="find_soul_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>1.png" alt="">
                    <h4><?php echo lang('create_profile'); ?></h4>
                    <p><?php echo lang('create_profile_para'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="find_soul_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>2.png" alt="">
                    <h4><?php echo lang('find_matches'); ?> </h4>
                    <p><?php echo lang('find_matches_para'); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="find_soul_item">
                    <img src="<?php echo AWS_CDN_FRONT_IMG ?>3.png" alt="">
                    <h4><?php echo lang('start_dating'); ?></h4>
                    <p><?php echo lang('start_dating_para'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Find Your Soul Area =================-->

<!--================Blog slider Area =================-->
<section class="blog_slider_area">
    <div class="welcome_title">
        <h3><?php echo lang('latest_events'); ?></h3>
        <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-title-b.png" alt="">
    </div>
    <div class="blog_slider_inner owl-carousel owl-theme">

        <?php 
            
        if(!empty($eventList)){ foreach ($eventList as $val) { 

            if(!empty($val->eventImageName)){ 
                $eventImg = AWS_CDN_EVENT_THUMB_IMG.$val->eventImageName;
            } else{                    
                $eventImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
            }
        ?>

            <div class="item">
                <div class="single_blog_slider">
                    <img src="<?php echo $eventImg;?>" alt="">
                    <div class="blog_item_content">
                        <h4><?php echo ucfirst($val->eventName); ?></h4>
                        <h5><?php echo date('d M, Y',strtotime($val->eventStartDate));?> <span>|</span> <?php echo $val->privacy;?></h5>
                    </div>
                </div>
            </div>

        <?php } } ?>

        <?php 

        $eventCount = count($eventList);

        $imgArray = array('2.jpg','3.jpg','4.jpg','5.jpg','2.jpg');

        for($i=$eventCount;$i<4;$i++){ ?>

            <div class="item">
                <div class="single_blog_slider">
                    <img src="<?php echo AWS_CDN_FRONT_IMG.$imgArray[$i]; ?>" alt="">
                </div>
            </div>

        <?php } ?>

    </div>
</section>
<!--================End Blog slider Area =================-->

<!--================Register Members slider Area =================-->
<section class="register_members_slider">
    <div class="container">
        <?php if(!empty($nearUsers)){  ?>

            <div class="welcome_title">
                <h3><?php echo lang('people_near_you'); ?></h3>
                <img src="<?php echo AWS_CDN_FRONT_IMG ?>w-title-b.png" alt="">
            </div>
            <div class="r_members_inner people-near-img owl-carousel owl-theme">

                <?php

                foreach($nearUsers as $key => $value){

                    if(!filter_var($value['profileImage'], FILTER_VALIDATE_URL) === false) { 
                        $img = $value['profileImage'];
                    }else if(!empty($value['profileImage'])){ 
                        $img = AWS_CDN_USER_THUMB_IMG.$value['profileImage'];
                    } else{                    
                        $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                    }

                ?>
                    <div class="item member_count">
                        <a href="<?php echo base_url('home/user/userDetail/').encoding($value['userId']).'/';?>">
                            <img src="<?php echo $img;?>" alt="">
                            <h4><?php echo ucfirst($value['fullName']);?></h4>
                            <h5><?php echo $value['age'].' '.lang('year_old');?></h5>
                        </a>
                    </div>

                <?php 

                if($key == 11){ 
                    break;
                } } ?>
            </div>
        <?php } ?>

    </div>
</section>
<!--================End Register Members slider Area =================-->
<script type="text/javascript">

    $(document).ready(function() {
        /*----------------------------------------------------*/
        /*  Home Slider3 js
        /*----------------------------------------------------*/
        function home_slider3(){

            if ( $('#home-slider3').length ){

                $("#home-slider3").revolution({

                    sliderType:"fullscreen",
                    sliderLayout:"fullwidth",
                    dottedOverlay:"none",
                    delay:9000,
                    navigation: {
                        keyboardNavigation:"off",
                        keyboard_direction: "horizontal",
                        mouseScrollNavigation:"off",
                        mouseScrollReverse:"default",
                        onHoverStop:"off",
                        touch:{
                            touchenabled:"on"
                        },
                        arrows: {
                            style:"Gyges",
                            enable:false,
                            hide_onmobile:true,
                            hide_under:600,
                            hide_onleave:true,
                            hide_delay:200,
                            hide_delay_mobile:1200,
                            left: {
                                h_align:"left",
                                v_align:"center",
                                h_offset:0,
                                v_offset:0
                            },
                            right: {
                                h_align:"right",
                                v_align:"center",
                                h_offset:0,
                                v_offset:0
                            }
                        },
                        bullets: {
                            enable:true,
                            hide_onmobile:false,
                            style:"uranus",
                            hide_onleave:false,
                            direction:"horizontal",
                            h_align:"right",
                            v_align:"bottom",
                            h_offset:75,
                            v_offset:50,
                            space:10,
                            tmp:'<span class="tp-bullet-inner"></span>'
                        }
                    },
                    responsiveLevels:[1240,991,767,480],
                    visibilityLevels:[1240,991,767,480],
                    gridwidth:[1240,991,767,500],
                    gridheight:[765,765,600,600],
                    spinner:"off",
                    stopLoop:"off",
                    stopAfterLoops:-1,
                    stopAtSlide:-1,
                    shuffle:"off",
                    hideThumbsOnMobile:"on",
                    hideSliderAtLimit:0,
                    hideCaptionAtLimit:0,
                    hideAllCaptionAtLilmit:0,
                    debugMode:false,
                    fallbacks: {
                        simplifyAll:"off",
                        nextSlideOnWindowFocus:"on",
                        disableFocusListener:true,
                    }
                });
            }
        }
        home_slider3();
        
        /*----------------------------------------------------*/
        /*  Testimonial Slider
        /*----------------------------------------------------*/
        function download_content(){
            if ( $('.download_content, .testimonials_slider').length ){
                $('.download_content, .testimonials_slider').owlCarousel({
                    loop:true,
                    margin:0,
                    items: 1,
                    nav:true,
                    dots: false,
                    autoplay: false,
                    smartSpeed: 1500,
                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
                })
            }
        }
        download_content();
        
        /*----------------------------------------------------*/
        /*  Dwinload Mobile Slider
        /*----------------------------------------------------*/
        
            $(".download_moblile_slider").owlCarousel({
              navigation : true, // Show next and prev buttons
              slideSpeed : 1500,
              paginationSpeed : 1500,
              items: 1,
              singleItem:true,
              smartSpeed: 1500
            });
        
        /*----------------------------------------------------*/
        /*  Blog Slider
        /*----------------------------------------------------*/
        function blog_slider(){

            if ( $('.blog_slider_inner').length ){

                $('.blog_slider_inner').owlCarousel({
                    loop:true,
                    margin:0,
                    items: 4,
                    nav:true,
                    dots: false,
                    autoplay: true,
                    smartSpeed: 1800,
                    navContainer: '.blog_slider_inner',
                    navText: ['<i class="fa fa-chevron-left"></i>','<i class="fa fa-chevron-right"></i>'],
                    responsive:{
                        0:{
                            items:1
                        },
                        450:{
                            items:2
                        },
                        700:{
                            items:3
                        },
                        900:{
                            items:4
                        }
                    }
                })
            }
        }
        blog_slider();        
        
        /*----------------------------------------------------*/
        /*  Members Slider
        /*----------------------------------------------------*/
        function members_slider(){
            var count = $('.member_count').length;
            if ( $('.r_members_inner').length ){

                $('.r_members_inner').owlCarousel({
                    loop: (count <= 6) ? false : true,
                    margin:28,
                    items: 6,
                    nav:(count <= 6) ? false : true,
                    dots:false,
                    autoplay: (count <= 6) ? false : true,
                    smartSpeed: 1800,
                    navContainer : (count <= 6) ? false : '.r_members_inner',
                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                    responsive:{
                        0:{
                            items:2
                        },
                        400:{
                            items:3
                        },
                        520:{
                            items:4
                        },
                        991:{
                            items:6
                        }
                    }
                })
            }
        }
        members_slider();
        
        /*----------------------------------------------------*/
        /*  Select js
        /*----------------------------------------------------*/
        /*Bootstrap Select picker*/
        $( document.body ).on( 'click', '.registration_form_s .btn-group .dropdown-menu li', function( event ) {

            window.location.href = $('a', this).attr('href');

            var $target = $( event.currentTarget );

            $target.closest( '.registration_form_s .form-group .btn-group' )
            .find( '[data-bind="label"]' ).text( $target.text() )
            .end()
            .children( '.registration_form_s .btn-group .dropdown-toggle' ).dropdown( 'toggle' );

            return false;

        });         
        
        /*----------------------------------------------------*/
        /*  Counter up js
        /*----------------------------------------------------*/
        /*function counterup(){
            if ( $('.counter').length ){
                $('.counter').counterUp({
                    delay: 20,
                    time: 1000
                });
            }
        }
        counterup();*/
    });
</script>