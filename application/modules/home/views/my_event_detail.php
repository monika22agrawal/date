<?php 
    $frontend_assets =  base_url().'frontend_asset/';
    //pr($myEventDetail['detail']);
?>
<div class="blnk-spce"></div>
<div class="wraper">
    <section class="shop_area product_details_main blog_grid_area evnt-dtl">
        <input type="hidden" id="eventId" value="<?php echo $myEventDetail['detail']->eventId; ?>" name="">
        <div class="container">
            <div class="row">                    
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12"> 
                    <div class="evnt-det-lft-sec-prt"> 
                        <div class="blog_grid_img">                   
                            <div id="sync1" class="owl-carousel owl-theme">

                                <?php foreach ($myEventDetail['detail']->eventImage as $key => $value) {

                                    if(!empty($value->eventImageName)){ 
                                        $eventImg = AWS_CDN_EVENT_MEDIUM_IMG.$value->eventImageName;
                                    } else{           
                                        $eventImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
                                    }

                                ?>
                                    <div class="item">
                                        <img src="<?php echo $eventImg;?>" />
                                    </div>

                                <?php } ?>
                            </div>
                        </div>
                        <div id="sync2" class="owl-carousel owl-theme">
                            <?php foreach ($myEventDetail['detail']->eventImage as $key => $value) {

                                if(!empty($value->eventImageName)){ 
                                    $eventImg = AWS_CDN_EVENT_THUMB_IMG.$value->eventImageName;
                                } else{                    
                                    $eventImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
                                }

                            ?>
                                <div class="item">
                                    <img src="<?php echo $eventImg;?>" />
                                </div>

                            <?php } ?>
                        </div> 
                        <div class="s_title mt-20">
                            <h4><?php echo lang('event_location'); ?></h4>
                        </div>
                        <div class="map-adress-add">
                            <div width="100%" frameborder="0" style="border:0;" allowfullscreen id="mapId" data-icon="<?php echo MAP_ICON_MAIL;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='infoCnt'><div class='map_add3'><?php echo $myEventDetail['detail']->eventPlace;?></div></div></div>" data-name="<?php echo $myEventDetail['detail']->fullName;?>" data-address="<?php echo $myEventDetail['detail']->eventPlace;?>" data-lat="<?php echo $myEventDetail['detail']->eventLatitude;?>" data-long="<?php echo $myEventDetail['detail']->eventLongitude;?>" class="map-section map-sec map-sec-2">
                            </div>
                            <div class="addres-blck">
                                <p><?php echo $myEventDetail['detail']->eventPlace; ?></p>
                            </div>
                        </div> 
                    </div>                      
                </div>
                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                    <div class="evnt-det-descrptn-sec apoin-othr-text ">
                        <div class="edt-dlte-icns-btn mb-5">
                            <p>
                                <?php 

                                $date = date('Y-m-d H:i:s');

                                if($myEventDetail['detail']->joinedMemberCount == 0){

                                    if($myEventDetail['detail']->eventEndDate > $date){ ?>

                                        <a href="<?php echo base_url('home/event/updateEvent/').encoding($myEventDetail['detail']->eventId).'/'; ?>">
                                            <span class="fa fa-edit"></span> <?php echo lang('edit'); ?>
                                        </a>

                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#eventDelModal">
                                            <span class="fa fa-trash"></span> <?php echo lang('delete'); ?>
                                        </a>

                                    <?php }else{ ?>

                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#eventDelModal">
                                        <span class="fa fa-trash"></span> <?php echo lang('delete'); ?>
                                    </a>
                                <?php

                                    }
                                }else{ 

                                    if($myEventDetail['detail']->eventEndDate > $date){ ?>

                                        <a href="<?php echo base_url('home/event/updateEvent/').encoding($myEventDetail['detail']->eventId).'/'; ?>">
                                            <span class="fa fa-edit"></span> <?php echo lang('edit'); ?>
                                        </a>
                                <?php } } ?>                                    
                            </p>
                        </div>
                        <div class="descrptn-sec">
                            <h2><?php echo ucfirst($myEventDetail['detail']->eventName);?></h2>
                        </div> 
                        <div class="dsply-block">
                            <div class="dsply-blck-lft">
                                <div class="our_txt_sec mt-15">
                                    <div class="txt_div">
                                        <?php if($myEventDetail['detail']->privacy == 'Private'){
                                            $cls = 'lock';
                                        }else{
                                            $cls = 'users';
                                        }
                                        ?>
                                        <input id="getPrivacy" type="hidden" name="" value="<?php echo $myEventDetail['detail']->privacy; ?>">
                                        <h5 href="javascript:void(0);"><i class="fa fa-<?php echo $cls;?>"></i><span> 
                                            
                                            <?php if($myEventDetail['detail']->privacy == 'Public'){

                                                echo lang('public_invitation');
                                            }else{
                                                echo lang('private_invitation');
                                            }

                                            ?>
                                        </span></h5>
                                    </div>
                                    <div class="txt_div">
                                        <h5 href="javascript:void(0);"><i class="fa fa-money"></i>                                            
                                            <?php if($myEventDetail['detail']->payment == 'Free'){

                                                echo lang('free_event');
                                            } else{
                                                echo lang('pain_event');
                                            }
                                            ?> <?php echo ' '.$myEventDetail['detail']->currencySymbol.''.$myEventDetail['detail']->eventAmount; ?></h5>
                                    </div>
                                </div> 
                            </div>
                            <div class="dsply-blck-rgt">
                                <div class="blog_share_area clr-detl mt-15">
                                    <span class="dropdown">
                                        <a href="#" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-share-alt" aria-hidden="true"></i><?php echo lang('share'); ?></a>
                                        <ul class="dropdown-menu share_icons" aria-labelledby="dropdownMenuButton">

                                            <!-- Facebook share -->
                                            <li>
                                                <div data-href="<?php echo base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';?>" data-layout="button" data-size="large" data-mobile-iframe="true">
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fapoim.com%2Fhome%2Fevent%2FmyEventDetail%2F<?php echo ($this->uri->segment(4)).'/';?>%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore social" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook fb"></i>&nbsp;<?php echo lang('facebook'); ?></a>
                                                </div>
                                            </li>

                                            <!-- Twitter share -->
                                            <li>
                                                <a href="https://twitter.com/share?url=<?php echo base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';?>" class="social twitter popup"><i class="fa fa-twitter tw"></i>&nbsp;<?php echo lang('twitter'); ?></a>
                                            </li>

                                            <!-- Google Plus Share -->
                                            <!-- <li>
                                                <a href="https://plus.google.com/share?url=<?php echo base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-google-plus gp"></i>&nbsp;<?php echo lang('google_plus'); ?></a>
                                            </li> -->

                                            <?php 

                                                $eventImgUrl = $myEventDetail['detail']->eventImage[0]->eventImageName;

                                                if(!empty($eventImgUrl)){

                                                    $img = AWS_CDN_EVENT_MEDIUM_IMG.$eventImgUrl;

                                                } else{
                                                    $img = AWS_CDN_EVENT_PLACEHOLDER_IMG;
                                                }

                                            ?>

                                            <!-- Linkedin Share -->
                                            <li>
                                                <a href="https://www.linkedin.com/shareArticle?url=<?php echo base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';?>&title=<?php echo $myEventDetail['detail']->eventName; ?>&text=<?php echo "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!";?>&submitted-image-url=<?php echo $img;?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-linkedin lk"></i>&nbsp;<?php echo lang('linkedin'); ?></a>
                                            </li>

                                            <!-- Pinterest Share -->
                                            <li>
                                                <a href="https://pinterest.com/pin/create/button/?url=<?php echo base_url('home/event/myEventDetail/').($this->uri->segment(4)).'/';?>&media=<?php echo $img;?>&text=<?php echo "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!" ;?>&title=<?php echo $myEventDetail['detail']->eventName; ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-pinterest pr"></i>&nbsp;<?php echo lang('pinterest'); ?></a>
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div> 
                        </div> 
                        <div class="clearfix"></div>                       
                        <div class="psted-by-sec flex-div">
                            <?php 

                                if(!filter_var($myEventDetail['detail']->profileImageName, FILTER_VALIDATE_URL) === false) { 
                                    $userImg = $myEventDetail['detail']->profileImageName;
                                }else if(!empty($myEventDetail['detail']->profileImageName)){ 
                                    $userImg = AWS_CDN_USER_THUMB_IMG.$myEventDetail['detail']->profileImageName;
                                } else{                    
                                    $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                            ?>
                            <img src="<?php echo $userImg;?>" /><span><?php echo lang('event_by'); ?> - <b><?php echo ucfirst($myEventDetail['detail']->fullName);?></b> - <?php echo lang('event_admin'); ?></span>
                        </div>
                        <div class="dsply-inlne-usres mt-15">
                            <div class="usr-blck-lst frst-evnt-wdth">
                                <h5><?php echo lang('event_limit_title'); ?></h5>
                                <p><?php echo $myEventDetail['detail']->userLimit; ?></p>
                            </div>
                            <div class="usr-blck-lst">
                                <h5><?php echo lang('who_join_event'); ?></h5>
                                <p>
                                    
                                    
                                    <?php

                                    $gender = explode(',', $myEventDetail['detail']->eventUserType); 

                                    $arr = array();

                                    if((in_array('1',$gender))) {array_push($arr, lang('male_gender'));}
                                    if((in_array('2',$gender))){array_push($arr, lang('female_gender'));}
                                    if((in_array('3',$gender))){array_push($arr, lang('transgender_gender'));}
                                    if(in_array('1',$gender) && in_array('2',$gender) && in_array('3',$gender)){
                                       $arr =  array(lang('all'));
                                    }

                                    echo implode(" , ",$arr);
                                    ?>

                                </p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="event-short-info mt-25">
                            <h4 class="course-title"><?php echo lang('event_date_time'); ?></h4>
                            <ul>
                                <li class="brdr-list" data-toggle="tooltip" data-placement="top" title="" data-original-title="Event Start Date">
                                    <b><?php echo lang('event_from'); ?>:</b> <?php echo date('d M Y, h:i A',strtotime($myEventDetail['detail']->eventStartDate));?>
                                </li>
                                <li data-toggle="tooltip" data-placement="top" title="" data-original-title="Event End Date">
                                    <b><?php echo lang('event_to'); ?>:</b> <?php echo date('d M Y, h:i A',strtotime($myEventDetail['detail']->eventEndDate));?>
                                </li>
                            </ul>
                        </div>

                        <!-- start group chat section -->
                        <?php 

                        if($myEventDetail['detail']->groupChat == '1'){ 

                        ?>
                            <div class="s_title mt-25">
                                <h4><?php echo lang('event_chat_title'); ?></h4>
                            </div>
                            <div class="mul-img-sec mt-15">
                                <ul>
                                    <a class="joinMembers" href="javascript:void(0);">

                                        <input type="hidden" id="getJoinCount" value="<?php echo count($myEventDetail['joinedMember']); ?>" name="">

                                        <?php 

                                        $totalJoinCount = $myEventDetail['detail']->joinedMemberCount + $myEventDetail['detail']->joinedCompMemberCount;

                                        if(!empty($myEventDetail['joinedMember'])){

                                            $count           = count($myEventDetail['joinedMember']);
                                            $arrcount = $count;

                                            foreach ($myEventDetail['joinedMember'] as $val) {

                                                if(!filter_var($val->userImgName, FILTER_VALIDATE_URL) === false) { 
                                                    $joinedMemImg = $val->userImgName;
                                                }else if(!empty($val->userImgName)){ 
                                                    $joinedMemImg = AWS_CDN_USER_THUMB_IMG.$val->userImgName;
                                                } else{                    
                                                    $joinedMemImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                } 
                                        ?>

                                        <li><img src="<?php echo $joinedMemImg;?>"></li>

                                        <?php 

                                            if($count < 3){ 

                                                if($arrcount < 3){
                                                    
                                                    $arrcount++;

                                                    if(!empty($val->companionMemId)){

                                                        if(!filter_var($val->compImgName, FILTER_VALIDATE_URL) === false) { 

                                                            $compMemberImg = $val->compImgName;

                                                        }else if(!empty($val->compImgName)){ 

                                                            $compMemberImg = AWS_CDN_USER_THUMB_IMG.$val->compImgName;

                                                        } else{        
                                                                    
                                                            $compMemberImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                        } ?>

                                                         <li><img src="<?php echo $compMemberImg;?>"></li>
                                                        <?php

                                                    }
                                                }
                                            }

                                        }  
                                        

                                        if($totalJoinCount > 3){

                                            $remainingJoinCount = $totalJoinCount-3;

                                        ?>

                                        <li class="brdr-icn mmbr-num"><?php echo $remainingJoinCount.'+';?></li>
                                    <?php } ?> 
                                    <a href="<?php echo base_url('home/chat?userId=').encoding('event_'.$myEventDetail['detail']->eventId).'&'.'type=event';?>"><li class="brdr-icn font-icn"><i class="fa fa-comments"></i></li></a>
                                <?php }else{ ?>
                                        <a href="<?php echo base_url('home/chat?userId=').encoding('event_'.$myEventDetail['detail']->eventId).'&'.'type=event';?>"><li class="brdr-icn font-icn"><i class="fa fa-comments"></i></li></a>
                                <?php } ?>

                                    </a>
                                </ul>
                            </div>
                        <?php

                        }

                        ?> 
                        <!-- end group chat section -->

                        <div class="clearfix"></div>

                        <!-- start show invited member list -->
                        <div class="s_title mt-25">
                            <h4><?php echo lang('invited_mem_tojoin'); ?></h4>
                        </div>
                        <div class="mul-img-sec mt-15">
                            <ul>
                                <!-- <a href="javascript:void(0);" id="invitedMembers"> -->
                                <a class="inviteMembers" href="javascript:void(0);">

                                    <input type="hidden" id="getinvitCount" value="<?php echo count($myEventDetail['invitedMember']); ?>" name="">

                                    <?php 
                                    if(!empty($myEventDetail['invitedMember'])){

                                        foreach ($myEventDetail['invitedMember'] as $value) {

                                            if(!filter_var($value->userImgName, FILTER_VALIDATE_URL) === false) { 
                                                $invMemImg = $value->userImgName;
                                            }else if(!empty($value->userImgName)){ 
                                                $invMemImg = AWS_CDN_USER_THUMB_IMG.$value->userImgName;
                                            } else{                    
                                                $invMemImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                            } 
                                    ?>

                                    <li><img src="<?php echo $invMemImg;?>"></li>

                                    <?php } }else{ 
                                        //echo '<li class="brdr-icn mmbr-num">0</li>';
                                        echo '<span class="no-mmbr">'.lang('no_invi_mem').'</span>';
                                    } 

                                    $totalInCount = $myEventDetail['detail']->invitedMemberCount;

                                        if($totalInCount > 3){
                                            $remainingInCount = $totalInCount-3;
                                    ?>
                                        <li class="brdr-icn mmbr-num"><?php echo $remainingInCount.'+';?></li>

                                    <?php } ?>

                                </a>
                            </ul>
                        </div>
                        <!-- end show invited member list -->

                        <div class="clearfix"></div>

                        <!-- start show joined member list -->
                        <div class="usrs-limit pt-30">
                            <h4><?php echo lang('event_join_mem'); ?></h4>                                           
                        </div>
                        <div class="mul-img-sec mt-15">
                            <ul>
                                <!-- <a href="javascript:void(0);" id="invitedMembers"> -->
                                <a class="joinMembers" href="javascript:void(0);">

                                    <input type="hidden" id="getJoinCount" value="<?php echo count($myEventDetail['joinedMember']); ?>" name="">

                                    <?php
                                    $totalJoinCount = $myEventDetail['detail']->joinedMemberCount + $myEventDetail['detail']->joinedCompMemberCount;

                                    if(!empty($myEventDetail['joinedMember'])){

                                        $count    = count($myEventDetail['joinedMember']);
                                        $arrcount = $count;

                                        foreach ($myEventDetail['joinedMember'] as $val) {

                                            if(!filter_var($val->userImgName, FILTER_VALIDATE_URL) === false) {
                                                $joinedMemImg = $val->userImgName;
                                            }else if(!empty($val->userImgName)){ 
                                                $joinedMemImg = AWS_CDN_USER_THUMB_IMG.$val->userImgName;
                                            } else{                    
                                                $joinedMemImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                            } 
                                    ?>

                                    <li><img src="<?php echo $joinedMemImg;?>"></li>

                                    <?php 

                                        if($count < 3){ 

                                            if($arrcount < 3){
                                                
                                                $arrcount++;

                                                if(!empty($val->companionMemId)){

                                                    if(!filter_var($val->compImgName, FILTER_VALIDATE_URL) === false) { 

                                                        $compMemberImg = $val->compImgName;

                                                    }else if(!empty($val->compImgName)){ 

                                                        $compMemberImg = AWS_CDN_USER_THUMB_IMG.$val->compImgName;

                                                    } else{        
                                                                
                                                        $compMemberImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                    } ?>

                                                     <li><img src="<?php echo $compMemberImg;?>"></li>
                                                    <?php

                                                }
                                            }
                                        }

                                    }  
                                    

                                    if($totalJoinCount > 3){

                                        $remainingJoinCount = $totalJoinCount-3;

                                    ?>

                                    <li class="brdr-icn mmbr-num"><?php echo $remainingJoinCount.'+';?></li>
                                <?php } }else{
                                    //echo '<li class="brdr-icn mmbr-num">0</li>';
                                    echo '<span class="no-mmbr">'.lang('no_join_mem').'</span>';
                                } ?>

                                </a>
                            </ul>
                        </div>
                        <!-- end show joined member list -->

                        <div class="clearfix"></div>
                        <!-- Start review section -->  
                        <?php

                        if(!empty($eventReviewList)){ ?>

                            <hr class="fde-line">

                            <div class="meetng-conclusion mt-25">

                                <div class="evnt-rv-head">
                                    <div class="s_title hed-sec">
                                        <h4 class="pb-5"><?php echo lang('event_review'); ?></h4>
                                        <img src="<?php echo AWS_CDN_FRONT_IMG; ?>widget-title-border.png" alt="">
                                    </div>
                                </div>
                                
                                <div class="scroll-prt-2 pr-10">
                                    <div class="author_posts_inners">

                                        <?php

                                            foreach ($eventReviewList as $get) {

                                                if(!filter_var($get->webShowImg, FILTER_VALIDATE_URL) === false) { 
                                                    $img = $get->webShowImg;
                                                }else if(!empty($get->webShowImg)){ 
                                                    $img = AWS_CDN_USER_THUMB_IMG.$get->webShowImg;
                                                } else{                    
                                                    $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                } 
                                        ?>
                                        <div class="media top-media">
                                            <div class="media-left revw-img">
                                                <img src="<?php echo $img;?>" alt="">
                                            </div>
                                            <div class="media-body">
                                                <div class=" dsply-block">
                                                    <h3 class="dsply-blck-lft rev-nme"><?php echo ucfirst($get->fullName);?></h3>
                                                    <h4 class="dsply-blck-rgt rev-dte"><?php echo date('M d, Y h:i:s a',strtotime($get->crd));?></h4>
                                                </div>
                                                <div class="clearfix"></div>
                                                <p class="str-sec-pra">

                                                    <?php 

                                                    $count = $get->rating;

                                                    for($i=1;$i<=$count;$i++){ ?>

                                                        <span class="fa fa-star"></span> 

                                                    <?php 

                                                    } 

                                                    $minCount = 5-$count; 
                                                    
                                                    for($j=1;$j<=$minCount;$j++){ ?>

                                                        <span class="fa fa-star-o"></span>

                                                    <?php 

                                                    }

                                                    ?>

                                                </p>
                                                <p><?php echo $get->comment;?></p>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>                               
                            </div>
                            <?php } ?>
                        <!-- End review section -->  
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Start show invited member list -->
<section id="sidebar-right" class="sidebar-menu sidebar-right">
    <div class="Notification" id="notifyOpen">
        <div class="notifyList">
            <div class="chat-user white chatChUser">
                <div class="">
                    <div class="dsply-block brdr-blck-sec">
                        <div class="dsply-blck-lft">
                            <div class="s_title mt-12">
                                <h4><?php echo lang('invited_mem_tojoin'); ?></h4>
                            </div>
                        </div>
                        <div class="chatOption">
                        <div class="dsply-blck-lft bck-arow bck-arow-mrgn-tp">
                            <a href="javascript:void(0);"><i id="sidebar_close_icon" class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="blog_comment_list scoll-lst grp-pad">
                        <div id="invite-member"></div>           
                    </div>                             
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End show invited member list -->

<!-- Start show joined member list -->
<section id="sidebar-right2" class="sidebar-menu sidebar-right2">
    <div class="Notification" id="notifyOpen">
        <div class="notifyList">
            <div class="chat-user white chatChUser">
                <div class="">
                    <div class="dsply-block brdr-blck-sec">
                        <div class="dsply-blck-lft">
                            <div class="s_title mt-12">
                                <h4><?php echo lang('event_join_mem'); ?></h4>
                            </div>
                        </div>
                        <div class="chatOption">
                            <div class="dsply-blck-lft bck-arow bck-arow-mrgn-tp">
                                <a href="javascript:void(0);"><i id="sidebar_close_icon2" class="fa fa-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="blog_comment_list scoll-lst grp-pad">
                        <div id="joined-member"></div>                  
                    </div>                             
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End show joined member list -->

<div class="sidebar_overlay sidebar_over"></div>
<div class="sidebar_overlay1"></div>

<!-- The Modal for delete event -->
<div class="modal fade" id="eventDelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('delete_event'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-10">
                    <p class="para text-left mb-15"><?php echo lang('delete_event_msg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn" data-eventid="<?php echo $myEventDetail['detail']->eventId; ?>" id="delete-event" href="javascript:void(0);"><?php echo lang('delete'); ?></button>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal for remove event's member -->
<!-- <div class="modal fade" id="removeMemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialogf" role="document">
        <div class="modal-content">
            <div class="modal-headerf">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo lang('remove_member'); ?></h4>
            </div>
            <div class="modal-bodyf">
                <div class="forgot-form">
                    <div class="modal-body">
                        <div><?php echo lang('remove_member_msg'); ?> <span id="memName"></span>. </div>
                    </div>
                    <form method="post" action="<?php echo base_url('home/event/removeMember');?>">
                        <input type="hidden" id="eId" value="" name="eventId">
                        <input type="hidden" id="eMId" value="" name="eventMemId">
                        <input type="hidden" id="type" value="" name="memberType">
                        <div class="rsvp-button mt30">
                            <a href="javascript:void(0);" class="btn btn-secondary remove-member"><?php echo lang('remove_btn'); ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="removeMemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="myModalLabel"><?php echo lang('remove_member');?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?php echo base_url('home/event/removeMember');?>">
                
                <div class="modal-body mdl-body">
                    <p class="text-center para "><?php echo lang('remove_member_msg'); ?> <span id="memName"></span>.</p>
                    <input type="hidden" id="eId" value="" name="eventId">
                    <input type="hidden" id="eMId" value="" name="eventMemId">
                    <input type="hidden" id="type" value="" name="memberType">
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn shre-btn remove-member"><?php echo lang('remove_btn'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
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

</script>