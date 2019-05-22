hide_loader();
function show_loader(){
    $('#tl_admin_loader').show();
}

function hide_loader(){
    $('#tl_admin_loader').hide();
}

//for showing messages on single popup
function msg(e){ 

    $("#myModalList").modal();
    $('#showMsgList').text($(e).data("msg"));
}

$( "#price_select" ).slider({
    range: true,
    min: 18,
    max:100,
    values: [ 18, 100 ],
    slide: function( event, ui ) {
        $( "#amount" ).val(ui.values[ 0 ] + "-" + ui.values[ 1 ] );
    }
});
$( "#amount" ).val($( "#price_select" ).slider( "values", 0 ) +"-" + $( "#price_select" ).slider( "values", 1 ) );


var pagionationUrl = BASE_URL+"home/searchResult/";
var isFunctionCall = false;

nearUserList(0); //load nearby user list initially when page is loaded

var ajaxFun = null;

function nearUserList(is_scroll=0)
{   
    if(ajaxFun != null){
        ajaxFun.abort();
        ajaxFun = null;
    }

    $('#map_map').html('');
    var viewType = $('#viewType').val();
    //var urlImg = BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
    
    var scroll_loader = $('#showLoader');
    //scroll_loader.hide();
    //for new request, reset isNext and offset
    if(is_scroll==0){
        scroll_loader.attr('data-isNext',1);
        scroll_loader.attr('data-offset',0); //set new offset
    }
    
    var offset = scroll_loader.attr('data-offset'),
        isNext = scroll_loader.attr('data-isNext'), //to see if we have next record to load
        list_cont = $('#nearUsers'); //container where list will be appended
    
    if(viewType == 'map' && isPayment == 1){ // to view map
        $('#map_map').html('<div id="map" class="map-sec"></div>');
    }
    
    //abort request if previous request is in progress OR next record is not available
    if(isFunctionCall || (isNext==0 && is_scroll==1)){
        return false;
    }

    isFunctionCall = true;
    $(".filter-icon").removeClass('open');
    $('.notFound').hide();
    var address          = $("#address").val(),
        searchName       = ($('#searchName').val()).trim(),
        gender           = $('#gender').val(),
        viewType         = viewType,
        userOnlineStatus = $('#userOnlineStatus').val(),
        latitude         = $("#lat").val(),
        longitude        = $("#long").val(),
        age              = $(".rangeVal").val(),        
        newAge           = age.split('-');

    if(typeof newAge[0] != 'undefined' && newAge[0] != ''){
        var minAge = newAge[0];
    }else{
        var minAge = 18;
    }

    if(typeof newAge[1] != 'undefined' && newAge[1] != ''){
        var maxAge = newAge[1];
    }else{
        var maxAge = 100;
    }

    ajaxFun = $.ajax({

        url: pagionationUrl,
        type: "POST",
        data:{page:offset,gender:gender,latitude:latitude,longitude:longitude,minAge:minAge,maxAge:maxAge,searchName:searchName,userOnlineStatus:userOnlineStatus,viewType:viewType},
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
                toastr.error(data.msg);
            }
        },
        complete:function(){
           isFunctionCall = false;
        }
        /*error:function (){
            scroll_loader.hide();
            toastr.error('Failed! Please try again');
        }*/
    });      
}

var typingTimer;                //timer identifier
var doneTypingInterval = 300 ;  //time in ms, 5 second for example
var $input = $('#searchName');

//on keyup, start the countdown
$input.on('keyup', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

//on keydown, clear the countdown 
$input.on('keydown', function () {
    clearTimeout(typingTimer);
});

//user is "finished typing," do something
function doneTyping () {
    nearUserList(0);
}

function clearData(){
    //$('input[name="gender"]').attr('checked', false);
    $('#both').prop('checked', true);
    $("#address").val('');
    $('#searchName').val('');
    $("#lat").val('');
    $("#long").val('');
    $(".rangeVal").val('');
    ajax_fun(BASE_URL+"home/searchResult/");
}

