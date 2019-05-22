let notifications = 'webnotification';

var setValue = function(key,value){

    if($('#'+key).length==0){
        $('<input>').attr({type: 'hidden', id: key, name: key,value:value}).appendTo('head');
    }else{
        $("#"+key).val(value);
    }
    return true;
}

var getValue = function(key){
    return $("#"+key).val();
}

var removeValue = function(key){
    return $("#"+key).val('');
}

var arrayshort = function(data){

    var array = [];
    $.each(data, function(key, value) {
        array.push(value);
    });
    return array.sort(function(a, b) {
    var a1 = a.timestamp,
        b1 = b.timestamp;
    if (a1 == b1) return 0;
        return a1 < b1 ? 1 : -1;
    });
}

var typingTimer;                //timer identifier
var doneTypingInterval = 100;  //time in ms, 5 second for example
var $input = $('#searchText'); // get input

//on keyup, start the countdown
$input.on('keyup', function () {

    clearTimeout(typingTimer);
    typingTimer = setTimeout(getChatHistory, doneTypingInterval); //"getChatHistory" is function for call
});

//on keydown, clear the countdown 
$input.on('keydown', function () {

    clearTimeout(typingTimer);

});

//for sending msg on enter press(key event)
$(document).keypress(function (e) {

    if (e.which == 13 && !e.shiftKey) {

        e.preventDefault();
        var s = $(this).val();
        $(this).val(s + "\n");
        sendMsg();
        $('#message').val('');       
    }
});

//for sending messages
function sendMsg(downloadURL='') {
    
    let chatType   = getValue('chatType');

    if( chatType == 'event' ){

        imageUrl = downloadURL ? downloadURL : '';

        sendGroupMsg(imageUrl);

    } else {

        let receiverId   = getValue('receiverId');
        let senderId     = getValue('senderId'); 
        let senderName   = getValue('senderName');
        let senderImg    = getValue('senderImg'); 
        let message      = $.trim($('#message').val()); // get textarea message

        let oppoName     = getValue('name');
        let oppoImg      = getValue('img');

        let status       = getValue('onlineStatus');

        if(message.length > 0 || downloadURL!=''){

            $('#message').val('');

            imageUrl = downloadURL ? downloadURL : '';

            image = downloadURL ? 1 : 0;

            var msgData = {

                uid             : senderId,
                message         : message,
                name            : '',
                profilePic      : '',
                image           : image,
                imageUrl        : imageUrl,
                deleteby        : "",
                lastMsg         : senderId,
                type            : 'user',
                firebaseId      : "",
                firebaseToken   : "",
                timestamp       : Date.now()            
            };

            msgData.isMsgReadTick = (status=="online") ? 1 : 0;
            firebase.database().ref().child('chat_rooms').child(senderId).child(receiverId).push(msgData);

            //msgData.isMsgReadTick = 2;
            firebase.database().ref().child('chat_rooms').child(receiverId).child(senderId).push(msgData);

            delete msgData.isMsgReadTick;

            msgData.uid                 = receiverId;
            msgData.name                = oppoName;
            msgData.profilePic          = oppoImg; 
            msgData.unreadCount         = 0;       
            msgData.isGroup             = false;       
            msgData.groupUserCount      = 0;       
     
            firebase.database().ref('/chat_history/' + senderId).child(receiverId).set(msgData);
          
            msgData.uid         = senderId;
            msgData.name        = senderName;
            msgData.profilePic  = senderImg;

            setUnread(senderId,msgData);

            let token = getValue('token');

            //if(token){

                notification = {

                    'title'         : senderName,
                    'body'          : message,
                    'type'          : 'chat',
                    'sender_name'   : senderName,
                    'message'       : message,
                    'time'          : Date.now(),
                    'opponentChatId': senderId,
                    'click_action'  : 'ChatActivity',
                    'sound'         : 'default'
                }

                senNotifcation(token,notification,notification);

           // }else{

                let webNotification = {
                                
                    'title' : senderName,
                    'body'  : downloadURL ? 'Image' : message,
                    'url'   : BASE_URL+'home/chat?uId='+ getValue('senderId') + '&type=user'
                };

                senWebNotifcation(receiverId,webNotification);
            //}

            $('#slimScrollDiv').animate({scrollTop: $('#slimScrollDiv').prop("scrollHeight")}, 1);

        }
    }    
}

function setUnread(senderId,msgData){

    let receiverId = getValue('receiverId');

    firebase.database().ref("chat_history").child(receiverId).child(senderId).once('value', function(snapshot) { 

        msgData.unreadCount = 1;

        if(snapshot.val()){

            let count = Number(snapshot.val().unreadCount) + Number(1);
            msgData.unreadCount = count;
        }
        firebase.database().ref('/chat_history/' + receiverId).child(senderId).set(msgData);
    });
}

function updateUnreadStatus(){      

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    var query = firebase.database().ref("chat_history").child(senderId).child(receiverId);

    query.once('value', function(snapshot) {

        if(snapshot.val()){

            if(snapshot.val().unreadCount > 0){
                            
                firebase.database().ref('chat_history').child(senderId).child(receiverId).child('unreadCount').set(0);
            }           
        }       
    });
}

// to get all user's chat history list
function getChatHistory(){

    let receiverId  = getValue('receiverId');
    let senderId    = getValue('senderId');
    let senderName    = getValue('senderName');

    firebase.database().ref("chat_history").child(senderId).on('value', function(snapshot) {

        $('#chatHistory').html('');

        if(snapshot.val()){

            rdata = arrayshort(snapshot.val());

            var str2 = $.trim($('#searchText').val());
            var str2 = str2.toLowerCase();

            if(str2){

                var rdata = rdata.filter(function(item){

                    userName = (item.name).toLowerCase()
                    return userName.indexOf(str2) != -1;
                });
            }

            $('#chatHistory').html('');

            var i = 1;

            $.each(rdata, function(key, value) {
                
                if(typeof value !== "undefined" || value != null){

                    var oneMsg = value;

                    oneMsg.timestamp = moment(oneMsg.timestamp).format('YYYY-MM-DD, hh:mm a');
                    senderChatType = '';
                    if(oneMsg.message){
                        senderChatType = (oneMsg.type == 'user') ? '<i class="fa fa-image"></i> Image' : oneMsg.message + ' : <i class="fa fa-image"></i> Image';
                    }

                    message = (oneMsg.image == 1) ? senderChatType : (oneMsg.message.length > 50) ? oneMsg.message.substr(0, 50) + '...' : oneMsg.message;

                    first = (i==1) ? 'first' : '';

                    defaultImg = (oneMsg.type == 'user') ? defaultUser : imageUrl;

                    oneMsg.profilePic = oneMsg.profilePic ? oneMsg.profilePic : defaultImg;

                    if(oneMsg.profilePic.includes("thumb") == false){
                        oneMsg.profilePic = oneMsg.profilePic.replace("/profile/", "/profile/thumb/");
                        oneMsg.profilePic = oneMsg.profilePic.replace("/event/", "/event/thumb/");
                    }

                    let msgCountChatType = (oneMsg.type == 'user') ? '<div class="favoriteStar"><p class="msg-count">'+oneMsg.unreadCount+'</p></div>' : '<div class="favoriteStar"><p class="msg-count"></p></div>';

                    let showCount = (oneMsg.unreadCount > 0) ? msgCountChatType : ""; 

                    let onclick =  (oneMsg.type == 'user') ? 'onclick="changeChatUser(this)"' : 'onclick="changeGroupChat(this)"';              

                    let usName = ((oneMsg.name).length > 20)  ? oneMsg.name.substring(0, 20)+'...' : oneMsg.name;

                    var htmlData = '<a href="javascript:void(0);"><div data-token="' + oneMsg.firebaseToken + '" data-uid="' + oneMsg.uid + '" data-name="' + usName + '" data-img="' + oneMsg.profilePic + '" class="notice notice-success active-user '+first+'" id="historyIndex'+i+'" '+onclick+' ><div class="media"><div class="media-left"><div class="user-image user-image-real"><img src=' + oneMsg.profilePic + ' /><div id="online'+ oneMsg.uid +'" class=""></div></div></div><div class="media-body"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><h4 class="media-heading cstm-media-heading">' + usName + '</h4><div id="msg'+oneMsg.uid+'" class="serviseslead ">' + message + '</div><div id="typing'+oneMsg.uid+'" class="serviseslead clr-red"></div></div></div></div></div>'+showCount+'</div></a>';

                    $('#chatHistory').append(htmlData);
                    
                    if(oneMsg.type == 'user'){

                        getHistoryOnline(oneMsg.uid);
                        isTyping(oneMsg.uid);                    
                    }

                    i++;
                }
            });

            if(rdata.length == 0){

                $('#chatHistory').html('<center style="color:#a51d29;" id="noMsg" >'+noRec+'</center>');
            }

        }else{

            if(checkHistory=='me') {

                $('#no-chat-user').show();
                $('#chat-user').hide();
                $('#tl_admin_loader').hide();
            }else{

                $('#no-chat-user').hide();
                $('#chat-user').show();
            }
        }
        //$('#tl_admin_loader').hide();
    });
} 

// to click to change another user for chating
function changeChatUser(e,opponentId='') {
   
    let senderId = getValue('senderId');
    setValue('chatType','user');

    if(opponentId == ""){

        let name        = $(e).data('name');
        var img         = $(e).data('img') ? $(e).data('img') : defaultUser;
        let receiverId  = $(e).data('uid');
        let token       = $(e).data('token');

        if(img.includes("thumb") == false){
            img = img.replace("/profile/", "/profile/thumb/");
        }

        setValue('name',name);
        setValue('img',img);
        setValue('receiverId',receiverId);
        setValue('token',token);

        $('#userinfo').html('<a href="'+BASE_URL+'home/user/userDetail?userId='+receiverId+'"><div class="media cstm-media"><img class="mr-3" src= ' + img + ' id="img'+receiverId+'"><div class="media-body cstm-media-body"><h5 class="mt-5" id="uName'+receiverId+'">' + name + '</h5><p id="onlineStatus'+receiverId+'" ></p><p id="userTyping'+receiverId+'" ></p></div></div></a>');

        firebase.database().ref("users").child(receiverId).once('value', function(snapshot) {

            if(snapshot.val()){

                userDetail = snapshot.val();

                var userImg = userDetail.profilePic ? userDetail.profilePic : defaultUser;

                if(userImg.includes("thumb") == false){
                    userImg = userImg.replace("/profile/", "/profile/thumb/");
                }
                let usName = ((userDetail.name).length > 20)  ? userDetail.name.substring(0, 20)+'...' : userDetail.name;
                setValue('name',usName);
                setValue('img',userImg);
                setValue('token',userDetail.firebaseToken);

                $("#uName"+receiverId).html(usName);
                $("#img"+receiverId).attr('src',userImg);
            }
        });

    }else{

        $('#userinfo').html('<a href="'+BASE_URL+'home/user/userDetail?userId='+opponentId+'"><div class="media cstm-media"><img class="mr-3" src="" id="img'+opponentId+'"><div class="media-body cstm-media-body"><h5 class="mt-5" id="uName'+opponentId+'"></h5><p id="onlineStatus'+opponentId+'" ></p><p id="userTyping'+opponentId+'" ></p></div></div></a>');

        firebase.database().ref("users").child(opponentId).once('value', function(snapshot) {

            if(snapshot.val()){

                userDetail = snapshot.val();

                var userImg = userDetail.profilePic ? userDetail.profilePic : defaultUser;

                if(userImg.includes("thumb") == false){
                    userImg = userImg.replace("/profile/", "/profile/thumb/");
                }

                let usName = ((userDetail.name).length > 20)  ? userDetail.name.substring(0, 20)+'...' : userDetail.name;
                setValue('name',usName);
                setValue('img',userImg);
                setValue('receiverId',opponentId);
                setValue('token',userDetail.firebaseToken);

                let receiverId  = getValue('receiverId');
                $("#uName"+receiverId).html(usName);
                $("#img"+receiverId).attr('src',userImg);
                //$('#userinfo').html('<img class="mr-3" src= ' + userImg + '><div class="media-body cstm-media-body"><h5 class="mt-5">' + userDetail.name + '</h5><p id="onlineStatus'+receiverId+'" ></p><p id="userTyping'+receiverId+'" ></p></div>');
            }
        });
    }

    setTimeout(function(){

        if(opponentId==senderId) {  
            
            $(".first").click();
        }  
        clearTyping();
    }, 3000);

    setTimeout(function(){

        $('#tl_admin_loader').hide();

    }, 2000);

    setValue('startFrom',0);

    let receiverId  = getValue('receiverId');

    $(".panel-footer").attr("id", "send_msg"+receiverId);

    $(".block_data").attr("id", "block_messgae"+receiverId);
    $("#send_msg"+receiverId).hide();

    let chatRoom = (senderId > receiverId) ? receiverId+'_'+senderId : senderId+'_'+receiverId;

    setValue('chatRoom',chatRoom);

    if( opponentId != senderId ) {  

        getOnline(receiverId);
    }

    getBlock();
    $('.message').html('');

    isTyping(receiverId);
    getChat(receiverId);

    $("#message").focus();
    $('#message').val('');
    $('#user_to_user').show();  
    $('#user_to_event').hide();  
}

function getChat(receiverId){

    checkReadMsgStatus();

    let senderId    = getValue('senderId');
    let startFrom   = Number(getValue('startFrom'));

    var query = firebase.database().ref("chat_rooms").child(senderId).child(receiverId).limitToLast(15);

    if(startFrom){

        var query = firebase.database().ref("chat_rooms").child(senderId).child(receiverId).orderByChild("timestamp").endAt(startFrom).limitToLast(15);
    }

    query.on('value', function(snapshot) {

        var chat = snapshot.val();
        setValue('startFrom',0);

        if(chat){

            //$('.message').html('');

            if(getValue('startFrom') == 0){

                var keys = Object.keys(chat);
                k = keys[0];

                setValue('startFrom',chat[k].timestamp);
            }

            var page = 1;

            if(getValue('receiverId') == receiverId){

                $.each(chat, function(key, value) {

                    var oneMsg = value;

                    oneMsg.showtimestamp    = moment(oneMsg.timestamp).format('hh:mm A');
                    timestamp               = (oneMsg.timestamp);
                    oneMsg.timestamp        = moment(oneMsg.timestamp).format('YYYY-MM-DD, hh:mm A');

                    let myMsg = ( oneMsg.uid != senderId ) ? '' : 'speech-right';

                    if(oneMsg.isMsgReadTick == 0){          // if user offline or net off

                        var tick = '<i class="fa fa-check"></i>';

                    }else if(oneMsg.isMsgReadTick == 1){    // if user online but not in chatroom

                        var tick = '<i class="fa fa-check"></i><i class="fa fa-check fa-fw"></i>';

                    }else{                                  // if user online & in chatroom

                        var tick = '<i class="fa fa-check clr-tick"></i><i class="fa fa-check fa-fw clr-tick"></i>';
                    }

                    tick = (oneMsg.uid == senderId) ? tick : '';

                    message = (oneMsg.image == 1) ? '<img class="img-cursor" onclick="showImage(this.src);" src=' + oneMsg.imageUrl + ' alt="" height="130" width="130"/>' : oneMsg.message;

                    imgCls = (oneMsg.image == 1) ? 'speech-img' : 'speech';
                    
                    $('#'+timestamp).remove();

                    var msgHtml = '<li class="mb-15" id=' +timestamp + '> <div class="media-body pl-15 pr-15 '+myMsg+'"> <div class="'+imgCls+'" data-toggle="tooltip" title="' + oneMsg.timestamp + '"> <p class="text-brk">' + message + '</p>  </div><p class="speech-time"> '+ tick + oneMsg.showtimestamp + '</p> </div> </li>';

                    (startFrom == 0) ? $('.message').append(msgHtml) : $('.get_message').append(msgHtml);
                    
                    page++;
                    
                });
            }

            if(page <= 15){

                setValue('startFrom',0);
            }

            if(startFrom == 0){

                $('#slimScrollDiv').animate({scrollTop: $('#slimScrollDiv').prop("scrollHeight")}, 1);

            }else{

                var getMsg = $('.get_message').html();
                $('.message').prepend(getMsg);
                $('.get_message').html('');
                $("#slimScrollDiv").animate({scrollTop: $("#slimScrollDiv").height()}, 1);
            }

        }else{

            $('.message').html('');
        }

        updateUnreadStatus();
    });
}

$('#slimScrollDiv').scroll(function() {

    if ($('#slimScrollDiv').scrollTop() == 0) {

        if ( getValue('startFrom') != 0 ) {

            receiverId = getValue('receiverId');
            chatType = getValue('chatType');

            if (chatType == 'user') {

                getChat(receiverId);

            }else{

                getEventChat(receiverId)
            }
            
        }
    }
}); 

// to upload imafe
$("#fileInput").change(function(e) {

    var file = e.target.files[0];

    var storageRef = firebase.storage().ref();
    var uploadTask = storageRef.child('chat_photos_apoim/' + Date.now()).put(file);

    $('#tl_admin_loader').show();

    //$('.message').append('<li class="mb-15"> <div class="media-body pl-15 pr-15 speech-right"> <div class="" > <p class="img-cursor"><img src=' + imageUrl + ' alt="loading..." height="100" width="130"></div><span class="message-time pull-right"> </span></p>  </div></li>');

    $('#slimScrollDiv').animate({scrollTop: $('#slimScrollDiv').prop("scrollHeight")}, 1);

    uploadTask.on('state_changed', function(snapshot) {

        var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;

        console.log('Upload is ' + progress + '% done');

        switch (snapshot.state) {

            case firebase.storage.TaskState.PAUSED:

                console.log('Upload is paused');
                break;

            case firebase.storage.TaskState.RUNNING:

                console.log('Upload is running');
                break;
        }

    }, function(error) {

        alert(error)

    }, function() {

        uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {

            sendMsg(downloadURL);
            $('#tl_admin_loader').hide();
        });
    });  
        
    $('#fileInput').val('');
});

//for image preview
function showImage(imgPath){

    var modal           = document.getElementById('myModal');    
    var modalImg        = document.getElementById("img01");

    modal.style.display = "block";
    modalImg.src        = imgPath;

    var span = document.getElementsByClassName("close-img-modal")[0];

    // When the user clicks on <span>, close the modal
    span.onclick = function() { 
        modal.style.display = "none";
    }
}

// set user's online status
function getOnline(receiverId){

    setValue('onlineStatus','offline');

    let query = firebase.database().ref("online").child(receiverId);

        query.on('value', function(snapshot) {

        if(snapshot.val()){

            var onlineData = snapshot.val();
            setValue('lastSeen',onlineData.timestamp);

            setValue('onlineStatus',onlineData.lastOnline);

            if(onlineData.lastOnline == 'online'){

                $("#onlineStatus"+receiverId).text(onlineStatus);
                $("#online"+receiverId).addClass('gren-crcl-online');      // show online on chat history

                checkOnlineStatus(receiverId);

            }else{

                let showtimestamp   = moment(onlineData.timestamp).fromNow();
                $("#onlineStatus"+receiverId).text(lastSeen+' '+showtimestamp);

                $("#online"+receiverId).removeClass('gren-crcl-online');   // hide online on chat history
            }
        }
    });
}

// set user's online status
function getHistoryOnline(receiverId){


    let query = firebase.database().ref("online").child(receiverId);

        query.on('value', function(snapshot) {

        if(snapshot.val()){

            var onlineData = snapshot.val();

            if(onlineData.lastOnline == 'online'){

                $("#online"+receiverId).addClass('gren-crcl-online');      // show online on chat history

            }else{

                $("#online"+receiverId).removeClass('gren-crcl-online');   // hide online on chat history
            }
        }
    });
}

function checkOnlineStatus(receiverId){     // when message delivered then show double tick

    let senderId    = getValue('senderId');
    var query       = firebase.database().ref("chat_rooms").child(receiverId).child(senderId);

    query.on('child_added', function(snapshot) {        
        if(snapshot.val()){
            if(getValue('onlineStatus') == 'online'){

                if(snapshot.val().isMsgReadTick == 0){

                    firebase.database().ref('chat_rooms').child(receiverId).child(senderId).child(snapshot.key).child('isMsgReadTick').set(1);
                }           
            }
        }
    }); 
}

function checkReadMsgStatus(){      // when message delivered & read then show double tick with green color

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    var query = firebase.database().ref("chat_rooms").child(receiverId).child(senderId);

    query.on('child_added', function(snapshot) {
     
        if(snapshot.val().uid==receiverId){
            setTimeout(function() {
                firebase.database().ref('chat_rooms').child(receiverId).child(senderId).child(snapshot.key).child('isMsgReadTick').set(2);
            }, 1500);       
        }
    });
}

var getBlock = function(){

    let chatRoom    = getValue('chatRoom');
    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    firebase.database().ref("block_users").child(chatRoom).on('value', function(snapshot) {

        if (snapshot.exists()) {

            if(snapshot.val().blockedBy == senderId || snapshot.val().blockedBy == "Both"){

                $('#block').hide();
                $('#unblock').show();
                $("#block_messgae"+receiverId).html(userBlock);

            }else{

                $('#block').show();
                $("#block_messgae"+receiverId).html(userByBlock);
            }

            $("#block_messgae"+receiverId).show();
            $("#send_msg"+receiverId).hide();

        } else{

            $('#unblock').hide();
            $('#block').show();
            $("#send_msg"+receiverId).show();
            $("#block_messgae"+receiverId).hide();
        }
    });
}

// for blocking user's
$("#block").click(function(){

    $('#unblock').show();
    $('#block').hide();

    let chatRoom = getValue('chatRoom');
    let senderId = getValue('senderId');

    swal({

        title               : sureMsg,
        text                : blockMsg,
        type                : "warning",
        showCancelButton    : true,
        confirmButtonColor  : '#a51d29',
        confirmButtonText   : yesBlockMsg,
        cancelButtonText    : noBlockMsg,
        closeOnConfirm      : true,
        closeOnCancel       : true
    },

    function(isConfirm) {

        if (isConfirm) {

            firebase.database().ref("block_users").child(chatRoom).once('value', function(snapshot) {

                if (snapshot.exists()) {

                    firebase.database().ref('block_users').child(chatRoom).child('blockedBy').set('Both');

                } else {

                    var blockData = {
                        blockedBy: senderId
                    };
                    firebase.database().ref('block_users').child(chatRoom).set(blockData);
                }
           });
        }
    });
});

// for unblocking user's
$("#unblock").click(function(){

    $('#unblock').hide();
    $('#block').show();

    let chatRoom    = getValue('chatRoom');
    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    firebase.database().ref("block_users").child(chatRoom).once('value', function(snapshot) {

        var block_id = snapshot.val().blockedBy;

        if (block_id == 'Both') {

            block_id = receiverId;
            firebase.database().ref().child('block_users').child(chatRoom).child('blockedBy').set(block_id);

        } else {

            if (block_id == senderId) {

                firebase.database().ref().child('block_users').child(chatRoom).set(null);
            }
        }
    });
});

function delateChat(){

    let receiverId  = getValue('receiverId'); 
    let senderId    = getValue('senderId');
    let senderImg   = getValue('senderImg');

    swal({
        title: sureMsg,
        text: notRecover,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#a51d29',
        confirmButtonText: yesBlockMsg,
        cancelButtonText: noBlockMsg,
        closeOnConfirm: true,
        closeOnCancel: true
    },

    function(isConfirm) {

        if (isConfirm) {

            firebase.database().ref('chat_rooms/').child(senderId).child(receiverId).set(null);
            firebase.database().ref('chat_history/' + senderId).child(receiverId).set(null);

        }
    });
}

// is typing
var typingTimer12;                  //timer identifier
var doneTypingInterval12 = 5000;    //time in ms, 5 second for example
var $msg = $('#message');           // get input 

//on keyup, start the countdown
$msg.on('keyup', function () {
    clearTimeout(typingTimer12);
    typingTimer12 = setTimeout(clearTyping, doneTypingInterval12);      //"getChatHistory" is function for call
});

//on keydown, clear the countdown 
$msg.on('keydown', function () {
    let chatType = getValue('chatType');
    if(chatType == 'user'){
        isTypingUpdate()
        clearTimeout(typingTimer12);
    }
});

var isTypingUpdate = function(){
 
    var receiverId = getValue('receiverId');
      
    var typingNode = senderId+"_"+receiverId; 
   
    data = {
        'receiverId' : receiverId,
        'senderId' : senderId,
        'isTyping' : 1
    };

   firebase.database().ref('isTyping').child(typingNode).set(data);
}

var clearTyping = function(){

    receiverId = getValue('receiverId'); 

    var typingNode = senderId+"_"+receiverId;

    firebase.database().ref('isTyping').child(typingNode).set(null);
} 

var isTyping = function(userId){

    receiverId = userId;
    
    var typingNode = receiverId+"_"+senderId; 
            
    reciveIdRef = firebase.database().ref().child('isTyping').child(typingNode);
    reciveIdRef.on("value",function(rdata){

        data = rdata.val();
        if(data){

            if(data.isTyping==1){

                var msg = 'typing...';
                $("#userTyping"+data.senderId).html(msg).show();
                $("#msg"+data.senderId).hide();
                $("#typing"+data.senderId).html(msg).show();
                $("#onlineStatus"+data.senderId).hide();
            }
        }else{

            $("#userTyping"+userId).hide();
            $("#msg"+userId).show();
            $("#typing"+userId).html('').hide();
            $("#onlineStatus"+userId).show();
        }
    });
}

// to send notification for app
function senNotifcation(to,notification,data){

    fetch('https://fcm.googleapis.com/fcm/send', {
        'method': 'POST',
        'headers': {
            'Authorization': 'key=' + key,
            'Content-Type': 'application/json'
        },
        'body': JSON.stringify({

            to: to,                             // receiverId token
            collapse_key: 'your_collapse_key',
            delay_while_idle : false,
            priority : "high", 
            content_available: true,
            notification: notification,         // data dictionary      
            data: data,                         // data dictionary   
            badge : 1,
            icon : 'icon',
        })

    }).then(function(response) {

        console.log(response);

    }).catch(function(error) {

        console.error(error);
    });
}

// to send web to web notification
function senWebNotifcation(userId,data){

    firebase.database().ref().child(notifications).child(userId).push(data);
}

