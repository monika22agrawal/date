<!-- Modal -->
<div class="modal fade" id="commonModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">            
            <form class="form-horizontal" role="form" id="editFormAjax" method="post" action="">
                <div class="modal-header head-brdr">
                    <h4 class="modal-title app-r">Appointment Details</h4>
                    <span data-dismiss="modal"><i class="fa fa-close"></i></span> 
                </div>

                <div class="modal-body">
                    <div class="row invoice-info">
                        <div class="meet-img">
                            <img class="meet-pt" src="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>ico_meating_point.jpg" alt="Image" width="80px" height="80px"  />
                        </div>
                        <div class="col-sm-12">
                            <div class="addrs-blck">
                                    <!-- <b class="add-name">Meeting Location</b> -->
                                    <address>
                                        <?php echo $appDetail->appointAddress; ?>
                                    </address>
                            </div>
                             <div class="addrs-blck meetMeta">
                                    <!-- <b class="add-name">Meeting Time</b> -->
                                    <?php echo date('d M Y',strtotime($appDetail->appointDate)).', '.date('h:i A',strtotime($appDetail->appointTime));?>
                            </div>
                             <div class="addrs-blck meetMeta">
                                   <!--  <b class="add-name">Appointment Status</b> -->
                                    <?php 
                                  
                                    if($appDetail->isFinish == '0'){                            

                                        if ($appDetail->isCounterApply == 1) {

                                            if($appDetail->appointmentStatus == '5'){

                                                echo '<p class="defaultStatus">Counter rejected</p>';

                                            }else {
                                                
                                                if ($appDetail->counterStatus == 0) {

                                                    echo '<p class="waitingStatus">New appointment request</p>';                                        

                                                }elseif($appDetail->counterStatus == 1){

                                                    echo '<p class="waitingStatus">Payment is pending</p>';

                                                }elseif($appDetail->counterStatus == 2){

                                                    echo '<p class="defaultStatus">Counter rejected</p>';

                                                }elseif($appDetail->counterStatus == 3){

                                                    echo '<p class="confirmStatus">Appointment confirmed</p>';
                                                }
                                            }
                                            
                                        }elseif($appDetail->appointmentStatus == '1'){    // 1:Pending,2:Accept,3:Reject,4:Complete 

                                            echo '<p class="waitingStatus">Waiting for approval</p>';

                                        }elseif($appDetail->appointmentStatus == '2'){

                                            if ($appDetail->offerType == 1 ) { // 1:Paid,2:Free

                                                echo '<p class="waitingStatus">Payment is pending</p>';

                                            }else{

                                                echo '<p class="confirmStatus">Appointment confirmed</p>';
                                            }                

                                        }elseif($appDetail->appointmentStatus == '3'){ 

                                            echo '<p class="defaultStatus">Request rejected</p>';

                                        }elseif($appDetail->appointmentStatus == '4'){ 

                                            echo '<p class="confirmStatus">Appointment confirmed</p>';

                                        }elseif($appDetail->appointmentStatus == '5'){ 

                                            echo '<p class="defaultStatus">Request cancelled</p>';
                                        }
                                    } else{

                                        echo '<p class="defaultStatus">Finished appointment</p>';
                                    }


                                    ?>  
                            </div>
                        </div>
                        <div class="UserMainBox">
                        <div class="col-sm-6 invoice-col">
                            <b class="add-name">Appointment By</b>
                            <div class="userInfoBox">

                                <?php 
                                    if(!filter_var($appDetail->byImage, FILTER_VALIDATE_URL) === false) { 

                                        $byImage = $appDetail->byImage;

                                    }else if(!empty($appDetail->byImage)){ 

                                        $byImage = AWS_CDN_USER_THUMB_IMG.$appDetail->byImage;

                                    } else{

                                        $byImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                                    }
                                ?>

                                <div class="box-img">
                                    <img class="meet-pt img-circle" src="<?php echo $byImage ?>" alt="Image" width="50px" height="50px"  />
                                </div>
                                <p style=" padding-top: 8px;"><?php echo display_placeholder_text($appDetail->ByName); ?></p>
                            </div>
                            <address><i class="fa fa-map-marker"></i>&nbsp;&nbsp;
                                <?php echo display_placeholder_text($appDetail->ByAddress); ?>
                            </address>
                        </div>
                        <div class="col-sm-6 invoice-col">
                            <b class="add-name">Appointment For</b>
                            <div class="userInfoBox">
                                <?php 
                                    if(!filter_var($appDetail->forImage, FILTER_VALIDATE_URL) === false) { 

                                        $forImage = $appDetail->forImage;

                                    }else if(!empty($appDetail->forImage)){ 

                                        $forImage = AWS_CDN_USER_THUMB_IMG.$appDetail->forImage;

                                    } else{

                                        $forImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                                    }
                                ?>
                                <div class="box-img">
                                    <img class="meet-pt img-circle" src="<?php echo $forImage ?>" alt="Image" width="50px" height="50px"  />
                                </div>
                                <p style=" padding-top: 8px;"><?php echo display_placeholder_text($appDetail->ForName); ?></p>
                            </div>
                            <address><i class="fa fa-map-marker"></i>&nbsp;&nbsp;
                                <?php echo $appDetail->ForAddress; ?>
                            </address>
                        </div>
                        <!-- /.col -->
                        </div>
                    </div>
                   <!-- /.row -->
                </div>
            </form>
        </div> <!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
 