<?php $frontend_assets =  base_url().'frontend_asset/';
//pr($eventRequest);
    if(!empty($eventRequest)){
        
        foreach ($eventRequest as $value) { 
?>

<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 eventReqVisitCount">

    <?php $compId = $eventMemId =''; 

    if($value->ownerType == 'Administrator'){

        $eventMemId = encoding($value->eventMemId);
        $query_str = '/?eventMemId='.$eventMemId;

    }elseif($value->ownerType == 'Shared Event'){

        $compId = encoding($value->compId);
        $query_str = '/?compId='.$compId;

    } 

    if(!empty($value->eventImageName)){

        $eventImg = AWS_CDN_EVENT_THUMB_IMG.$value->eventImageName;

    } else{                    
        $eventImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
    }

    if(strtotime($value->eventEndDate) < strtotime(date('Y-m-d H:i:s'))){

        $status = '<div class="alert alert-danger"><strong>'.lang('event_passed_status').'</strong></div>';

    }else{

        if($value->memberStatus == '1'){
            $status = '<div class="alert alert-success"><strong>'.lang('confirmed_payment_status').'</strong></div>';
        }
        else if($value->memberStatus == '2'){
            $status = '<div class="alert alert-warning"><strong>'.lang('payment_pending_status').'</strong></div>';
        }
        else if($value->memberStatus == '3'){
            $status = '<div class="alert alert-success"><strong>'.lang('confirmed_status').'</strong></div>';
        }
        else if($value->memberStatus == '4'){
            $status = '<div class="alert alert-danger"><strong>'.lang('request_rejected_status').'</strong></div>';
        }
        else if($value->memberStatus == '5'){
            $status = '<div class="alert alert-warning"><strong>'.lang('pending_request_status').'</strong></div>';
        }
        else if ($value->memberStatus == '6'){
            $status = '<div class="alert alert-danger"><strong>'.lang('request_canceled_status').'</strong></div>';
        }
    }

    ?>

    <a href="<?php echo base_url('home/event/eventRequestDetail/').encoding($value->eventId).$query_str;?>">
        <div class="blog_grid_item our_event">
            <div class="blog_grid_img">
                <img src="<?php echo $eventImg;?>" alt="">
                <div class="new_tag">
                    <h4><?php echo ($value->payment == 'Free') ? lang('free_event') : lang('pain_event'); ?><span><?php echo ' '.$value->currencySymbol.''.$value->eventAmount; ?></h4>
                </div>
            </div>
            <div class="blog_grid_content our_sec_br">
              <a href="<?php echo base_url('home/event/eventRequestDetail/').encoding($value->eventId).$query_str;?>"><h3><?php echo wordwrap(substr(ucfirst($value->eventName), 0, 25), 20); ?></h3></a>
                <div class="blog_grid_date">
                   
                    <div class="our_txt_sec">   
                        <div class="txt_div">
                            <h5><i class="fa fa-calendar"></i> <?php echo date('d M, Y',strtotime($value->eventStartDate));?></h5>    
                        </div>
                        <div class="txt_div">
                            <?php if($value->privacy == 'Private'){ 
                                $cls = 'lock';
                            }else{
                                $cls = 'users';
                            }
                            ?>
                            <h5><i class="fa fa-<?php echo $cls;?>"></i> <?php echo ($value->privacy  == 'Public') ? lang('public_invitation') : lang('private_invitation');?></h5>
                        </div>

                    </div>
                 <p class="apin-ads-mrkr apin-ads-mrkr2"><i class="fa fa-map-marker"></i> <?php echo wordwrap(substr($value->eventPlace, 0, 75), 50); ?></p> 
                 <div class="psted-by-sec flex-div mb-20 alwys_flex">

                            <?php                             
                                $postedBy = '';
                                if($value->ownerType == 'Shared Event'){

                                    $postedBy = lang('event_shared');

                                }else{
                                    $postedBy = lang('event_admin');
                                }

                                if(!filter_var($value->webProfileImage, FILTER_VALIDATE_URL) === false) {

                                    $userImg = $value->webProfileImage;

                                }else if(!empty($value->webProfileImage)){

                                    $userImg = AWS_CDN_USER_THUMB_IMG.$value->webProfileImage;

                                } else{

                                    $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }
                            ?>

                            <a class="dsply-nne" href="<?php echo base_url('home/user/userDetail/').encoding($value->userId); ?>"><img src="<?php echo $userImg;?>" /></a><span><?php echo lang('event_by'); ?> - <b data-toggle="tooltip" data-placement="top" title="<?php echo ucfirst($value->fullName);?>"><a href="<?php echo base_url('home/user/userDetail/').encoding($value->userId); ?>"><?php echo limit_text($value->fullName,10); ?></a></b> - <?php echo $postedBy;?></span>
                        </div>  
                </div>
                <div class="apoin-othr-text mt-20">
                    <?php echo $status; ?>
                </div>
                <div class="event_btns mt-20 evnt-min">
                    
                    <?php

                    if($value->eventEndDate > date('Y-m-d H:i:s')){

                        if(($value->ownerType == 'Administrator') && ($value->memberStatus == '5')){ ?>

                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="<?php echo lang('accept'); ?>" class="event_btn add-rmv-btn eventRequestStatus btn-green" data-status="1" data-eid="<?php echo $value->eventId; ?>" data-mid="<?php echo $value->memberId; ?>" data-evtmemid="<?php echo $value->eventMemId; ?>" data-evtpayment="<?php echo $value->payment; ?>" data-groupchat="<?php echo $value->groupChat; ?>"><i class="fa fa-check"></i></a>

                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="<?php echo lang('reject'); ?>" class="event_btn add-rmv-btn eventRequestStatus" data-status="2" data-eid="<?php echo $value->eventId;?>" data-mid="<?php echo $value->memberId; ?>" data-evtmemid="<?php echo $value->eventMemId; ?>" data-groupchat="<?php echo $value->groupChat; ?>"><i class="fa fa-close"></i></a>

                    <?php } } ?>

                </div>
            </div>
        </div>
    </a>
</div>

<?php  } }else{ echo "<div class='notFound'><h3>".lang('event_no_record')."</h3></div>" ;

} ?>
<?php if($offset==0){ ?>
    <span id="appendDataForEventReq"></span>
    <input type="hidden" id="eventReqCount-count" value="<?php echo $eventReqCount;?>">
    <div class="text-center loadMoreBtn" id="load_more_event_req" style="display:none;">  
        <a href="javascript:void(0);" class="btn form-control login_btn" onclick="eventRequest();"><?php echo lang('load_more'); ?></a>
    </div>
<?php } ?>