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

/* insert event's record while creating event*/
function createEvent(eventDetail) {
	
	// Display the key/value pairs
	data = {};
	for (var pair of eventDetail.entries()) { // to get formdata key value
		data[pair[0]] = pair[1];
	}

	let memberObj = {

		compId             	: '',
        eventMemId         	: '',
        memberType          : 'Admin',
        userId      		: data.orgenizerId
	}
	
	let eventData = {

        eventId             : data.eventId,
        eventName         	: data.eventName,
        eventImage          : data.eventImg,
        createrId      		: data.orgenizerId,
        createrName       	: data.orgenizerName,
        createrImage      	: data.orgenizerImage,
        eventStartDate      : data.eventStartDate,
        eventEndDate        : data.eventEndDate
    };

    firebase.database().ref().child('events').child('event_'+data.eventId).set(eventData);

    firebase.database().ref().child('events').child('event_'+data.eventId).child('eventUsers').child(data.orgenizerId).set(memberObj);

    setValue('senderId',data.orgenizerId);
    setValue('eventId',data.eventId);
    setValue('eventName',data.eventName);
    setValue('eventImage',data.eventImg);
    setValue('firstMsg','You created this event');

    createChatHistory();
}

function eventImgUpdate(){
    
    let eventId =  $('#eventId').val() ? $('#eventId').val() : $('#setEventVal').data('eventId');
    let eventImg =  $('#eventImg0').attr('src');
	
    firebase.database().ref('events').child('event_'+eventId).child('eventImage').set(eventImg);
}


function joinEventMember(eventId,memberId,eventMemberId,compId=''){

	let memberObj = {

		compId             	: compId ? compId.toString() : '',
        eventMemId         	: eventMemberId.toString(),
        memberType          : compId ? 'Companion' : 'Member',
        userId      		: memberId.toString()
	}

	firebase.database().ref().child('events').child('event_'+eventId).child('eventUsers').child(memberId).set(memberObj);

	// to get event detail
	firebase.database().ref("events").child('event_'+eventId).once('value', function(snapshot) { 

        if(snapshot.val()){

            let eventName = snapshot.val().eventName;
            let eventImg = snapshot.val().eventImage;
            setValue('eventName',eventName);
   			setValue('eventImage',eventImg);
        }

        setValue('senderId',memberId);
	    setValue('eventId',eventId);
	    setValue('firstMsg','Now you are member');

	    createChatHistory(); // to create every event's member chat history after accepting request

    }); 
}

function deleteEvent(eventId){

	firebase.database().ref('events').child('event_'+eventId).set(null);
}

function createChatHistory(downloadURL = ''){

	let senderId 	= getValue('senderId');
	let eventId     = getValue('eventId');
	let eventName   = getValue('eventName');
	let eventImage  = getValue('eventImage');
	let firstMsg  	= getValue('firstMsg');

	imageUrl = downloadURL ? downloadURL : '';

    image = downloadURL ? 1 : 0;

    var msgData = {

    	deleteby		: "",
		firebaseId		: "",
		firebaseToken	: "",
		image 			: 0,
		imageUrl 		: "",
		isMsgReadTick 	: 0,
		lastMsg 		: senderId,
		message      	: firstMsg,
		name 			: eventName,
		profilePic 		: eventImage,
		timestamp 		: Date.now(),
		type  			: "event",
		uid 			: "event_"+eventId,
        isGroup             : false,       
        groupUserCount      :  0,    
		unreadCount 	: 0            
    }

	firebase.database().ref('/chat_history/' + senderId).child("event_"+eventId).set(msgData);
}


function changeGroupChat(e,eventId=''){

	let senderId = getValue('senderId');
	setValue('chatType','event');
    $(".panel-footer").hide();
    $(".block_data").hide();

	if(eventId == ""){

        let name        = $(e).data('name');
        var img         = $(e).data('img') ? $(e).data('img') : imageUrl;
        let receiverId  = $(e).data('uid');
        let token       = $(e).data('token');

        if(img.includes("thumb") == false){
            img = img.replace("/event/", "/event/thumb/");
        }

        setValue('name',name);
        setValue('img',img);
        setValue('receiverId',receiverId);

        $('#userinfo').html('<div class="media cstm-media"><img class="mr-3" src= ' + img + '><div class="media-body cstm-media-body"><h5 class="mt-5">' + name + '</h5><p></p></div></div>');

        firebase.database().ref("events").child(receiverId).once('value', function(snapshot) {
        	
            if(snapshot.val()){

                firebase.database().ref("events").child(receiverId).child("eventUsers").child(senderId).once('value', function(snapshot2) {

                    redirectData = snapshot2.val();

                    if(redirectData.memberType == 'Admin'){                     // redirect to my event detail

                        eventId = receiverId.replace('event_','');
                        url = BASE_URL+'home/event/myEventDetail?eventId='+eventId;

                    }else if(redirectData.memberType == 'Member'){              // redirect to event request detail for member

                        eventId = receiverId.replace('event_','');

                        $eventMemId = redirectData.eventMemId;
                        $query_str = '&eventMemId='+$eventMemId;

                        url = BASE_URL+'home/event/eventRequestDetail?eventId='+eventId+$query_str;
                        
                    }else if(redirectData.memberType == 'Companion'){           // redirect to event request detail for companion

                        eventId = receiverId.replace('event_','');

                        $compId = redirectData.compId;
                        $query_str = '&compId='+$compId;

                        url = BASE_URL+'home/event/eventRequestDetail?eventId='+eventId+$query_str;
                    }

                    setValue('url',url);

                    userDetail 	= snapshot.val();

                    var userImg = userDetail.eventImage ? userDetail.eventImage : imageUrl;

                    if(userImg.includes("thumb") == false){
                        userImg = userImg.replace("/event/", "/event/thumb/");
                    }

                    let eventEndDate = userDetail.eventEndDate;
                    let memberCount = Object.keys(userDetail.eventUsers).length;

                    let eventName = ((userDetail.eventName).length > 20)  ? userDetail.eventName.substring(0, 20)+'...' : userDetail.eventName;

                    setValue('name',eventName);
                    setValue('img',userImg);
                    setValue('eventEndDate',eventEndDate);
                    setValue('token',userDetail.firebaseToken);

                    setValue('orgId',userDetail.createrId);

                    $('#userinfo').html('<a href="'+url+'"><div class="media cstm-media"><img class="mr-3" src= ' + userImg + '><div class="media-body cstm-media-body"><h5 class="mt-5">' + eventName + '</h5><p>'+memberCount+' '+member+' </p></div></div></a>');

                    getEventOrganizerData();

                });
            }
        });

    }else{

        firebase.database().ref("events").child(eventId).once('value', function(snapshot) {

            if(snapshot.val()){

                firebase.database().ref("events").child(eventId).child("eventUsers").child(senderId).once('value', function(snapshot2) {

                    redirectData = snapshot2.val();

                    if(redirectData.memberType == 'Admin'){                     // redirect to my event detail

                        eventId = eventId.replace('event_','');
                        url = BASE_URL+'home/event/myEventDetail?eventId='+eventId;

                    }else if(redirectData.memberType == 'Member'){              // redirect to event request detail for member

                        eventId = eventId.replace('event_','');

                        $eventMemId = redirectData.eventMemId;
                        $query_str = '&eventMemId='+$eventMemId;

                        url = BASE_URL+'home/event/eventRequestDetail?eventId='+eventId+$query_str;
                        
                    }else if(redirectData.memberType == 'Companion'){           // redirect to event request detail for companion

                        eventId = eventId.replace('event_','');

                        $compId = redirectData.compId;
                        $query_str = '&compId='+$compId;

                        url = BASE_URL+'home/event/eventRequestDetail?eventId='+eventId+$query_str;
                    }

                    setValue('url',url);

                    userDetail 	= snapshot.val();
                    
                    var userImg = userDetail.eventImage ? userDetail.eventImage : imageUrl;

                    if(userImg.includes("thumb") == false){
                        userImg = userImg.replace("/event/", "/event/thumb/");
                    }

                    let eventEndDate = userDetail.eventEndDate;
                    let memberCount = Object.keys(userDetail.eventUsers).length;

                    let eventName = ((userDetail.eventName).length > 20)  ? userDetail.eventName.substring(0, 20)+'...' : userDetail.eventName;

                    setValue('name',userDetail.eventName);
                    setValue('img',userImg);
                    setValue('receiverId','event_'+eventId);
                    setValue('eventEndDate',eventEndDate);

                    setValue('orgId',userDetail.createrId);

                    let receiverId  = getValue('receiverId');

                    $('#userinfo').html('<a href="'+url+'"><div class="media cstm-media"><img class="mr-3" src= ' + userImg + '><div class="media-body cstm-media-body"><h5 class="mt-5">' + eventName + '</h5><p id="" >'+memberCount+' '+member+' </p></div></div></a>');

                    getEventOrganizerData();
                });
            }
        });
    }

    setTimeout(function(){

        if(eventId==senderId) {  

            $(".first").click();
        }  

    }, 3000);

    setTimeout(function(){

        let receiverId  = getValue('receiverId');
        let eventEndDate =	getValue('eventEndDate');

        var d = new Date();

        d = moment(d).format('YYYY-MM-DD hh:mm A');

        $('#tl_admin_loader').hide();

        if(eventEndDate < d){

            $("#send_msg"+receiverId).hide();
            $("#block_messgae"+receiverId).show();
            $("#block_messgae"+receiverId).html(cantMsg);

        }else{

            $("#send_msg"+receiverId).show();
             $("#block_messgae"+receiverId).hide();
        }

    }, 1500);

    setValue('startFrom',0);

    let receiverId  = getValue('receiverId');

    $(".panel-footer").attr("id", "send_msg"+receiverId);
    $(".block_data").attr("id", "block_messgae"+receiverId);

    $('.message').html('');
    setValue('deleteChat','');
   	getMuteStatus();

 	getEventChat(receiverId);

    $("#message").focus();
    $('#message').val('');  
    $('#user_to_user').hide();  
    $('#user_to_event').show(); 
}

function getEventOrganizerData(){

	let orgId  = getValue('orgId');

    firebase.database().ref("users").child(orgId).once('value', function(orgData) {

	    if(orgData.val()){

	        ordDetail = orgData.val();

	        let createrImg = ordDetail.profilePic ? ordDetail.profilePic : defaultUser;

	        setValue('orgId',ordDetail.uid);
            setValue('orgImg',createrImg);
            setValue('orgName',ordDetail.name);
	    }
	});
}

//for sending messages
function sendGroupMsg(downloadURL='') {
    
   	let receiverId  = getValue('receiverId');
	let senderImg 	= getValue('eventImage');
	let senderId 	= getValue('senderId');
	let senderName 	= getValue('senderName');
	let eventId 	= getValue('receiverId');
   	let message     = $.trim($('#message').val()); // get textarea message

	let status      = getValue('onlineStatus');

   if(message.length > 0 || downloadURL!=''){

        $('#message').val('');

        imageUrl = downloadURL ? downloadURL : '';

        image = downloadURL ? 1 : 0;

        var msgData = {

            uid             : senderId,
            message         : message,
            name            : senderName,
            profilePic      : '',
            image           : image,
            imageUrl        : imageUrl,
            deleteby        : "",
            isMsgReadTick	: 0,
            lastMsg         : senderId,
            firebaseId      : "",
            firebaseToken   : "",
            timestamp       : Date.now()
        };

       	firebase.database().ref().child('group_chat_rooms').child(eventId).push(msgData);

        msgData.isGroup         = false; 
        msgData.groupUserCount  = 0;  
        
       	createEventChatHistory(msgData);
   	}    
}

function createEventChatHistory(msgData){

	let eventId 	= getValue('receiverId');
	let senderId 	= getValue('senderId');
	let senderName 	= getValue('senderName');

	let eventName 	= getValue('name');
	let eventImg 	= getValue('img');

	firebase.database().ref("events").child(eventId).child('eventUsers').once('value', function(snapshot) {

		if(snapshot.val()){

			rdata = snapshot.val();

			msgData.deleteby = msgData.firebaseId = msgData.firebaseToken = "";

			msgData.message 		= (msgData.image!=1) ? senderName +' : '+ msgData.message : senderName;

			msgData.name 			= eventName;

			msgData.profilePic 		= eventImg;

			msgData.timestamp 		= Date.now();

			msgData.type 			= "event";

			msgData.uid 			= eventId;

			$.each(rdata, function(key, value) {

                if(typeof value !== "undefined" || value != null){

    				var oneMsg 		= value;
    				var userId 		= oneMsg.userId;
    				var eventMemId 	= oneMsg.eventMemId;
    				var compId 		= oneMsg.compId;
    				var ownerType	= oneMsg.memberType;

    				msgData.unreadCount 	= (userId == senderId) ? 0 : 1;

    				if( userId != senderId ){

    					setEventUnread(userId,msgData);
                        
    					firebase.database().ref("event_chat_deleteMute").child(eventId).child(userId).once('value', function( muteData ) {

    			            if( muteData.val() ){

    			                muteUserDetail = muteData.val();

    			                if( muteUserDetail.mute != 1 ){

    			                	checkUserToken(userId,eventMemId,compId,ownerType,msgData);
    			                }
    			            }else{
                                checkUserToken(userId,eventMemId,compId,ownerType,msgData);
                            }
    			        });

    				}else{

    					firebase.database().ref('/chat_history/' + userId).child(eventId).set(msgData);
    				}
                }

			});
		}
	});
}

function checkUserToken(userId,eventMemId,compId,ownerType,msgData){

    let eventId     = getValue('receiverId');
    let senderId    = getValue('senderId');
    let senderName  = getValue('senderName');

    let eventName   = getValue('name');
    let eventImg    = getValue('img');
    let ret = eventId.replace('event_','');
 
    firebase.database().ref("users").child(userId).once('value', function( uData ) {

        if( uData.val() ){

            userDetail = uData.val();

            //if( userDetail.firebaseToken ){

                let orgId   = getValue('orgId');
                let orgImg  = getValue('orgImg');
                let orgName = getValue('orgName');

                data = {

                    'eventId'                       : ret,
                    'eventName'                     : eventName,
                    'eventImage'                    : eventImg,
                    'eventOrganizerId'              : orgId,
                    'eventOrganizerName'            : orgName,
                    'eventOrganizerProfileImage'    : orgImg,
                    'eventType'                     : '',
                    'eventMemId'                    : eventMemId,
                    'compId'                        : compId,
                    'ownerType'                     : ownerType
                }


                notification = {

                    'title'         : eventName,
                    'body'          : msgData.message,
                    'type'          : 'group_chat',
                    'sender_name'   : senderName,
                    'message'       : msgData.message,
                    'opponentChatId': senderId,
                    'click_action'  : 'ChatActivity',
                    'sound'         : 'default',
                    payLoadEvent    : data
                    
                }

                senNotifcation(userDetail.firebaseToken,notification,notification);

            //}else{

                let webNotification = {
    
                    'title' : eventName,
                    'body'  : msgData.imageUrl ? 'Image' : msgData.message,
                    'url'   : BASE_URL+'home/chat?uId='+ eventId + '&type=event'
                };

                senWebNotifcation(userId,webNotification);
            //}
        }
    });
}


function setEventUnread(senderId,msgData){

    let receiverId = getValue('receiverId');

    firebase.database().ref("chat_history").child(senderId).child(receiverId).once('value', function(snapshot) {

        msgData.unreadCount = 1;

        if(snapshot.val()){

            //let count = Number(snapshot.val().unreadCount) + Number(1);
            let count = 1;
            msgData.unreadCount = count;
        }
        firebase.database().ref('/chat_history/' + senderId).child(receiverId).set(msgData);
    });
}



function getEventChat(receiverId){

    let startFrom   = Number(getValue('startFrom'));

    var query = firebase.database().ref("group_chat_rooms").child(receiverId).limitToLast(15);

    if(startFrom){

        var query = firebase.database().ref("group_chat_rooms").child(receiverId).orderByChild("timestamp").endAt(startFrom).limitToLast(15);
    }

    query.on('value', function(snapshot) {

    	var chat = snapshot.val();
        setValue('startFrom',0);

        if(chat){

            if(getValue('startFrom') == 0){

                var keys = Object.keys(chat);
                k = keys[0];

                setValue('startFrom',chat[k].timestamp);
            }

            var page = 1;


            if(getValue('receiverId') == receiverId){

                $.each(chat, function(key, value) {

                	var oneMsg= value;

                	if(getValue('deleteChat')<oneMsg.timestamp || getValue('deleteChat')==''){

	                    oneMsg.showtimestamp    = moment(oneMsg.timestamp).format('hh:mm A');
	                    timestamp               = (oneMsg.timestamp);
	                    oneMsg.timestamp        = moment(oneMsg.timestamp).format('YYYY-MM-DD, hh:mm A');

                        let myMsg = ( oneMsg.uid != senderId ) ? '' : 'speech-right';
	                    let senderName = ( oneMsg.uid != senderId ) ? oneMsg.name : '';

	                    message = (oneMsg.image == 1) ? '<img class="img-cursor" onclick="showImage(this.src);" src=' + oneMsg.imageUrl + ' alt="" height="130" width="130"/>' : oneMsg.message;

	                    imgCls = (oneMsg.image == 1) ? 'speech-img' : 'speech';
	                    
	                    $('#'+timestamp).remove();
	                    var msgHtml = '<li class="mb-15" id=' +timestamp + '> <div class="media-body pl-15 pr-15 '+myMsg+'"><div class="grp-cht-msg">'+senderName+'</div>  <div class="'+imgCls+'" data-toggle="tooltip" title="' + oneMsg.timestamp + '"><p class="text-brk">' + message + '</p>  </div><p class="speech-time"> ' + oneMsg.showtimestamp + '</p> </div> </li>';

	                    (startFrom == 0) ? $('.message').append(msgHtml) : $('.get_message').append(msgHtml);
	                    
	                    page++;

                	}else{
                		$('.message').html('');
                	}
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


var getMuteStatus = function(){

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    firebase.database().ref("event_chat_deleteMute").child(receiverId).child(senderId).child('lastDeleted').on('value', function( snapshot ) {

    	if(snapshot.val()){

    		setValue('deleteChat',snapshot.val());
    	}else{

    		removeValue('deleteChat');
    	}
	});

    firebase.database().ref("event_chat_deleteMute").child(receiverId).child(senderId).child('mute').on('value', function( snapshot ) {

        if (snapshot.exists()) {

        	if(snapshot.val()==1){

        		$("#mute").hide();
        		$("#unmute").show();

        	}else{

        		$("#mute").show();
        		$("#unmute").hide();
        	}

        } else{
        	$("#mute").show();
        	$("#unmute").hide();
        }
    });
}

// for mute chat
$("#mute").click(function(){

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    swal({

        title               : sureMsg,
        text                : cantRecMsg,
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

    		firebase.database().ref().child('event_chat_deleteMute').child(receiverId).child(senderId).child('mute').set('1');

        }
    });
});

// for unmute chat
$("#unmute").click(function(){

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    firebase.database().ref().child('event_chat_deleteMute').child(receiverId).child(senderId).child('mute').set('0');
});


// to delete group chat
function deleteGroupChat(){

    let senderId    = getValue('senderId');
    let receiverId  = getValue('receiverId');

    swal({

        title               : sureMsg,
        text                : notRecover,
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

        	firebase.database().ref().child('event_chat_deleteMute').child(receiverId).child(senderId).child('lastDeleted').set(Date.now());
        	setValue('deleteChat',Date.now());
        	firebase.database().ref().child('chat_history').child(senderId).child(receiverId).child('message').set('');
        	getEventChat(receiverId);

        }
    });
}

function getGroupMembers(){

	$('#groupMember').html('');
	$('#groupDetail').html('');

	let eventId = getValue('receiverId');
	let senderId = getValue('senderId');
	let senderImg = getValue('senderImg');

	firebase.database().ref("events").child(eventId).child('eventUsers').once('value', function(snapshot) {

		if(snapshot.val()){

			rdata = snapshot.val();
            rdata = gMember_filter( arrayFliter(rdata));

			let memberCount = Object.keys(rdata).length;
			let	eventName 	= getValue('name');
            let eventImage  = getValue('img');
    		let url	= getValue('url');

			let htmlGroupData = '<a href="'+url+'"><div class="dsply-blck-lft group-img"><img src="'+eventImage+'" /></div><div class="dsply-blck-rgt-lft grp-det mt-5"><h3>'+eventName+'</h3><a><p id="membrs">'+memberCount+' '+member+' </p></a></div></a>';

			$('#groupDetail').append(htmlGroupData);
			
			$.each(rdata, function(key, value) {

                if(typeof value !== "undefined" || value != null){

    				let oneMsg 		= value;

    				let userId 		= oneMsg.userId;
    				let eventMemId 	= oneMsg.eventMemId;
    				let compId 		= oneMsg.compId;
    				let ownerType	= oneMsg.memberType;

    				//if( userId != senderId  ){

    					firebase.database().ref("users").child(userId).once('value', function( uData ) {
                		
    			            if( uData.val() ){

    			                userDetail = uData.val();

    			                var profilePic	= userDetail.profilePic ? userDetail.profilePic : defaultUser;
                                if(profilePic.includes("thumb") == false){
                                    profilePic = profilePic.replace("/profile/", "/profile/thumb/");
                                }
    							var name		= userDetail.name;

                                name = (userId == senderId) ? 'You' : name;

                                url = (userId == senderId) ? BASE_URL+'home/user/userProfile' : BASE_URL+'home/user/userDetail?userId='+userId;

    							let htmlMemberData = '<a href="'+url+'"><div class="blog_comment_item mt-20"><div class="media"><div class="media-left"><img src="'+profilePic+'" alt=""></div><div class="media-body group-mmbr"><h4 class="pb-0 pt-15">'+name+'</h4><p>'+ownerType+'</p></div></div></div></a>';
    							$('#groupMember').append(htmlMemberData);
    			            }
    			        });	
    				/*}else{
                        setValue('oType',ownerType);
                    }*/
                }
			});

			// let oType = getValue('oType');

			// let data = '<div class="blog_comment_item mt-20"><div class="media"><div class="media-left"><img src="'+senderImg+'" alt=""></div><div class="media-body"><h4>You</h4><p>'+oType+'</p></div></div></div>';

			// $('#groupMember').append(data);
		}
	});
}

let gMember_filter = function(data){

    let members = data.filter(function(o1){
        return o1.memberType != 'Admin';

    }).map(function(o){

       return o;       
    });

     // for showing admin
    let admin = data.filter(function(o1){
        return o1.memberType == 'Admin';

    }).map(function(o){

       return o;       
    });

    // for showing all members
    members.unshift(admin[0]); 
    data = members;
    let result = data.filter(function(o1){
        return o1.userId != senderId;

    }).map(function(o){

       return o;       
    });

    // for showing own on end of list (You)
    let result2 = data.filter(function(o1){
        return o1.userId == senderId;

    }).map(function(o){
       return o;       
    });

   result.push(result2[0]);
   return result;  
}

let arrayFliter= function(data){

    var array = [];

    $.each(data, function(key, value) {
        
        if(typeof value != "undefined" || value != null) {
            array.push(value); 
        }
    });
    return array;
}

