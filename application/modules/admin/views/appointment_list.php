<div class="row">
    <?php  
    if(!empty($appointment)) { 

        foreach ($appointment as $value) {

            if(!filter_var($value->forImage, FILTER_VALIDATE_URL) === false) { 

                $userImg = $value->forImage;

            }else if(!empty($value->forImage)){ 

                $userImg = AWS_CDN_USER_THUMB_IMG.$value->forImage;

            } else{

                $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
            }
    ?>
    
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="appointment">
    <div class="apoin-lst-sec">
        <div class="lst-pic-nme-dte">
            <img src="<?php echo $userImg;?>" />
            <div class="pic-sde-txt">
                <h2 class="pt-0 mb-0"><?php echo ucfirst($value->ForName); ?></h2>
                <p class="apoin-tme-sec"><span><?php echo date('d ',strtotime($value->appointDate));?> </span><?php echo date('M, ',strtotime($value->appointDate));?> <?php echo date('Y',strtotime($value->appointDate));?>, <?php echo date('h:i A',strtotime($value->appointTime));?> </p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="apoin-othr-text mt-20">
            
            <?php

                        if($value->isFinish == '0'){                            

                            if ($value->isCounterApply == 1) {

                                if($value->appointmentStatus == '5'){

                                    echo '<p class="defaultStatus">Counter rejected</p>';

                                }else {
                                    
                                    if ($value->counterStatus == 0) {

                                        echo '<p class="waitingStatus">New appointment request</p>';                                        

                                    }elseif($value->counterStatus == 1){

                                        echo '<p class="waitingStatus">Payment is pending</p>';

                                    }elseif($value->counterStatus == 2){

                                        echo '<p class="defaultStatus">Counter rejected</p>';

                                    }elseif($value->counterStatus == 3){

                                        echo '<p class="confirmStatus">Appointment confirmed</p>';
                                    }
                                }
                                
                            }elseif($value->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete 

                                echo '<p class="waitingStatus">Waiting for approval</p>';

                            }elseif($value->appointmentStatus == '2'){

                                if ($value->offerType == 1 ) { // 1:Paid,2:Free

                                    echo '<p class="waitingStatus">Payment is pending</p>';

                                }else{

                                    echo '<p class="confirmStatus">Appointment confirmed</p>';
                                }                

                            }elseif($value->appointmentStatus == '3'){ 

                                echo '<p class="defaultStatus">Request rejected</p>';

                            }elseif($value->appointmentStatus == '4'){ 

                                echo '<p class="confirmStatus">Appointment confirmed</p>';

                            }elseif($value->appointmentStatus == '5'){ 

                                echo '<p class="defaultStatus">Request cancelled</p>';
                            }
                        } else{

                            echo '<p class="defaultStatus">Finished appointment</p>';
                        }

                        ?> 
                        <span class="timemeta"><?php echo time_elapsed_string($value->crd);?></span>
            <p class="apin-ads-mrkr"><span class="fa fa-map-marker"></span><?php echo $value->appointAddress; ?></p>
            <div class="clearfix"></div>
            <div class="prce-block-prt">

                <div class="lst-ofrd-prce ofrd-pcr">
                    <h3 class="mt-10"><?php echo !empty($value->offerPrice) ? '$ '.$value->offerPrice : 'Free';?></h3>
                    <p class="price-tag">Offered Price</p>
                </div>

                <?php if(!empty($value->counterPrice)){ ?>

                    <div class="lst-ofrd-prce cnter-pcr ml-25 float-right">
                        <h3 class="mt-10"><?php echo '$ '.$value->counterPrice;?></h3>
                        <p class="price-tag">Counter Price</p>
                    </div>

                <?php } ?>
            </div>

            <div class="clearfix"></div>
            <div class="fde-line"></div>

            <div class="clearfix"></div>
        </div>
    </div>
</div>
    <?php } ?>
</div>
<?php if( $offset == 0 ){?>
<div>
    <div id="memberData"></div><!--load data -->
    
    <div class="PaginationBlock">
        <input type="hidden" name="totalCount" id="totalCountss" value="<?php echo $total_count?>">
        <div id="loadMember" class="text-center" >
            <button class="btn btn-flat margin" id="btnMem" >Load More</button>
        </div>
    </div>
</div>
<?php }

} else { ?>
    <div class="media-blck">
        <div class="text-center">
            <div> No Appointment Available!</div>
        </div>        
    </div>
<?php } ?>