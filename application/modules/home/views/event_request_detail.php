<?php 
    $frontend_assets =  base_url().'frontend_asset/';
   // pr($reqDetail['detail']->joinedMemberCount);
?>
<script type="text/javascript">
    var noEvent        = '<?php echo lang('event_not_exist');?>';
    var shareEvent     = '<?php echo lang('event_share_msg');?>';
</script>
<div class="blnk-spce"></div>
<div class="wraper">
    <input type="hidden" name="" value="<?php echo $reqDetail['detail']->groupChat;?>" id="getGroupChat">
    <!--================Shop left sidebar Area =================-->
    <section class="shop_area product_details_main blog_grid_area evnt-dtl">
         <input type="hidden" id="eventId" value="<?php echo $reqDetail['detail']->eventId; ?>" name="">
        <div class="container">
            <div class="row">                    
                <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12"> 
                    <div class="evnt-det-lft-sec-prt"> 
                        <div class="blog_grid_img">                   
                            <div id="sync1" class="owl-carousel owl-theme">

                                <?php foreach ($reqDetail['detail']->eventImage as $key => $value) {

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
                        <?php //if($reqDetail['detail']->memberStatus == '1' || $reqDetail['detail']->memberStatus == '3' ){?>
                            <div id="sync2" class="owl-carousel owl-theme">
                                <?php foreach ($reqDetail['detail']->eventImage as $key => $value) {

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
                        <?php //} ?>
                        <div class="s_title mt-20">
                            <h4><?php echo lang('event_location_title'); ?></h4>
                        </div>
                        <div class="map-adress-add">
                          
                            <div width="100%" frameborder="0" style="border:0;" allowfullscreen id="mapId" data-icon="<?php echo MAP_ICON_MAIL;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='infoCnt'><div class='map_add3'><?php echo $reqDetail['detail']->eventPlace;?></div></div></div>" data-name="<?php echo $reqDetail['detail']->fullName;?>" data-address="<?php echo $reqDetail['detail']->eventPlace;?>" data-lat="<?php echo $reqDetail['detail']->eventLatitude;?>" data-long="<?php echo $reqDetail['detail']->eventLongitude;?>" class="map-section map-sec map-sec-2">
                            </div>
                            <div class="addres-blck">
                                <p><?php echo $reqDetail['detail']->eventPlace; ?></p>
                            </div>
                        </div> 
                    </div>                      
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <div class="evnt-det-descrptn-sec apoin-othr-text ">
                        <div class="descrptn-sec">
                            <h2><?php echo ucfirst($reqDetail['detail']->eventName);?></h2>
                        </div> 
                        <div class="dsply-block">
                            <div class="dsply-blck-lft">
                                <div class="our_txt_sec mt-15">
                                    <div class="txt_div">
                                        <?php if($reqDetail['detail']->privacy == 'Private'){

                                            $cls = 'lock';

                                        }else{

                                            $cls = 'users';
                                        }
                                        ?>
                                        <input id="getPrivacy" type="hidden" name="" value="<?php echo $reqDetail['detail']->privacy; ?>">
                                        <h5 href="javascript:void(0);"><i class="fa fa-<?php echo $cls;?>"></i><span> 
                                            <?php if($reqDetail['detail']->privacy == 'Public'){

                                                echo lang('public_invitation');
                                            }else{
                                                echo lang('private_invitation');
                                            }

                                            ?> </span></h5>
                                    </div>
                                    <div class="txt_div">
                                        <h5 href="javascript:void(0);"><i class="fa fa-money"></i> 
                                            <?php if($reqDetail['detail']->payment == 'Free'){

                                                echo lang('free_event');
                                            } else{
                                                echo lang('pain_event');
                                            }
                                            ?> <?php echo ' '.$reqDetail['detail']->currencySymbol.''.$reqDetail['detail']->eventAmount; ?></h5>
                                    </div>
                                </div> 
                            </div>
                            <div class="dsply-blck-rgt">
                                <div class="blog_share_area clr-detl mt-15">
                                    <span class="dropdown">
                                        <a href="#" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-share-alt" aria-hidden="true"></i><?php echo lang('share'); ?></a>
                                        <ul class="dropdown-menu share_icons" aria-labelledby="dropdownMenuButton">

                                            <?php 

                                                $compId =  $eventMemId = '';
                                                if(isset($_GET['compId'])){
                                                   
                                                    $compId =  ($_GET['compId']); 
                                                    $query_str = '/?compId='.$compId; 

                                                }elseif (isset($_GET['eventMemId'])) {
                                                    
                                                    $eventMemId =  ($_GET['eventMemId']);
                                                    $query_str = '/?eventMemId='.$eventMemId;                                                    
                                                }

                                                $url = base_url('home/event/eventRequestDetail/').($this->uri->segment(4)).$query_str;
                                            ?>

                                            <!-- Facebook share -->
                                            <li>
                                                <div data-href="<?php echo $url;?>" data-layout="button" data-size="large" data-mobile-iframe="true">
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fapoim.com%2Fhome%2Fevent%2FeventRequestDetail%2F<?php echo ($this->uri->segment(4)).$query_str;?>%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore social" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-facebook fb"></i>&nbsp;<?php echo lang('facebook'); ?></a>
                                                </div>
                                            </li>

                                            <!-- Twitter share -->
                                            <li>
                                                <a href="https://twitter.com/share?url=<?php echo $url;?>" class="social twitter popup"><i class="fa fa-twitter tw"></i>&nbsp;<?php echo lang('twitter'); ?></a>
                                            </li>

                                            <!-- Google Plus Share -->
                                            <!-- <li>
                                                <a href="https://plus.google.com/share?url=<?php echo $url;?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-google-plus gp"></i>&nbsp;<?php echo lang('google_plus'); ?></a>
                                            </li> -->

                                            <?php

                                                $eventImgUrl = $reqDetail['detail']->eventImage[0]->eventImageName;

                                                if(!empty($eventImgUrl)){

                                                    $img = AWS_CDN_EVENT_MEDIUM_IMG.$eventImgUrl;

                                                } else{

                                                    $img = AWS_CDN_EVENT_PLACEHOLDER_IMG;
                                                }
                                            ?>

                                            <!-- Linkedin Share -->
                                            <li>
                                                <a href="https://www.linkedin.com/shareArticle?url=<?php echo $url;?>&title=<?php echo $reqDetail['detail']->eventName; ?>&text=<?php echo "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!";?>&submitted-image-url=<?php echo $img;?>" onclick="javascript:window.open(this.href,'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-linkedin lk"></i>&nbsp;<?php echo lang('linkedin'); ?></a>
                                            </li>

                                            <!-- Pinterest Share -->
                                            <li>
                                                <a href="https://pinterest.com/pin/create/button/?url=<?php echo base_url('home/event/eventRequestDetail/').($this->uri->segment(4)).'/';?>&media=<?php echo $img;?>&text=<?php echo "Meet, chat with people around the world. Make friends, join events, schedule appointments and lot more fun with APOIM. Signup today!" ;?>&title=<?php echo $reqDetail['detail']->eventName; ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="social"><i class="fa fa-pinterest pr"></i>&nbsp;<?php echo lang('pinterest'); ?></a>
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div> 
                        </div> 
                        <div class="clearfix"></div>                            
                        <div class="psted-by-sec flex-div">

                            <?php 

                                if($reqDetail['detail']->ownerType == 'Shared_Event'){

                                    $name = $reqDetail['detail']->ownerName;
                                    $imgName = $reqDetail['detail']->ownerImageName;

                                }else{

                                    $name = $reqDetail['detail']->fullName;
                                    $imgName = $reqDetail['detail']->profileImageName;
                                }

                                if(!filter_var($imgName, FILTER_VALIDATE_URL) === false) {

                                    $userImg = $imgName;

                                }else if(!empty($imgName)){

                                    $userImg = AWS_CDN_USER_THUMB_IMG.$imgName;

                                } else{

                                    $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                            ?>

                            <a href="<?php echo base_url('home/user/userDetail/').encoding($reqDetail['detail']->eventOrganizer); ?>"><img src="<?php echo $userImg;?>" /></a><span><?php echo lang('event_by'); ?> - <a href="<?php echo base_url('home/user/userDetail/').encoding($reqDetail['detail']->eventOrganizer); ?>"><b><?php echo ucfirst($name);?></b></a> - <?php echo lang('event_admin'); ?></span>
                        </div>
                        <div class="dsply-inlne-usres mt-15">
                            <div class="usr-blck-lst frst-evnt-wdth">
                                <h5><?php echo lang('event_limit_title'); ?></h5>
                                <p><?php echo $reqDetail['detail']->userLimit; ?></p>
                            </div>
                            <div class="usr-blck-lst">
                                <h5><?php echo lang('who_join_event'); ?></h5>
                                <?php 

                                    $gender = explode(',', $reqDetail['detail']->eventUserType); 
                                ?>
                                <input type="hidden" id="getGender" value="<?php echo $reqDetail['detail']->eventUserType;?>">
                                <p>
                                    <!-- <?php //echo $reqDetail['detail']->eventUserType; ?> -->
                                    <?php

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
                                    <b><?php echo lang('event_from'); ?>:</b> <?php echo date('d M Y, h:i A',strtotime($reqDetail['detail']->eventStartDate));?>
                                </li>
                                <li data-toggle="tooltip" data-placement="top" title="" data-original-title="Event End Date">
                                    <b><?php echo lang('event_to'); ?>:</b> <?php echo date('d M Y, h:i A',strtotime($reqDetail['detail']->eventEndDate));?>
                                </li>
                            </ul>
                        </div>

                        <!-- start group chat section -->
                        <?php 

                        if($reqDetail['detail']->groupChat == '1' && ($reqDetail['detail']->memberStatus == '1' || $reqDetail['detail']->memberStatus == '3' )){ 

                        ?>
                            <div class="s_title mt-25">
                                <h4><?php echo lang('event_chat_title'); ?></h4>
                            </div>
                            <div class="mul-img-sec mt-15">
                                <ul>
                                    <a class="joinMembers memberImg" href="javascript:void(0);" id="joinedMembers">
                                        
                                        <input type="hidden" id="get-JoinCount" value="<?php echo count($reqDetail['joinedMember']); ?>" name="">
                                        <?php 

                                        $totalJoinCount = $reqDetail['detail']->joinedMemberCount + $reqDetail['detail']->joinedCompMemberCount;

                                        if(!empty($reqDetail['joinedMember'])){

                                            $count           = count($reqDetail['joinedMember']);
                                            $arrcount = $count;
                                            foreach ($reqDetail['joinedMember'] as $val) {

                                                if(!filter_var($val->userImgName, FILTER_VALIDATE_URL) === false){ 
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

                                        <a href="<?php echo base_url('home/chat?userId=').encoding('event_'.$reqDetail['detail']->eventId).'&'.'type=event';?>"><li class="brdr-icn font-icn"><i class="fa fa-comments"></i></li></a>
                                        <?php }else{ ?> 
                                        <a href="<?php echo base_url('home/chat?userId=').encoding('event_'.$reqDetail['detail']->eventId).'&'.'type=event';?>"><li class="brdr-icn font-icn"><i class="fa fa-comments"></i></li></a>
                                        <?php } ?>
                                    </a>
                                </ul>
                            </div>
                        <?php

                        }

                        ?>
                        <!-- end group chat section -->

                        <div class="clearfix"></div>

                        <!-- start show joined member list -->
                        <?php if($reqDetail['detail']->memberStatus == '1' || $reqDetail['detail']->memberStatus == '3' ){?>

                            <div class="usrs-limit pt-30">
                                <h4><?php echo lang('event_join_mem'); ?></h4>                                           
                            </div>
                            <div class="mul-img-sec mt-15">
                                <ul>
                                    <a class="joinMembers memberImg" href="javascript:void(0);" id="joinedMembers">
                                        
                                        <input type="hidden" id="get-JoinCount" value="<?php echo count($reqDetail['joinedMember']); ?>" name="">

                                        <?php

                                        $totalJoinCount = $reqDetail['detail']->joinedMemberCount + $reqDetail['detail']->joinedCompMemberCount;
                                        
                                        if(!empty($reqDetail['joinedMember'])){

                                            $count           = count($reqDetail['joinedMember']);
                                            $arrcount = $count;
                                            
                                            foreach ($reqDetail['joinedMember'] as $val) {

                                                if(!filter_var($val->userImgName, FILTER_VALIDATE_URL) === false){
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
                        <?php } ?>
                        <!-- end show joined member list -->

                        <div class="clearfix"></div>

                         <!-- start show companion member list -->
                        <?php if($reqDetail['detail']->ownerType == 'Administrator'){ if(empty($reqDetail['companionMemberAccept'])){ if($reqDetail['detail']->companionMemberCount > 0){?>

                            <div class="usrs-limit pt-30">
                                <h4><?php echo lang('event_comp_mem'); ?></h4>                                           
                            </div>
                            <div class="mul-img-sec mt-15">
                                <ul>
                                    <a class="compMembers memberImg" href="javascript:void(0);" id="compMembers">
                                        
                                        <input type="hidden" id="get-compCount" value="<?php echo count($reqDetail['companionMember']); ?>" name="">

                                        <?php

                                        if(!empty($reqDetail['companionMember'])){
                                            
                                            foreach ($reqDetail['companionMember'] as $val) {

                                                if(!filter_var($val->userImgName, FILTER_VALIDATE_URL) === false) {

                                                    $compMemImg = $val->userImgName;

                                                }else if(!empty($val->userImgName)){

                                                    $compMemImg = AWS_CDN_USER_THUMB_IMG.$val->userImgName;

                                                } else{                 
                                                    $compMemImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                                }
                                        ?>

                                        <li><img src="<?php echo $compMemImg;?>"></li>

                                        <?php } ?>

                                        <?php $totalCompCount = $reqDetail['detail']->companionMemberCount;

                                            if($totalCompCount > 3){
                                                $remainingCompCount = $totalCompCount-3;
                                        ?>
                                            <li class="brdr-icn mmbr-num"><?php echo $remainingCompCount.'+';?></li>

                                        <?php } } else {

                                            //echo '<li class="brdr-icn mmbr-num">0</li>';
                                            echo '<span class="no-mmbr">'.lang('no_comp_mem').'</span>';
                                        }
                                        ?>
                                    </a>
                                </ul>
                            </div>

                        <?php } }else { ?> 

                            <div class="usrs-limit pt-30">
                                <h4><?php echo lang('event_comp_mem'); ?></h4>                                           
                            </div>
                            <div class="mul-img-sec mt-15">
                                <ul>
                                                                            
                                    <?php 

                                    if(!filter_var($reqDetail['companionMemberAccept']->userImgName, FILTER_VALIDATE_URL) === false) {

                                        $compMemImg = $reqDetail['companionMemberAccept']->userImgName;

                                    }else if(!empty($reqDetail['companionMemberAccept']->userImgName)){

                                        $compMemImg = AWS_CDN_USER_THUMB_IMG.$reqDetail['companionMemberAccept']->userImgName;

                                    } else{                 
                                        $compMemImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                    }
                                    ?>
                                    <li class="camMemberList">
                                        <div class="userIn">
                                            <img src="<?php echo $compMemImg;?>"> 
                                            <span><?php echo ucfirst($reqDetail['companionMemberAccept']->fullName); ?><br/>
                                                <?php 

                                                    if($reqDetail['companionMemberAccept']->companionMemberStatus == 1){
                                                        echo '<small>'.lang('confirmed_payment_status').'</small>';
                                                    }elseif($reqDetail['companionMemberAccept']->companionMemberStatus == 2){
                                                        echo '<small>'.lang('payment_pending_status').'</small>';
                                                    }elseif($reqDetail['companionMemberAccept']->companionMemberStatus == 3){
                                                        echo '<small>'.lang('confirmed_status').'</small>';
                                                    }

                                                ?>
                                            </span>
                                        </div>
                                        <div class="rsvp-button btnInline text-right campPay pay-btn">

                                        <?php if($reqDetail['companionMemberAccept']->companionMemberStatus == 2){?>
                                                
                                                <a href="javascript:void(0);" onclick="openStripeModel(this)" data-eid="<?php echo $reqDetail['companionMemberAccept']->eventId;?>" data-compmemid="<?php echo $reqDetail['companionMemberAccept']->companionMemId;?>" data-compid="<?php echo $reqDetail['companionMemberAccept']->compId;?>" data-emid="<?php echo $reqDetail['companionMemberAccept']->eventMem_Id;?>" data-pType="5" data-eamt="<?php echo $reqDetail['companionMemberAccept']->eventAmount; ?>" data-groupchat="<?php echo $reqDetail['companionMemberAccept']->groupChat; ?>" data-title="Companion Payment" class="btn form-control login_btn"><?php echo lang('pay'); ?> <?php echo $reqDetail['companionMemberAccept']->currencySymbol.''.$reqDetail['companionMemberAccept']->eventAmount; ?></a>

                                        <?php } ?>
                                        
                                        <?php if($reqDetail['companionMemberAccept']->companionMemberStatus == 1 || $reqDetail['companionMemberAccept']->companionMemberStatus == 3){?>

                                            <p class="str-sec-pra commnt-icn dsply-blck-rgt text-right fvrte">
                                                <a href="<?php echo base_url('home/chat?userId=').encoding($reqDetail['companionMemberAccept']->companionMemId).'&'.'type=user';?>"><span class="fa fa-comments"></span></a>
                                            </p>

                                        <?php } ?>
                                        </div>
                                    </li>
                                    
                                </ul>
                            </div>

                        <?php } } ?>
                        <!-- end show companion member list -->

                        <div class="clearfix"></div>

                        <?php 

                        if($reqDetail['detail']->ownerType == 'Shared_Event'){

                            if(!filter_var($reqDetail['detail']->profileImageName, FILTER_VALIDATE_URL) === false) {

                                $sharedUserImg = $reqDetail['detail']->profileImageName;

                            }else if(!empty($reqDetail['detail']->profileImageName)){ 

                                $sharedUserImg = AWS_CDN_USER_THUMB_IMG.$reqDetail['detail']->profileImageName;

                            } else{                    
                                $sharedUserImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                            }

                        ?>

                            <div class="usrs-limit mt-30">
                                <h4><?php echo lang('event_comp_mem'); ?></h4>          
                            </div>
                            <div class="author_posts_inners">
                                <div class="media lst-brdr">
                                    <div class="media-left prfle-fvrte">
                                        <a href="<?php echo base_url('home/user/userDetail/').encoding($reqDetail['detail']->memberId); ?>"><img src="<?php echo $sharedUserImg;?>" class="brdr-img" alt=""></a>
                                    </div>
                                    <div class="media-body prfle-fvrte-info pt-15">
                                        <div class="dsply-block">
                                            <a href="<?php echo base_url('home/user/userDetail/').encoding($reqDetail['detail']->memberId); ?>"><h3 class="dsply-blck-lft nme-prson"><?php echo $reqDetail['detail']->fullName;?></h3></a>
                                            <p class="str-sec-pra commnt-icn dsply-blck-rgt text-right fvrte">
                                                <a href="<?php echo base_url('home/chat?userId=').encoding($reqDetail['detail']->memberId).'&'.'type=user';?>"><span class="fa fa-comments"></span></a>
                                            </p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <h4 class="fvrte-stus"><?php echo lang('event_shared_by'); ?></h4>
                                    </div>
                                </div>
                            </div> 

                        <?php  } ?>

                        <div class="containerr mt-35">

                        <?php

                            if($reqDetail['detail']->eventEndDate > date('Y-m-d H:i:s')){

                                if($reqDetail['detail']->ownerType != 'Shared_Event'){

                                    if($reqDetail['detail']->payment == 'Paid'){
                                                    
                                        if($reqDetail['detail']->memberStatus == 5 && $reqDetail['detail']->ownerType == 'Administrator'){ 

                                        ?>

                                            <button type="button" class="btn form-control login_btn eventRequestStatus" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-mid="<?php echo $reqDetail['detail']->memberId;?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="1"><?php echo lang('accept'); ?></button>

                                            <button type="button" class="btn form-control login_btn ml-15 eventRequestStatus" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-mid="<?php echo $reqDetail['detail']->memberId;?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="2"><?php echo lang('reject'); ?></button>

                                        <?php } else {
                                    
                                            if($reqDetail['detail']->memberStatus == 1 && $reqDetail['detail']->companionMemberCount == 0 && $reqDetail['detail']->ownerType == 'Administrator'){

                                                if( $reqDetail['detail']->confirmedCount < $reqDetail['detail']->userLimit ){

                                            ?>

                                            <div class="containerr mt-35">
                                                
                                                <button type="button" class="btn form-control login_btn ml-15" onclick="shareMembers(0);" data-toggle="modal" data-target="#myModal"><?php echo lang('invite_comp_mem'); ?></button>

                                            </div>

                                            <?php } else { ?>

                                                <div class="rsvp-button btnInline text-center"> 
                                                    
                                                    <button type="button" class="btn form-control login_btn ml-15" data-toggle="modal" data-target="#myModalShare"><?php echo lang('invite_comp_mem'); ?></button>

                                                </div>

                                            <?php 

                                                }

                                            } elseif($reqDetail['detail']->memberStatus == 2 && $reqDetail['detail']->ownerType == 'Administrator'){ 

                                                    if( $reqDetail['detail']->confirmedCount < $reqDetail['detail']->userLimit ){

                                            ?>
                                                

                                                <div class="containerr mt-35">

                                                    <button type="button" class="btn form-control login_btn" onclick="openStripeModel(this)" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-mid="<?php echo $reqDetail['detail']->memberId;?>" data-emid="<?php echo $reqDetail['detail']->eventMemId;?>" data-pType="4" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-eamt="<?php echo $reqDetail['detail']->eventAmount; ?>" data-title="Event Payment"><?php echo lang('pay'); ?> <?php echo $reqDetail['detail']->currencySymbol.''.$reqDetail['detail']->eventAmount; ?></button>

                                                </div>
                                
                                            <?php } else{ ?>

                                                <div class="containerr mt-35">
                                                            
                                                    <button type="button" class="btn form-control login_btn ml-15" data-toggle="modal" data-target="#myModalShare"><?php echo lang('pay'); ?> <?php echo $reqDetail['detail']->currencySymbol.''.$reqDetail['detail']->eventAmount; ?></button>

                                                </div>

                                            <?php } ?>
                                    
                                        <?php } } ?>

                                    <?php } elseif($reqDetail['detail']->payment == 'Free'){ ?> 

                                        <?php if($reqDetail['detail']->memberStatus == 5 && $reqDetail['detail']->ownerType == 'Administrator'){ ?>

                                            <button type="button" class="btn form-control login_btn eventRequestStatus" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-mid="<?php echo $reqDetail['detail']->memberId;?>" data-evtmemid="<?php echo $reqDetail['detail']->eventMemId;?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="1"><?php echo lang('accept'); ?></button>

                                            <button type="button" class="btn form-control login_btn ml-15 eventRequestStatus" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-mid="<?php echo $reqDetail['detail']->memberId;?>" data-evtmemid="<?php echo $reqDetail['detail']->eventMemId;?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="2"><?php echo lang('reject'); ?></button>

                                        <?php } else {
                                    
                                            if($reqDetail['detail']->memberStatus == 3 && $reqDetail['detail']->companionMemberCount == 0 && $reqDetail['detail']->ownerType == 'Administrator'){

                                                if( $reqDetail['detail']->confirmedCount < $reqDetail['detail']->userLimit ){

                                            ?>
                                                    <div class="containerr mt-35">
                                                        
                                                        <button type="button" class="btn form-control login_btn ml-15" onclick="shareMembers(0);" data-toggle="modal" data-target="#myModal"><?php echo lang('invite_comp_mem'); ?></button>

                                                    </div>

                                                <?php

                                                } else {

                                                ?> 

                                                    <div class="containerr mt-35">
                                                            
                                                        <button type="button" class="btn form-control login_btn ml-15" data-toggle="modal" data-target="#myModalShare"><?php echo lang('invite_comp_mem'); ?></button>

                                                    </div>

                                            <?php

                                                }
                                            }
                                        }
                                    } 
                                }
                            ?>

                            <?php if($reqDetail['detail']->ownerType == 'Shared_Event'){

                                if ($reqDetail['detail']->memberStatus == '5') { ?>
                                    
                                    <button type="button" class="btn form-control login_btn comp-status" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-eventmemid="<?php echo $reqDetail['detail']->eventMemId;?>" data-compid="<?php echo decoding($_GET['compId']);?>" data-userid="<?php echo $this->session->userdata('userId');?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="1"><?php echo lang('accept'); ?></button>

                                    <button type="button" class="btn form-control login_btn ml-15 comp-status" data-eid="<?php echo $reqDetail['detail']->eventId;?>" data-eventmemid="<?php echo $reqDetail['detail']->eventMemId;?>" data-compid="<?php echo decoding($_GET['compId']);?>" data-userid="<?php echo $this->session->userdata('userId');?>" data-payment="<?php echo $reqDetail['detail']->payment; ?>" data-groupchat="<?php echo $reqDetail['detail']->groupChat; ?>" data-status="2"><?php echo lang('reject'); ?></button>

                                <?php } ?> 

                            <?php  } }else{
                                echo lang('event_expired');
                            } ?>
                            
                        </div>
                        <div class="clearfix"></div>
                        <!-- Start review section -->  
                        <?php

                        $date = date('Y-m-d H:i:s');

                        if (($reqDetail['detail']->memberStatus == '1') || ($reqDetail['detail']->memberStatus == '3')) { 

                            if($reqDetail['detail']->eventStartDate < $date){

                        ?>
                            <hr class="fde-line">
                            <div class="meetng-conclusion mt-25">
                                <div class="evnt-rv-head">
                                    <div class="s_title hed-sec">
                                        <h4 class="pb-5"><?php echo lang('event_review'); ?></h4>
                                        <img src="<?php echo AWS_CDN_FRONT_IMG; ?>widget-title-border.png" alt="">
                                    </div>
                                    <?php if($reqDetail['detail']->reviewStatus != 1){ ?>
                                        <div class="hed-sec-btn">
                                            <button type="button" class="btn form-control login_btn frnd-sec-btn mr-10" data-toggle="modal" data-target="#review-pop"><?php echo lang('give_review'); ?></button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php

                                if(!empty($eventReviewList)){ ?>

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
                                <?php } ?>
                            </div>

                        <?php } } ?>
                        <!-- End review section -->  
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<input type="hidden" id="memberId" name="memberId" value="">
<!-- Start modal popup for invite friend's list -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('invite_comp_mem'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="flter-lst-invte-member">
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <div class="mob-res">
                                <div class="search_widget loc-search text-center">
                                    <div class="input-group">
                                        <input class="form-control" id="searchName" placeholder="Search By Name" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pl-0">
                            <div class="flter-icn">
                                <a href="javascript:void(0)"><img id="img-flter" src="<?php echo AWS_CDN_FRONT_IMG;?>filter.png" data-toggle="tooltip" title="More Filters" /></a>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="list-flter-sec">
                                <div class="flter-sec-invite" id="filter-invite">

                                    <div class="form-group regfld mt-20">
                                        <input class="form-control" id="usraddress" placeholder="Search By Location" name="searchLocation" type="text">
                                        <input type="hidden" name="address" id="usrsadd">
                                        <input type="hidden" name="latitude" id="usrsearchlat">
                                        <input type="hidden" name="longitude" id="usrsearchlong">
                                        <input type="hidden" name="city" id="eventCity">
                                        <input type="hidden" name="state" id="eventState">
                                        <input type="hidden" name="country" id="eventCountry">
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                        <div class="inlne-str-rate">

                                            <p class="mb-15"><?php echo lang('select_popularity'); ?></p>

                                            <div class="populr-rte" id="termSheetPopup">

                                                <input id="5-str" class="rate_Checkbox" name='rating' type="checkbox" value="5">
                                                <label for="5-str">5<span class="fa fa-star"></span></label>

                                                <input id="4-str" class="rate_Checkbox" name='rating' type="checkbox" value="4">
                                                <label for="4-str">4<span class="fa fa-star"></span></label>

                                                <input id="3-str" class="rate_Checkbox" name='rating' type="checkbox" value="3">
                                                <label for="3-str">3<span class="fa fa-star"></span></label>

                                                <input id="2-str" class="rate_Checkbox" name='rating' type="checkbox" value="2">
                                                <label for="2-str">2<span class="fa fa-star"></span></label>

                                                <input id="1-str" class="rate_Checkbox" name='rating' type="checkbox" value="1">
                                                <label for="1-str">1<span class="fa fa-star"></span></label>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group text-right pay-btn mt-30">
                                            <button type="button" onclick="shareMembers(0,1);" class="btn form-control login_btn"><?php echo lang('apply_filter'); ?></button>
                                            <a href="javascript:void(0)" onclick="resetAll();"><?php echo lang('reset_filter'); ?></a>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="profile_list scroll-prt">
                                    <div id="share-members"></div>
                                </div>
                                <br>
                                <div id='showLoader' class="show_loader clearfix" data-offset="0" data-isNext="1">                    
                                    <img src='<?php echo AWS_CDN_FRONT_IMG;?>Spinner-1s-80px.gif' alt=''>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <!-- <a href="javascript:void(0)" onclick="save()" class="btn form-control login_btn snd-invite">Send Invitation</a> -->
                    
                    <a href="javascript:void(0);" id="share-event" data-eventid="<?php echo $reqDetail['detail']->eventId;?>" data-eventp="<?php echo $reqDetail['detail']->privacy;?>" data-evenmemid="<?php echo $reqDetail['detail']->eventMemId;?>" class="btn form-control login_btn snd-invite"><?php echo lang('share'); ?></a>

                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End modal popup for invite friend's list -->
<!-- The Modal For User Limit exceed -->
<div class="modal fade" id="myModalShare" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('event_title'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('user_limit_exceed'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <a href="javascript:void(0);">
                        <button data-dismiss="modal" aria-label="Close" class="btn form-control login_btn forgot-password"><?php echo lang('ok'); ?></button>
                    </a>
                    <a href="javascript:void(0)"  data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start show companion member's list -->
<section id="sidebar-right" class="sidebar-menu sidebar-right">
    <div class="Notification" id="notifyOpen">
        <div class="notifyList">
            <div class="chat-user white chatChUser">
                <div class="">
                    <div class="dsply-block brdr-blck-sec">
                        <div class="dsply-blck-lft">
                            <div class="s_title mt-12">
                                <h4><?php echo lang('event_comp_mem'); ?></h4>
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
                        <div id="companion-member"></div>           
                    </div>                             
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End show companion member's list -->

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

<!-- start event review pop up--> 
<div class="modal fade" id="review-pop" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('give_review');?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reviewForm" method="post" action="<?php echo base_url('home/event/giveReview/');?>">
                <input type="hidden" name="receiverId" value="<?php echo $reqDetail['detail']->eventOrganizer; ?>">
                <input type="hidden" name="referenceId" value="<?php echo $reqDetail['detail']->eventId; ?>">
                <div class="modal-body mdl-body">
                    <p class="text-center para pop-up-pra"><?php echo lang('appointment_review_thoughts'); ?></p>
                    <p class="str-sec-pra revw-str text-center mt-10">
                        <?php
                            for($i=1;$i<=5;$i++){
                        ?>
                            <span id="rate_<?php echo $i;?>" onclick="rateEvent('<?php echo $i;?>')" class="fa fa-star-o"></span> 
                        <?php } ?>
                        <input type="hidden" id="rate_value" name="rating" value=""/>
                    </p>
                    <div class="regfrm mdl-pad mt-20">
                        <div class="form-group regfld">
                            <textarea class="form-control review-textarea" name="comment" maxlength=200 required="" type="text" placeholder="Write Comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn shre-btn giveReview"><?php echo lang('submit'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end event review pop up--> 

<div class="sidebar_overlay sidebar_over"></div>
<div class="sidebar_overlay1"></div>

<script type="text/javascript">

    let senderId        = '<?php echo $this->session->userdata('userId');?>';
    let senderName      = "<?php echo $this->session->userdata('fullName');?>";
    var eventId         = '<?php echo decoding($this->uri->segment('4'));?>';
    let eventName       = "<?php echo ucfirst($reqDetail['detail']->eventName);?>";
    let eventImage      = '<?php echo AWS_CDN_EVENT_MEDIUM_IMG.$reqDetail['detail']->eventImage[0]->eventImageName;?>';

    function rateEvent(no)
    {
        for(var i=1;i<=5;i++)
        {
            if(i<=no)
            {
                $("#rate_"+i).attr('class','fa fa-star');
            }
            else
            {
                $("#rate_"+i).attr('class','fa fa-star-o');
            }
        }
        $("#rate_value").val(no);
    }

    //review form validation
    var review_form = $("#reviewForm");

    review_form.validate({
        rules: {
            comment : {
                required: true               
            }                      
        },
        messages: {
            
            comment : {
                required: "Please give review."
            }
        }
    }); //End counter validation

    // to give review for aapoinment with user
    $('body').on('click', ".giveReview", function (event) {

        if(review_form.valid() !== true){
            return false;
        } 

        var rating = $("#rate_value").val();

        if(rating == ''){
            toastr.error('Please give rating.');
            return false;
        }
        
        $(".shre-btn").removeClass("giveReview");
        $("#review").hide();

        var _that = $(this), 
        form = _that.closest('form'),
        formData = new FormData(form[0]),
        f_action = form.attr('action');

        $.ajax({

            type: "POST",
            url: f_action,
            data: formData, //only input
            processData: false,
            contentType: false,
            dataType: "JSON",  
            beforeSend: function () { 
                show_loader(); 
            },
            success: function (data, textStatus, jqXHR) {  
                hide_loader();        
               
                if (data.status == 1){ 
                    toastr.success(data.msg); 
                    window.setTimeout(function () {                      
                        location.reload();
                    }, 200);

                }else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 200);

                }else {
                    $(".shre-btn").addClass("giveReview");
                    window.setTimeout(function () {
                        location.reload();
                    }, 200);
                } 
            },

            error:function (){
                hide_loader(); 
                toastr.error('Failed! Please try again');
            }
        });
    });

    $(document).ready(function(){

        setValue('senderId',senderId);
        setValue('senderName',senderName);
        setValue('eventId',eventId);
        setValue('eventName',eventName);
        setValue('eventImage',eventImage);
        
        /*Start show joined member list*/
        $(".sidebar_overlay1").click(function(){
            openClose2();
        });
        $("#sidebar_close_icon2").click(function(){
            openClose2();
        });
        function openClose2(){

            $("#sidebar-right2").toggleClass("sidebar-open2");
            $(".sidebar_overlay1").toggleClass("sidebar_overlay_active1"); 
            $("body").toggleClass("hide_overflow"); 
        }
        var total = $("#get-JoinCount").val();            

        if(total > 0){
            $(".joinMembers").click(function(){
                openClose2();
            });
        }
        /*End show joined member list*/

        /*Start show companion member list*/
        $(".sidebar_overlay").click(function(){
            openClose();
        });
        $("#sidebar_close_icon").click(function(){
            openClose();
        });
        function openClose(){    
            $("#sidebar-right").toggleClass("sidebar-open");
            $(".sidebar_overlay").toggleClass("sidebar_overlay_active"); 
            $("body").toggleClass("hide_overflow"); 
        }

        var totalComp = $("#get-compCount").val();
        if(totalComp > 0){
            $(".compMembers").click(function(){
                openClose();
            });
        }
        /*End show companion member list*/
        
    });

    $('.scroll-prt').scroll(function() {

        let div = $(this).get(0);
        if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
            // do the lazy loading here
            shareMembers(1);
        }
    });
    
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