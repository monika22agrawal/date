<?php //pr($payment->paymentType);?>
<!-- Modal -->
<div class="modal fade" id="commonModals" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" role="form" id="editFormAjax" method="post" action="">
                <div class="modal-header head-brdr">
                    <h4 class="modal-title app-r">Payment Details</h4>
                    <span data-dismiss="modal"><i class="fa fa-close" style="cursor:pointer;"></i></span> 
                </div>
                <div class="modal-body">
                    <div class="row invoice-info">
                        <center>
                            <div class="col-sm-12 invoice-col">
                                <?php
                                
                                if(!filter_var($payment->image, FILTER_VALIDATE_URL) === false) { 

                                    $imgPath = $payment->image;

                                }else if(!empty($payment->image)){ 

                                    $imgPath = AWS_CDN_USER_THUMB_IMG.$payment->image;

                                } else{                   

                                    $imgPath = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }
                                ?>
                                <center><img src="<?php echo $imgPath; ?>" class="meet-pt img-circle" width="50px" height="50px" style="box-shadow: 5px 4px 5px #888888;color: #d6d0d0;"></center>
                            </div>
                        </center>
                        <center>
                            <div class="col-sm-12 invoice-col">
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php echo display_placeholder_text(ucfirst($payment->fullName)); ?></p>
                                    </center>
                                </div>
                            </div>
                        </center>
                        <div class="UserMainBox">
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Transaction id</b></center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php echo display_placeholder_text($payment->transactionId); ?></p>
                                    </center>
                                </div>
                            </div>
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Charge id</b> </center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php echo display_placeholder_text($payment->chargeId); ?></p>
                                    </center>
                                </div>
                            </div>
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Amount</b> </center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style="">$<?php echo display_placeholder_text($payment->amount); ?></p>
                                    </center>
                                </div>
                            </div>
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Payment type</b> </center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php if($payment->paymentType == 1){
                                                echo  'Top user';
                                            }elseif($payment->paymentType == 2){
                                                echo 'View map';
                                            }elseif($payment->paymentType == 3){
                                                echo 'Event Join Payment';
                                            }elseif($payment->paymentType == 4){
                                                echo 'Companion Payment';
                                            }elseif($payment->paymentType == 0){
                                                echo 'Monthly Subscription';
                                            }elseif($payment->paymentType == 6){
                                                echo 'Business Subscription';
                                            }elseif($payment->paymentType == 7){
                                                echo 'Appointment Payment';
                                            }else{
                                                echo 'No Payment';
                                            }; ?></p>
                                    </center>
                                </div>
                            </div>
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Payment status</b> </center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php if($payment->paymentStatus == 'succeeded'){echo "Success";}elseif ($payment->paymentStatus == 'active') {
                                            echo "Subscribed";
                                            } else{ echo "Pending";} ?></p>
                                    </center>
                                </div>
                            </div>
                            <div class="col-sm-6 invoice-col">
                                <center><b class="add-name">Payment Date</b> </center>
                                <div class="userInfoBox" style="margin-top: 0px !important;">
                                    <center>
                                        <p style=""><?php echo date('d M, Y', strtotime($payment->crd)); ?></p>
                                    </center>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- Modal -->