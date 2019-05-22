<?php

if(!empty($myApp)){ foreach ($myApp as $value) { 

    $status = $acceptBtn = $rejectBtn = $applyCounterBtn = '';  

    $userImg        = ($value->appointById == $this->session->userdata('userId')) ? $value->forImage : $value->byImage;
    $userName       = ($value->appointById == $this->session->userdata('userId')) ? $value->ForName : $value->ByName;
    $userId       = ($value->appointById == $this->session->userdata('userId')) ? $value->appointForId : $value->appointById;
    
    if(!filter_var($userImg, FILTER_VALIDATE_URL) === false) {

        $userImg = $userImg;

    }else if(!empty($userImg)){ 

        $userImg = AWS_CDN_USER_THUMB_IMG.$userImg;

    } else{

        $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
    }

    if($value->appointById == $this->session->userdata('userId')){

        if($value->isFinish == '1'){

            $status = '<div class="alert alert-success"><strong>'.lang('appointment_finished').'</strong></div>';

        }else{

            if ($value->isCounterApply == 1) {

                if($value->appointmentStatus == '5'){

                    $status = '<div class="alert alert-danger"><strong>'.lang('counter_rejected').'</strong></div>';

                }else {
                    
                    if ($value->counterStatus == 0) {

                        //$counterPrice & btn A/C  //show

                        $status = '<div class="alert alert-warning"><strong>'.lang('new_app_request').'</strong></div>';

                        $acceptBtn = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="'.lang('accept').'" class="event_btn appCounterStatus btn-green" data-counterstatus="1" data-appforid="<?php echo $value->appointForId; ?>" data-appId="'.$value->appId.'"><i class="fa fa-check"></i></a>';

                        $rejectBtn = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="'.lang('reject').'" class="event_btn appCounterStatus" data-counterstatus="2" data-appforid="<?php echo $value->appointForId; ?>" data-appId="'.$value->appId.'"><i class="fa fa-close"></i></a>';

                    }elseif($value->counterStatus == 1){

                        $status = '<div class="alert alert-warning"><strong>'.lang('payment_pending').'</strong></div>';

                    }elseif($value->counterStatus == 2){

                        $status = '<div class="alert alert-danger"><strong>'.lang('counter_rejected').'</strong></div>';

                    }elseif($value->counterStatus == 3){

                        $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';
                    }
                }
                
            }elseif($value->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete 

                $status = '<div class="alert alert-warning"><strong>'.lang('waiting_approval').'</strong></div>';

            }elseif($value->appointmentStatus == '2'){

                if ($value->offerType == 1 ) { // 1:Paid,2:Free

                    $status = '<div class="alert alert-warning"><strong>'.lang('payment_pending').'</strong></div>';

                }else{

                    $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';
                }                

            }elseif($value->appointmentStatus == '3'){ 

                $status = '<div class="alert alert-danger"><strong>'.lang('request_rejected').'</strong></div>';

            }elseif($value->appointmentStatus == '4'){ 

                $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';

            }elseif($value->appointmentStatus == '5'){ 

                $status = '<div class="alert alert-danger"><strong>'.lang('request_cancelled').'</strong></div>';
            }
        }

    }else{

        if($value->isFinish == '1'){

            $status = '<div class="alert alert-success"><strong>'.lang('appointment_finished').'</strong></div>';     

        }else{

            if ($value->isCounterApply == 1) {

                if ($value->counterStatus == 0) {

                    $status = '<div class="alert alert-warning"><strong>'.lang('waiting_approval').'</strong></div>';

                }elseif($value->counterStatus == 1){

                    $status = '<div class="alert alert-warning"><strong>'.lang('payment_pending').'</strong></div>';

                }elseif($value->counterStatus == 2){

                    $status = '<div class="alert alert-danger"><strong>'.lang('counter_rejected').'</strong></div>';

                }elseif($value->counterStatus == 3){

                    $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';
                    $countPrice = ''; //show
                }
                
            }elseif($value->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete

                $status = '<div class="alert alert-warning"><strong>'.lang('new_app_request').'</strong></div>';
                //$counterPrice & btn A/C  & counter apply popup//show
                $acceptBtn = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="'.lang('accept').'" class="event_btn add-rmv-btn appStatus btn-green" data-status="2" data-appId="'.$value->appId.'"><i class="fa fa-check"></i></a>';

                $rejectBtn = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="'.lang('reject').'" class="event_btn add-rmv-btn appStatus" data-status="3" data-appId="'.$value->appId.'"><i class="fa fa-close"></i></a>';
                
                if ($value->offerType == 1 ) { // 1:Paid,2:Free

                    $applyCounterBtn = '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="'.lang('apply_counter').'" class="event_btn" onclick="counterModel('.$value->appId.','.$value->offerPrice.','.$value->appointById.');"><i class="fa fa-hourglass"></i></a>';
                }

            }elseif($value->appointmentStatus == '2'){

                if ($value->offerType == 1 ) { // 1:Paid,2:Free

                    $status = '<div class="alert alert-warning"><strong>'.lang('payment_pending').'</strong></div>';

                }else{

                    $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';
                }                

            }elseif($value->appointmentStatus == '4'){ 

                $status = '<div class="alert alert-success"><strong>'.lang('appointment_confirmed').'</strong></div>';
            }
        }
    }
?>
<script type="text/javascript">
    
    //$('#appLength<?php echo $value->appId;?>').remove();
</script>

<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12" id="appLength<?php echo $value->appId;?>">
    <div class="apoin-lst-sec">
        <div class="lst-pic-nme-dte">
            <a class="wdth_25" href="<?php echo base_url('home/user/userDetail/').encoding($userId);?>"><img src="<?php echo $userImg;?>" /></a>
            <div class="pic-sde-txt name-len">
                <a href="<?php echo base_url('home/user/userDetail/').encoding($userId);?>"><h2><?php echo wordwrap(substr(ucfirst($userName), 0, 27), 15); ?></h2></a>
                <p class="apoin-tme-sec"><span><?php echo date('d ',strtotime($value->appointDate));?> </span><?php echo date('M, ',strtotime($value->appointDate));?> <?php echo date(' Y',strtotime($value->appointDate));?>, <?php echo date('h:i A',strtotime($value->appointTime));?> </p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="apoin-othr-text mt-20">
            
            <?php echo $status; ?>
            <p class="apin-ads-mrkr pt-20"><span class="fa fa-map-marker"></span><?php echo wordwrap(substr($value->appointAddress, 0, 75), 50); ?></p>
            <div class="clearfix"></div>
            <div class="prce-block-prt mt-20">

                <div class="lst-ofrd-prce ofrd-pcr">
                    <h3><?php echo !empty($value->offerPrice) ? '$ '.$value->offerPrice : lang('free_event');?></h3>
                    <p class="price-tag"><?php echo lang('offered_price'); ?></p>
                </div>

                <?php if(!empty($value->counterPrice)){ ?>

                    <div class="lst-ofrd-prce cnter-pcr ml-25">
                        <h3><?php echo '$ '.$value->counterPrice;?></h3>
                        <p class="price-tag"><?php echo lang('counter_price_title');?></p>
                    </div>

                <?php } ?>
            </div>

            <div class="clearfix"></div>
            <div class="fde-line"></div>

            <div class="dsply-btn-prt mt-20">
                <div class="mre-dtl">
                    <a href="<?php echo base_url('home/appointment/viewAppOnMap/').encoding($value->appId).'/';?>" class="login_btn btn_focs_whte"><?php echo lang('app_detail');?></a>
                </div>
                <div class="event_btns">

                    <?php echo $acceptBtn; ?>

                    <?php echo $rejectBtn; ?>

                    <?php echo $applyCounterBtn; ?>
                    
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<?php         
        } 
    } elseif($page == 0){ 
        echo "<div class='notFound'><h3>".lang('no_appointment_available')."</h3></div>" ;
    } 
?>
<input type="hidden" id="totalMyApp-count" value="<?php echo count($myApp);?>">
<input type="hidden" id="type" value="<?php echo $type; ?>">