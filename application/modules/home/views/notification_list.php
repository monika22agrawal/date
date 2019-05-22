<?php $url = base_url('home'); if(!empty($notiList)){

    foreach($notiList as $list){

        if(!empty($list->notificationType)){

            if($list->notificationType == "friend_request" || $list->notificationType == "accept_request" || $list->notificationType == "add_like" || $list->notificationType == "add_favorite"){

                $url = base_url('home/user/userDetail/').encoding($list->message->referenceId).'/'; // other user profile

            } else if($list->notificationType == "create_appointment" || $list->notificationType == "delete_appointment"){

                $url = base_url('home/appointment/'); // appoinment listing

            } else if($list->notificationType == "confirmed_appointment" || $list->notificationType == "finish_appointment" || $list->notificationType == "review_appointment"){

                $url = base_url('home/appointment/viewAppOnMap/').encoding($list->message->referenceId).'/'; // appointment details
                
            } else if($list->notificationType == "create_event" || $list->notificationType == "companion_payment" ||
                    $list->notificationType == "join_event" || $list->notificationType == "event_payment" ||
                    $list->notificationType == "share_event" || $list->notificationType == "companion_accept" || $list->notificationType == "companion_reject" || $list->notificationType == "review_event" ){

                if($this->session->userdata('userId') == $list->message->createrId){

                    $url = base_url('home/event/myEventDetail/').encoding($list->message->referenceId).'/'; // for my event detail

                }else {
                    
                    $compId = $eventMemId = '';
                    if(!empty($list->message->eventMemId)){
                        $eventMemId = encoding($list->message->eventMemId);
                        $query_str = '/?eventMemId='.$eventMemId;
                    }elseif(!empty($list->message->compId)){
                        $compId = encoding($list->message->compId);
                        $query_str = '/?compId='.$compId;
                    }

                    $url = base_url('home/event/eventRequestDetail/').encoding($list->message->referenceId).$query_str.'/'; // event request details
                    
                }
            }
        }
        if(!filter_var($list->image, FILTER_VALIDATE_URL) === false) { 
            $img = $list->image;
        }else if(!empty($list->image)){ 
            $img = AWS_CDN_USER_THUMB_IMG.$list->image;
        } else{                    
            $img = AWS_CDN_USER_PLACEHOLDER_IMG;
        }
    ?>
    
    <li>
        
            <div class="author_posts_inners frnd-btn">
                <div class="media">
                    <div class="media-left prfle-fvrte">
                        <a href="<?php echo base_url('home/user/userDetail/').encoding($list->userId);?>"><img src="<?php echo $img; ?>" class="brdr-img-crcle" alt="<?php echo AWS_CDN_USER_PLACEHOLDER_IMG; ?>"></a>
                    </div>
                    
                    <div class="media-body prfle-fvrte-info2">                        
                        <div class="dsply-block">
                            <a href="<?php echo $url;?>"><h3 class="dsply-blck-lft mb-5"><!-- <span>Judi Tao</span> --> <?php echo $list->message->body; ?></h3></a>
                        </div>
                        <div class="clearfix"></div>
                        <h4 class="fvrte-stus2 pt-5"><?php echo $list->timeElapsed;?></h4>
                    </div>                    
                </div>                                                    
            </div>
        
    </li>
<?php } }else{
    echo "<div class='notFound'><h3>".lang('no_noti_msg')."</h3></div>" ; 
} ?>
