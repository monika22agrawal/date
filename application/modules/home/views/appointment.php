<?php 
    $frontend_assets =  base_url().'frontend_asset/';    
?>
<div class="blnk-spce"></div>
<div class="wraper">
    <!--================Blog grid Area =================-->
    <section class="blog_grid_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12 search_area">
                    <div class="pull-left">
                        <select id="app-type" class="selectpicker slct-view dropdwn-lft mrgn-btm-drpdwn">
                            <option class="evt-rqst" value="0"><?php echo lang('all_appointment'); ?></option>
                            <option class="my-evt" value="2"><?php echo lang('received_appointment'); ?></option>
                            <option class="evt-rqst" value="1"><?php echo lang('sent_appointment'); ?></option>
                            <option class="my-finish" value="3"><?php echo lang('finished_appointment'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id="sentAppList"></div>
                <br>
                <div id='showLoader' class="show_loader clearfix" data-offset="0" data-isNext="1">                    
                    <img src='<?php echo AWS_CDN_FRONT_IMG ?>Spinner-1s-80px.gif' alt=''>
                </div>
            </div>
        </div>
    </section>
</div>
<!--================End Blog grid Area =================-->
<input type="hidden" id="page-count" value="">

<!-- Start Modal for apply counter -->
<div class="modal fade" id="ofr-prce" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('counter_price_title'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="ofrForm" method="post" action="<?php echo base_url('home/appointment/applyCounter/');?>">
                <div class="modal-body mdl-body">
                    <h2 class="text-center pop-up-hedng"><?php echo lang('offer_price'); ?><span id="set-ofr-prc"></span></h2>
                    <div class="regfrm mdl-pad mt-20">
                        <p class="para text-left mb-15"><?php echo lang('counter_price'); ?></p>
                        <div class="form-group regfld">
                            <input type="text" min="1" maxlength=6 class="form-control" onkeypress="return isNumberKey(event);" placeholder="<?php echo lang('counter_price'); ?>" name="counterPrice" required="">
                            <input type="hidden" id="appId" name="appointId" value="">
                            <input type="hidden" id="appointById" name="appointById" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer mdl-ftr">
                    <div class="form-group text-right pay-btn">
                        <button type="button" class="btn form-control login_btn shre-btn applyCounter"><?php echo lang('submit'); ?></button>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal for apply counter -->

<script>

    $(window).scroll(function() {

        var type = $('#type').val();
        var page = $("#page-count").val();

        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            
            sentAppList(page,type,1);
        }
    });
  
</script>