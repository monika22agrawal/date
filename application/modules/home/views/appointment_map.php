<?php 
    //pr($detail);
    $frontend_assets =  base_url().'frontend_asset/';

    $acceptBtn = $rejectBtn = $finishBtn = $applyCounterBtn = $payBtn = $giveReviewBtn = $cancelBtn = '';

    if($detail->isFinish == 1){ 

        $reviewId = ($this->session->userdata('userId') == $detail->reviewForUserId) ? $detail->reviewForUserId : $detail->reviewByUserId;
        if($this->session->userdata('userId') !=  $reviewId){
            
            $giveReviewBtn = '<button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte" data-toggle="modal" data-target="#review">'.lang('give_review').'</button>';
        }

    }else{

        if($detail->appointById == $this->session->userdata('userId')){       

            if ($detail->isCounterApply == 1) {

                if ($detail->counterStatus == 0) {  

                    //$counterPrice & btn A/C  //show
                    $acceptBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte appCounterStatus" data-counterstatus="1" data-appforid="'.$detail->appointForId.'" data-appid="'.$detail->appId.'">'.lang('accept').'</button></a>';

                    $rejectBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte appCounterStatus" data-counterstatus="2" data-appforid="'.$detail->appointForId.'" data-appid="'.$detail->appId.'">'.lang('reject').'</button></a>';

                }elseif($detail->counterStatus == 1){

                    // show pay btn
                    $payBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte" data-appforid="'.$detail->appointForId.'" onclick="openStripeModel(this)" data-pType="7" data-payamt="'.$detail->counterPrice.'" data-title="'.lang('pay_appointment').'" data-appid="'.$detail->appId.'">'.lang('pay').'</button></a>';


                }elseif($detail->counterStatus == 2){

                    // nothing will show

                }elseif($detail->counterStatus == 3){

                    // show finish btn
                    $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';

                }
                
            }elseif($detail->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete 

               //nothing will show

            }elseif($detail->appointmentStatus == '2'){

                if ($detail->offerType == 1 ) { // 1:Paid,2:Free

                    // show pay btn
                    $payBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte" data-appforid="'.$detail->appointForId.'" onclick="openStripeModel(this)" data-pType="7" data-payamt="'.$detail->offerPrice.'" data-title="'.lang('pay_appointment').'" data-appid="'.$detail->appId.'">'.lang('pay').'</button></a>';

                }else{

                    // show finish btn
                    $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';
                } 

            }elseif($detail->appointmentStatus == '4'){

                // show finish btn
                $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';
            }        

        }else{        

            if ($detail->isCounterApply == 1) {

                if($detail->counterStatus == 3){

                    // show finish btn
                    $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';
                }
                
                // show finish btn
                $cancelBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte cnclBtn cancelApp" data-appid="'.$detail->appId.'">'.lang('cancel_meeting').'</button></a>';
                
            }elseif($detail->appointmentStatus == '1'){  // 1:Pending,2:Accept,3:Reject,4:Complete 

                //$counterPrice & btn A/C & counter apply popup//show
                $acceptBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte add-rmv-btn appStatus" data-status="2" data-appid="'.$detail->appId.'">'.lang('accept').'</button></a>';

                $rejectBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte add-rmv-btn appStatus" data-status="3" data-appid="'.$detail->appId.'">'.lang('reject').'</button></a>';
                
                if ($detail->offerType == 1 ) { // 1:Paid,2:Free

                    $applyCounterBtn = '<a href="javascript:void(0)" data-toggle="modal" data-target="#frgt-pswrd"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte" onclick="counterModel('.$detail->appId.','.$detail->offerPrice.','.$detail->appointById.');">Counter</button></a>';
                }

            }elseif($detail->appointmentStatus == '2'){

                if ($detail->offerType != 1 ) { // 1:Paid,2:Free

                    // show finish btn
                    $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';
                }      

                // show finish btn
                $cancelBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte cnclBtn cancelApp" data-appid="'.$detail->appId.'">'.lang('cancel_meeting').'</button></a>';       

            }elseif($detail->appointmentStatus == '4'){

                // show finish btn
                $finishBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte hideclass appDetailStatus" data-status="finish" data-appid="'.$detail->appId.'">'.lang('finish_meeting').'</button></a>';

                // show finish btn
                $cancelBtn = '<a href="javascript:void(0);"><button type="button" class="btn form-control login_btn frnd-sec-btn mr-10 mt-10 btn_focs_whte cnclBtn cancelApp" data-appid="'.$detail->appId.'">'.lang('cancel_meeting').'</button></a>';
            }
        }
    }
?>
<div class="wraper">
    <!--================Banner Area =================-->
    <section class="banner_area">
        <div class="container">
            <div class="banner_content">
                <h3 title=""><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG; ?>banner/t-left-img.png" alt=""><?php echo lang('appointment'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG; ?>banner/t-right-img.png" alt=""></h3>
            </div>
        </div>
    </section>
    <!--================End Banner Area =================--> 
    <div class="container">
        <div class="contact_form_area">
            <div class="row d-flex1">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="mob-res">
                        <div class="route-distnce">
                            <div class="map-img-src">
                                <div class="map-src">
                                    <img src="<?php echo AWS_CDN_FRONT_IMG; ?>inactive_running.png" alt="">
                                    <span><?php echo ($walkingMode['time'] != '') ? $walkingMode['time'] : 'NA';?></span>
                                </div>
                                <div class="map-src">
                                    <img src="<?php echo AWS_CDN_FRONT_IMG; ?>inactive_car.png" alt="">
                                    <span><?php echo ($drivingMode['time'] != '') ? $drivingMode['time'] : 'NA';?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section class="map_area dtl-map mt-20">
                        <div id="map" class="map-section map-sec" width="100%" height="600px" frameborder="0" style="border:0; min-height: 490px;" allowfullscreen>
                        </div>
                    </section>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="frm-rgt-sec">
                        <!-- <form class="text-left apoint-rgt"> -->
                            <div class="appmap-lctin appmap-lctin-rel">
                                <h3 class="text-center"><?php echo date('d M Y,',strtotime($detail->appointDate));?><span><?php echo date('h:i A',strtotime($detail->appointTime));?></span></h3>
                                <div class="form-group">
                                    <div class="appiomnt_info mt-30 rest-adress">
                                        <div class="media">
                                            <?php if(!empty($detail->businessId)){

                                                if(!empty($detail->businessImage)){ 

                                                    $bizImg = AWS_CDN_BIZ_THUMB_IMG.$detail->businessImage;

                                                } else{

                                                    $bizImg = base_url().BUSINESS_DEFAULT_IMG;
                                                }
                                            ?>
                                                <div class="media-left rest-image">
                                                    <img src="<?php echo $bizImg;?>">
                                                </div>
                                            <?php } ?>
                                            <div class="media-body">

                                                <?php if(!empty($detail->businessId)){?>

                                                    <h4 class="media-heading"><?php echo $detail->businessName;?></h4>

                                                <?php } ?>

                                                <p><i class="fa fa-map-marker"></i> <?php echo $detail->appointAddress;?></p>
                                                <!-- <h5>2.5 Km</h5> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit-dlte-icn">
                                <ul>
                                    <?php if($this->session->userdata('userId') == $detail->appointById ){ ?>

                                        <?php if($detail->isCounterApply == 0 && $detail->appointmentStatus == 1){ 

                                        ?>

                                            <li><a href="<?php echo base_url('home/appointment/updateAppointment/').encoding($detail->appId).'/'.encoding($detail->appointForId).'/';?>"><i class="fa fa-edit"></i></a></li>

                                        <?php  }else{ ?>

                                            <!-- <li><a href="javascript:void(0);" data-toggle="modal" data-target="#modalCheckUpdateApp"><i class="fa fa-edit"></i></a></li> -->

                                        <?php } ?> 

                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#myModal"><i class="fa fa-trash"></i></a></li>

                                    <?php } ?>
                                </ul>
                            </div>
                            <?php

                                if(!filter_var($detail->forImage, FILTER_VALIDATE_URL) === false) { 
                                    $forImage = $detail->forImage;
                                }else if(!empty($detail->forImage)){ 
                                    $forImage = AWS_CDN_USER_THUMB_IMG.$detail->forImage;
                                } else{                    
                                    $forImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                                if(!filter_var($detail->byImage, FILTER_VALIDATE_URL) === false) { 
                                    $byImage = $detail->byImage;
                                }else if(!empty($detail->byImage)){ 
                                    $byImage = AWS_CDN_USER_THUMB_IMG.$detail->byImage;
                                } else{                    
                                    $byImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }
                            ?>
                            <div class="appuserDet mt-30">
                                <div class="userDetBlock appiomUserInfo">
                                    <div class="text-center mapuser">
                                        <?php
                                            $userDetailUrl1 = base_url('home/user/userDetail/').encoding($detail->appointById);
                                            if($detail->appointById == $this->session->userdata('userId')){

                                                $userDetailUrl1 = base_url('home/user/userProfile/');
                                            }                                            
                                        ?>
                                        <a href="<?php echo $userDetailUrl1;?>">
                                            <div class="mapuser-image"><img src="<?php echo $byImage;?>" alt="" class="img-circle"></div>
                                            <h4><?php echo ucfirst($detail->ByName); ?></h4>
                                        </a>
                                    </div>
                                </div>                                
                                <div class="userDetBlock blc1">
                                    <div class="mapIcon1">
                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>mapicon.jpg">
                                    </div>
                                </div>
                                <div class="userDetBlock appiomUserInfo">
                                    <div class="text-center mapuser">
                                        <?php
                                            $userDetailUrl = base_url('home/user/userDetail/').encoding($detail->appointForId);
                                            if($detail->appointForId == $this->session->userdata('userId')){

                                                $userDetailUrl = base_url('home/user/userProfile/');
                                            }                                            
                                        ?>
                                        <a href="<?php echo $userDetailUrl; ?>">
                                            <div class="mapuser-image"><img src="<?php echo $forImage;?>" alt="" class="img-circle"></div>
                                            <h4><?php echo ucfirst($detail->ForName); ?></h4>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <hr class="fde-line">
                            <div class="appuserDet mt-10">
                                <div class="userDetBlock appiomUserInfo">
                                    <?php if(!empty($detail->offerPrice)){?>
                                        <div class="text-center mapuser">
                                            <div class="ofr-prce-det mrgn-price">
                                                <p><span><?php echo lang('offer_price'); ?></span><?php echo '$'.$detail->offerPrice;?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="userDetBlock blc1"></div>
                                <div class="userDetBlock appiomUserInfo">
                                    <?php if(!empty($detail->counterPrice)){?>
                                        <div class="text-center mapuser">
                                            <div class="coun-prce-det mrgn-price">
                                                <p><span><?php echo lang('counter_price_title'); ?></span><?php echo '$'.$detail->counterPrice;?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="res-btns mt-30">

                                <?php echo $acceptBtn;?>
                                <?php echo $rejectBtn;?>
                                <?php echo $applyCounterBtn;?>
                            </div>
                            <div class="text-right mt-30">

                                <?php echo $finishBtn;?>
                                <?php echo $cancelBtn;?>
                                <?php echo $payBtn;?>
                                <?php echo $giveReviewBtn;?>

                            </div>
                    </div>

                    <!-- start review section -->
                    <?php

                        $byName = $byComment = $byRating = $byCrd = $forName = $forComment = $forRating = $forCrd = '';

                        if($this->session->userdata('userId') == $detail->reviewByUserId){

                            $byName = ucfirst($detail->ForName);

                            $byComment = $detail->reviewForComment;

                            $byRating = $detail->reviewForRating;

                            $byCrd = $detail->reviewForCreatedDate;

                            $byImage_1 = $byImage;

                            $byImage = $forImage;


                            $forComment = $detail->reviewByComment;

                            $forRating = $detail->reviewByRating;

                            $forCrd = $detail->reviewByCreatedDate;

                            $forImage = $byImage_1;
                            
                        }else{

                            $forComment = $detail->reviewForComment;

                            $forRating = $detail->reviewForRating;

                            $forCrd = $detail->reviewForCreatedDate;

                            $forImage = $forImage;


                            $byName = ucfirst($detail->ByName);

                            $byComment = $detail->reviewByComment;

                            $byRating = $detail->reviewByRating;

                            $byCrd = $detail->reviewByCreatedDate;

                            $byImage = $byImage;
                        }
                    ?>
                    <?php if(!empty($forRating) || !empty($byRating)) { ?>

                        <div class="meetng-conclusion mt-25">

                            <div class="s_title">
                                <h4 class="pb-5"><?php echo lang('meeting_conclusion'); ?></h4>
                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>widget-title-border.png" alt="">
                            </div>

                            <div class="mt-25">
                                <div class="author_posts_inners">
                                    <?php if(!empty($byRating)) { ?>
                                        <div class="media top-media mb-15">
                                            <div class="media-left revw-img">
                                                <img src="<?php echo $byImage;?>" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class=" dsply-block">
                                                    <h3 class="dsply-blck-lft rev-nme"><?php echo $byName;?></h3>
                                                    <h4 class="dsply-blck-rgt rev-dte"><?php echo time_elapsed_string($byCrd);?></h4>
                                                </div>
                                                <div class="clearfix"></div>
                                                <p class="str-sec-pra">
                                                    <?php $count = $byRating;

                                                    for($i=1;$i<=$count;$i++){ ?>

                                                        <span class="fa fa-star"></span> 

                                                    <?php } $minCount = 5-$count; 
                                                    
                                                    for($j=1;$j<=$minCount;$j++){ ?>

                                                        <span class="fa fa-star-o"></span>

                                                    <?php } ?>
                                                </p>
                                                <p><?php echo $byComment;?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if(!empty($forRating)) { ?>
                                        <div class="media top-media mb-15">
                                            <div class="media-left revw-img">
                                                <img src="<?php echo $forImage;?>" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class=" dsply-block">
                                                    <h3 class="dsply-blck-lft rev-nme"><?php echo lang('review_by_you'); ?></h3>
                                                    <h4 class="dsply-blck-rgt rev-dte"><?php echo time_elapsed_string($forCrd);?></h4>
                                                </div>
                                                <div class="clearfix"></div>
                                                <p class="str-sec-pra">
                                                    <?php $count = $forRating;

                                                    for($i=1;$i<=$count;$i++){ ?>

                                                        <span class="fa fa-star"></span> 

                                                    <?php } $minCount = 5-$count; 

                                                    for($j=1;$j<=$minCount;$j++){ ?>

                                                        <span class="fa fa-star-o"></span>

                                                    <?php } ?>
                                                </p>
                                                <p><?php echo $forComment;?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- end review section -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start modal to delete appointment -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('delete_appointment'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-10">
                    <p class="para text-left mb-15"><?php echo lang('sure_delete_appointment'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn shre-btn hideclass appDetailStatus" data-status="delete" data-appid="<?php echo $detail->appId;?>"><?php echo lang('yes'); ?></button>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End modal to delete appointment -->

<!-- Start modal to check status for update appointment -->
<div class="modal fade" id="modalCheckUpdateApp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('update_appointment');?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-10">
                    <p class="para text-left mb-15"><?php echo lang('app_accepted'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn" data-dismiss="modal"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End modal to check status for update appointment -->

<!-- Start modal for apply counter -->
<div class="modal fade" id="ofr-prce" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('counter_price_title'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ofrForm" method="post" action="<?php echo base_url('home/appointment/applyCounter/');?>">
                <div class="modal-body mdl-body">
                    <h2 class="text-center pop-up-hedng"><?php echo lang('offer_price'); ?><span id="set-ofr-prc"></span></h2>
                    <div class="regfrm mdl-pad mt-20">
                        <p class="para text-left mb-15"><?php echo lang('counter_price'); ?></p>
                        <div class="form-group regfld">
                            <input type="text" min="1" maxlength=6 class="form-control" onkeypress="return isNumberKey(event);" placeholder="<?php echo lang('counter_price'); ?>" name="counterPrice" required title="<?php echo lang('counter_price_req');?>">
                            <input type="hidden" id="appId" name="appointId" value="">
                            <input type="hidden" id="appointById" name="appointById" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn shre-btn applyCounter"><?php echo lang('submit'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End modal for apply counter -->

<!-- Start modal For Giving Review -->
<div class="modal fade" id="review" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('give_review'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reviewForm" method="post" action="<?php echo base_url('home/appointment/giveReview/');?>">
                <input type="hidden" name="receiverId" value="<?php echo ($this->session->userdata('userId') == $detail->appointById) ? $detail->appointForId : $detail->appointById;?>">
                <input type="hidden" name="referenceId" value="<?php echo $detail->appId;?>">
                <div class="modal-body mdl-body">
                    <p class="text-center para pop-up-pra"><?php echo lang('appointment_review_thoughts'); ?></p>
                    <p class="str-sec-pra revw-str text-center mt-10">
                        <?php
                            for($i=1;$i<=5;$i++){
                        ?>
                            <span id="rate_<?php echo $i;?>" onclick="rate('<?php echo $i;?>')" class="fa fa-star-o"></span> 
                        <?php } ?>
                        <input type="hidden" id="rate_value" name="rating" value=""/>
                    </p>
                    <div class="regfrm mdl-pad mt-20">
                        <div class="form-group regfld">
                            <textarea class="form-control review-textarea" name="comment" maxlength=200 required type="text" placeholder="<?php echo lang('write_comment');?>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn shre-btn giveReview btn_focs_whte"><?php echo lang('submit'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End modal For Giving Review -->

<script type="text/javascript">
    var data = <?php echo $data; ?>
</script>