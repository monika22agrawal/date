<?php 
if(!empty($joined_member)){

    foreach ($joined_member['list'] as $value) { 

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
<div class="friendVisit joinedVisitCount" id="jMember">
    <div class="media">
        <div class="media-left">
            <div class="usImg">
                <a><img src="<?php echo $memberImg; ?>"></a>
            </div>
        </div>
        <div class="media-body">
            <div class="row">
                <div class="col-md-10 nme-cmfrm-mar">
                    <h4 class="media-heading"><a><?php echo ucfirst($value->memberName); ?></a></h4>                    
                     <?php 
                        if($value->memberStatus == 1){
                            echo '<div class="serviseslead statusSuccess">Confirmed payment</div>';
                        }elseif($value->memberStatus == 2){
                            echo '<div class="serviseslead statusWaiting">Joined,Payment is pending </div> ';
                        }elseif($value->memberStatus == 3){
                            echo '<div class="serviseslead statusSuccess">Confirmed</div>';
                        }elseif($value->memberStatus == 5){
                            echo '<div class="serviseslead  statusWaiting">Pending request</div>';
                        }elseif($value->memberStatus == 4){
                            echo '<div class="serviseslead  statusDanger">Request rejected</div>';
                        }elseif($value->memberStatus == 6){
                            echo '<div class="serviseslead statusDanger">Request canceled</div>';
                        } 
                    ?>                    
                </div>
                <!-- companionId -->
                <div class="col-md-2 nme-cmfrm-mar">
                    <?php if($value->memBlockStatus == 1) : ?>
                    <a href="<?php echo base_url()."admin/users/blockUnblockMem/" .encoding($value->eventMemId).'/'.encoding($value->eventId).'/'.$eventOrgId; ?>"  class="label label-danger" title="Block">Block</a>
                    <?php else : ?>
                    <a href="<?php echo base_url()."admin/users/blockUnblockMem/" .encoding($value->eventMemId).'/'.encoding($value->eventId).'/'.$eventOrgId; ?>" class="label label-primary"  title="Unblock">Unblock</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(!empty($value->companionName) && !empty($value->companionMemberStatus)){?>
            <div class="media nastedMedia">
                <div class="media-left">
                    <div class="usImg">
                        <a><img src="<?php echo $compMemberImg; ?>"></a>
                    </div>
                </div>
                <div class="media-body">
                    <div class="row">
                        <div class="col-md-10 nme-cmfrm-mar">
                            <h4 class="media-heading"><a><?php echo ucfirst($value->companionName); ?></a></h4>
                            <?php if($value->memberStatus == 1){
                                echo '<div class="serviseslead statusSuccess">Confirmed payment</div>';
                            }elseif($value->memberStatus == 2){
                                echo '<div class="serviseslead statusWaiting">Joined,Payment is pending </div> ';
                            }elseif($value->memberStatus == 3){
                                echo '<div class="serviseslead statusSuccess">Confirmed</div>';
                            }elseif($value->memberStatus == 5){
                                echo '<div class="serviseslead  statusWaiting">Pending request</div>';
                            }elseif($value->memberStatus == 4){
                                echo '<div class="serviseslead  statusDanger">Request rejected</div>';
                            }elseif($value->memberStatus == 6){
                                echo '<div class="serviseslead statusDanger">Request canceled</div>';
                            } ?>
                        </div>
                        <div class="col-md-2 nme-cmfrm-mar">
                            <?php if($value->compBolckStatus == 1) : ?>
                                <a href="<?php echo base_url()."admin/users/blockUnblockComp/" .encoding($value->companionEventMemberId).'/'.encoding($value->eventId).'/'.$eventOrgId;; ?>"  class="label label-danger" title="Block"><i class="fa fa-close"></i></a>
                                <?php else : ?>
                                <a href="<?php echo base_url()."admin/users/blockUnblockComp/" .encoding($value->companionEventMemberId).'/'.encoding($value->eventId).'/'.$eventOrgId;; ?>" class="label label-primary"  title="Unblock"><i class="fa fa-check"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>                   
                </div>
            </div>
            <?php } ?>                 
        </div>
    </div>
</div>
<?php } 

    if($offset==0){?>
    <div>
        <div id="moreData">
        <!--load data -->
        </div>

        <div class="PaginationBlock">
            <input type="hidden" name="totalCount" id="totalCount" value="<?php echo $total_count?>">
            <div id="loadMore" class="text-center" >
                <button class="btn btn-flat margin" id="btnLoad" >Load More</button>
            </div>
        </div>
    </div>

    <?php }

 }else{ ?>

    <div class="media-blck">
        <div class="text-center">
            <img src="<?php echo AWS_CDN_BACK_CUSTOM_IMG ?>team.png" alt="Image" width="80px" />
            <div class=""> No Member Available!</div>
        </div>
       
    </div>

<?php } ?>
    

