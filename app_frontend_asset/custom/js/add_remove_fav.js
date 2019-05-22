$('#apoim-gal').lightGallery({
  selector: '.pic',
  loop:false,
  zoom:false,
  fullScreen:false,
  share:false,
  download:false,
  autoplayControls:false,
  thumbnail: false
});
//for showing messages on single popup
function msg(e){  

    $("#myModal").modal();
    $('#showMsg').text($(e).data("msg"));
}

var appUrl = BASE_URL+"home/user/appReviewList/";
var isFunctionCall = false;
var urlImg = frontImgPath+'Spinner-1s-80px.gif';

function appReviewList(page,type,id)
{
    if(!isFunctionCall){

        isFunctionCall = true;
        $.ajax({

            url: appUrl,

            type: "POST",

            data:{page:page,type:type,id:id}, 

            beforeSend: function() {
               //$('#showLoader').html("<img id='showLoader' src='"+urlImg+"' alt=''>");                                    
            },                       
            success: function(data){
                
                //$('#showLoader').html("");

                if(page == 0){
                    $("#appointmentReviewList").html(data);
                }else{
                    $("#appointmentReviewList").append(data);    
                }
                $("#page-count").val(Number(page)+Number(1));

            },
            complete:function(){
                isFunctionCall = false;
            },

            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        }); 
    }       
}

function markFavorite(favUserId,favStatus){

    var url = BASE_URL+'home/user/addRemoveFavorite';
    var urlImg = BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';

    $.ajax({

        type     : "POST",
        url      :url,
        data     : {isFavorite:favStatus,favUserId:favUserId}, //only input               
        dataType : "JSON",

        beforeSend: function () {
            //$("#favUser").html("<img id='zlodaer' src='"+urlImg+"' alt=''>");
            show_loader(); 
        },

        success: function (data, textStatus, jqXHR) { 
            
            $('#favUser').load(' #fvUser');
           
            if (data.status == 1){
                
            } else if(data.status == 2) {

            } else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            } else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }
            window.setTimeout(function () {
                hide_loader();
            }, 1200);             
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    }); 
}

function markLike(likeUserId,likeStatus){
    
    var totlike = $('#getLike').text();

    var url = BASE_URL+'home/user/addRemoveLike';
    var urlImg = BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';

    $.ajax({

        type     : "POST",
        url      : url,
        data     : {isLike:likeStatus,likeUserId:likeUserId}, //only input               
        dataType : "JSON",

        beforeSend: function () {
            //$("#likeUser").html("<img id='zlodaer' src='"+urlImg+"' alt=''>");
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {

            $('#likeUser').load(' #lkUser');
            $('#getLike').load(' #getLike');
                

            if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            } else if (data.status != 1 && data.status != 2){

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }   
            window.setTimeout(function () {
                hide_loader();
            }, 1200);
                         
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    }); 
}

// for add friend
$('body').on('click', ".addFriend", function (event) {

    $(".addFriend").removeClass("addFriend");

    var requestFor = $(this).data('requestfor'),
        url = BASE_URL+'home/user/sendFriendRequest';
           
    $.ajax({

        type     : "POST",
        url      : url,
        data     : {requestFor:requestFor}, //only input  
        dataType : "JSON",

        beforeSend: function () {
            show_loader();
        },

        success: function (data, textStatus, jqXHR) {

            hide_loader();
            $('#showCncl').load(' #showCncl');
            if (data.status == 1){
               /* $('#hideAddIcon').hide();
                $('#showCncl').show();*/

                toastr.success(data.msg);
                /*window.setTimeout(function () {
                    window.location.href = data.url;
                }, 1000);*/

            }else if(data.status == 2) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 1000);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 1000);
            }           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
});

// for accept / reject / cancel friend request
$('body').on('click', ".requestStatusFromDetail", function (event) {

    $(".requestStatusFromDetail").removeClass("requestStatusFromDetail");

    var status = $(this).data('status'),
        requestFor = $(this).data('requestfor'),
        url = BASE_URL+'home/user/friendRequest';
        
    $.ajax({

        type     : "POST",
        url      : url,
        data     : {status:status,requestFor:requestFor}, //only input               
        dataType : "JSON", 

        beforeSend: function () {
            show_loader(); 
        },

        success: function (data, textStatus, jqXHR) {

            hide_loader();        
            $('#showCncl').load(' #showCncl');
            if (data.status == 1){ 
                
                toastr.success(data.msg);
                
            }else if(data.status == 2) {
               
                toastr.success(data.msg);

            }else if(data.status == 3) {

                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 1000);

            }else if(data.status == 4) {

                toastr.success(data.msg);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            } else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 1000);
            }
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });  
});

function initMapBusiness() {

    var name = $('#mapId').data('name');
    var address = $('#mapId').data('address');
    var latitude = $('#mapId').data('lat');
    var longitude = $('#mapId').data('long');
    var icon = $('#mapId').data('icon');
    var img = $('#mapId').data('img');

    var map = new google.maps.Map(document.getElementById('mapId'), {
        zoom: 11,
        center:  new google.maps.LatLng(latitude,longitude)
    });

    var contentString = img;

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(latitude, longitude),
        map: map,
        icon: icon,
        animation: google.maps.Animation.DROP,
        title: 'Uluru (Ayers Rock)'
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });
}
    
//$(window).load(function() {

    initMapBusiness();
//});