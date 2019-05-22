<?php 
    
if(!empty($friendList)){ foreach ($friendList as $value) { 

    if(!filter_var($value->profileImage, FILTER_VALIDATE_URL) === false) { 
        $userImg = $value->profileImage;
    }else if(!empty($value->profileImage)){ 
        $userImg = AWS_CDN_USER_THUMB_IMG.$value->profileImage;
    } else{                    
        $userImg = AWS_CDN_USER_PLACEHOLDER_IMG;
    } 

    $memberIds = explode(',', $memberId);

?>
<div class="author_posts_inners">
    <div class="media">
        <div class="media-left prfle-fvrte">
            <img src="<?php echo $userImg; ?>" class="brdr-img" alt="">
        </div>
        <div class="media-body prfle-fvrte-info pt-15">
            <div class="dsply-block">
                <h3 class="dsply-blck-lft flter_blck pb-8"><?php echo ucfirst($value->fullName); ?></h3>
                <div class="str-sec-pra dsply-blck-rgt flter_chck text-right fvrte">
                    <div class="keeplogin">
                        <input onclick="getCheckUncheck(this)" class="friend_checkbox" id="box-<?php echo $value->userId; ?>" type="checkbox" value="<?php echo $value->userId; ?>" <?php echo in_array($value->userId, $memberIds) ? 'checked' : ''; ?> >
                        <label for="box-<?php echo $value->userId; ?>"></label>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            
            <p class="str-sec-pra">

                <?php $count = $value->total_rating;

                for($i=1;$i<=$count;$i++){ ?>

                    <span class="fa fa-star"></span> 

                <?php } $minCount = 5-$count;
                
                for($j=1;$j<=$minCount;$j++){ ?>

                    <span class="fa fa-star-o"></span>

                <?php } ?>
            </p>
        </div>
    </div>
</div>
<?php } } else {

    echo "<div class='notFound'><h3>".lang('no_user_found')."</h3></div>";

} ?>