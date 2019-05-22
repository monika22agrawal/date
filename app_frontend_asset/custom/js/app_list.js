let urlImg = IMG_BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
sentAppList('0','all',0);
$("select").change(function(){

    var appType = $('#app-type').val();

    switch (appType){

        case '1' :  // my appointment
            sentAppList('0','appointById',0);
            break;

        case '2' :  // appointment request
            sentAppList('0','appointForId',0);
            break;

        case '3' :  // finished appointment
            sentAppList('0','finished',0);
            break;

        case '0' :  // all appointment
            sentAppList('0','all',0);
            break;

        default :  // for default show all appointments
            sentAppList('0','all',0);
    }
});

var isFunctionCall = false;

function sentAppList(page,type,is_scroll=0)
{
    var url = BASE_URL+'home/appointment/appointmentList/';
    var scroll_loader = $('#showLoader');
    //scroll_loader.hide();
    //for new request, reset isNext and offset
    if(is_scroll==0){
        scroll_loader.attr('data-isNext',1);
        scroll_loader.attr('data-offset',0); //set new offset
    }
    
    var offset = scroll_loader.attr('data-offset'),
        isNext = scroll_loader.attr('data-isNext'), //to see if we have next record to load
        list_cont = $('#sentAppList'); //container where list will be appended

    //abort request if previous request is in progress OR next record is not available
    if(isFunctionCall || (isNext==0 && is_scroll==1)){
        return false;
    }

    isFunctionCall = true;

    $.ajax({

        url: url,
        type: "POST",
        data:{page:offset,type:type},
        dataType: "json",

        beforeSend: function() {
            
            scroll_loader.show();
        },

        success: function(data){

            if (data.status == 1){
                
                scroll_loader.hide();

                if(offset == 0){
                    list_cont.html(data.html);
                }else{
                    list_cont.append(data.html);    
                }
                
                scroll_loader.attr('data-isNext',data.isNext);
                scroll_loader.attr('data-offset',data.newOffset); //set new offset
                
            }else if(data.status == -1) {
                //session exipred
                toastr.error(data.msg);
                if(data.url){
                    window.setTimeout(function () {
                        window.location = data.url;
                    }, 2000);
                }
            }else{
                scroll_loader.hide();
                toastr.error(data.msg);
            }
        },
        complete:function(){
           isFunctionCall = false;
        },
        error:function (){
            scroll_loader.hide();
            toastr.error(commonMsg);
        }
    });
}

// to accept or reject aapointment request
$('body').on('click', ".appStatus", function (event) {
    
    $(".add-rmv-btn").removeClass("appStatus");
    var appId = $(this).attr('data-appId'),
        appStatus = $(this).attr('data-status'),
        url = BASE_URL+'home/appointment/appointmentStatus';

    $.ajax({

        type: "POST",
        url:url,
        data: {appId:appId,appStatus:appStatus}, //only input               
        dataType: "JSON",

        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  
            hide_loader();        
           
            if (data.status == 1){                        
                location.reload();

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else {
                toastr.error(data.msg);
                $(".add-rmv-btn").addClass("appStatus");
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

// modal popup for apply counter price
function counterModel(appId,ofrPrice,appointById){

    $('#ofr-prce').modal();
    $('#set-ofr-prc').html(ofrPrice);
    $('#appId').val(appId);
    $('#appointById').val(appointById);
}

//counter form validation
var ofr_form = $("#ofrForm");

ofr_form.validate({
    rules: {
        counterPrice : {
            required: true               
        }                        
    },
    messages: {
        
        counterPrice : {
            required: "Please enter counter price."
        }
    }
}); //End counter validation

// to apply counter price for aapoinment
$('body').on('click', ".applyCounter", function (event) {

    if(ofr_form.valid() !== true){
        return false;
    } 
    
    $(".shre-btn").removeClass("applyCounter");
    $("#ofr-prce").hide();

    var _that = $(this), 
    form = _that.closest('form'),
    formData = new FormData(form[0]),
    f_action = form.attr('action');

    $.ajax({

        type: "POST",
        url: f_action,
        data: formData, //only input
        processData: false,
        contentType: false,
        dataType: "JSON",  
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {
            hide_loader();        
           
            if (data.status == 1){ 
                toastr.success(data.msg); 
                window.setTimeout(function () {                      
                    location.reload();
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else {
                $(".shre-btn").addClass("applyCounter");
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

// to update applied counter price for accept or reject
$('body').on('click', ".appCounterStatus", function (event) {
    
    $(".ad-rm-btn").removeClass("appCounterStatus");
    var appId = $(this).attr('data-appId'),
        counterStatus = $(this).attr('data-counterstatus'),
        appointForId = $(this).attr('data-appforid'),
        url = BASE_URL+'home/appointment/updateCounter';

    $.ajax({

        type: "POST",
        url:url,
        data: {appId:appId,appointForId:appointForId,counterStatus:counterStatus}, //only input               
        dataType: "JSON", 

        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  
            hide_loader();        
           
            if (data.status == 1){

                toastr.success(data.msg); 
                window.setTimeout(function () {                      
                    window.location.href  = data.url;
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {                      
                    window.location.href  = data.url;
                }, 200);

            }else {
                $(".ad-rm-btn").addClass("appCounterStatus");
                window.setTimeout(function () {                      
                    window.location.href  = data.url;
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});