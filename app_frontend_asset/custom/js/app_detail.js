var geocoder;
var map;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var colorVariable = [];
var locations = data;

function initialize() {

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: new google.maps.LatLng(locations[0][1], locations[0][2]),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();
    var marker, i;

    var request = {
        travelMode: google.maps.TravelMode.DRIVING
    };
    var lat_lng = new Array();
    var info = [];

    for (i = 0; i < locations.length; i++) {

        var myLatlng = new google.maps.LatLng(locations[i][1], locations[i][2]);

        lat_lng.push(myLatlng);

        colorVariable.push(locations[i][6]);

        var info_html = "<div style='width:219px;' class='map_loca_name_row'><div class='map_img1'>"+'<img style="border-radius: 50%;height: 47px;width: 47px;" src="'+locations[i][3]+'">'+"</div> <div class='infoCnt'><a class='map_nme2'>"+locations[i][4]+"</a><div class='map_add3'>"+locations[i][0]+"</div></div></div>";

        info.push(info_html);

        marker = new google.maps.Marker({

            position: myLatlng,
            map: map,
            icon: locations[i][5],
            animation: google.maps.Animation.DROP
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {

            return function() {
                infowindow.setContent(info[i]);
                infowindow.open(map, marker);
            }

        })(marker, i));

        if (i == 0) request.origin = marker.getPosition();

        else if (i == locations.length - 1) request.destination = marker.getPosition();

        else {
            if (!request.waypoints) request.waypoints = [];
            request.waypoints.push({
                location: marker.getPosition(),
                stopover: true
            });
        }
    }
     //Loop and Draw Path Route between the Points on MAP
    for (var i = 0; i < lat_lng.length; i++) {
        if ((i + 1) < lat_lng.length) {
            var src = lat_lng[i];
            var des = lat_lng[i + 1];
            getDirections(src, des, colorVariable[i], map);
        }
    }
}
google.maps.event.addDomListener(window, "load", initialize);

function getDirections(src, des, color, map) {

    //Intialize the Direction Service
    var service = new google.maps.DirectionsService();

    service.route({
        origin: src,
        destination: des,
        travelMode: google.maps.DirectionsTravelMode.DRIVING

    }, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            //Intialize the Path Array
            var path = [];
            for (var i = 0; i < result.routes[0].overview_path.length; i++) {
                path.push(result.routes[0].overview_path[i]);
            }
            //Set the Path Stroke Color
            var polyOptions = {
                strokeColor: color,
                strokeOpacity: 3,
                strokeWeight: 4,
                path: path,
                map: map
            }
            poly = new google.maps.Polyline(polyOptions);
            poly.setMap(map);
        }
    });
}

// for appointment finish and delete
$('body').on('click', ".appDetailStatus", function (event) {
    $(".hideclass").removeClass("appDetailStatus");
    var appId = $(this).data('appid'),
        appStatus = $(this).data('status'),
        url = BASE_URL+'home/appointment/finishdeleteAppointment';

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

                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else if(data.status == 2) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else if(data.status == 3) {

                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            } else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
            } else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
            }           
        },

        error:function (){
            toastr.error(commonMsg);
        }
    });       
});

// to accept or reject aapointment request
$('body').on('click', ".appStatus", function (event) {
    
    $(".add-rmv-btn").removeClass("appStatus");
    
    var appId = $(this).attr('data-appid'),
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
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else {
                toastr.error(data.msg);
                $(".add-rmv-btn").addClass("appStatus");
                window.setTimeout(function () {
                    location.reload();
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
    $('#set-ofr-prc').html('$'+ofrPrice+'');
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
                    location.reload();
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
    var appId = $(this).attr('data-appid'),
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
                    location.reload();
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {                      
                    window.location.href  = data.url;
                }, 200);

            }else {
                $(".ad-rm-btn").addClass("appCounterStatus");
                window.setTimeout(function () {               
                   location.reload();
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

// to cancel aapointment request
$('body').on('click', ".cancelApp", function (event) {
    
    $(".cnclBtn").removeClass("cancelApp");
    
    var appId   = $(this).attr('data-appid'),
        url     = BASE_URL+'home/appointment/cancelAppointment';

    $.ajax({

        type: "POST",
        url:url,
        data: {appId:appId}, //only input               
        dataType: "JSON",

        beforeSend: function () {

            show_loader(); 
        },

        success: function (data, textStatus, jqXHR) {

            hide_loader();        
           
            if (data.status == 1){
                
                toastr.success(data.msg);                      
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);

            }else {

                toastr.error(data.msg);
                $(".cnclBtn").removeClass("cancelApp");
                window.setTimeout(function () {
                    location.reload();
                }, 200);
            } 
        },

        error:function (){

            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

/* Appointment payment*/
function appointmentFinalPayment(token) {
    
    var appId = $('#appIdPay').val(),
        amount = $('#appAmount').val(),
        appForId = $('#appForId').val(),
        url = BASE_URL+'home/appointment/appointmentPayment';

    $.ajax({

        type: "POST",
        url:url,
        data: {'stripeToken':token.id,appId:appId,amount:amount,appForId:appForId}, //only input               
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
                    window.location.href  = data.url;
                }, 200);

            }else {
                toastr.error(data.msg);
                window.setTimeout(function () {                      
                   location.reload();
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
}


function rate(no)
{
    for(var i=1;i<=5;i++)
    {
        if(i<=no)
        {
            $("#rate_"+i).attr('class','fa fa-star');
        }
        else
        {
            $("#rate_"+i).attr('class','fa fa-star-o');
        }
    }
    $("#rate_value").val(no);
}


//review form validation
var review_form = $("#reviewForm");

review_form.validate({
    rules: {
        comment : {
            required: true               
        }                      
    },
    messages: {
        
        comment : {
            required: "Please give review."
        }
    }
}); //End counter validation

// to give review for aapoinment with user
$('body').on('click', ".giveReview", function (event) {

    if(review_form.valid() !== true){
        toastr.error(commonMsgReq);
        return false;
    } 

    var rating = $("#rate_value").val();

    if(rating == ''){
        toastr.error('Please give rating.');
        return false;
    }
    
    $(".shre-btn").removeClass("giveReview");
    $("#review").hide();

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
                $(".shre-btn").addClass("giveReview");
                window.setTimeout(function () {
                    location.reload();
                }, 200);
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

