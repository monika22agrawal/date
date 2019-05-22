<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url().APP_FRONT_ASSETS ?>custom/css/jquery.emojipicker.css">
<link type="text/css" rel="stylesheet" href="<?php echo base_url().APP_FRONT_ASSETS ?>custom/css/jquery.emojipicker.tw.css">

<style type="text/css">
    .pre-img-modal{display:none;position:fixed;z-index:1;padding-top:12%;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.9);padding-top: 50px;}
    .modal-content-cht{margin:auto;display:block;width:80%;max-width:700px;}
    #myModal{z-index:99999;}
    #myModal .close-img-modal{opacity:1;}
    
.cicle-i{float: right;font-size: 12px;margin-top: 22px;color:#a51d29}
@-webkit-keyframes zoom{from{-webkit-transform:scale(0)}
to{-webkit-transform:scale(1)}
}
@keyframes zoom{from{transform:scale(0)}
to{transform:scale(1)}
}
.close-img-modal{position:absolute;top:15px;right:35px;color:#f1f1f1;font-size:40px;font-weight:bold;transition:0.3s;}
.close-img-modal:hover,
.close-img-modal:focus{color:#bbb;text-decoration:none;cursor:pointer;}

@media only screen and (max-width:700px){.modal-content{width:100%;}}
/*--added css--*/
.log_div img {
    border-radius: 100%;
    width: 120px;
    height: 120px;
}
.nr-usr-parnt{
  width:100%;
}
.text-brk{
    white-space: pre-wrap;
}
.norecordSec {
    min-height: calc(100vh - 68px);
    padding: 50px 0;
    display: inline-block;
    width: 100%;
}
.noChatFound {
    text-align: center;
}
.noChatFound img {
    max-width: 100px;
}
.noChatFound h2 {
    font-weight: 600;
    font-size: 20px;
    margin-top: 20px;
    color: #828282;
}
.noChatFound p {
    margin-top: 15px;
    color: #828282;
    font-size: 16px;
    line-height: 28px;
}
</style>
<div class="blnk-spce"></div>
<div class="wraper chatWraper">
    <!--================Shop left sidebar Area =================-->
    <div id="no-chat-user" style="display: none;" class="norecordSec">
        <div class="col-md-12">
            <div class="noChatFound">
                <img src="<?php echo AWS_CDN_FRONT_IMG; ?>ico_no_chat.png">
                <h2><?php echo lang('no_message'); ?></h2>
                <p><?php echo lang('no_chat_message'); ?></p>
            </div>
        </div>
    </div>
    <section id="chat-user" class="shop_area product_details_main blog_grid_area evnt-dtl back-clr">
        <div class="row padding-row">
            <div class="col-lg-10 col-md-12 col-lg-offset-1 col-sm-12 col-xs-12">
                <div class="row" >
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 frst-blck-pad side_msg">
                        <div class="chatUserList cstm-chat-list">
                            <div class="chat-massage-block">
                                <div class="searchwrap">
                                    <div class="search_widget">
                                        <div class="input-group cstm-srch-wdth">
                                            <span class="fa fa-arrow-left newMsg-back" aria-hidden="true"></span>
                                            <input type="text" id="searchText" name="searchText" class="form-control" placeholder="<?php echo lang('search_here_placeholder');?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="scrollbox">
                                    <div class="scrollbox-content slimScrollDiv csScroll cstm-scroll-div pt-0 pb-0" id="chatHistory"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 scnd-blck-pad">
                        <div class="chat-user white chatChUser chat-disable show-hide-chat">
                            <div class="ChatUsHead cstm-chathed">                               
                                <div id="userinfo"></div>
                                <!-- for one to one chat menu -->
                                <div id="user_to_user" class="chatOption cstm-chat-optn" style="display:none;">
                                    
                                    <div class="dropdown mrgn-rgt2">

                                        <span class="fa fa-comment newMsg" aria-hidden="true"></span>

                                        <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>

                                        <ul class="dropdown-menu actn-icons">
                                            <li>
                                                <a href="javascript:void(0)" style="display: none;" id="block" ><span class="fa fa-ban"></span><?php echo lang('chat_user_block'); ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" style="display: none;" id="unblock" ><span class="fa fa-ban"></span> <?php echo lang('chat_user_unblock'); ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" onclick="delateChat();"><span class="fa fa-trash"></span><?php echo lang('chat_delete'); ?></a>
                                            </li>
                                        </ul>

                                    </div>

                                </div>
                                <!-- for one to one chat menu -->

                                <!-- for event chat menu -->
                                <div id="user_to_event" class="chatOption cstm-chat-optn" style="display:none;"> 

                                    <div class="dropdown mrgn-rgt2">

                                        <span class="fa fa-comment newMsg" aria-hidden="true"></span>

                                        <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </a>

                                        <ul class="dropdown-menu actn-icons">
                                            <li>
                                                <a href="javascript:void(0);" onclick="deleteGroupChat();"><span class="fa fa-trash"></span><?php echo lang('chat_clear'); ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" style="display: none;" id="mute" ><span class="fa fa-bell"></span><?php echo lang('chat_mute'); ?></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" style="display: none;" id="unmute" ><span class="fa fa-bell-slash"></span><?php echo lang('chat_unmute'); ?></a>
                                            </li>                                            
                                            <li>
                                                <a href="javascript:void(0);" id="notify" onclick="getGroupMembers();"><span class="fa fa-info-circle"></span><?php echo lang('chat_group_info'); ?></a>
                                            </li>                              
                                        </ul>

                                    </div>

                                </div>
                                <!-- for event chat menu -->
                            </div>

                            <div class="clearfix"></div>

                            <div class="slimScrollDiv csScroll cstm-scroll-div" id="slimScrollDiv">
                                <div class="scrollerchat">
                                    <div class="pad-all">
                                        <ul class="list-unstyled media-block message"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="block_data" align="center" class="hidden"></div>
                            <div class="panel-footer bottom_wrapper" style="display:none;">
                                <div class="message_input_wrapper">
                                    <div class="form-group regfld cht-text form-wrapper1 mb-0">
                                        <textarea class="form-control emPicker emojiable-option" id="message" placeholder="<?php echo lang('chat_type_msg');?>"></textarea>
                                    </div>
                                </div>
                                <span class="uploadicon">
                                    <input accept="image/*" style="display:none;" type="file" name="file-1" id="fileInput">
                                    <i style="cursor: pointer;" onclick="document.getElementById('fileInput').click()" class="fa fa-image  fa-2x send-file" aria-hiddennn="true"></i>
                                </span>
                                <div class="send_message">
                                    <button type="button" onclick="sendMsg();" class="btn form-control login_btn"><span><?php echo lang('chat_send'); ?></span><i class="fa fa-send"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Shop left sidebar Area =================-->
</div>
 <!--side sliders for chatting-->
<section id="sidebar-right" class="sidebar-menu sidebar-right">
    <div class="Notification" id="notifyOpen">
        <div class="notifyList">
            <div class="chat-user white chatChUser">
                <div class="ChatUsHead grp-pad">
                    <div class="dsply-block">
                        <div id="groupDetail"></div>
                    </div>               
                    <div class="chatOption">                        
                        <div class="dsply-blck-lft bck-arow">
                            <a href="javascript:void(0);"><i id="sidebar_close_icon" class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="blog_comment_list scoll-lst grp-pad">

                    <div id="groupMember"></div>
                   
                </div>  
            </div>
        </div>
    </div>
</section>
<div class="sidebar_overlay sidebar_over"></div>
<div class="sidebar_overlay1"></div>
<div class="get_message" style="display:none;"></div>

<!-- image preview modal -->
<div id="myModal" class="pre-img-modal">
    <span class="close-img-modal">&times;</span>
    <img class="modal-content-cht" id="img01">
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script type="text/javascript" src="<?php echo base_url().APP_FRONT_ASSETS ?>custom/js/emojis_picker.js"></script>

<script type="text/javascript">
    
    $('.emPicker').emojiPicker();
    $(".emojiable-option").on("keyup", function () { });
   
    let noRec       = "<?php echo lang('no_record_found'); ?>";
    let userBlock   = "<?php echo lang('user_block_msg'); ?>";
    let userByBlock = "<?php echo lang('user_block_by_msg'); ?>";
    let sureMsg     = "<?php echo lang('sure_msg'); ?>";
    let blockMsg    = "<?php echo lang('block_msg'); ?>";
    let yesBlockMsg = "<?php echo lang('yes_block_msg'); ?>";
    let noBlockMsg  = "<?php echo lang('no_cancel_msg'); ?>";
    let notRecover  = "<?php echo lang('not_able_recover_msg'); ?>";
    let cantMsg     = "<?php echo lang('cant_send_msg'); ?>";
    let cantRecMsg  = "<?php echo lang('cant_rec_noti_msg'); ?>";
    let onlineStatus = "<?php echo lang('online'); ?>";
    let member        = "<?php echo lang('member'); ?>";
    let lastSeen        = "<?php echo lang('last_seen'); ?>";



    let imageUrl    = "<?php echo AWS_CDN_EVENT_PLACEHOLDER_IMG ?>";
    let defaultUser = "<?php echo AWS_CDN_USER_PLACEHOLDER_IMG ?>";
    let senderId    = '<?php echo $this->session->userdata('userId');?>';
    let senderName  = '<?php echo $myDetail->fullName; ?>';
    let senderImg   = '<?php echo $myDetail->profileImage; ?>';
    let key         = '<?php echo NOTIFICATION_KEY; ?>';
    let chatType    = '<?php echo (isset($_GET['type']) && !empty($_GET['type'])) ? $_GET['type'] : '' ; ?>';
    
    let receiverId  = '<?php echo (isset($_GET['userId']) && !empty($_GET['userId'])) ? decoding($_GET['userId']) : $this->session->userdata('userId');?>';

    let checkHistory  = (senderId == receiverId) ? 'me' : '';

    $(document).ready(function(){

        //slider on group chat
        $("#notify").click(function(){
          $("#sidebar-right2").removeClass("member");
            openClose();
        });
        $(".sidebar_overlay").click(function(){
            openClose();
        });
        $("#sidebar_close_icon").click(function(){
            openClose();
        });
        function openClose(){    
            $("#sidebar-right").toggleClass("sidebar-open");
            $(".sidebar_overlay").toggleClass("sidebar_overlay_active"); 
            $("body").toggleClass("hide_overflow"); 
        }

        setValue('senderId',senderId);
        setValue('receiverId',receiverId);
        setValue('senderName',senderName);
        setValue('senderImg',senderImg);

        getChatHistory();

        if(chatType == 'user'){
            
            changeChatUser('',receiverId);
        
        } else if(chatType == 'event'){
            
            changeGroupChat('',receiverId)

        } else {

            setTimeout(function(){        

                $(".first").click();
           
            }, 3000);
        }
       

        $(".newMsg").click(function() {

            $(".side_msg").css({ "left": "0" });

        });

        $(".newMsg-back").click(function() {

            $(".side_msg").css({ "left": "-100%" });

        });

        var width = $(window).width();

        if( width < 767 ){  

            $(".newMsg-back").click(function() {
                $(".side_msg").css({ "left": "-100%" });
            });
            $("#chatHistory").click(function() {
                $(".side_msg").css({ "left": "-100%" });
            });
        }
    });

</script>