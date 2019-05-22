<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        User's Detail
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo site_url('admin/users/userList');?>">Users</a></li>
        <li class="active">Profile</li>
    </ol>
    <button type="button" onclick="window.history.back();" class="btn bg-red btn-flat margin pull-right">Back</button>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile m-t-40">

                    <?php

                        if(!filter_var($detail->imgName, FILTER_VALIDATE_URL) === false) { 

                            $profileImage = $detail->imgName;

                        }else if(!empty($detail->imgName)){

                            $profileImage = AWS_CDN_USER_THUMB_IMG.$detail->imgName;

                        } else{

                            $profileImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                        }

                    ?>

                    <img class="profile-user-img img-responsive img-circle" src="<?php echo $profileImage;  ?>" alt="User profile picture">
                    
                </div>
                <h3 class="profile-username text-center"><?php echo display_placeholder_text($detail->fullName).' ('.display_placeholder_text($detail->age).')'; ?></h3>
                <!-- /.box-body -->
                <div class="subscrtn-imge text-center">
                    <ul>
                        <?php if($detail->mapPayment == 1){ ?>
                            <li><a title="View On Map"><img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG; ?>payment.png"/></a></li>
                        <?php }
                        if($detail->showTopPayment == 1){?>
                            <li><a title="Top User List"><img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG; ?>top_user_list.png"/></a></li>
                        <?php }                        
                        if($detail->bizSubscriptionStatus == 1){ ?>
                            <li><a title="Business Promotion"><img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG; ?>profits.png"/></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="clearfix"></div>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Friends</b> <a class="pull-right"><?php echo $detail->totalFriends; ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Likes</b> <a class="pull-right"><?php echo $detail->totalLikes; ?></a>
                    </li>
                    <li class="list-group-item">
                        <b>Visits</b> <a class="pull-right"><?php echo $detail->totalVisits; ?></a>
                    </li>
                </ul>
            </div>
            <!-- /.box -->
            <!-- About Me Box -->
            <div class="box box-primary p-0">
                <div class="box-header with-border p-10">
                    <h3 class="box-title">About User</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <strong><i class="fa fa-user margin-r-5"></i>Gender</strong>
                    <p><?php if($detail->gender == 1){ echo 'Male'; }elseif($detail->gender == 2){ echo 'Female'; }elseif($detail->gender == 3){ echo 'Transgender'; }else{ echo 'NA';} ?></p>
                    <hr>
                    <strong><i class="fa fa-map-marker margin-r-5"></i>Address</strong>
                    <p><?php echo display_placeholder_text($detail->address); ?></p>
                    <hr>
                    <strong><i class="fa fa-briefcase margin-r-5"></i>Work</strong>
                    <p><?php echo display_placeholder_text($detail->work) ?></p>
                    <hr>
                    <strong><i class="fa fa-graduation-cap margin-r-5"></i>Education</strong>
                    <p><span class="label label-info"><?php echo display_placeholder_text($detail->education); ?></span></p>
                    <hr>
                    <?php $colors = ['danger','warning','primary','success','info','danger','primary','warning','danger','success','info','primary','warning','success','info']; ?>
                    <strong><i class="fa fa-language margin-r-5"></i>I Speak</strong>
                    <p>
                        <?php if(!empty($detail->language)) { 
                            $lang = explode(",",$detail->language); 
                            foreach ($lang as $k => $v) { ?>
                                <span class="label label-<?php echo $colors[$k]; ?>"><?php echo $v; ?></span>
                        <?php } 
                        }else{
                            echo 'NA';
                        }  ?>
                    </p>
                    <hr>
                    <strong><i class="fa fa-arrows-v margin-r-5"></i>Height</strong>
                    <?php $colors = ['info','warning','primary','success','info','danger','primary','warning','danger','success','info','primary','warning','success','info']; ?>
                    <p>
                    <p><?php echo display_placeholder_text($detail->height); ?></p>
                    </p>
                    <hr>
                    <strong><i class="fa fa-sitemap valyes margin-r-5"></i>Weight</strong>
                    <p><?php echo display_placeholder_text($detail->weight); ?></p>
                    <hr>
                    <strong><i class="fa fa-user margin-r-5"></i>Relationship</strong>
                    <p><?php echo display_placeholder_text($detail->relationship); ?></p>
                    <hr>
                    <strong><i class="fa fa-mobile margin-r-5"></i>Contact Number</strong>
                    <p><?php echo ($detail->countryCode) ? display_placeholder_text($detail->countryCode.'-'.$detail->contactNo) : 'NA'; ?></p>
                    <hr>
                    <strong><i class="fa fa-file-text-o margin-r-5"></i>About</strong>
                    <p><?php echo display_placeholder_text($detail->about); ?></p>
                    <input type="hidden" id="userId" name="" value="<?php echo $userId; ?>">
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.col -->
        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active" ><a href="#activity" data-toggle="tab">Favourites</a></li>
                    <li ><a href="#reviews" data-toggle="tab">My Events</a></li>
                    <li ><a href="#recommends" data-toggle="tab">Appointments</a></li>
                    <li ><a href="#friends" data-toggle="tab">My Friends</a></li>
                    <li ><a href="#payDetail" data-toggle="tab">Payment Detail</a></li>
                    <li ><a href="#usrImage" data-toggle="tab">Media</a></li>
                    <li ><a href="#idProof" data-toggle="tab">ID Verification</a></li>
                    <li ><a href="#bizDetail" data-toggle="tab">Business Detail</a></li>
                </ul>
                <div class="tab-content usr-lst-block">
                    <div class="active tab-pane" id="activity">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <table class="table table-striped" id="favourit_list" style="margin-left:5px;margin-right: 5px;width:100%;">
                                        <thead>
                                            <th>S.no</th>
                                            <th>Full Name</th>
                                            <th>Work</th>
                                            <th>Profile Image</th>
                                            <th style="width: 12%">Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <!--  user review -->
                    <div class="tab-pane" id="reviews">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <table class="table table-striped" id="event_list" style="margin-left:5px;margin-right: 5px;width:100%;">
                                        <thead>
                                            <th>S.no</th>
                                            <th>Event Name</th>
                                            <th>Event Place</th>
                                            <th>Event Type</th>
                                            <th style="width: 12%">Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <div class="tab-pane" id="recommends">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <div id="appList"></div>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <div class="tab-pane" id="friends">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <table class="table table-striped" id="friend_list" style="margin-left:5px;margin-right: 5px;width:100%;">
                                        <thead>
                                            <th>S.no</th>
                                            <th>Name</th>
                                            <th>Work</th>
                                            <th>Image</th>
                                            <th style="width: 12%">Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <div class="tab-pane" id="payDetail">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <table class="table table-striped" id="Payment_list" style="margin-left:5px;margin-right: 5px;width:100%;">
                                        <thead>
                                            <th>S.No.</th>
                                            <th>Name</th>
                                            <th>Transaction Id</th>
                                            <th>Amount</th>
                                            <th>Payment Status</th>
                                            <th>Payment Type</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <div class="tab-pane" id="usrImage">
                        <div class="box-left">
                            <div class="tab-content ">
                                <div class="">
                                    <div id="userimg"></div>
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                    </div>
                    <div class="tab-pane" id="idProof">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="body">
                                    <div id="aniimated-idproof" class="list-unstyled row clearfix galleryList">                                      
                                        <div class="col-lg-12" >                                            
                                            
                                            <?php if(!empty($detail->idWithHand)){?>
                                            <a href="<?php echo AWS_CDN_IDPROOF_IMG_PATH.$detail->idWithHand; ?>">
                                                <img class="img-responsive thumbnail resize mb-0" src="<?php echo AWS_CDN_IDPROOF_THUMB_IMG.$detail->idWithHand; ?>">
                                            </a>
                                               <!--  <a href="#" onclick="window.open('<?php //echo base_url().'../uploads/idProof/large/'.$detail->idWithHand; ?>')" class="btn btn-theme float-right bg-aqua" title="View Document"><i class="fa fa-eye"></i></a> -->
                                                
                                            <?php } else{?>
                                                <center><span><font>No Identity Available !</font></span></center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <?php if(!empty($detail->idWithHand)){ if($detail->isVerifiedId == 2){ ?>

                            <h6 class="dsapprve-notify"><i class="fa fa-remove"></i> Disapprove</h6>                            

                        <?php } elseif($detail->isVerifiedId == 1){ ?>

                            <h6 class="apprve-notify"><i class="fa fa-check"></i> Approved</h6>                            

                        <?php } } ?>

                        <?php if(!empty($detail->idWithHand)){ if($detail->isVerifiedId == 1){?>

                            <button style="cursor: not-allowed;" class="btn btn-success btns-new apprve-btn" title="Approved">Approved</button>
                        <?php } else{ ?>
                            <a href="<?php echo base_url()."admin/users/indentityProofStatus/1/".$detail->userId; ?>"  class="btn btn-success btns-new apprve-btn" title="Verify">Approve</a>
                        <?php } ?>

                        <?php if($detail->isVerifiedId == 2){?>
                            <button style="cursor: not-allowed;" class="btn btn-danger btns-new dsaprve-btn" title="Disapproved">Disapproved</button>
                        <?php } else{ ?>
                            <a href="<?php echo base_url()."admin/users/indentityProofStatus/2/".$detail->userId; ?>"  class="btn btn-danger btns-new dsaprve-btn" title="Disapprove">Disapprove</a>
                        <?php } } ?>

                    </div>
                    <div class="tab-pane" id="bizDetail">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="body">
                                    <div id="aniimated-biz" class="list-unstyled row clearfix galleryList">                                      
                                        <div class="col-lg-12" >
                                            
                                            <?php if(!empty($bizDetail->businessImage)){?>
                                            <a href="<?php echo $bizDetail->businessImage; ?>">
                                                <img class="img-responsive thumbnail resize mb-0" src="<?php echo $bizDetail->businessImage; ?>">
                                            </a>
                                            <p class="bsnes-nme mt-10"><?php echo $bizDetail->businessName; ?></p>
                                            <p class="bsness-addrss"><span class="fa fa-map-marker"></span><?php echo $bizDetail->businessAddress; ?></p>
                                                
                                            <?php } else{?>
                                                <center><span><font>No Business Available !</font></span></center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <?php if(!empty($bizDetail->businessImage)){ if(!empty($bizDetail->bizSubscriptionId)){  ?>

                            <button style="cursor: default;" class="btn btn-success" title="Subscribed">Subscribed</button>

                        <?php  } else{ ?>

                            <button style="cursor: default;" class="btn btn-success btns-new dsaprve-btn" title="Not Subscribed">Not Subscribed</button>

                        <?php } } ?>

                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- for payment detail popup -->
<div id="form-modal-box"></div>
<script type="text/javascript">

    function show_loader(){
        $('#tl_admin_loader').show();
    }
    
    function hide_loader(){
        $('#tl_admin_loader').hide();
    }
    
    var off=0;
    var lim = 4;
    appoinList();
    
    function appoinList(){
    
        var base_url = $('#tl_admin_main_body').attr('data-base-url');
        
        $.ajax({
            url: base_url+"admin/users/appointment_list_ajax",
            type: "POST",
            data:{user_id: $('#userId').val(),offset:off,limit:lim},              
            cache: false,   
            beforeSend: function() {
            
                show_loader()
            },                          
            success: function(data){ 
                
                hide_loader()
                if(off==0){
                   
                    $('#appList').html(data);
    
                }else{
                    
                    $("#memberData").append(data);
                }
    
                var totalCounts = $('#totalCountss').val(); 
                var resultCounts = $('div[id=appointment]').length; 
                if(totalCounts>resultCounts){
    
                    $('div#loadMember').show();
    
                }else{
    
                    $('div#loadMember').hide();
                    
                }   
                off += lim;
            }
        });
    }
    
    $(document).on('click',"#btnMem", function(event){ 
    
        var totalCounts = $('#totalCountss').val(); 
        var resultCounts = $('div[id=appointment]').length;
        if(totalCounts>resultCounts){
            
            $('#btnMem').show();
    
        }else{
    
            $('#btnMem').hide('fast');
            
        }  
        appoinList();
    });
    
</script>