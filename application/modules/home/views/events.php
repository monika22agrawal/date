<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<script type="text/javascript">
    var noEvent        = '<?php echo lang('event_not_exist');?>';
</script>
<div class="blnk-spce"></div>
<!--================Blog grid Area =================-->
<div class="wraper">
<section class="blog_grid_area">
    <div class="container">
        <div class="row">
            <div class="blog_grid_inner">
                <div class="col-md-12 search_area">
                    <div class="pull-left">
                        <select id="event-type" class="selectpicker slct-view dropdwn-lft">
                            <option class="evt-rqst" value="2"><?php echo lang('event_request'); ?></option>
                            <option class="my-evt" value="1"><?php echo lang('my_event'); ?></option>                            
                        </select>
                    </div>
                    <div class="pull-right">                        
                        <a href="<?php echo base_url('home/event/createEvent');?>" class="register_angkar_btn btn_focs_whte"><i class="fa fa-plus"></i> <?php echo lang('create_event'); ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id="event-request"></div>
            </div>
        </div>
    </div>
</section>
</div>
<!--================End Blog grid Area =================-->

<!-- Start Modal popup for check friends to invite or create event -->
<div class="modal fade" id="myModalCheckFriend" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('no_friends'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('no_friends_msg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <div class="form-group text-right pay-btn">
                    <a href="javascript:void(0)">
                        <button type="button" class="btn form-control login_btn" data-dismiss="modal" aria-label="Close"><?php echo lang('ok'); ?></button>
                    </a>
                    <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Start Modal popup for check friends to invite or create event -->

<!-- Start modal popup for delete event -->
<div class="modal fade" id="eventDeletModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content mdl-subs">
            <div class="modal-header mdl-hdr">
                <h5 class="modal-title prmte" id="exampleModalLabel"><?php echo lang('delete_event'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>           
            <div class="modal-body mdl-body">
                <div class="regfrm mdl-pad mt-20">
                    <p class="para text-left mb-15"><?php echo lang('delete_event_msg'); ?></p>
                </div>
            </div>
            <div class="modal-footer mdl-ftr">
                <form method="post" action="<?php echo base_url('home/event/deleteEvent');?>">
                    <input type="hidden" id="eId" value="" name="eventId">
                    <div class="form-group text-right pay-btn">
                        <a href="javascript:void(0)">
                            <button type="button" id="delete-event-list" class="btn form-control login_btn" data-dismiss="modal" aria-label="Close"><?php echo lang('delete_btn'); ?></button>
                        </a>
                        <a href="javascript:void(0)" data-dismiss="modal"><?php echo lang('close'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Event modal popup for delete event -->
<script type="text/javascript">

    $(window).bind("pagehide", function() {
      // update hidden input field
      
      $('#event-type').val('');
    });
    
    /* for scroll using ajax pagination*/
    
    $(window).scroll(function() {

        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            $('#load_more_event_req').click();                   
        }
    });

</script>