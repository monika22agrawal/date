$("select").change(function(){

    var eventType = $('#event-type').val();

    switch (eventType){

        case '1' :  // my event
            myEvent('1');
            break;

        case '2' :  // event request
            eventRequest('1');
            break;

        default :  // my event
            myEvent('1');
    }
});


// to show my event's list
var offset=0;
var limit=6;
var pagionationUrl=BASE_URL+"home/event/myEventList/";
function myEvent(e='')
{
    if(e==1){   
        offset=0;
    }
    $.ajax({
        url: pagionationUrl,
        type: "POST",
        data:{offset:offset,limit:limit},              
        beforeSend: function() {
            $('#tl_admin_loader').show();                                 
        },                       
        success: function(data){

            $('#tl_admin_loader').hide();
            
            if(offset>0){
                $('#appendDataForMyEvent').append(data);
            }else{
                $("#event-request").html(data);
            }
            var totalMyEvent=$("#myEventCount-count").val();
            var totalMyDataLoadedIn=$('.myEventVisitCount').length;

            if(totalMyEvent>totalMyDataLoadedIn){
                $("#load_more_my_event").show();
            }else{
                $("#load_more_my_event").hide();
            }
            offset+=limit;
        },
        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });        
}

// open model for unfriend 
function openDeleteModel(eventId){
    $("#eventDeletModal").modal();
    $('#eId').val(eventId);
}

// to delete event from list
$('body').on('click', "#delete-event-list", function (event) {

    $(this).attr('id', 'close');
    $(this).unbind();

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

                let eventId = $('#eId').val();

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

// to show my event's request list
var offsetReq=0;
var limitReq=6; 
var reqPagionationUrl=BASE_URL+"home/event/eventRequestList/";
eventRequest('1');
function eventRequest(e='')
{
    if(e==1){ 
        offsetReq=0;
    }

    $.ajax({
        url: reqPagionationUrl,
        type: "POST",
        data:{offsetReq:offsetReq,limitReq:limitReq},              
        beforeSend: function() {
            $('#tl_admin_loader').show();                                 
        },                       
        success: function(res){

            $('#tl_admin_loader').hide();
            
            if(offsetReq>0){
                $('#appendDataForEventReq').append(res);
            }else{
                $("#event-request").html(res);
            }
            var totalReq=$("#eventReqCount-count").val();
            var totalReqDataLoadedIn=$('.eventReqVisitCount').length;

            if(totalReq>totalReqDataLoadedIn){
                $("#load_more_event_req").show();
            }else{
                $("#load_more_event_req").hide();
            }
            offsetReq+=limitReq;
        },
        error:function (){
            $('#tl_admin_loader').hide();
            toastr.error(commonMsg);
        }
    });        
}

// to accept or reject event request for member
$('body').on('click', ".eventRequestStatus", function (event) {
       
    var eventId         = $(this).data('eid'),
        memberId        = $(this).data('mid'),
        eventMemberId   = $(this).data('evtmemid'),
        payment         = $(this).data('evtpayment'),
        groupChat         = $(this).data('groupchat'),
        status          = $(this).data('status'),
        url             = BASE_URL+'home/event/joinMember';

    $.ajax({

        type: "POST",
        url:url,
        data: {eventId:eventId,memberId:memberId,status:status}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {
            hide_loader();       
           
            if (data.status == 1){

                toastr.success(data.msg);  

                if((payment == 'Free') && (groupChat == 1)){
                    joinEventMember(eventId,memberId,eventMemberId);  // sending member while accepting request acc to event in firebase 
                }
                
                window.setTimeout(function () {                   
                    location.reload();
                }, 500);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            }else {
                toastr.error(data.msg);   
                window.setTimeout(function () {                   
                    location.reload();
                }, 500);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(noEvent);
        }
    });       
});