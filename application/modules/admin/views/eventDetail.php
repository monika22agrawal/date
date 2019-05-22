<?php
    $backUrl = $this->session->userdata('redirectUrl');
    if($backUrl){
        $url = $backUrl;
    }else{
        $url = base_url('admin/users/eventList');
    }
?>
<link href="<?php echo AWS_CDN_BACK_DIST_CSS ?>owl.carousel.min.css" rel="stylesheet">
  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <a href="<?php echo $url; ?>" class="btn btn-flat margin pull-right">Back</a>
    </section>
    <!-- Main content -->
    <section class="content con-pd">
        <!-- Default box -->
        <div class="">
            <div class="box-header evnt-head">
                <h3 class="box-title"> Event Details</h3>
            </div>
            <div class="box-body">
                <div class="col-md-6">
                     <div class="card">
                        <div class="evnt-det-lft-sec-prt"> 
                            <div id="sync1" class="owl-carousel owl-theme">

                                <?php foreach ($eventDetail['detail']->eventImage as $key => $value) {

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
                            <?php foreach ($eventDetail['detail']->eventImage as $key => $value) {

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
                    </div> 
                    <!-- <div class="card box-body box-profile mrgn-top-20"> -->
                    <div class="card box-body box-profile">
                            <div class="eveN">
                                <p><?php echo display_placeholder_text(ucfirst($eventDetail['detail']->eventName));  ?></p>
                            </div>

                            <?php 

                                if(!filter_var($eventDetail['detail']->profileImageName, FILTER_VALIDATE_URL) === false) { 

                                    $profileImage = $eventDetail['detail']->profileImageName;

                                }else if(!empty($eventDetail['detail']->profileImageName)) { 

                                    $profileImage = AWS_CDN_USER_THUMB_IMG.$eventDetail['detail']->profileImageName;

                                } else{

                                    $profileImage = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                            ?>

                            <img class="profile-user-img img-responsive img-circle usr-prImg" src="<?php echo $profileImage; ?>" alt="User profile picture" ">
                            <h3 class="profile-username text-center"><?php echo display_placeholder_text($eventDetail['detail']->fullName);  ?></h3>
                            <h3 class="profile-username text-center"><?php echo display_placeholder_text($eventDetail['detail']->privacy);  ?>  Event</h3>
                        </div>
                        
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="eventIcon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="box-body box-profile m-t-40">

                            <strong class="eveDtl">Event location</strong>
                            <p class="eveD"><?php echo display_placeholder_text($eventDetail['detail']->eventPlace) ?></p>
                            <hr>

                            <strong class="eveDtl">Event start date & time</strong>
                            <p class="eveD"><span><?php $start = date('d',strtotime($eventDetail['detail']->eventStartDate)); 
                                echo $start;
                                if($start == '1' || $start == '21' || $start == '31'){
                                    echo '<sup>st</sup>';
                                } elseif($start == '2' || $start == '22'){
                                    echo '<sup>nd</sup>';
                                }elseif($start == '3' || $start == '23'){
                                    echo '<sup>rd</sup>';
                                }else{
                                    echo '<sup>th</sup>';
                                }
                                ?>
                                </span> <?php echo date('M Y, h:i A',strtotime($eventDetail['detail']->eventStartDate));?>
                            </p>
                            <hr>

                            <strong class="eveDtl"> Event end date & time</strong>
                            <p class="eveD"><span><?php $start = date('d',strtotime($eventDetail['detail']->eventEndDate)); 
                                echo $start;
                                if($start == '1' || $start == '21' || $start == '31'){
                                    echo '<sup>st</sup>';
                                } elseif($start == '2' || $start == '22'){
                                    echo '<sup>nd</sup>';
                                }elseif($start == '3' || $start == '23'){
                                    echo '<sup>rd</sup>';
                                }else{
                                    echo '<sup>th</sup>';
                                }
                                ?>
                                </span> <?php echo date('M Y, h:i A',strtotime($eventDetail['detail']->eventEndDate));?>
                            </p>
                            <hr>

                            <strong class="eveDtl">Max. user limit to join event</strong>
                            <p class="eveD"><?php echo display_placeholder_text($eventDetail['detail']->userLimit)?></p>
                            <hr>

                            <strong class="eveDtl">Who can join event</strong>
                            <p class="eveD"><?php echo display_placeholder_text($eventDetail['detail']->eventUserType)?></p>

                            <hr>
                            <strong class="eveDtl">Payment</strong>
                            <p class="eveD"><?php echo $eventDetail['detail']->payment; ?> <?php echo $eventDetail['detail']->currencySymbol.''.$eventDetail['detail']->eventAmount; ?></p>

                        </div>
                        <input type="hidden" id="eventId" name="" value="<?php echo $eventId; ?>">
                        <input type="hidden" id="eventOrgId" name="" value="<?php echo $this->uri->segment(5); ?>">
                        <input type="hidden" id="userId" name="" value="<?php echo $userId; ?>">
                    </div>
                    <div class="nav-tabs-custom mrgn-top-20">
                        <ul class="nav nav-tabs">
                            <li class="active" style="width:200px;"><a href="#activity" data-toggle="tab">Members joined event</a></li>
                            <li style="width:220px;"><a href="#reviews" data-toggle="tab">Invited members to join event</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="box-left">
                                    <div class="tab-content exe">
                                        <div id="joinedMember">

                                        </div>
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <!--  user review -->
                            <div class="tab-pane" id="reviews">
                                <div class="box-left">
                                    <div class="tab-content exe">
                                        <div id="inviteMember">

                                        </div>
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.box-body -->
                    <!-- /.box-body -->
                </div>
                <!-- /.row -->
            </div>
        </div>
        <!-- /.box -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script src="<?php echo AWS_CDN_BACK_DIST_JS ?>owl.carousel.min.js"></script>
<script>
    //event slider
$(document).ready(function() {

  var sync1 = $("#sync1");
  var sync2 = $("#sync2");
  var slidesPerPage = 5; //globaly define number of elements per page
  var syncedSecondary = true;

  sync1.owlCarousel({
    items : 1,
    slideSpeed : 2000,
    nav: false,
    autoplay: true,
    dots: false,
    loop: true,
    responsiveRefreshRate : 200,
    navText: ['<svg width="100%" height="100%" viewBox="0 0 11 20"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M9.554,1.001l-8.607,8.607l8.607,8.606"/></svg>','<svg width="100%" height="100%" viewBox="0 0 11 20" version="1.1"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M1.054,18.214l8.606,-8.606l-8.606,-8.607"/></svg>'],
  }).on('changed.owl.carousel', syncPosition);

  sync2
    .on('initialized.owl.carousel', function () {
      sync2.find(".owl-item").eq(0).addClass("current");
    })
    .owlCarousel({
    items : slidesPerPage,
    dots: false,
    nav: false,
    smartSpeed: 200,
    slideSpeed : 500,
    slideBy: slidesPerPage, //alternatively you can slide by 1, this way the active slide will stick to the first item in the second carousel
    responsiveRefreshRate : 100
  }).on('changed.owl.carousel', syncPosition2);

  function syncPosition(el) {
    //if you set loop to false, you have to restore this next line
    //var current = el.item.index;
    
    //if you disable loop you have to comment this block
    var count = el.item.count-1;
    var current = Math.round(el.item.index - (el.item.count/2) - .5);
    
    if(current < 0) {
      current = count;
    }
    if(current > count) {
      current = 0;
    }
    
    //end block

    sync2
      .find(".owl-item")
      .removeClass("current")
      .eq(current)
      .addClass("current");
    var onscreen = sync2.find('.owl-item.active').length - 1;
    var start = sync2.find('.owl-item.active').first().index();
    var end = sync2.find('.owl-item.active').last().index();
    
    if (current > end) {
      sync2.data('owl.carousel').to(current, 100, true);
    }
    if (current < start) {
      sync2.data('owl.carousel').to(current - onscreen, 100, true);
    }
  }
  
  function syncPosition2(el) {
    if(syncedSecondary) {
      var number = el.item.index;
      sync1.data('owl.carousel').to(number, 100, true);
    }
  }
  
  sync2.on("click", ".owl-item", function(e){
    e.preventDefault();
    var number = $(this).index();
    sync1.data('owl.carousel').to(number, 300, true);
  });
});
</script>

 