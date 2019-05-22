$(document).ready(function(){

    /*Start show joined member list*/
    $(".sidebar_overlay1").click(function(){
        openClose2();
    });
    $("#sidebar_close_icon2").click(function(){
        openClose2();
    });
    function openClose2(){

        $("#sidebar-right2").toggleClass("sidebar-open2");
        $(".sidebar_overlay1").toggleClass("sidebar_overlay_active1"); 
        $("body").toggleClass("hide_overflow"); 
    }
    var jointotal = $("#getJoinCount").val();            

    if(jointotal > 0){
        $(".joinMembers").click(function(){
            openClose2();
        });
    }
    /*End show joined member list*/

    
    /*Start show invited member list*/
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

    var invittotal = $("#getinvitCount").val();
    if(invittotal > 0){
        $(".inviteMembers").click(function(){
            openClose();
        });
    }
    /*End show invited member list*/
});

// show invited friend list
var offset=0;
var limit=6;
var eventId = $('#eventId').val();
var invitedPagionationUrl=BASE_URL+"home/event/invitedMembers/";
load_invited_friends();
function load_invited_friends()
{    
    $.ajax({
        url: invitedPagionationUrl,
        type: "POST",
        data:{offset:offset,limit:limit,eventId:eventId},              
        cache: false,  
        beforeSend: function() {
            $('#tl_admin_loader').show();                           
        },                       
        success: function(res){
            $('#tl_admin_loader').hide();

            if(offset>0){

                $('#appendDataForInvited').append(res);

            }else{

                $('#invite-member').html(res);
            }

            var totalIn=$("#inviteMemCount-count").val();
            var totalDataLoadedIn=$('.invitedVisitCount').length;

            if(totalIn>totalDataLoadedIn){

                $("#load_more_btn_invited").show();

            }else{
                
                $("#load_more_btn_invited").hide();
            }
            offset+=limit;
        },
        error:function (){
            $('#tl_admin_loader').hide();
            toastr.error(commonMsg);
        }
    });        
}

// show joined friend list
var joinOffset=0;
var joinLimit=6;
var eventId = $('#eventId').val();
var joinedPagionationUrl=BASE_URL+"home/event/joinedMembers/";
var type = 'myEvent';
load_joined_friends();
function load_joined_friends()
{    
    $.ajax({

        url: joinedPagionationUrl,
        type: "POST",
        data:{joinOffset:joinOffset,joinLimit:joinLimit,eventId:eventId,type:type},              
        cache: false,  

        beforeSend: function() {

            $('#tl_admin_loader').show();                           
        },       

        success: function(data){

            $('#tl_admin_loader').hide();

            if(joinOffset>0){

                $('#appendDataForJoined').append(data);

            }else{

                $('#joined-member').html(data);
            }

            var total=$("#joinMemCount-count").val();
            var totalDataLoaded=$('.joinedVisitCount').length;

            if(total>totalDataLoaded){

                $("#load_more_btn_joined").show();

            }else{

                $("#load_more_btn_joined").hide();
            }
            joinOffset+=joinLimit;
        },
        error:function (){
            $('#tl_admin_loader').hide();
            toastr.error(commonMsg);
        }
    });        
}

// to delete event
$('body').on('click', "#delete-event", function (event) {

    $(this).attr('id', 'close');
    $(this).unbind();
    var eventId = $(this).data('eventid'),
        url = BASE_URL+'home/event/deleteEvent';
        
    $.ajax({
        type: "POST",
        url: url,
        data: {eventId:eventId}, //only input    
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  

            hide_loader();        
           
            if (data.status == 1){ 
               
                toastr.success(data.msg);

                //deleteEvent(eventId); // to delete event from firebase

                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
               
            }else if(data.status == 2) {

                toastr.error(data.msg);                

            }else if(data.status == 3) {

                toastr.error(data.msg);                

            }else if(data.status == 4) {

                toastr.error(data.msg);                

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);
            }else {

                toastr.error(data.msg);            
            }           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
});

// open model for remove event's member 
function openRemoveModel(eventMemId,eventId,memName,type){
    $("#removeMemModal").modal();
    $('#eMId').val(eventMemId);
    $('#eId').val(eventId);
    $('#type').val(type);
    $('#memName').text(memName);
}

// to delete event from list
$('body').on('click', ".remove-member", function (event) {
    
    var _that = $(this), 
        form = _that.closest('form'),      
        formData = new FormData(form[0]),
        f_action = form.attr('action');

    $.ajax({
        type: "POST",
        url: f_action,
        data: formData, //only input    
        dataType: "JSON", 
        processData: false,
        contentType: false,
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus) {  

            hide_loader();        
          
            if (data.status == 1){ 
               
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
               
            }else if(data.status == 2) {

                toastr.error(data.msg);                

            }else if(data.status == 3) {

                toastr.error(data.msg);                

            }else if(data.status == 4) {

                toastr.error(data.msg);                

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);
            }else {

                toastr.error(data.msg);            
            }           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
});
/* to show business location map*/
function initMapEventPlace() {

    var name = $('#mapId').data('name');
    var address = $('#mapId').data('address');
    var latitude = $('#mapId').data('lat');
    var longitude = $('#mapId').data('long');
    var icon = $('#mapId').data('icon');
    var img = $('#mapId').data('img');

    var map = new google.maps.Map(document.getElementById('mapId'), {
        zoom: 14,
        center:  new google.maps.LatLng(latitude, longitude)
    });

    var contentString = img;

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(latitude, longitude),
        map: map,
        icon: icon,
        animation: google.maps.Animation.DROP
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}
    
//$(window).load(function() {

    initMapEventPlace();
//});