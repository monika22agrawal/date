<?php 
    $frontend_assets =  base_url().'frontend_asset/';
    //pr($myEventDetail);
?>
<div class="blnk-spce"></div>
<div class="wraper">
    <section class="shop_area product_details_main blog_grid_area evnt-dtl">
        <input type="hidden" id="eventId" value="<?php echo $myEventDetail->eventId; ?>" name="">
        <div class="container">
            <div class="row">                    
                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12"> 
                    <div class="evnt-det-lft-sec-prt"> 
                        <div class="blog_grid_img">                   
                            <div id="sync1" class="owl-carousel owl-theme">

                                <?php foreach ($myEventDetail->eventImage as $key => $value) {

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
                            <?php foreach ($myEventDetail->eventImage as $key => $value) {

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
                            <h4>Event Location</h4>
                        </div>
                        <div class="map-adress-add">
                            <div width="100%" frameborder="0" style="border:0;" allowfullscreen id="mapId" data-icon="<?php echo MAP_ICON_MAIL;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='infoCnt'><div class='map_add3'><?php echo $myEventDetail->eventPlace;?></div></div></div>" data-name="<?php echo $myEventDetail->fullName;?>" data-address="<?php echo $myEventDetail->eventPlace;?>" data-lat="<?php echo $myEventDetail->eventLatitude;?>" data-long="<?php echo $myEventDetail->eventLongitude;?>" class="map-section map-sec map-sec-2">
                            </div>
                            <div class="addres-blck">
                                <p><?php echo $myEventDetail->eventPlace; ?></p>
                            </div>
                        </div> 
                    </div>                      
                </div>
                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                    <div class="evnt-det-descrptn-sec apoin-othr-text ">                      
                        <div class="descrptn-sec">
                            <h2><?php echo ucfirst($myEventDetail->eventName);?></h2>
                        </div> 
                        <div class="dsply-block">
                            <div class="dsply-blck-lft">
                                <div class="our_txt_sec mt-15">
                                    <div class="txt_div">
                                        <?php if($myEventDetail->privacy == 'Private'){
                                            $cls = 'lock';
                                        }else{
                                            $cls = 'users';
                                        }
                                        ?>
                                        <h5 href="javascript:void(0);"><i class="fa fa-<?php echo $cls;?>"></i><span> <?php echo $myEventDetail->privacy; ?> </span></h5>
                                    </div>
                                    <div class="txt_div">
                                        <h5 href="javascript:void(0);"><i class="fa fa-money"></i> <?php echo $myEventDetail->payment; ?> <?php echo ' '.$myEventDetail->currencySymbol.''.$myEventDetail->eventAmount; ?></h5>
                                    </div>
                                </div> 
                            </div>
                        </div> 
                        <div class="clearfix"></div>                       
                        <div class="psted-by-sec flex-div">
                            <?php 

                                if(!filter_var($myEventDetail->profileImageName, FILTER_VALIDATE_URL) === false) { 
                                    $userImg = $myEventDetail->profileImageName;
                                }else if(!empty($myEventDetail->profileImageName)){ 
                                    $userImg = AWS_CDN_USER_THUMB_IMG.$myEventDetail->profileImageName;
                                } else{                    
                                    $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }

                            ?>
                            <img src="<?php echo $userImg;?>" /><span>By - <b><?php echo ucfirst($myEventDetail->fullName);?></b> - Administrator</span>
                        </div>
                        <div class="dsply-inlne-usres mt-15">
                            <div class="usr-blck-lst frst-evnt-wdth">
                                <h5>Users limit to Join event</h5>
                                <p><?php echo $myEventDetail->userLimit; ?></p>
                            </div>
                            <div class="usr-blck-lst ml-15">
                                <h5>Who can Join event</h5>
                                <p><?php echo $myEventDetail->eventUserType; ?></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="event-short-info mt-25">
                            <h4 class="course-title">Event Date and Time</h4>
                            <ul>
                                <li class="brdr-list" data-toggle="tooltip" data-placement="top" title="" data-original-title="Event Start Date">
                                    <b>From:</b> <?php echo date('d M Y, h:i A',strtotime($myEventDetail->eventStartDate));?>
                                </li>
                                <li data-toggle="tooltip" data-placement="top" title="" data-original-title="Event End Date">
                                    <b>To:</b> <?php echo date('d M Y, h:i A',strtotime($myEventDetail->eventEndDate));?>
                                </li>
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                        <div class="containerr mt-35">
                            <a href="<?php echo base_url('home/login');?>" class="btn form-control login_btn ml-15" >Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    function initMapEventPlace() {

    var name = $('#mapId').data('name');
    var address = $('#mapId').data('address');
    var latitude = $('#mapId').data('lat');
    var longitude = $('#mapId').data('long');
    var icon = $('#mapId').data('icon');
    var img = $('#mapId').data('img');

    var map = new google.maps.Map(document.getElementById('mapId'), {
        zoom: 14,
        center:  new google.maps.LatLng(latitude, longitude)
    });

    var contentString = img;

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(latitude, longitude),
        map: map,
        icon: icon,
        animation: google.maps.Animation.DROP
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}
    
//$(window).load(function() {

    initMapEventPlace();
//});
    
</script>
