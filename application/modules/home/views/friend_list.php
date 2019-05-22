<?php 
if( !empty($friendList) )
{ 
    foreach($friendList as $row){

        if(!filter_var($row->webProfileImage, FILTER_VALIDATE_URL) === false) { 
            $img = $row->webProfileImage;
        }else if(!empty($row->webProfileImage)){ 
            $img = AWS_CDN_USER_THUMB_IMG.$row->webProfileImage;
        } else{                    
            $img = AWS_CDN_USER_PLACEHOLDER_IMG;
        } 
?>

        <!-- <a href="<?php //echo base_url('home/user/userDetail/').encoding($row->userId).'/';?>"><img src="<?php //echo $row->profileImage;?>"></a> -->
        <div id="frndId<?php echo $row->friendId?>" class="author_posts_inners frnd-btn friendVisitCount">
            <div class="media">
                <div class="media-left prfle-fvrte">
                    <a href="<?php echo base_url('home/user/userDetail/').encoding($row->userId);?>"><img src="<?php echo $img;?>" class="brdr-img" alt=""></a>
                </div>
                <div class="media-body prfle-fvrte-info pt-15">
                    <div class="dsply-block">
                        <h3 class="dsply-blck-lft mb-10"><?php echo ucfirst($row->fullName);?></h3>
                    </div>
                    <div class="clearfix"></div>
                    <h4 class="fvrte-stus pt-5"><?php echo !empty($row->work) ? ucfirst($row->work) : 'NA';?></h4>
                </div>
                <div class="frnd-lst-btn">
                    <button type="button" onclick="openModel('<?php echo $row->friendId; ?>','<?php echo ucfirst($row->fullName);?>');" class="btn form-control login_btn frnd-sec-btn btn_focs_whte"><?php echo lang('unfriend'); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
}elseif ($page == 0){

    echo "<div class='notFound'><h3>".lang('no_record_found')."</h3></div>"; ?>          

<?php } ?>
<input type="hidden" id="total-count" value="<?php echo $friendListCount;?>">




