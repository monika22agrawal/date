<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<div class="wraper">
    <!--================Banner Area =================-->
    <section class="banner_area">
        <div class="container">
            <div class="banner_content">
                <h3 title="Appointment"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('update_appointment');?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
            </div>
        </div>
    </section>
    <!--================End Banner Area =================--> 
    <!--================Map Area =================-->
    <div class="container">
        <div class="contact_form_area">

            <form id="updateApp" method="post" action="<?php echo base_url('home/appointment/updateMyApp/').$this->uri->segment(4).'/';?>">

                <div class="row d-flex1">

                    <input type="hidden" id="lat" name="appointLatitude" value="">
                    <input type="hidden" id="long" name="appointLongitude" value="">
                    <input type="hidden" name="appointForId" value="<?php echo $appDetail->appointForId;?>">

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="mob-res">
                            <div class="search_widget loc-search text-center">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="address" value="<?php echo empty($appDetail->business_id) ? $appDetail->appointAddress : '';?>" placeholder="<?php echo lang('enter_meeting_location'); ?>" name="appointAddress" >
                                </div>
                            </div>
                        </div>

                        <section class="map_area Apoint-map mt-20">

                            <?php if($userDetail->gender == '1'){ 
                                    $mapIcon = MAP_ICON_MAIL;
                                }else{
                                    $mapIcon = MAP_USER_FEMAIL;
                                }                            

                                if(!filter_var($userDetail->imgName, FILTER_VALIDATE_URL) === false) { 
                                    $userImg = $userDetail->imgName;
                                }else if(!empty($userDetail->imgName)){ 
                                    $userImg = AWS_CDN_USER_THUMB_IMG.$userDetail->imgName;
                                } else{                    
                                    $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
                                }
                            ?>
                            
                            <div id="mapId" data-icon="<?php echo $mapIcon;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='map_img1'><img style='border-radius: 50%;height: 47px;width: 47px;' src='<?php echo $userImg;?>'></div> <div class='infoCnt'><a href='<?php echo base_url()."home/user/userDetail/".$this->uri->segment(4).'/'?>' class='map_nme2'><?php echo $userDetail->fullName;?></a><div class='map_add3'><?php echo $userDetail->address;?></div></div></div>" data-name="<?php echo $userDetail->fullName;?>" data-address="<?php echo $userDetail->address;?>" data-lat="<?php echo $userDetail->latitude;?>" data-long="<?php echo $userDetail->longitude;?>" class="map-section map-sec" frameborder="0" style="border:0; min-height: 360px;" allowfullscreen>
                            </div>

                        </section>
                        <section class="rest-slder-sec mt-20">
                            <div class="owl-carousel owl-theme rest-slider">

                                <?php $cls=0; $myBiz=array(); if(!empty($bizList)){ 

                                foreach ($bizList as $key => $value) { 

                                    $divSelect = ''; 
                                    if($value->businessId == $appDetail->business_id){

                                        $cls=1;
                                        $divSelect='first';
                                        $myBiz = $value;

                                    }

                                    if(!empty($value->businessImage)){

                                        $bizImg = AWS_CDN_BIZ_THUMB_IMG.$value->businessImage;

                                    } else{

                                        $bizImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
                                    }

                                ?>

                                <div onclick="showImage(this,'<?php echo $value->businessId ?>');" class="slider-section item <?php echo $divSelect;?>" id="bizId<?php echo $value->businessId;?>" data-img="<?php echo $bizImg;?>" data-name="<?php echo $value->businessName;?>" data-dis="<?php echo round($value->distance).' KM';?>" data-add="<?php echo $value->businessAddress;?>" data-lat="<?php echo $value->businesslat;?>" data-long="<?php echo $value->businesslong;?>">
                                    <a href="javascript:void(0);">
                                        <img src="<?php echo $bizImg;?>">
                                        <div class="rest-name">
                                            <h4><?php echo $value->businessName;?></h4>
                                            <p><?php echo round($value->distance).' KM';?></p>
                                        </div>
                                    </a>
                                </div>

                                <?php } } ?>                  
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="frm-rgt-sec">
                            <form class="text-left frm-rgt">
                                <div class="form-group">
                                    <div class="appiomUserInfo">

                                        <div class="media">
                                            <div class="media-left">
                                                <a>
                                                    <img src="<?php echo $userImg;?>">
                                                </a>
                                            </div>
                                            <div class="media-body pt-20">
                                                <h4 class="media-heading"><a><?php echo ucfirst($userDetail->fullName);?></a></h4>
                                                <p class="str-sec-pra"><?php echo !empty(($userDetail->work) && ($userDetail->work != 'NA')) ? ucfirst($userDetail->work) : '';?>
                                                    <!-- <span class="fa fa-star"></span>
                                                    <span class="fa fa-star"></span>
                                                    <span class="fa fa-star"></span>
                                                    <span class="fa fa-star"></span>
                                                    <span class="fa fa-star"></span> -->
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $date = $appDetail->appointDate.' '.$appDetail->appointTime;?>
                                <div class="form-group regfld">
                                    <div class="datepicker">
                                        <input type="text" class="form-control" placeholder="<?php echo lang('enter_date_time');?>" id='datetimepicker4' name="appointDate" value="<?php echo date('Y-m-d h:i A',strtotime($date)); ?>" required="">
                                        <span class="dte-icn">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>

                                <?php if($userDetail->appointmentType == 1){ ?>

                                    <div class="form-group regfld">
                                        <input type="text" min=1 maxlength=6 class="form-control" onkeypress="return isNumberKey(event);" placeholder="<?php echo lang('offer_price_palce');?>" name="offerPrice" value="<?php echo !empty($appDetail->offerPrice) ? $appDetail->offerPrice : '';?>" required="">
                                    </div>

                                    <input type="hidden" name="offerType" value="1">

                                <?php }else { ?> 

                                    <input type="hidden" name="offerType" value="2">

                                <?php } ?>

                                <div class="form-group">
                                    <div class="appiomnt_info mt-30">
                                        <!-- to set selected business or address -->
                                        <div id="appMeet" class="media">
                                           <div class="media-body"><p class="media-heading"> <i class="fa fa-map-marker"></i> <?php echo $appDetail->appointAddress; ?></p></div>
                                        </div>
                                    </div>
                                </div>

                                <?php if(empty($appDetail->business_id)){ ?>

                                    <input id="setHAdd" type="hidden" name="bizAdd" value="<?php echo $appDetail->appointAddress; ?>">

                                    <input id="setHLat" type="hidden" name="bizLat" value="<?php echo $appDetail->appointLatitude; ?>">

                                    <input id="setHLong" type="hidden" name="bizLong" value="<?php echo $appDetail->appointLongitude; ?>">

                                <?php }else{ ?>

                                    <input id="setHAdd" type="hidden" name="bizAdd" value="<?php echo !empty($myBiz->businessAddress) ? $myBiz->businessAddress : '';?>">

                                    <input id="setHLat" type="hidden" name="bizLat" value="<?php echo !empty($myBiz->businesslat) ? $myBiz->businesslat : '';?>">

                                    <input id="setHLong" type="hidden" name="bizLong" value="<?php echo !empty($myBiz->businesslong) ? $myBiz->businesslong : '';?>">

                                <?php } ?>

                                <input id="setHBizId" type="hidden" name="bizId" value="<?php echo !empty($myBiz->businessId) ? $myBiz->businessId : '';?>">

                                <a href="javascript:void(0);">
                                    <div class="text-right">
                                        <button type="button" id="makeApp" class="btn form-control login_btn"><?php echo lang('update');?></button>
                                    </div>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </form> 
        </div>
    </div>
    <!--================End Map Area =================-->
</div>
<script type="text/javascript">

    function showImage(e,id) {

        $("#address").val('');   
        $('.slider-section').removeClass('image-active');
        $(e).addClass('image-active');

        var img = $('#bizId'+id).data('img');
        var name = $('#bizId'+id).data('name');
        var dis = $('#bizId'+id).data('dis');
        var add = $('#bizId'+id).data('add');
        var lat = $('#bizId'+id).data('lat');
        var long = $('#bizId'+id).data('long');
       
        $("#appMeet").html('<div class="media-left"><img id="setBizImg" src="'+img+'"></div><div class="media-body"><h4 id="setBizName" class="media-heading">'+name+'</h4><p id="setBizAdd">'+add+'</p><p id="setBizDis">'+dis+'</p></div>');

        $('#setBizName').text(name);
        $('#setBizImg').attr('src', img);
        $('#setHAdd').val(add);
        $('#setHBizId').val(id);
        $('#setHLat').val(lat);
        $('#setHLong').val(long);
    }

    function initMap() {

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
            animation: google.maps.Animation.DROP,
            title: 'Uluru (Ayers Rock)'
        });

        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
    }
    
    //$(window).load(function() {

        initMap();
    //});

    $(document).ready(function(){

        $('.first').click();
        $('.first').addClass('image-active');
        var defDate = new Date();
        //defDate = new Date(defDate.getFullYear()+"-"+(defDate.getMonth()+1)+"-"+(defDate.getDate()));
        // you can do 00 or 30 min logic here
        defDate.setHours(defDate.getHours() + 1);
        
        $('#datetimepicker4').datetimepicker({
            minDate: defDate,
            showClose:true,
            useCurrent:false,
            //defaultDate:'<?php //echo date('Y-m-d h:i A',strtotime($date)); ?>',
            format: 'YYYY-MM-DD h:mm A'
        });
         
        $("#makeApp").click(function(){

            var add = $('#setHAdd').val();

            if(add == '' || typeof add === "undefined"){

                toastr.error('Please select address.');
                return false;
            }

            var form = $("#updateApp");

            form.validate({

                rules: {
                    appointDate : {
                        required: true             
                    },
                    offerPrice : {
                        required: true             
                    }                
                },
                messages:{
                    appointDate : {
                        required: "Please select appointment date and time."
                    },
                    offerPrice : {
                        required: "Please enter offer price."
                    }
                }
            });

            if (form.valid() === true){

                $("#makeApp").hide();
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
                             
                        if (data.status == 2){ 
                            
                            toastr.success(data.msg);

                            window.setTimeout(function () {
                                window.location.href = data.url;
                            }, 200);

                        }else if(data.status == 1) {

                            toastr.error(data.msg);

                        }else if(data.status == 3) {

                            toastr.error(data.msg);
                               
                        }else if(data.status == -1) {
                            
                            toastr.error(data.msg);
                            window.setTimeout(function () {
                                window.location.href = data.url;
                            }, 200);

                        }else{

                            toastr.error(data.msg);                        
                        }
                        $("#makeApp").show(); 
                    },

                    error:function (){
                        hide_loader();
                        toastr.error('Failed! Please try again');
                        $("#makeApp").show();
                    }
                });
            }        
        });
    });
</script>