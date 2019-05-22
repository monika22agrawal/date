<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<!--================Banner Area =================-->
<div class="wraper">    
<section class="banner_area banner_area2">
    
        <div class="container">
            <div class="banner_content">
                <h3 title="Near You"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('near_you'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
                <div class="advanced_search">
                    <form method="post" autocomplete="off" >
                        <div class="search_inner">
                            <div class="search_item">
                                <h5 class="pb-8"><?php echo lang('location_title'); ?></h5>
                                <div class="input-group loctn-inpt">                                    
                                    <input id="address" type="text" class="form-control" onkeyup="checkAddress();" name="address" placeholder="<?php echo lang('location_palceholder'); ?>">
                                </div>
                            </div>
                            <div class="search_item Filter_Width">
                                <h5 class="pb-8"><?php echo lang('show_me'); ?></h5>
                                <select id="gender" class="selectpicker">
                                    <option value=""><?php echo lang('all'); ?></option>
                                    <option value="2"><?php echo lang('girls'); ?></option>
                                    <option value="1"><?php echo lang('guys'); ?></option>
                                    <option value="3"><?php echo lang('transgender'); ?></option>
                                    
                                </select>
                            </div>
                            <div class="search_item">
                                <h5 class="pb-8"><?php echo lang('filter_by'); ?></h5>
                                <select id="userOnlineStatus" class="selectpicker">
                                    <option value="1"><?php echo lang('all'); ?></option>
                                    <option value="2"><?php echo lang('online'); ?></option>
                                    <option value="3"><?php echo lang('new'); ?></option>
                                </select>
                            </div>
                            <div class="search_item age-slidr">
                                <h5 class="pb-8"><?php echo lang('age_group'); ?></h5>
                                <div id="price_select"></div>
                                <div class="price_inner">
                                    <input id="amount" class="rangeVal" readonly style="border:0; color:#f6931f; font-weight:bold;">
                                </div>
                            </div>
                            <div class="search_item">
                                <a href="javascript:void(0);" onclick="nearUserList(0);">
                                    <button type="button" value="LogIn" class="btn form-control login_btn mrgn-top srch-btn btn_focs_whte"><?php echo lang('search'); ?></button>
                                </a>
                                <p><a href="" class="link-font pt-10 pl-5"><?php echo lang('reset'); ?></a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
</section>
<!--================ End Banner Area =================-->

<!--================ Active Memebers Area =================-->
<section class="actives_members">
    
        <div class="container">
            <div class="search_area mble-drpdwn row mb-10">
                <div class="pull-left">
                    <select onchange="checkMapConditions();" id="viewType" class="selectpicker slct-view">
                        <option value="list" class="lst-vew"><?php echo lang('list_view'); ?></option>
                        <option <?php echo (!empty($this->uri->segment(3)) && $this->uri->segment(3) == 2) ? 'selected' : ''; ?> value="map" class="map-vew"><?php echo lang('map_view'); ?></option>
                    </select>
                </div>
                <div class="pull-right mob-res">
                    <div class="search_widget">
                        <div class="input-group">
                            <input type="text" id="searchName" class="form-control" value="" placeholder="<?php echo lang('search_here_placeholder'); ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row people-near-img2">
                <div id="nearUsers"></div>
                <br>
                <div id='showLoader' class="show_loader clearfix" data-offset="0" data-isNext="1">
                    
                    <img src='<?php echo AWS_CDN_FRONT_IMG;?>Spinner-1s-80px.gif' alt=''>
                </div>
                <div class='notFound showHide'>
                    <h3><?php echo lang('no_user_found'); ?></h3>
                </div>
            </div>
        </div>
        <div id="map_map"></div>
    </section>
<div class="clearfix"></div>
<!--================ End Active Memebers Area =================-->

<!--================ Map Area =================-->

<?php if($this->session->userdata('front_login') == true){ if($this->session->userdata('mapPayment') == 0){?>
<div class="container showHide" id="showSub">
    <div class="ansBlock">
        <div class="rgster-bsness text-center">
            <img class="bsnes-image pb-15" src="<?php echo AWS_CDN_FRONT_IMG;?>payment.png" />
            <div class="subscrptn-text mt-10">
                <h5><?php echo lang('pay_for_appointment'); ?></h5>
                <p class="price mt-15">
                   <?php echo lang('pay'); ?> <span> $50 </span> <?php echo lang('lifetime'); ?>
                </p>
                <p class="mt-15 prce-pra"><?php echo lang('appointment_pay'); ?></p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="form-wizard-buttons mt-30 text-center">
        <button type="button" onclick="openStripeModel(this)" data-pType="2" data-pagetype="2" data-title="<?php echo lang('view_user_map'); ?>" class="form-control login_btn mb-30" data-toggle="modal" data-target="#subscrbe-mdl"><?php echo lang('subscribe'); ?>
        </button>
    </div>
</div>
<?php } } ?>
<!--================ End Map Area =================-->
</div>
<input type="hidden" value="" id="lat">
<input type="hidden" value="" id="long">

<!-- The Modal For Check Appointment Status -->

<div class="modal fade" id="myModalList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('action'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-10">
                    <p class="para text-left mb-15" id="showMsgList"></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" class="btn form-control login_btn" data-dismiss="modal"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('cancel'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End The Modal For Check Appointment Status -->

<!-- Modal for check map conditions -->
<div class="modal fade" id="myModalMap" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('view_users'); ?></h5>
                <button type="button" class="close checkMap" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('login_to_view_mapuser'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <a href="<?php echo base_url('home/login');?>">
                        <button type="button" value="LogIn" class="btn form-control login_btn forgot-password"><?php echo lang('ok'); ?></button>
                    </a>
                    <a href="javascript:void(0)" class="checkMap" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ENd Modal for check map conditions -->

<input type="hidden" id="page-count" value="">
<input type="hidden" id="total-near-count" value="">

<script type="text/javascript">
    $('.checkMap').click(function(){
       
        $('.lst-vew').click();      
        //$('#viewType').val('');
    });

    var isLogin = '<?php echo $this->session->userdata('front_login');?>';
    var isPayment = '<?php echo $this->session->userdata('mapPayment');?>';

    /* to manage user's list and map tab */
    function checkMapConditions(){

        var viewType = $('#viewType').val();
        $('#showSub').hide();

        if(viewType == 'map'){

            if(isLogin == true){

                if(isPayment == 1){

                    nearUserList(0);

                }else{

                    // show stripe div
                    $("#nearUsers").html('');
                    $('#showSub').show();
                }

            }else{

                // show login popup
                $('#myModalMap').modal('show');
            }

        }else{

            nearUserList(0);
        }
    }
    
    
    // to get user's online/ofline status using firebase 
    function checkOnline(id){
        
        $('.notFound').hide();
        var userOnlineStatus = $('#userOnlineStatus').val();

        firebase.database().ref("online").child(id).on('value', function(snapshot) {

            if (snapshot.exists()) {
                var onoff = snapshot.val().lastOnline;
            }else {
                var onoff = 'offline';
            }

            if(onoff == 'offline'){

                $('#checkOnline'+id).removeClass('green-online');
                $('#checkOnline'+id).addClass('green-ofline');

                /*show online/offline for mobile view*/
                $('#mobileOnOff'+id).removeClass('green-online');
                $('#mobileOnOff'+id).addClass('green-ofline');

            }else{

                $('#checkOnline'+id).removeClass('green-ofline');
                $('#checkOnline'+id).addClass('green-online');

                /*show online/offline for mobile view*/
                $('#mobileOnOff'+id).removeClass('green-ofline');
                $('#mobileOnOff'+id).addClass('green-online');

            }

            if(userOnlineStatus == 2 && onoff == 'offline'){
                $('#userOnId'+id).remove();
                var viewType = $('#viewType').val(); // to check list view or map view for calling function
                var totalMyDataLoaded=$('.usersVisitCount').length;

                if(totalMyDataLoaded == 0 && viewType == 'list'){

                    $('.notFound').show();
                }
            }                
        });
    }
    
    /* for scroll using ajax pagination*/
    
    $(window).scroll(function() {

        var viewType = $('#viewType').val(); // to check list view or map view for calling function

        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            
            if(viewType == 'list'){
                nearUserList(1);
            }            
        }
    });
   
</script>