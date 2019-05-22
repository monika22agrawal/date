<?php 

$frontend_assets =  base_url().'frontend_asset/';

if( (!empty($appReviewList) && !empty($appReviewList[0]->userId)) ){ foreach ($appReviewList as $get) { ?>

    <div class="media top-media mt-10">
        <div class="media-left revw-img">
            <?php 
                if(!filter_var($get->webShowImg, FILTER_VALIDATE_URL) === false) { 
                    $img = $get->webShowImg;
                }else if(!empty($get->webShowImg)){ 
                    $img = AWS_CDN_USER_THUMB_IMG.$get->webShowImg;
                } else{                    
                    $img = AWS_CDN_USER_PLACEHOLDER_IMG;
                }
                $userRedirect = '';
                if(($this->session->userdata('userId') != '') && ($get->userId == $this->session->userdata('userId'))){

                    $userRedirect = base_url('home/user/userProfile/');
                    
                }else {
                    $userRedirect = base_url('home/user/userDetail/').encoding($get->userId);
                }
            ?>
            <a href="<?php echo $userRedirect;?>"><img src="<?php echo $img;?>" alt=""></a>
        </div>
        <div class="media-body str-mrgn">
            <?php if($get->reviewType == 1){
                if($userId == $this->session->userdata('userId')){
            ?>
                <a href="<?php echo base_url('home/appointment/viewAppOnMap/').encoding($get->appId).'/';?>">
                    <div class=" dsply-block">
                        <h3 class="dsply-blck-lft rev-nme"><?php echo ucfirst($get->fullName);?></h3>
                        <h4 class="dsply-blck-rgt rev-dte"><?php echo time_elapsed_string($get->crd);?></h4>
                    </div>
                </a>
            <?php }else{ ?>

                <div class=" dsply-block">
                    <h3 class="dsply-blck-lft rev-nme"><?php echo ucfirst($get->fullName);?></h3>
                    <h4 class="dsply-blck-rgt rev-dte"><?php echo time_elapsed_string($get->crd);?></h4>
                </div>

            <?php } } else{ ?>
                <div class=" dsply-block">
                    <h3 class="dsply-blck-lft rev-nme"><?php echo ucfirst($get->fullName);?></h3>
                    <h4 class="dsply-blck-rgt rev-dte"><?php echo time_elapsed_string($get->crd);?></h4>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
            <?php 
            if($get->reviewType == 2) {

                $myDetail = '';
                if($get->eventOrganizer == $this->session->userdata('userId')){

                    $myDetail = base_url('home/event/myEventDetail/').encoding($get->eventId).'/';

                }else{

                    if($get->ownerType == 'Administrator'){

                        $eventMemId = encoding($get->eventMemId);
                        $query_str = '/?eventMemId='.$eventMemId;
                        $myDetail = base_url('home/event/eventRequestDetail/').encoding($get->eventId).$query_str;

                    }elseif($get->ownerType == 'Shared Event'){

                        $compId = encoding($get->compId);
                        $query_str = '/?compId='.$compId;
                        $myDetail = base_url('home/event/eventRequestDetail/').encoding($get->eventId).$query_str;

                    } 
                }                

                if($userId == $this->session->userdata('userId')){

             ?>
                        <a href="<?php echo $myDetail;?>"><h6 class="dsply-blck-lft rev-nme rev_evnt_nme"><?php echo ucfirst($get->eventName);?><span>:- <?php echo date('d M, Y',strtotime($get->eventStartDate));?></span></h6></a>
                        <div class="clearfix"></div>

                    <?php } else{ ?>

                        <h6 class="dsply-blck-lft rev-nme rev_evnt_nme"><?php echo ucfirst($get->eventName);?><span>:- <?php echo date('d M, Y',strtotime($get->eventStartDate));?></span></h6>
                        <div class="clearfix"></div>

            <?php } } ?>

            <p class="str-sec-pra">

                <?php 

                $count = $get->rating;

                for($i=1;$i<=$count;$i++){ ?>

                    <span class="fa fa-star"></span> 

                <?php 

                }

                $minCount = 5-$count; 
                
                for($j=1;$j<=$minCount;$j++){ ?>

                    <span class="fa fa-star-o"></span>

                <?php

                }

                ?>

            </p>
            <p><?php echo $get->comment;?></p>
        </div>
    </div>
<?php } } elseif ($page == 0){

    echo "<div class='notFound'><h3>".lang('no_record_found')."</h3></div>"; ?>          

<?php } ?>
<input type="hidden" id="total-count" value="<?php echo count($appReviewList);?>">