<?php 
	$frontend_assets =  base_url().'frontend_asset/';
	if(!empty($myEvent)){ foreach ($myEvent as $value) { 

        if(!empty($value->eventImageName)){ 
            $eventImg = AWS_CDN_EVENT_THUMB_IMG.$value->eventImageName;
        } else{                    
            $eventImg = AWS_CDN_EVENT_PLACEHOLDER_IMG;
        }
?>

<div class="col-md-4 col-sm-6 col-xs-6 col-xxs-12 myEventVisitCount">
    <a href="<?php echo base_url('home/event/myEventDetail/').encoding($value->eventId).'/';?>">
        <div class="blog_grid_item our_event">
            <div class="blog_grid_img">
                <img src="<?php echo $eventImg;?>" alt="">
                <div class="new_tag">
                    <h4><?php echo ($value->payment == 'Free') ? lang('free_event') : lang('pain_event'); ?><span><?php echo ' '.$value->currencySymbol.''.$value->eventAmount; ?></h4>
                </div>
            </div>
            <div class="blog_grid_content our_sec_br">
              <a href="<?php echo base_url('home/event/myEventDetail/').encoding($value->eventId).'/';?>"><h3><?php echo wordwrap(substr(ucfirst($value->eventName), 0, 25), 20); ?></h3></a>
                <div class="blog_grid_date">
                    <!-- <div class="our_event_grid">
                        <a><h4>Shared By <span>Admin</span></h4></a>
                    </div>  -->
                    <div class="our_txt_sec">   
                        <div class="txt_div">
                            <h5><i class="fa fa-calendar"></i> <?php echo date('d M, Y',strtotime($value->eventStartDate));?></h5>    
                        </div>
                        <div class="txt_div">
                            <?php if($value->privacy == 'Private'){ 
                                $cls = 'lock';
                            }else{
                                $cls = 'users';
                            }
                            ?>
                            <h5><i class="fa fa-<?php echo $cls;?>"></i> <?php echo ($value->privacy  == 'Public') ? lang('public_invitation') : lang('private_invitation');?></h5>
                        </div>
                    </div>
                </div>
                <p class="evt-add"><i class="fa fa-map-marker pt-15"></i> <?php echo wordwrap(substr($value->eventPlace, 0, 75), 50); ?></p>
                <div class="event_btns evnt-min">
                    <?php $date = date('Y-m-d H:i:s'); 
                           
                        if($value->joinMemCount == 0 || $value->eventEndDate < $date) { 
                            
                        ?>
                            <a href="javascript:void(0);" onclick="openDeleteModel('<?php echo $value->eventId;?>')" data-toggle="tooltip" data-placement="top" title="<?php echo lang('delete_btn'); ?>" class="event_btn"><i class="fa fa-trash"></i></a>
                            <!-- <a href="<?php //echo base_url('home/event/updateEvent/').encoding($value->eventId).'/'; ?>" data-toggle="tooltip" data-placement="top" title="Accept" class="event_btn"><i class="fa fa-edit"></i></a> -->

                        <?php } ?>
                </div>
            </div>
        </div>
    </a>
</div>
<?php  } }else{ echo "<div class='notFound'><h3>No event found.</h3></div>" ;

} ?>
<?php if($offset==0){ ?>
    <span id="appendDataForMyEvent"></span>
    <input type="hidden" id="myEventCount-count" value="<?php echo $myEventCount;?>">
    <div class="text-center loadMoreBtn" id="load_more_users" style="display:none;">  
        <a href="javascript:void(0);" class="btn form-control login_btn" onclick="myEvent();"><?php echo lang('load_more'); ?></a>
    </div>
<?php } ?>