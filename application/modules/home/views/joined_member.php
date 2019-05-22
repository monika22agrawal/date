<?php 
    $frontend_assets =  base_url().'frontend_asset/';    
?>
<?php $countAll = $joinMemCount; 
//pr($joinedList['eventCreaterDetail']);
if(!empty($joinedList['list'])){ 

    foreach ($joinedList['list'] as $value) {

        if(!filter_var($value->memberImageName, FILTER_VALIDATE_URL) === false) { 

            $memberImg = $value->memberImageName;

        }else if(!empty($value->memberImageName)){ 

            $memberImg = AWS_CDN_USER_THUMB_IMG.$value->memberImageName;

        } else{                   

            $memberImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        } 

        if(!filter_var($value->companionImageName, FILTER_VALIDATE_URL) === false) { 

            $compMemberImg = $value->companionImageName;

        }else if(!empty($value->companionImageName)){ 

            $compMemberImg = AWS_CDN_USER_THUMB_IMG.$value->companionImageName;

        } else{        
                    
            $compMemberImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        }   
?>
<!-- 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request -->

<div class="blog_comment_item mt-20 joinedVisitCount rem-mem">
    <!-- 1 = Confirmed payment,2 =Joined,Payment is pending,3=Confirmed,4=Request rejected,5=Pending request -->
    <!-- start show member section -->
    <div class="media">
        <div class="media-left">
            <a href="<?php echo base_url('home/user/userDetail/').encoding($value->memberId);?>"><img src="<?php echo $memberImg; ?>" alt=""></a>
        </div>
        <div class="media-body">
            <h4><?php echo ucfirst($value->memberName); ?></h4>
            <?php if($value->memberStatus == 1){
                echo '<p>Confirmed payment</p>';
            }elseif($value->memberStatus == 2){
                echo '<p>Joined, Payment is pending</p>';
            }elseif($value->memberStatus == 3){
                echo '<p>Confirmed</p>';
            }
            ?> 
        </div>
        <?php if($countAll > 1 && $value->memberStatus == 2 && $value->eventEndDate > date('Y-m-d H:i:s')){?>
            <div class="favoriteStar cancleReq cross-mem-icn">
                <a onclick="openRemoveModel('<?php echo $value->eventMemId;?>','<?php echo ucfirst($value->eventId);?>','<?php echo ucfirst($value->memberName);?>','joined')" data-toggle="modal" href="javascript:void(0);"><img src="<?php echo AWS_CDN_FRONT_IMG;?>close_button.png"></a>
            </div> 
        <?php } ?>
    </div>
    <!-- end show member section -->

    <!-- start show companion member section -->
    <?php if(!empty($value->companionName) && !empty($value->companionMemberStatus)){?>
        <div class="media reply_comment mt-15">
            <div class="media-left">
                <a href="<?php echo base_url('home/user/userDetail/').encoding($value->companionUserId);?>"><img src="<?php echo $compMemberImg; ?>" alt=""></a>
            </div>
            <div class="media-body">
                <h4><?php echo ucfirst($value->companionName); ?></h4>
                <?php if($value->companionMemberStatus == 1){
                    echo '<p>Confirmed payment</p>';
                }elseif($value->companionMemberStatus == 2){
                    echo '<p>Joined, Payment is pending</p>';
                }elseif($value->companionMemberStatus == 3){
                    echo '<p>Confirmed</p>';
                }
                ?>
            </div>
        </div>
    <?php } ?>
    <!-- end show companion member section -->   
    
</div>

<?php
    }
}else{
    echo "<div class='notFound'><h3>".lang('event_no_join_mem_msg')."</h3></div>" ; 
}
?>
<?php if($offset==0){?>
    <span id="appendDataForJoined"></span>
    <input type="hidden" id="joinMemCount-count" value="<?php echo $joinMemCount;?>">
    <div class="text-center loadMoreBtn mt-20" id="load_more_btn_joined" style="display:none;">    
        <a href="javascript:void(0);" class="btn form-control login_btn" onclick="load_joined_friends();"><?php echo lang('load_more'); ?></a>
    </div>
<?php } ?>
