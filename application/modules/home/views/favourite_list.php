<?php $i=1; if(!empty($favUser)){ foreach ($favUser as $value) { ?>

    <div id="favId<?php echo $value->favId?>" class="author_posts_inners newFavLen friendVisit row_user_<?php echo $i;?>">
        <div class="media">
            <div class="media-left prfle-fvrte">
                <?php  
                if(!filter_var($value->userImg, FILTER_VALIDATE_URL) === false) { 
                    $img = $value->userImg;
                }else if(!empty($value->userImg)){ 
                    $img = AWS_CDN_USER_THUMB_IMG.$value->userImg;
                } else{                    
                    $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                } ?>
                <a href="<?php echo base_url('home/user/userDetail/').encoding($value->favUserId);?>"><img src="<?php echo $img;?>" class="brdr-img" alt=""></a>
            </div>
            <div class="media-body prfle-fvrte-info pt-15">
                <div class="dsply-block">
                    <h3 class="dsply-blck-lft inline-div"><?php echo !empty($value->fullName) ? ucfirst($value->fullName) : "NA"; ?></h3>
                    <p class="str-sec-pra dsply-blck-rgt text-right fvrte">
                        <a class="" onclick="removeFav('<?php echo $value->favId?>');" href="javascript:void(0)"><span class="fa fa-star"></span></a>
                    </p>
                </div>
                <div class="clearfix"></div>
                <h4 class="fvrte-stus pt-5"><?php echo ucfirst($value->workName); ?></h4>
            </div>
        </div>
    </div>

<?php  $i++; } }elseif ($page == 0){

    echo "<div class='notFound'><h3>".lang('no_record_found')."</h3></div>"; ?>      

<?php } ?> 
<input type="hidden" id="total-count" value="<?php echo count($favUser);?>">    
