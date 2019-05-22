<?php 
    $frontend_assets =  base_url().'frontend_asset/';
     
    $countAll = $inviteMemCount; if(!empty($invitedList['list'])){ foreach ($invitedList['list'] as $value) {
   
    if(!filter_var($value->userImgName, FILTER_VALIDATE_URL) === false) { 
        $img = $value->userImgName;
    }else if(!empty($value->userImgName)){ 
        $img = AWS_CDN_USER_THUMB_IMG.$value->userImgName;
    } else{                    
        $img = AWS_CDN_USER_PLACEHOLDER_IMG;
    }   
?>

<div class="blog_comment_item invitedVisitCount mt-20 rem-mem">
    <div class="media">
        <div class="media-left">
            <a href="<?php echo base_url('home/user/userDetail/').encoding($value->memberId);?>"><img src="<?php echo $img;?>" alt=""></a>
        </div>
        <div class="media-body">
            <h4><?php echo ucfirst($value->fullName);?></h4>
            <p><?php echo ucfirst($value->workName);?></p>
        </div>
         <?php if($countAll > 1 && $value->eventEndDate > date('Y-m-d H:i:s')){?>
        <div class="favoriteStar cancleReq cross-mem-icn">
            <a onclick="openRemoveModel('<?php echo $value->eventMemId;?>','<?php echo ucfirst($value->eventId);?>','<?php echo ucfirst($value->fullName);?>','invited')" data-toggle="modal" href="javascript:void(0);"><img src="<?php echo AWS_CDN_FRONT_IMG;?>close_button.png"></a>
        </div> 
    <?php } ?>
    </div>
   
</div>
<?php
    }
}else{
    echo "<div class='notFound'><h3>".lang('event_no_invi_mem_msg')."</h3></div>" ; 
}
?>
<?php if($offset==0){ ?>
<span id="appendDataForInvited"></span>
<input type="hidden" id="inviteMemCount-count" value="<?php echo $inviteMemCount;?>">
<div class="text-center loadMoreBtn mt-20" id="load_more_btn_invited" style="display:none;">    
    <a href="javascript:void(0);" class="btn form-control login_btn" onclick="load_invited_friends();"><?php echo lang('load_more'); ?></a>
</div>
<?php } ?>