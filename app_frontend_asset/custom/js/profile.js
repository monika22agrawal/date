function initializeAdd() {

    var autocomplete = new google.maps.places.Autocomplete(document.getElementById("usraddress"));
    
    autocomplete.addListener('place_changed', function() {
      
        var place = autocomplete.getPlace();
       
        //console.log(place.address_components);
        var country = '', city = '', cityAlt = ''; state = '';

        for(var i = 0; i < place.address_components.length; i += 1) {

            var addressObj = place.address_components[i];

            for(var j = 0; j < addressObj.types.length; j += 1) {

                if (!country && addressObj.types[j] === 'country') {

                    //console.log(addressObj.types[j]); // confirm that this is 'country'
                    country = addressObj.long_name; // confirm that this is the country name
                }

                if (!state && addressObj.types[j] === 'administrative_area_level_1') {

                    //console.log(addressObj.types[j]); // confirm that this is 'state'
                    state =  addressObj.long_name; // confirm that this is the state name
                }

                if (!city && addressObj.types[j] === 'administrative_area_level_2') {

                    //console.log(addressObj.types[j]); // confirm that this is 'city'
                    city = addressObj.long_name; // confirm that this is the city name
                }                
            }

            if (city && state && country) {
                break;
            }
        }

        var lat = place.geometry.location.lat(),
            lng = place.geometry.location.lng();

        if(lat != '' && lng != ''){

            $("#usrlat").val(lat);
            $("#usrlong").val(lng);
            $('#usrproCity').val(city);
            $('#usrproState').val(state);
            $('#usrproCountry').val(country);
        }
        // place.geometry  -- this is used to detect whether User entered the name of a Place that was not suggested and pressed the Enter key, or the Place Details request failed.
    });
}
google.maps.event.addDomListener(window, 'load', initializeAdd); //initialise google autocomplete API on load
// to update notification status
function notiStatus(){

    let value = $('input[name=isNotification]:checked').val();
    let status = value ? value : '0';
    let url = BASE_URL+"home/user/notificationStatus/";

    $.ajax({
        url: url,
        type: "POST",
        data:{status:status},              
        cache: false,  
        beforeSend: function() {
            //show_loader();                               
        },                       
        success: function (data, textStatus, jqXHR) {  
            let val = JSON.parse(data);
            hide_loader();        
           
            if (val.status == 1){                 

            }else if(val.status == -1) {

                toastr.error(val.msg);
                window.setTimeout(function () {
                    window.location.href = val.url;
                }, 500);
            }else {

                toastr.error(val.msg);
            }           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
}

function imageSlider(){

    $.ajax({

        url: BASE_URL+"home/user/userImgSlider/",

        type: "POST",

        data:{}, 
                       
        success: function(data){

            let res = JSON.parse(data);
            
            $('#loadImgSlider').html(res.slider);
        },
    });
    overView()
}
imageSlider();

function overView(){

    $.ajax({

        url: BASE_URL+"home/user/userOverviewTab/",

        type: "POST",

        data:{}, 
                       
        success: function(data){

            let res = JSON.parse(data);
            
            $('#overView').html(res.overView);

            $('#uDetail').html(res.uDetail);
        },
    });
}

// for showing favorite list records
var url = BASE_URL+"home/user/favouriteList/";
var isFavFunctionCall = false;
var urlImg = IMG_BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
function favoriteList(page)
{    
    if(!isFavFunctionCall){

        var isFavFunctionCall = true;
        $.ajax({

            url: url,

            type: "POST",

            data:{page:page}, 

            beforeSend: function() {
                $('.showLoader').html("<img src='"+urlImg+"' alt=''>");                              
            },                       
            success: function(data){
                
                $('.showLoader').html("");

                if(page == 0){
                    $("#favouriteList").html(data);
                }else{
                    $("#favouriteList").append(data);    
                }
                $("#page-count").val(Number(page)+Number(1));
                $("#tabType").val('favTab');

            },
            complete:function(){
                isFavFunctionCall = false;
            },
            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        }); 
    }        
}

// show friend list

var friendsPagionationUrl=BASE_URL+"home/user/friendList/";
var isFrndFunctionCall = false;
var urlImg = IMG_BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
function friendsList(page)
{    
    if(!isFrndFunctionCall){

        var isFrndFunctionCall = true;

        $.ajax({

            url: friendsPagionationUrl,

            type: "POST",

            data:{page:page}, 

            beforeSend: function() {
                $('.showLoader').html("<img src='"+urlImg+"' alt=''>");                              
            },                       
            success: function(data){
                
                $('.showLoader').html("");

                if(page == 0){
                    $("#friend-list").html(data);
                }else{
                    $("#friend-list").append(data);    
                }
                $("#page-count").val(Number(page)+Number(1));
                $("#tabType").val('friendTab');

            },
            complete:function(){
                isFrndFunctionCall = false;
            },
            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        }); 
    }        
}

// show friend request list
var isFrndReqFunctionCall = false;
var urlImg = IMG_BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
var friendRequestPageUrl=BASE_URL+"home/user/friendRequestList/";
function friendsRequestList(page)
{    
    if(!isFrndReqFunctionCall){

        var isFrndReqFunctionCall = true;

        $.ajax({

            url: friendRequestPageUrl,

            type: "POST",

            data:{page:page}, 

            beforeSend: function() {
                $('.showLoader').html("<img src='"+urlImg+"' alt=''>");                              
            },                       
            success: function(data){
                
                $('.showLoader').html("");

                if(page == 0){
                    $("#friend-request-list").html(data);
                }else{
                    $("#friend-request-list").append(data);    
                }
                $("#page-count").val(Number(page)+Number(1));
                $("#tabType").val('friendReqTab');

            },
            complete:function(){
                isFrndReqFunctionCall = false;
            },
            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        }); 
    }       
} 

// open model for unfriend 
function openModel(friendId,fName){

    $("#myModal").modal();
    $('#fId').val(friendId);
    $('#fName').text(fName);
}

// for unfriend from friend list
$('body').on('click', ".unfriend-user", function (event) {

    var _that = $(this), 
        form = _that.closest('form'),      
        formData = new FormData(form[0]),
        f_action = form.attr('action');
        fId = $('#fId').val();

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

                $("#myModal").modal('hide');
                toastr.success(data.msg);
                $('#frndId'+fId).remove();
                

            }else if(data.status == 2) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);
            }else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);
            }           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
});

// for accept / reject friend request
$('body').on('click', ".requestStatus", function (event) {
    
    var status = $(this).data('status'),
        requestFor = $(this).data('requestfor'),
        url = BASE_URL+'home/user/friendRequest';
        
    $.ajax({
        type: "POST",
        url:url,
        data: {status:status,requestFor:requestFor}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {

            hide_loader();        
           
            if (data.status == 1){ 
                $('#remove-userId'+requestFor).hide();
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);

            }else if(data.status == 2) {
                $('#remove-userId'+requestFor).hide();
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);

            }else if(data.status == 3) {

                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);

            }else if(data.status == 4) {

                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 500);
            } else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 500);
            } 
           
        },

        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });  
});

// for change password
$(".chngPwd").click(function(){

    var btn = $(this);
    var form = $("#chngPassword");

    form.validate({

        rules: {
            oldpassword:{
                required: true,
            },
            newpassword: { 
                required: true,
                minlength: 8
            }, 
            confirmpassword: { 
                required: true,
                equalTo: "#newpassword",
                minlength: 8
           }
        },
        messages:{
            oldpassword:{
                required: oldPwd
            },
            newpassword: { 
                required: newPwd,
                minlength: newPwdLen
            },
            confirmpassword: { 
                required: conPwd,
                minlength: newPwdLen,
                equalTo: equalPwd
            }
        }
    });

    if (form.valid() === true){

        var oldP = $('#oldpassword').val();
        var newP = $('#newpassword').val();
        $.ajax({ 
            url: BASE_URL+'home/user/changePassword',
            data: {oldP:oldP,newP:newP},
            type: 'post',
            success: function(data) {

                var res = jQuery.parseJSON( data );

                if(res.status == 1){

                    toastr.success(updatePwd);
                    $('#chnge-paswrd').modal('hide');
                    $('#oldpassword,#newpassword,#confirmpassword ').val(''); 

                }else if(res.status == 0){ 

                    toastr.error(incPwd);
                    
                }else if(res.status == -1) {

                    toastr.error(res.msg);
                    window.setTimeout(function () {
                        window.location.href = res.url;
                    }, 2000);
                }
            },

            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        });        
    }
});

// for removing from favorite
function removeFav(favId){

    $('#favId'+favId).remove();
    var url = BASE_URL+'home/user/removeFavoriteFromList';

    $.ajax({
        type: "POST",
        url:url,
        data: {favId:favId}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  

            hide_loader();        
           
            if (data.status == 1){ 
                
                var count = $('#favCnt').html();
                var newCount = count - 1;
                if(newCount == 0){
                    $('#favCnt').text('');
                }else{
                    $('#favCnt').text(newCount);
                }
                
                if($('.newFavLen').length == 0){
                    $('#favouriteList').html("<div class='notFound'><h3>No favourite available.</h3></div>");
                }

            }else if(data.status == 2) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }else {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }           
        },

        error:function (){
            toastr.error(commonMsg);
        }
    });
}

$("#file-11").change(function(){

    input = this;

    if (input.files && input.files[0]) {   

        size = (input.files[0].size/1024).toFixed(2);
        let fileExtension = ['jpeg', 'jpg', 'png'];

        if ($.inArray($(input).val().split('.').pop().toLowerCase(), fileExtension) == -1) {

            $('#businessImage-err').html(imgFormateAlwd+" : "+fileExtension.join(', '));
            return false;

        }else if(size>10240){     

            $('#businessImage-err').html(imgMaxFive);
            return false;

        }else{

            let reader = new FileReader();
            reader.onload = function(e) {
                $('#pImg11').attr('src', e.target.result);
                $('#businessImage-err').html('');
            }
            reader.readAsDataURL(input.files[0]);
        }

    }else{

        $('#pImg11').attr('src',defaultImg);
        $('#businessImage-err').html('');
    }
});
    
// for submit add business record
$('body').on('click', ".submitBusinessData", function (event) {

    var that = $(this);
    var form = $("#businessForm");

    form.validate({

        rules: {
            businessName : {
                required: true, 
                minlength: 3,
                maxlength:100
            },
            businessAddress : {
                required: true
            }                     
        },
        messages: {
            businessName : {
                required: bizNameReq,
                minlength : bizNameMin,
                maxlength: bizNameMax
            },
            businessAddress : {
                required: bizAddReq
            }
        }
   });

   if (form.valid() === true){ 

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

                }  else if(data.status == 2) {

                    toastr.success(data.msg);
                    that.next().click();  

                } else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);

                } else {

                    toastr.error(data.msg);
                }           
            },
            error:function (){

                hide_loader();
                toastr.error(commonMsg);
            }
        });
    }
});

function stripeModel(e){

    $("#myModalCheckFriend").modal();

    $('.cancelSubscription').data('getsubstype',$(e).data("substype")); 
}

// to cancel subscription
$('body').on('click', ".cancelSubscription", function (event) {

    let type = $('.cancelSubscription').data("getsubstype");

    if(type == 1){

        var url = BASE_URL+'home/subscription/cancelSubscription';

    }else if(type == 2){

        var url = BASE_URL+'home/business/cancelBizSubscription';
    }    
    
    $.ajax({
        type: "POST",
        url:url,
        data: {}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {

            hide_loader();        
           
            if (data.status == 1){ 
                toastr.success(data.msg);
                window.setTimeout(function () {
                    window.location.reload();
                }, 200);

            }else if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 200);
                
            } else {

                toastr.error(data.msg);
            }           
        },

        error:function (){
            hide_loader();   
            toastr.error(commonMsg);
        }
    });  
});

/* Verification section*/

    function isNumberKey1(evt) {

        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function autotab(original, destination) {

        if (original.getAttribute && original.value.length == original.getAttribute("maxlength"))
            destination.focus()
    }

    /* Start Mobile Verification Section */
    $('#step-3').hide();
    $(".mobileView").click(function(){
        $('#step-1').show();        
        $('.hide-btn').hide();
    });

    /*$(".mobileView").click(function(){
        $('#temp-1').show(); 
        $('.hide-btn').hide();       
    });*/

    var reg_form = $("#myform");

    reg_form.validate({

        rules: {
            contactNo: {
                required: true,
                number:true,
                minlength: 7,
                maxlength:12,
                remote:BASE_URL+'home/verification/checkCNO'
            }/*, 
            code1 : {
                required: true
            }  */                    
        },
        messages: {
            contactNo: {
                required: mobNumReq,
                number:mobNumDigReq,
                minlength:mobNumMinDig,
                maxlength:mobNumMaxDig,
                remote:mobNumAlready
            }/*,
            code1 : {
                required: "Please enter confirmation code to verify your mobile number."
            }*/
        }
    });

    // to send verification code into mobile number
    $('body').on('click', ".stepForm-1", function (event) {  
           
        if(reg_form.valid() !== true){
            toastr.error(commonMsgReq);
            return false;
        }    
        var form_data = {
            'contactNo' : $("#contactNo").val(),
            'countryCode' : $("#countryCode").val()
        }

        $.ajax({
            url: BASE_URL+'home/verification/contactVerification/',
            type: "POST",
            data: form_data,
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                show_loader();
            },
            success: function(data) {

                hide_loader();
                if(data.status==1){

                    $('#step-1').hide();
                    $('#step-2').show().removeClass('showHide');
                    toastr.success(data.msg);

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
    
    // to resend verification code into mobile number
    $('body').on('click', ".resend-code", function (event) {  
            
        var form_data = {
            'contactNo' : $("#contactNo").val(),
            'countryCode' : $("#countryCode").val()
        }

        $.ajax({
            url: BASE_URL+'home/verification/contactVerification/',
            type: "POST",
            data: form_data,
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                show_loader();
            },
            success: function(data) {

                hide_loader();
                if(data.status==1){

                    $('#step-1').hide();
                    $('#step-2').show().removeClass('showHide');
                    toastr.success(data.msg);
                    $('#set_otp').val(data.otp);

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

    // to match generated verification code with user's code
    $('body').on('click', ".stepForm-2", function (event) {
  
        if(reg_form.valid() !== true){
            toastr.error(commonMsgReq);
            return false;
        } 
        var _that = $(this); 
        form = _that.closest('form');
        formData = new FormData(form[0]);            
        f_action = form.attr('action');
        
        $.ajax({
            type: "POST",
            url: f_action,
            data: formData, //only input
            processData: false,
            contentType: false,
            dataType: "JSON", 
            beforeSend: function() {
                show_loader();
            },
            success: function(data) {

                hide_loader();
                if(data.status=="1"){
                    $('#step-2').hide();
                    $('#step-3').show();
                    location.reload();
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

    /* End Mobile Verification Section */

    /* Start Id Verification Section */
    
    $(".idVeriView").click(function(){
        $('#veri-step-1').show();        
        $('.hide-ver-btn').hide();
        $('.showMsg').show();
    });

    $("#file-12").change(function(){

    input = this;

    if (input.files && input.files[0]) {    

        size = (input.files[0].size/1024).toFixed(2);

        let fileExtension = ['jpeg', 'jpg', 'png'];

        if ($.inArray($(input).val().split('.').pop().toLowerCase(), fileExtension) == -1) {

            $('#idWithHand-err').html(imgFormateAlwd+" : "+fileExtension.join(', '));
            return false;

        }else if(size>10240){

            $('#idWithHand-err').html(imgMaxFive);
            return false;

        }else{

            let reader = new FileReader();

            reader.onload = function(e) {

            $('#pImg2').attr('src', e.target.result);
            $('#idWithHand-err').html('');

            }

            reader.readAsDataURL(input.files[0]);
        }

        }else{

            $('#pImg2').attr('src',defaultImg);
            $('#idWithHand-err').html('');
        }
    });
    
    $('body').on('click', ".submitIdVerificationData", function (event) {
            
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
                    
                    $(".rem-clss").removeClass("submitIdVerificationData");
                    toastr.success(data.msg);
                    location.reload();

                }  else if(data.status == -1) {

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

    /* END Id Verification Section */

    /* Start Face varification */

    $(".faceVeriView").click(function(){
        
        $('#face-step-1').show();        
        $('.hide-face-ver-btn').hide();
        $('.showFaceMsg').show();
    });

    $("#face-file-12").change(function(){
        
        input = this;

        if (input.files && input.files[0]) {    

            size = (input.files[0].size/1024).toFixed(2);

            let fileExtension = ['jpeg', 'jpg', 'png'];

            if ($.inArray($(input).val().split('.').pop().toLowerCase(), fileExtension) == -1) {

                $('#faceImage-err').html(imgFormateAlwd+" : "+fileExtension.join(', '));
                return false;

            }else if(size>10240){

                $('#faceImage-err').html(imgMaxFive);
                return false;

            }else{

                let reader = new FileReader();

                reader.onload = function(e) {

                    $('#facePImg').attr('src', e.target.result);
                    document.getElementById('facePImg').src = window.URL.createObjectURL(input.files[0]);
                    $('#faceImage-err').html('');
                }

                reader.readAsDataURL(input.files[0]);
            }

        }else{

            $('#facePImg').attr('src',defaultImg);
            $('#faceImage-err').html('');
        }
    });
    
    $('body').on('click', ".submitFaceVerificationData", function (event) {
            
        var _that = $(this), 
            form = _that.closest('form'),      
            formData = new FormData(form[0]),
            f_action = form.attr('action');
           
        $.ajax({
            type        : "POST",
            url         : f_action,
            data        : formData, //only input
            processData : false,
            contentType : false,
            dataType    : "JSON",

            beforeSend: function () { 
                show_loader(); 
            },
            success: function (data, textStatus, jqXHR) {

                hide_loader();        
               
                if (data.status == 1){
                    
                    $(".face-rem-clss").removeClass("submitFaceVerificationData");
                    toastr.success(data.msg);
                    location.reload();

                }  else if(data.status == -1) {

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

    /* END Face varification */

/* Verification section*/

/* Start to update more info user's detail*/

var formProfile = $("#up-profile");

    formProfile.validate({
        rules: {
             
            work_id: { 
                required: true
            },
            edu_id: { 
                required: true
            },
            height: {
                required:true
            },
            weight: {
                required:true,
                min: 1
            },
            unit: {
                required:true
            },
            relationship: {
                required:true
            }
        },
        messages:{
            weight: {
                required:weightReq,
                min: weightReqVal
            }
        }
    });

    $("#updateUserProfile").click(function(){
        
        //var numTotalImg = $('img.img_count_no').length,
        var formProfile = $("#up-profile"),
            interest_cont = $('#interestBox');
            AnswerInput = $('#languageBox');

        //form validation
        if (formProfile.valid() !== true){
            //put toatsr error message here
            toastr.error(commonMsgReq);
            return false;
        }
        
        //interest check
        if(interest_cont.val()=='' || interest_cont.val()==null){
            
            //put toatsr error message here
            toastr.error(intReq);
            return false;
        }

        //interest check
        if(AnswerInput.val()=='' || AnswerInput.val()==null){
            
            //put toatsr error message here
            toastr.error(lanReq);
            return false;
        }
        
        //all good, validation passed, proceed to submit form now
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
                    //window.location.href = data.url;
                }else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);

                }else{
                   
                    toastr.error(data.msg);                   
                } 
            },

            error:function (){

                hide_loader(); 
                toastr.error(commonMsg);
            }
        });
        
    });

/* End to update more info user's detail*/


/* Start to update basic info user's detail*/

// to upload image using ajax
function addMoreProfileImg(e){

    //var numTotalImg = $('.img_count_no').length;
    var imgCount    = $('#imgCount').val();
    
    if(imgCount < 5)
    {
        var file_data   = $('#newImgMy').prop("files")[0],
        hid_inp         = $('#imgCount');
        form_data       = new FormData();

        form_data.append("profileImage", file_data);
        form_data.append("imgCount", imgCount);

        $.ajax({

            url         : BASE_URL+'home/user/addProfileImage',
            cache       : false,
            dataType    : 'json',
            contentType : false,
            processData : false,
            data        : form_data,
            type        : 'post',

            beforeSend: function () {
                
                $('#tl_admin_loader').show();
            },

            success: function(data) {

                $('#tl_admin_loader').hide();

                if(data.status == 1){

                    imageSlider();
                    $(".updateSliderImg div.item:last").before(data.html);
                   // $('.updateSliderImg').load(' .updateSliderImg');
                    $("#apoim-gal").html(data.sliderImg);
                    
                }else if(data.status == 2){
                    
                    toastr.error(data.msg);
                   
                }else if(data.status == -1) {
                    
                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 2000);

                }else{                   
                    toastr.error(data.msg);
                }
                hid_inp.val(data.count);
                $('#newImgMy').val('');
            },
            error:function (){
                hide_loader(); 
                toastr.error(commonMsg);
            }
        }); 

    }else{        
        $('#tl_admin_loader').hide();      
        toastr.error(fiveImgOnlyReq);           
    }               
}
$("#exampleFormControlSelect14").change(function(){

    var genVal = $(this).val();
    
    if(genVal == 2){
        $('#gender-hide-show').show();
    }else{
        $('#gender-hide-show').hide();
    }
});
var formInfo = $("#up_basic_info");
    formInfo.validate({
        rules: {
            fullName : {
                required: true, 
                minlength: 3,
                maxlength:100,
                lettersonly: true        
            },
            birthday : {
                required: true             
            },
            gender: { 
                required: true
            }, 
            showOnMap: {
                required: true
            },
            address: {
                required:true
            }
        },
        messages:{
            fullName : {
                required: fullNameReq,
                minlength : fullNameMinLen,
                maxlength: fullNameMaxLen
            },
            birthday : {
                required: birthReq
            }
        }
    });

    $("#updateBasicInfo").click(function(){
        
        var numTotalImg = $('.img_count_no').length,
            formInfo = $("#up_basic_info");
        
        //form validation
        if (formInfo.valid() !== true){
            //put toatsr error message here
            toastr.error(allFieldReq);
            return false;
        }
        
        //profile image check
        if(numTotalImg==0){
            toastr.error(oneImgReq);
            return false;
        }
        
        //all good, validation passed, proceed to submit form now
        var _that   = $(this), 
        form        = _that.closest('form'),      
        formData    = new FormData(form[0]),
        f_action    = form.attr('action');
        latitude    = $('#usrlat').val();
        longitude   = $('#usrlong').val();
        city        = $('#usrproCity').val();
        state       = $('#usrproState').val();
        country     = $('#usrproCountry').val();
      
        formData.append('latitude',latitude);
        formData.append('longitude',longitude);
        formData.append('city',city);
        formData.append('state',state);
        formData.append('country',country);

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
                    overView();
                    toastr.success(data.msg);
                    //window.location.href = data.url;
                }else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);

                }else{
                   
                    toastr.error(data.msg);                   
                } 
            },

            error:function (){
                hide_loader(); 
                toastr.error(commonMsg);
            }
        });        
    });

    // to show popup for deleting image
    function deleteProfileImages(id){

        var hid_inp = $('#imgCount');
        var faceVerifyStatus = $('#faceVerifyStatus').val();
        if(hid_inp.val() == 1 && faceVerifyStatus == 1){
            $('#chngtxt').text(reverifyFaceReq);
        }

        $("#myModaldelImg").modal('show');
        $(".set_del_btn").attr("onclick", "deleteProfileImg('"+id+"')");
    }

    // to delete image
    function deleteProfileImg(userImgId){

        var hid_inp     = $('#imgCount');
        var imgCount    = hid_inp.val();
        var faceVerifyStatus = $('#faceVerifyStatus').val();

        $.ajax({

            url         : BASE_URL+'home/user/deleteProfileImages',
            data        : {userImgId:userImgId,imgCount:imgCount,faceVerifyStatus:faceVerifyStatus},
            type        : 'POST',
            dataType    : "JSON",

            beforeSend: function () {
                $('#tl_admin_loader').show();
            },

            success: function(data) {
                
                $('#tl_admin_loader').hide();

                if(data.status  ==  1){

                    $("#myModaldelImg").modal('hide');
                    $('.addMore').hide();
                    imageSlider();
                     
                }if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 2000);

                }else{
                    $('.addMore').show();                    
                }

                $('#dImg'+userImgId).html('');
                $('#dImg'+userImgId).hide();
                
                hid_inp.val(data.remcount);
            },

            error:function (){

                $('#tl_admin_loader').hide(); 
                toastr.error(commonMsg);
            }
        });
    }

    // for appointment type
    $(".makeFreePay").click(function(){
        $("#freeApp").prop("checked", true);        
        $("#okPayBtn").click();
    });

    // for appointment type
    $(".makeFree").click(function(){
        $("#freeApp").prop("checked", true);        
    });

    // for appointment type
    $(".getAppPay").click(function(){
        var selAppValue = $('input[name=appointmentType]:checked').val(); 
        if(selAppValue == '1'){
            var accStatus = $('#getAccStatus').val();
            
            if(accStatus == 0){
                $('#myModalShowPayment').modal('show');
                $('#bank-msg').text(addbankReq);
            }/*else{
                $('#myModalShowPayment').modal('show');
                $('#bank-msg').text('Do you want to update bank account details?');
            }*/
        }
    });

/* End to update basic info user's detail*/

/* Start to manage bank account detail*/
$('body').on('click', ".addBankAccountP", function (event) {

    var form = $("#add_band_accP");

    form.validate({

        rules: {
            firstName : {
                required: true, 
                minlength: 3,
                maxlength:100
            },
            lastName : {
                required: true, 
                minlength: 3,
                maxlength:100
            },
            /*routingNumber: {
                required: true
            },*/
            dob: {
                required: true
            },
            accountNumber : {
                required: true              
            } 
            /*postalCode : {
                required: true
            },
            ssnLast : {
                required: true
            }  */                     
        },
        messages: {
            firstName : {
                required: eBankFN,
                minlength : eBankFNMin,
                maxlength: eBankFNMax
            },
            lastName : {
                required: eBankLN,
                minlength : eBankLNMin,
                maxlength: eBankLNMax
            },
            /*routingNumber : {
                required: eBankRN
            },*/
            dob : {
                required: eBankBD
            },
            accountNumber : {
                required: eBankACN
            }
            /*postalCode : {
                required: eBankPC
            },
            ssnLast : {
                required: eBankSSN
            }*/
        }
    }); 

    if (form.valid() === true){
        
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
                        window.location.href = data.url;
                    }, 500);

                } else if(data.status == 2) {

                    $('#getAccStatus').val('1');
                    $('.addBankAccountP').text('Update Account');
                    $("#awesome-item-11").prop("checked", true);
                    toastr.success(data.msg);
                    
                } else if(data.status == 3) {
                    
                    toastr.error(data.msg);
                    
                } else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 2000);

                }else {
                    
                    toastr.error(data.message[0]);
                }           
            },

            error:function (){
                toastr.error(commonMsg);
            }
        });
    }
});

/* End to manage bank account detail*/

/* Start review list section */

var appUrl = BASE_URL+"home/user/appReviewList/";
var isFunctionCall = false;
var urlImg = BASE_URL+'frontend_asset/img/Spinner-1s-80px.gif';
function appReviewList(page,type,id)
{
    if(!isFunctionCall){

        isFunctionCall = true;
        $.ajax({

            url: appUrl,

            type: "POST",

            data:{page:page,type:type,id:id}, 

            beforeSend: function() {
                $('.showLoader').html("<img src='"+urlImg+"' alt=''>");                              
            },                       
            success: function(data){
                
                $('.showLoader').html("");

                if(page == 0){

                    $("#appointmentReviewList").html(data);

                }else{

                    $("#appointmentReviewList").append(data);    
                }

                $("#page-count").val(Number(page)+Number(1));
                $("#tabType").val('reviewTab');
            },
            complete:function(){
                isFunctionCall = false;
            }
        }); 
    }       
}
/* end review list section */

/* to show business location map*/
function initMapProfileBusiness() {

    var name        = $('#mapId').data('name');
    var address     = $('#mapId').data('address');
    var latitude    = $('#mapId').data('lat');
    var longitude   = $('#mapId').data('long');
    var icon        = $('#mapId').data('icon');
    var img         = $('#mapId').data('img');

    var map = new google.maps.Map(document.getElementById('mapId'), {
        zoom: 11,
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
initMapProfileBusiness();
