<?php

$frontend_assets =  base_url().'frontend_asset/';    

if(!empty($compList['list'])){ 

    foreach ($compList['list'] as $value) {  

        if(!filter_var($value->userImgName, FILTER_VALIDATE_URL) === false) { 
            $compImg = $value->userImgName;
        }else if(!empty($value->userImgName)){ 
            $compImg = AWS_CDN_USER_THUMB_IMG.$value->userImgName;
        } else{                    
            $compImg = AWS_CDN_USER_PLACEHOLDER_IMG;
        } 
?>

    <div class="blog_comment_item mt-20 compVisitCount">
        <div class="media">
            <div class="media-left">
                <a href="<?php echo base_url('home/user/userDetail/').encoding($value->companionMemId);?>"><img src="<?php echo $compImg; ?>" alt=""></a>
            </div>
            <div class="media-body">
                <h4><?php echo ucfirst($value->fullName); ?></h4>
                <?php if($value->companionMemberStatus == 1){
                    echo '<p>'.lang('confirmed_payment_status').'</p>';
                }elseif($value->companionMemberStatus == 2){
                    echo '<p>'.lang('payment_pending_status').'</p>';
                }elseif($value->companionMemberStatus == 3){
                    echo '<p>'.lang('confirmed_status').'</p>';
                }elseif($value->companionMemberStatus == 4){
                    echo '<p>'.lang('request_rejected_status').'</p>';
                }elseif($value->companionMemberStatus == 6){
                    echo '<p>'.lang('request_canceled_status').'</p>';
                }else{
                    echo '<p>'.lang('pending_request_status').'</p>';
                }
                ?> 
            </div>
            <?php if($value->companionMemberStatus == 2){?>

                <div class="rsvp-button btnInline text-right campPay pay-btn">  
                    
                    <a href="javascript:void(0);" onclick="openStripeModel(this)" data-eid="<?php echo $value->eventId;?>" data-compmemid="<?php echo $value->companionMemId;?>" data-compid="<?php echo $value->compId;?>" data-emid="<?php echo $value->eventMem_Id;?>" data-pType="5" data-eamt="<?php echo $value->eventAmount; ?>" data-groupchat="<?php echo $value->groupChat; ?>" data-title="Companion Payment" class="btn form-control login_btn">Pay <?php echo $value->currencySymbol.''.$value->eventAmount; ?></a>
                </div>

            <?php } ?>
        </div>
    </div>

<?php }

} else {
    echo "<div class='notFound'><h3>".lang('event_no_comp_mem_msg')."</h3></div>" ; 
}
if($offset==0) { ?>

    <span id="appendDataForComp"></span>
    <input type="hidden" id="compMemCount-count" value="<?php echo $compMemCount;?>">
    <div class="text-center loadMoreBtn mt-20" id="load_more_btn_companion" style="display:none;">    
        <a href="javascript:void(0);" class="btn form-control login_btn" onclick="load_companion_member();"><?php echo lang('load_more'); ?></a>
    </div>

<?php } ?>