<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<script type="text/javascript">
    var eventName       = '<?php echo lang('event_name_req'); ?>',
        eNameMinLen     = '<?php echo lang('event_name_min_len'); ?>',
        eNameMaxLen     = '<?php echo lang('event_name_max_len'); ?>',
        eStartDate      = '<?php echo lang('event_start_date_time'); ?>',
        eEndDate        = '<?php echo lang('event_end_date_time'); ?>',
        ePrivacy        = '<?php echo lang('event_privacy_msg'); ?>',
        epayment        = '<?php echo lang('event_payment_msg'); ?>',
        eAmount         = '<?php echo lang('event_amt_msg'); ?>',
        eAmountVal      = '<?php echo lang('event_amt_msg_val'); ?>',
        eMaxUser        = '<?php echo lang('event_max_user_limit'); ?>',
        eMaxUserVal     = '<?php echo lang('event_user_limit_val'); ?>',
        eUserType       = '<?php echo lang('event_user_type_msg'); ?>',
        eEndTimeMsg     = '<?php echo lang('event_end_time_msg'); ?>',
        eMoreThanFiveImg   = '<?php echo lang('event_more_than_five'); ?>',
        eInvfrnd        = '<?php echo lang('event_invitation_friend'); ?>',
        eImgReq         = '<?php echo lang('event_img_req'); ?>';
        
</script>
<div class="wraper">
    <!--================Banner Area =================-->
    <section class="banner_area">
        <div class="container">
            <div class="banner_content">
                <h3 title="Event"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG; ?>banner/t-left-img.png" alt=""><?php echo lang('event_title'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG; ?>banner/t-right-img.png" alt=""></h3>
            </div>
        </div>
    </section>
    <!--================End Banner Area =================--> 
    <div class="container">
        <div class="contact_form_area crete-evnt-frm">
            <div class="row d-flex1">
                <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
                    <div class="frm-rgt-sec">
                        <div class="text-left">   
                            <div class="form-wizard form-header-classic form-body-classic" id="crete-evnt-form">
                                <div class="form-wizard-steps form-wizard-tolal-steps-4 progressbar">
                                    <div class="form-wizard-progress prgrs-line">
                                        <div class="form-wizard-progress-line" data-now-value="15" data-number-of-steps="4" style="">
                                        </div>
                                    </div>
                                    <div id="prog_active1" class="form-wizard-step active">
                                        <div class="form-wizard-step-icon prgrs-num">
                                            <span>1</span>
                                        </div>
                                    </div>
                                    <div id="prog_active2" class="form-wizard-step">
                                        <div class="form-wizard-step-icon prgrs-num">
                                            <span>2</span>
                                        </div>
                                    </div>
                                    <div id="prog_active3" class="form-wizard-step">
                                        <div class="form-wizard-step-icon prgrs-num">
                                            <span>3</span>
                                        </div>
                                    </div>
                                    <div class="form-wizard-step ">
                                        <div class="form-wizard-step-icon prgrs-num">
                                            <span>4</span>
                                        </div>
                                    </div>
                                </div>
                                <form autocomplete="off" id="eventForm" method="POST" action="<?php echo base_url('home/event/createEventSubmit');?>" >

                                    <input type="hidden" name="eventLatitude" value="" id="lat">
                                    <input type="hidden" name="eventLongitude" value="" id="long">
                                    <input type="hidden" name="bankAccountStatus" value="<?php echo $this->session->userdata('bankAccountStatus');?>" id="getAccStatus">

                                    <input type="hidden" name="orgenizerId" value="<?php echo $this->session->userdata('userId');?>">
                                    <input type="hidden" name="orgenizerImage" value="<?php echo $userDetail->profileImage;?>" >
                                    <input type="hidden" name="orgenizerName" value="<?php echo $userDetail->fullName;?>">
   
                                    <fieldset data-step="1" class="first">

                                        <div class="col-lg-12 col-md-12 col-sm-12 mb-20" style="text-align:center;">

                                            <div class="log_div bsnes-img text-center mt-30">

                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>placeholder-image.png" id="pImg">

                                                <div class="text-center upload_pic_in_album bsnes-cam">
                                                    <input onchange="$('#eventImage-err').html('');" accept="images/*" class="inputfile hideDiv" id="file-1" name="eventImage" onchange="document.getElementById('pImg').src = window.URL.createObjectURL(this.files[0])" style="display: none;" type="file">
                                                    <label for="file-1" class="upload_pic">
                                                        <span class="fa fa-camera"></span>
                                                    </label>
                                                </div>                                                
                                            </div>
                                            <div id="eventImage-err"></div>
                                        </div>
                                        <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                        <div class="form-group regfld mt-20">
                                            <input class="form-control" placeholder="<?php echo lang('event_name_place'); ?>" name="eventName" value="<?php echo set_value('eventName'); ?>" required="" type="text">
                                        </div>
                                        <div class="form-group regfld">
                                            <div class="datepicker">
                                                <input id="datetimepicker6" class="form-control" name="eventStartDate" required="" type="text" placeholder="<?php echo lang('event_start_datetime_place'); ?>">
                                                <span class="dte-icn">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group regfld">
                                            <div class="datepicker">
                                                <input id="datetimepicker7" class="form-control" name="eventEndDate" required="" type="text" placeholder="<?php echo lang('event_end_datetime_place'); ?>">
                                                <span class="dte-icn">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="">
                                            <div class="radio-btns-apoim">
                                                <div class="radio__container">
                                                    <p class="mb-10"><?php echo lang('who_join_event'); ?></p>
                                                    <!-- <div class="radio-inline">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" id="awesome-item-5" name="eventUserTypeG" type="checkbox" value="1">
                                                        <label class="radio__label gen_event" for="awesome-item-5"><?php echo lang('male_gender'); ?></label>
                                                    </div>
                                                    <div class="radio-inline">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" id="awesome-item-6" name="eventUserTypeG" type="checkbox" value="2">
                                                        <label class="radio__label gen_event" for="awesome-item-6"><?php echo lang('female_gender'); ?></label>
                                                    </div>
                                                    <div class="radio-inline">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" id="awesome-item-7" name="eventUserTypeG" type="checkbox" value="3">
                                                        <label class="radio__label gen_event" for="awesome-item-7"><?php echo lang('transgender_gender'); ?></label>
                                                    </div>
                                                    <div class="radio-inline">
                                                        <input class="radio" id="awesome-item-11" name="eventUserTypeG" type="checkbox" value="" onclick="checkUncheckAll()">
                                                        <label class="radio__label gen_event" for="awesome-item-11"><?php echo lang('all'); ?></label>
                                                    </div> -->
                                                    <div class="checkbox-inline keeplogin pl-0 mb-10">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" type="checkbox" name="eventUserTypeG" value="1" id="awesome-item-5">
                                                        <label for="awesome-item-5"><?php echo lang('male_gender'); ?></label>
                                                    </div>
                                                    <div class="checkbox-inline keeplogin mb-10">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" type="checkbox" name="eventUserTypeG" value="2" id="awesome-item-6">
                                                        <label for="awesome-item-6"><?php echo lang('female_gender'); ?></label>
                                                    </div>
                                                    <div class="checkbox-inline keeplogin mb-10">
                                                        <input onclick="updateSelection(this)" class="radio gender_checkbox" type="checkbox" name="eventUserTypeG" value="3" id="awesome-item-7">
                                                        <label for="awesome-item-7"><?php echo lang('transgender_gender'); ?></label>
                                                    </div>
                                                    <div class="checkbox-inline keeplogin mb-10">
                                                        <input onclick="checkUncheckAll()" type="checkbox" name="eventUserTypeG" value="" id="awesome-item-11">
                                                        <label for="awesome-item-11"><?php echo lang('all'); ?></label>
                                                    </div>

                                                </div>
                                                <div class="genderError"></div>
                                            </div>
                                        </div>

                                        <div class="form-wizard-buttons mt-30 text-right">
                                            <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?></button>
                                        </div>

                                    </fieldset>

                                    <fieldset data-step="2">

                                        <div class="mob-res mt-15">
                                            <div class="search_widget loc-search text-center">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="address" placeholder="<?php echo lang('event_location'); ?>" name="eventPlace">
                                                </div>
                                            </div>
                                        </div>
                                        <section class="map_area Apoint-map mt-20">
                                            <?php if($userDetail->gender == '1'){ 
                                                $mapIcon = MAP_ICON_MAIL;
                                            }else{
                                                $mapIcon = MAP_USER_FEMAIL;
                                            }
                                            ?>
                                            <div id="mapId" data-icon="<?php echo $mapIcon;?>" data-img="<div style='width:219px;' class='map_loca_name_row'><div class='map_img1'><img style='border-radius: 50%;height: 47px;width: 47px;' src='<?php echo $userDetail->profileImage;?>'></div> <div class='infoCnt'><a href='<?php echo base_url()."home/user/userDetail/".$this->uri->segment(4).'/'?>' class='map_nme2'><?php echo $userDetail->fullName;?></a><div class='map_add3'><?php echo $userDetail->address;?></div></div></div>" data-name="<?php echo $userDetail->fullName;?>" data-address="<?php echo $userDetail->address;?>" data-lat="<?php echo $userDetail->latitude;?>" data-long="<?php echo $userDetail->longitude;?>" class="map-section" width="100%" height="400px" frameborder="0" style="border:0; min-height: 400px;" allowfullscreen></div>
                                        </section>
                                        <section class="rest-slder-sec mt-20">
                                            <div class="owl-carousel owl-theme rest-slider">

                                                <?php $cls=0; $myBiz= array(); 

                                                if(!empty($bizList)){ 

                                                    foreach ($bizList as $key => $value) { 

                                                        $divSelect = ''; 
                                                        if($value->userId == $this->session->userdata('userId')){

                                                            $cls=1;
                                                            $divSelect='first';
                                                            $myBiz = $value;

                                                        }elseif ($value->userId == decoding($this->uri->segment(4))) {
                                                            
                                                            if(empty($myBiz)){
                                                               $myBiz = $value;  
                                                            }
                                                            if($cls==1){

                                                                $cls=0;
                                                                $divSelect='';
                                                            }else{
                                                                 
                                                                $cls=1;
                                                                $divSelect='first';
                                                            }
                                                        }

                                                        if(!empty($value->businessImage)){ 
                                                            $bizImg = AWS_CDN_BIZ_THUMB_IMG.$value->businessImage;
                                                        } else{                    
                                                            $bizImg = AWS_CDN_BIZ_PLACEHOLDER_IMG;
                                                        }
                                                ?>

                                                <div onclick="showImage(this,'<?php echo $value->businessId ?>');" class="slider-section item <?php echo $divSelect;?> <?php echo !empty($cls) ? 'image-active' : '';?>" id="bizId<?php echo $value->businessId;?>" data-img="<?php echo $bizImg;?>" data-name="<?php echo $value->businessName;?>" data-dis="<?php echo round($value->distance).' '.lang('km');?>" data-add="<?php echo $value->businessAddress;?>" data-lat="<?php echo $value->businesslat;?>" data-long="<?php echo $value->businesslong;?>">
                                                    <a href="javascript:void(0);">
                                                        <img src="<?php echo $bizImg;?>">
                                                        <div class="rest-name">
                                                            <h4><?php echo $value->businessName;?></h4>
                                                            <p><?php echo round($value->distance).' '.lang('km');?></p>
                                                        </div>
                                                    </a>
                                                </div>
                                                <?php } } ?>

                                            </div>
                                            
                                        </section>

                                        <input id="setHAdd" type="hidden" name="bizAdd" value="<?php echo !empty($myBiz->businessAddress) ? $myBiz->businessAddress : '';?>" required>

                                        <input id="setHLat" type="hidden" name="bizLat" value="<?php echo !empty($myBiz->businesslat) ? $myBiz->businesslat : '';?>">

                                        <input id="setHLong" type="hidden" name="bizLong" value="<?php echo !empty($myBiz->businesslong) ? $myBiz->businesslong : '';?>">

                                        <input id="setHBizId" type="hidden" name="bizId" value="<?php echo !empty($myBiz->businessId) ? $myBiz->businessId : '';?>">
                                        
                                        <div class="media" id="appMeet"></div>

                                        <div class="form-wizard-buttons mt-30 text-right">
                                            <input type="button" name="previous" id="switch-step-1" class="login_btn previous action-button-previous btn-previous" value="<?php echo lang('previous_step'); ?>"/>
                                            <button type="button" name="next" class="btn-next btn form-control login_btn btn_focs_whte" value="Next"><?php echo lang('next_process'); ?></button>
                                        </div>
                                    </fieldset>
                                    <fieldset data-step="3" id="last-step">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">    
                                                <div class="form-group regfld mt-15">
                                                    <input type="text" class="form-control" min="1" placeholder="<?php echo lang('event_user_limit');?>" name="userLimit" onkeypress="return isNumberKey1(event);" value="<?php echo set_value('userLimit'); ?>" required="" >
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="radio-btns-apoim">
                                                    <div class="radio__container">
                                                        <p><?php echo lang('event_privacy'); ?></p>
                                                        <div class="radio-inline">
                                                            <input class="radio" id="awesome-item-1" name="privacy" type="radio" value="1" checked>
                                                            <label class="radio__label" for="awesome-item-1"><?php echo lang('public_invitation'); ?></label>    
                                                        </div>
                                                        <div class="radio-inline">
                                                            <input class="radio" id="awesome-item-2" name="privacy" type="radio" value="2">
                                                            <label class="radio__label" for="awesome-item-2"><?php echo lang('private_invitation'); ?></label>    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="radio-btns-apoim">
                                                    <div class="radio__container">
                                                        <p><?php echo lang('event_payment'); ?></p>
                                                        <div class="radio-inline">
                                                            <input class="radio getPay" id="awesome-item-3" name="payment" type="radio" value="1" <?php echo $this->input->post('payment') == '1' ? ' checked="checked"' : '';?>>
                                                            <label class="radio__label" for="awesome-item-3"><?php echo lang('pain_event'); ?></label>    
                                                        </div>
                                                        <div class="radio-inline">
                                                            <input class="radio getPay" id="freeEvent" name="payment" type="radio" value="2" <?php echo $this->input->post('payment') == '2' ? ' checked="checked"' : '';?> checked="">
                                                            <label class="radio__label" for="freeEvent"><?php echo lang('free_event'); ?></label>    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 

                                            <div class="row showHide" id="cur">

                                                <?php $currencyName = json_decode(file_get_contents(APPPATH.'third_party/currency.json'));?>

                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 edt-otr-inf">

                                                     <div class="form-group">

                                                        <select class="form-control" name="currencySymbol" id="exampleFormControlSelect14" required title="<?php echo lang('required_currency'); ?>">

                                                            <option value=""><?php echo lang('select_currency'); ?></option>

                                                            <?php foreach($currencyName as $currency):?>

                                                                <option value="<?php echo $currency->code.','.$currency->symbol; ?>" <?php if( $currency->code.','.$currency->symbol == 'EUR,€'){ echo "selected='selected'";}?> ><?php echo $currency->name_plural.' ('.$currency->symbol.')'; ?></option>

                                                            <?php endforeach; ?>

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                    <div class="form-group regfld">
                                                        <input id="myInput" type="text" min="1" class="form-control" placeholder="<?php echo lang('event_amount'); ?>" value="<?php echo set_value('eventAmount'); ?>" name="eventAmount" required="" title="<?php echo lang('required_amount'); ?>">
                                                    </div>
                                                </div>

                                            </div>   

                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="tgle-grp-cht tgle-grp-cht1">
                                                    <p class="mb-15"><?php echo lang('event_group_chat'); ?></p>
                                                    <div class="tgle">
                                                        <div class="toggle-group">
                                                            <input name="groupChat" id="on-off-switch" checked="" tabindex="1" type="checkbox">
                                                            <label for="on-off-switch">
                                                            </label>
                                                        <div class="onoffswitch pull-right" aria-hidden="true">
                                                            <div class="onoffswitch-label">
                                                                <div class="onoffswitch-inner"></div>
                                                                <div class="onoffswitch-switch"></div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>  
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-wizard-buttons mt-30 text-right ">

                                                    <input type="button" name="previous" class="login_btn previous action-button-previous btn-previous getprev" value="<?php echo lang('previous_step'); ?>"/>

                                                    <button type="button" id='setEventVal' data-myval="0" name="next" class="btn-next btn form-control login_btn btn_focs_whte"><?php echo lang('next_process'); ?></button>

                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset data-step="4">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="uplod-imge mt-15">
                                                    <h2 class="crete-evnt-hed text-center" id="setEventName"></h2>
                                                    <p class="txt-prt-reg"><?php echo lang('event_img_title'); ?></p>
                                                    <div class="fde-line"></div>
                                                    <p class="text-center"><?php echo lang('event_img_max'); ?></p>
                                                    <div class="upload_secs">
                                                        <div class="upload_lft">
                                                            <div class="upld-imge">
                                                                <div class="image-upload-wrap">
                                                                    <input class="file-upload-input" type='file' onchange="addMoreEventImg(this)" id="newImgMy" name="image" accept="image/*" />
                                                                    <div class="drag-text">
                                                                        <img src="<?php echo AWS_CDN_FRONT_IMG;?>upload-to-cloud.png" />
                                                                    </div>
                                                                </div>
                                                                <div class="file-upload-content">
                                                                    <img class="file-upload-image" src="#" alt="your image" />
                                                                    <div class="image-title-wrap">
                                                                        <button type="button" onclick="removeUpload()" class="remove-image"><?php echo lang('event_img_remove'); ?><span class="image-title"><?php echo lang('event_upload_img'); ?></span></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="note-txt text-center"><?php echo lang('upload_max_img_size'); ?></p>
                                                    <div class="upload_secs text-center mt-10">
                                                        <div id="uploadedEvImgs">
                                                            <div class="upload_rht">
                                                                <img id="setEvImg" src="" />
                                                                <!-- <i class="fa fa-close"></i> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="form-wizard-buttons mt-30 text-right">      
                                                        <a onclick="eventImgUpdate()" id="setRedUrl" href="<?php echo base_url('home/event');?>" type="button" class="btn-submit btn form-control login_btn btn_focs_whte"><?php echo lang('done'); ?></a>
                                                        <!-- <span class="skp-anchr ml-20">
                                                            <a onclick="eventImgUpdate()" href="<?php //echo base_url('home/event');?>">Skip</a>
                                                        </span> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </fieldset>
                                   
                                    <a href="javascript:void(0);" style="display: none;" class="addEventSClass addEvent"><?php echo lang('event_invite_friend'); ?></a> 
                                    <input type="hidden" id="memberId" name="memberId" value="">
                                </form>                                   
                            </div>                             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start modal popup for invite friend's list -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('event_invite_member'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="flter-lst-invte-member">
                    <div class="row">
                        <div class="pl-15 pr-15">
                            <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                <div class="mob-res">
                                    <div class="search_widget loc-search text-center">
                                        <div class="input-group">
                                            <input class="form-control" id="searchName" placeholder="<?php echo lang('search_by_name'); ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pl-0">
                                <div class="flter-icn">
                                    <a href="javascript:void(0)"><img id="img-flter" src="<?php echo AWS_CDN_FRONT_IMG;?>filter.png" data-toggle="tooltip" title="<?php echo lang('event_more_filter');?>" /></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="list-flter-sec">
                                <div class="flter-sec-invite" id="filter-invite">

                                    <div class="form-group regfld mt-20">
                                        <input class="form-control" id="usraddress" placeholder="<?php echo lang('search_by_location'); ?>" name="searchLocation" type="text">
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
                                            <button type="button" onclick="allFriends(0,1);" class="btn form-control login_btn"><?php echo lang('apply_filter'); ?></button>
                                            <a href="javascript:void(0)" onclick="resetAll();"><?php echo lang('reset_filter'); ?></a>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="profile_list scroll-prt">
                                    <div id="all-friends"></div>
                                    <br>
                                    <div id='showLoader' class="show_loader clearfix" data-offset="0" data-isNext="1">                    
                                        <img src='<?php echo AWS_CDN_FRONT_IMG;?>Spinner-1s-80px.gif' alt=''>
                                    </div>
                                </div>
                                
                                
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <a href="javascript:void(0)" onclick="save()" class="btn form-control login_btn snd-invite btn_hvr_red"><?php echo lang('send_invitation'); ?></a>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End modal popup for invite friend's list -->

<!-- Start Modal popup for add bank account -->
<div class="modal fade" id="myModalCheckPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('add_acc_paid_event'); ?></h5>
                <button type="button" class="close makeFree" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>  
            <form id="add_band_acc" action="<?php echo base_url('home/payment/addBankAccount');?>" method="post">
                <div class="modal-body mdl-body">
                    <p class="para text-left mb-15"><?php echo lang('add_acc_paid_event_msg'); ?></p>
                    <div class="regfrm mdl-pad mt-20">
                        <div class="form-group regfld">

                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="<?php echo lang('first_name'); ?>" name="firstName" value="<?php echo set_value('firstName'); ?>" required="">
                            </div>

                            <div class="form-group">       
                                <input type="text" name="lastName" value="<?php echo set_value('lastName'); ?>" class="form-control" placeholder="<?php echo lang('last_name'); ?>">
                            </div>

                            <div class="form-group">
                                <input type="text" id="date" name="dob" value="<?php echo set_value('dob'); ?>" class="form-control" placeholder="<?php echo lang('date_of_birth'); ?>" readonly>
                            </div>

                            <!-- <div class="form-group">
                                <input type="text" onkeypress="return isNumberKey1(event);" name="routingNumber" value="<?php echo set_value('routingNumber'); ?>" class="form-control" placeholder="<?php echo lang('routing_number'); ?>">
                            </div> -->

                            <div class="form-group">    
                                <input type="text" onkeypress="return isNumberKey1(event);" name="accountNumber" value="<?php echo set_value('accountNumber'); ?>" class="form-control" placeholder="<?php echo lang('iban_number'); ?>">
                            </div>

                            <!-- <div class="form-group">
                                <input type="text" onkeypress="return isNumberKey1(event);" name="postalCode" value="<?php echo set_value('postalCode'); ?>" class="form-control" placeholder="<?php echo lang('postal_code'); ?>">
                            </div>

                            <div class="form-group">
                                <input type="text" onkeypress="return isNumberKey1(event);" name="ssnLast" value="<?php echo set_value('ssnLast'); ?>" class="form-control" placeholder="<?php echo lang('ssn_last'); ?>">
                            </div> -->
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn addBankAccount addBank"><?php echo lang('add_bank_acc'); ?></button>
                        <a href="javascript:void(0)" class="makeFree" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal popup for add bank account -->

<!-- Start Modal popup for update bank account -->
<div class="modal fade" id="myModalShowPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('update_acc'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('update_acc_msg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <a href="<?php echo base_url('home/user/userProfile/1');?>">
                        <button type="button" class="btn form-control login_btn"><?php echo lang('ok'); ?></button>
                    </a>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal popup for update bank account -->

<!-- 1.Event start date/time should be more than 3 hr from current time ( eg. current time -12:00 PM then start time should be 3:00 PM or more)
2. Strat date/ time and end date/time should have difference of half n hour (eg. start time -12:00 PM end time should be 12:30 PM or more) 
link - https://eonasdan.github.io/bootstrap-datetimepicker/
-->
<script>

    $('.scroll-prt').scroll(function() {

        let div = $(this).get(0);
        if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
            // do the lazy loading here
            allFriends(1);
        }
    });
    
</script>
