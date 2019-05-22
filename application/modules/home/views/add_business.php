<?php 
   $frontend_assets =  base_url().'frontend_asset/';
?>

<!--================Banner Area =================-->
<div class="wraper">
    <section class="banner_area">
    	<div class="container">
    		<div class="banner_content">
    			<h3 title="Business"><img class="left_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-left-img.png" alt=""><?php echo lang('add_biz_title'); ?><img class="right_img" src="<?php echo AWS_CDN_FRONT_IMG;?>banner/t-right-img.png" alt=""></h3>
            </div>
        </div>
    </section>
<!--================End Banner Area =================-->

<!--================Contact From Area =================-->

	<section class="contact_form_area">
		<div class="container">
			<div class="row">
                <div class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3 col-sm-12 col-xs-12">
                	<div class="form-wizard form-header-classic form-body-classic" id="msform">
    					<fieldset>
    						<div class="animated">
    							<form id="businessForm" method="POST" action="<?php echo base_url('home/business/addBusinessData');?>" enctype="multipart/form-data">

    								<input type="hidden" value="<?php echo !empty($businessDetail) ? '1' : '2'; ?>" name="businessType">
                        			<input type="hidden" id="lat" name="businesslat" value="<?php echo (!empty($businessDetail->businesslat) && $businessDetail->businesslat) ? $businessDetail->businesslat : '';?>">
                        			<input type="hidden" id="long" name="businesslong" value="<?php echo (!empty($businessDetail->businesslong) && $businessDetail->businesslong) ? $businessDetail->businesslong : '';?>">

	    							<div class="rgster-bsness text-center">
		                                <img class="bsnes-image" src="<?php echo AWS_CDN_FRONT_IMG ?>busness-img.png" />
		                                <p class="para pt-20">“<?php echo lang('add_biz_msg'); ?>”</p>
		                                <div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center;">
		                                    <div class="log_div bsnes-img text-center mt-30">
		                                        <img src="<?php echo (!empty($businessDetail->businessImage)) ? AWS_CDN_BIZ_THUMB_IMG.$businessDetail->businessImage : AWS_CDN_FRONT_IMG.'placeholder-image.png'; ?>" id="pImg">
		                                        <div class="text-center upload_pic_in_album bsnes-cam">
		                                            <input accept="images/*" class="inputfile hideDiv" id="file-1" name="businessImage" onchange="document.getElementById('pImg').src = window.URL.createObjectURL(this.files[0])" style="opacity: 0;" type="file">
		                                            <label for="file-1" class="upload_pic">
		                                                <span class="fa fa-camera"></span>
		                                            </label>
		                                        </div>
		                                        <div id="businessImage-err"></div>
		                                    </div>
		                                </div>

		                                <div class="clearfix"></div>

		                                <div class="regfrm mt-20">

		                                    <div class="form-group regfld">

		                                        <input class="form-control" name="businessName" value="<?php if(!empty($businessDetail->businessName)){ echo $businessDetail->businessName; }else{ echo ($this->input->post('businessName') != '') ? $this->input->post('businessName') : ''; } ?>" required type="text" placeholder="<?php echo lang('biz_name_place');?>">
		                                        
		                                    </div>

		                                    <div class="form-group regfld">

		                                        <input class="form-control" id="address" name="businessAddress" value="<?php if(!empty($businessDetail->businessAddress)){ echo $businessDetail->businessAddress; }else{ echo ($this->input->post('businessAddress') != '') ? $this->input->post('businessAddress') : ''; } ?>" required type="text" placeholder="<?php echo lang('add_location');?>">

		                                    </div>

		                                    <div class="form-wizard-buttons mt-30 text-center">

			    								<button type="button" name="next" class="btn form-control login_btn btn_focs_whte rem-cls submitBusinessData" value="Next"><?php echo lang('done'); ?></button>

			    								<!-- <button type="button" style="display: none;" name="next" class="btn-next btn form-control login_btn"><?php echo lang('next_process'); ?>
                                        		</button> -->

			    							</div>
                                            <div class="skp-anchr text-right">
                                                <a href="<?php echo base_url('home/nearByYou');?>"><?php echo lang('skip_btn'); ?></a>
                                            </div>

		                                </div>
		                            </div>
	                            </form>
    						</div>
    					</fieldset>
    					<fieldset>
    						<div class="animated">
    							<div class="ansBlock">
    								<div class="rgster-bsness text-center">
	                                <img class="bsnes-image pb-15" src="<?php echo AWS_CDN_FRONT_IMG;?>profits.png" />
	                                <div class="subscrptn-text mt-10">
                                        <h5><?php echo lang('promote_biz');?></h5>
                                        <p class="price mt-15">
                                           <?php echo lang('pay'); ?>
                                           <span>
                                               $50
                                           </span> 
                                           / <?php echo lang('promote_biz_month');?>
                                        </p>
                                        <p class="mt-15 prce-pra"><?php echo lang('promote_biz_msg');?></p>
                                    </div>
	                                <div class="clearfix"></div>
	                            </div>
    							</div>
    							<div class="form-wizard-buttons mt-15 text-center">
    								<!-- <input type="button" name="previous" class="previous action-button-previous btn-previous" value="Previous"/> -->
    								<!-- <button type="button" name="next" class="btn form-control login_btn" data-toggle="modal" data-target="#subscrbe-mdl" value="Next">Subscribe
    								</button> -->
    								<?php if(empty($bizSubsDetail)){?>

    									<button type="button" onclick="openStripeModel(this)" data-ptype="6" data-title="<?php echo lang('promote_biz_title');?>" class="form-control login_btn"><?php echo lang('subscribe');?></button>

    								<?php }else{

                                            $p_currency = $p_amount = $p_interval = ''; $plan_name = 'Free';

                                            if(isset($planDetail) && $planDetail['status'] === true){
                                                $plan_detail = $planDetail['data'];
                                                $plan_name = $plan_detail->nickname;
                                                $p_currency = $plan_detail->currency;
                                                $p_amount = $plan_detail->amount;
                                                $p_interval = $plan_detail->interval;
                                            }
                                            
                                            $plan_text = lang('upgrade_premium_monthly');
                                            $upgrade = true; //show upgrade button only when plan is Free
                                            $cancel_text = '';

                                            if(isset($subsDetail['data']) && !empty($subsDetail['data'])){
                                                
                                                $end_time = date('F d, Y',$subsDetail['data']['current_period_end']);
                                                $plan_text = ''.lang('you_are_on').' <b>'.$plan_name.'</b> '.lang('plan_ending_on').' '.$end_time.'. '.lang('your_current_plan').' <span><sup>$</sup>'.$p_amount.'</span> '.lang('per').' '.$p_interval;
                                                $upgrade = false;

                                               
                                                $cancel_text = '<button type="button" data-toggle="modal" data-target="#myModalCheckFriend" class="form-control login_btn mt-15">'.lang('cancel_subscription').'</button>';


                                                if($subsDetail['data']['cancel_at_period_end'] == true){
                                                    $cancel_text = '<p>'.lang('canceled_subscription_msg').'</p>';
                                                }
                                            }
                                            echo '<div>'.$plan_text.'</div>';
                                            echo '<div>'.$cancel_text.'</div>';                                          
                                        } ?>

    							</div>
    							<div class="skp-anchr text-right">
    								<a href="<?php echo base_url('home/nearByYou');?>"><?php echo lang('skip_btn'); ?></a>
    							</div>
    						</div>
    					</fieldset>
    				</div>
    			</div>
    			<div class="clearfix"></div>
	    	</div>
	    </div>
	</section>
</div>
<!--================End Contact From Area =================-->
<script src="<?php echo AWS_CDN_FRONT_JS ?>form_js_form-wizard.js"></script>
<!-- The Modal for cancel subscription -->
<div class="modal fade" id="myModalCheckFriend" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('cancel_subscription'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <div class="form-group regfld">
                        <label class="mb-10"><?php echo lang('cancel_subs_popupmsg'); ?></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <button type="button" value="LogIn" class="btn form-control login_btn btn_focs_whte cancelBizSubscription"><?php echo lang('ok'); ?></button>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

$("#file-1").change(function(){

   	input = this;

   	if (input.files && input.files[0]) {   

      	size = (input.files[0].size/1024).toFixed(2);
      	let fileExtension = ['jpeg', 'jpg', 'png', 'gif'];

      	if ($.inArray($(input).val().split('.').pop().toLowerCase(), fileExtension) == -1) {

	        $('#businessImage-err').html("<?php echo lang('format_allowed');?> : "+fileExtension.join(', '));
	        return false;

      	}else if(size>10240){     

	        $('#businessImage-err').html("<?php echo lang('max_five_img');?>");
	        return false;

      	}else{

          	let reader = new FileReader();
          	reader.onload = function(e) {
	            $('#pImg').attr('src', e.target.result);
	            $('#businessImage-err').html('');
	        }
          	reader.readAsDataURL(input.files[0]);
     	}

   	}else{

      	$('#pImg').attr('src','<?php echo AWS_CDN_BIZ_PLACEHOLDER_IMG;?>');
      	$('#businessImage-err').html('');
   	}
});
    
// for submit add business record
$('body').on('click', ".submitBusinessData", function (event) {

	var that = $(this);
   	var form = $("#businessForm");

   	form.validate({

      	rules: {
         	businessName : {
	            required: true, 
	            minlength: 3,
	            maxlength:100
         	},
         	businessAddress : {
            	required: true
         	}                     
      	}
   });

   if (form.valid() === true){ 

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

	            if (data.status == 1){

	               	$(".rem-cls").removeClass("submitBusinessData");
	                toastr.success(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);
	                //that.next().click();

	            }  else if(data.status == 2) {

	                toastr.success(data.msg);
                  window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);
	                //that.next().click();  

	            } else if(data.status == -1) {

	                toastr.error(data.msg);
	                window.setTimeout(function () {
	                    window.location.href = data.url;
	                }, 500);

	            } else {

	                toastr.error(data.msg);

	            }           
	        },
	        error:function (){

	            hide_loader();
	            toastr.error(commonMsg);
	        }
      	});
   	}
});

// to cancel subscription
$('body').on('click', ".cancelBizSubscription", function (event) {

    var url = BASE_URL+'home/business/cancelBizSubscription';
    $('#myModalCheckFriend').modal('hide');
    
    $.ajax({
        type: "POST",
        url:url,
        data: {}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {

            hide_loader();        
           
            if (data.status == 1){ 
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 200);

            }else if (data.status == 2){ 
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
                
            } else {

                toastr.error(data.msg);
            }           
        },

        error:function (){
            hide_loader();   
            toastr.error(commonMsg);
        }
    });  
});
</script>