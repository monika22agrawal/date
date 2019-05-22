hide_loader();
function show_loader(){
    $('#tl_admin_loader').show();
}

function hide_loader(){
    $('#tl_admin_loader').hide();
}

jQuery.validator.addMethod("email", function(value, element) {
    return this.optional( element ) || ( /^[a-zA-Z]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/.test( value ) && /^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/.test( value ) );
}, 'Please enter valid email address.');

/*================= START REGISTRATION SECTION =================*/

// set date picker year minimum 18 year age from current year
$(function () {

    var todayDate = new Date().getYear();

    $('#datepicker4').datetimepicker({
        format : 'YYYY-MM-DD',
        minDate : '1919-01-01',
        maxDate: new Date(new Date().setYear(todayDate + 1882)),
    });
});

setTimeout(function() {
    $('.alert-danger').fadeOut();
    $('.alert-success').fadeOut();
    $('.alert-warning').fadeOut();
}, 5000);

//registration form validation
var reg_form = $("#myform");

reg_form.validate({
    rules: {
        email : {
            required: true,
            email: true,
            remote:BASE_URL+'home/login/checkEmail'            
        },
        password : {
            required: true,
            minlength: 8         
        },
        otp : {
            required: true,
            number: true         
        },             
        fullName : {
            required: true, 
            minlength: 3,
            maxlength:100,
            lettersonly: true        
        },
        birthday : {
            required: true               
        },
        gender : {
            required: true
        },
        purpose : {
            required: true
        },
        dateWith : {
            required: true
        },
        eventInvitation : {
            required: true
        }                        
    },
    messages: {
        
        email : {
            required: emailReq,
            email:emailValid,
            remote:emailAlready
        },
        password:{
            required: pwdReq,
            minlength : pwdMinLen
        },
        otp : {
            required: emailCode,
        },
        fullName : {
            required: fullNReq,
            minlength : fullNMinLen,
            maxlength: fullNMaxLen
        },
        birthday : {
            required: birthReq
        },
        gender : {
            required: genderReq
        },
        purpose : {
            required: purposeReq
        },
        dateWith : {
            required: dateWReq
        },
        eventInvitation : {
            required: eventtypeReq
        }
    },
    errorPlacement: function(error, element) 
    {

      /*   if(element.attr("type") == "radio")
        {
            //error.appendTo( element.closest('boxed').next() );
            error.appendTo( element.parents().next() );
        }*/
        //error.insertAfter( element );
        if($('input[name=gender]:checked').length==0)
        {
            error.appendTo( element.parents().next('.genderError') );

        }else{
            error.insertAfter( element );
        }

        if($('input[name=purpose]:checked').length==0)
        {
            error.appendTo( element.parents().next('.purposeError') );

        }else{
            error.insertAfter( element );
        }

        if($('input[name=dateWith]:checked').length==0)
        {
            error.appendTo( element.parents().next('.dateWithError') );
            
        }else{
            error.insertAfter( element );
        }

        if($('input[name=eventInvitation]:checked').length==0)
        {
            error.appendTo( element.parents().next('.eventInvitationError') );
        }else{
            error.insertAfter( element );
        }        
    }
}); //End register validation


// to send verification code through email
$('body').on('click', ".stepForm-1", function (event) {

    if(reg_form.valid() !== true){
        toastr.error(commonMsgReq);
        return false;
    }    
    
    var form_data = {
        'email' : $("#email").val()
    }

    $.ajax({
        url: BASE_URL+'home/login/emailVerification/',
        type: "POST",
        data: form_data,
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            show_loader();
        },
        success: function(data) {

            hide_loader();
            if(data.status=="0"){
                
                toastr.error(data.error);

            }else if(data.status=="1"){
                //$("#err-sucess").show();
                // $('#step-5').hide();
                // $('#step-6').show().removeClass('showHide');
                //$("#error-sucess").html('Verification code has been sent successfully'); 

            }else if(data.status=="2"){

                //$("#err-invalid").show();
                toastr.error(data.error);
            }      
        },
        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});


// to resend verification code through email
$('body').on('click', ".stepForm-4", function (event) {  
        
    var form_data = {
        'email' : $("#email").val()
    }

    $.ajax({
        url: BASE_URL+'home/login/emailVerification/',
        type: "POST",
        data: form_data,
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            show_loader();
        },
        success: function(data) {

            hide_loader();
            if(data.status=="0"){

                toastr.error(data.error);

            }else if(data.status=="1"){
                //$("#err-sucess").show();
                // $('#step-5').hide();
                // $('#step-6').show().removeClass('showHide');
                //$("#error-sucess").html('Verification code has been sent successfully'); 

            }else if(data.status=="2"){

                $("#err-invalid").show();
                $("#error-invalid").html(data.error);
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

    var that = $(this);

    if(reg_form.valid() !== true){
        return false;
    }    
    var form_data = {
        'otp' : $("#get_otp").val(),
        'email' : $("#email").val()
    }

    $.ajax({
        url: BASE_URL+'home/login/matchVerificationCode/',
        type: "POST",
        data: form_data,
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            show_loader();
        },
        success: function(data) {

            hide_loader();
            if(data.status=="1"){
                
                // $('.stepForm-2').tr('otpcls');
                //$('.otpcls').removeClass('stepForm-2');
                //var get_current_step = $(this).parents('fieldset').attr('data-step');
                that.next().click();

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

// to submit final from for registration
$('body').on('click', ".stepForm-3", function (event) {

    if(reg_form.valid() !== true){
        toastr.error(commonMsgReq);
        return false;
    }    
    var _that   = $(this); 
    form        = _that.closest('form');
    formData    = new FormData(form[0]);            
    f_action    = form.attr('action');
    address     = $('#addr').val();
    latitude    = $('#latitude').val();
    longitude   = $('#longitude').val();
    city        = $('#city').val();
    state       = $('#state').val();
    country     = $('#country').val();
    formData.append('address',address);
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
            //$('.stepForm-3').prop('disabled', true);
            show_loader();
        },
        success: function (data, textStatus, jqXHR) {            
            
            if(data.status == 1){

                toastr.success(resSuccess);
                window.setTimeout(function () {
                    window.location.href = BASE_URL+'home/business/addBusiness';
                }, 200);                
                
            }else if(data.status == 0){
                
                hide_loader(); 
                toastr.error(data.msg);
                
            }else{

                window.location.href = BASE_URL+'home/login/registration';                          
            }
            $('.stepForm-3').prop('disabled', false);
        },
        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });
});

/*================= END REGISTRATION SECTION =================*/


/*================= LOGIN SECTION =================*/

// For login
$(".spinner").click(function(){

    var form = $("#loginForm");

    form.validate({

        rules: {
            
            email : {
                required: true,
                email: true,
            }, 
            password : { 
                required:true,
                minlength: 8 
            }                       
        },
        messages: {
            email : {
                required: emailReq,
                email:emailValid
            },
            password : {
                required : pwdReq,
                minlength : pwdMinLen
            }
        }
    });

    if(form.valid() === true){

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
            beforeSend: function () { 
                show_loader();
            },
            success: function (data, textStatus, jqXHR) {  

                hide_loader();

                if(data.status == '1'){

                    toastr.success(data.message);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 200);
                }else{
                    toastr.error(data.message);
                }
            },
            error:function (){
                hide_loader(); 
                toastr.error(commonMsg);
            }
        });       
    }
});
 
// Facebook login

function socialLogin(fbData){

    $.ajax({
        type: "POST",
        url:BASE_URL+'home/login/checkSocial',
        data: fbData, 
        beforeSend: function () { 
            show_loader();
        },
        success: function(data)
        {
            hide_loader(); 

            if(data == 'SR'){ 
                
                var url = BASE_URL+'home/login/registration';
                var form = $('<form action="' + url + '" method="post">' +
                  '<input type="text" name="page" value="1234" />' +
                  '</form>');
                $('body').append(form);
                form.submit();

            }else if(data == 'AE'){

                toastr.error(emailAlready);

            }else if(data == 'NA'){
                
                toastr.error(userInactive);
               
            }else if(data == 'SL'){
                
                window.location = BASE_URL+'home/nearByYou';
            }
        },
        error:function (){
            hide_loader();
            toastr.error(commonMsg);
        }
    });
}

function getUserData() {
    
    FB.api('/me',{fields: 'name,id,email'}, function(response) {  
        
        var fbData = {
            name:  response.name,
            socialId: response.id,
            email: (response.email != '' && typeof response.email != "undefined" ) ? response.email : '',
            socialType: "facebook",
            profileImage:"https://graph.facebook.com/"+response.id+"/picture?type=large"
        };     
                 
        facebookLogout();
        sessionStorage.setItem("fullName", fbData.name);
        sessionStorage.setItem("socialId", fbData.socialId);
        sessionStorage.setItem("email", fbData.email);
        sessionStorage.setItem("socialType", fbData.socialType);
        sessionStorage.setItem("profileImage", fbData.profileImage);
        socialLogin(fbData);
    });
}

function facebookLogout() {
    FB.logout(function() {})
}

window.fbAsyncInit = function() {
    //SDK loaded, initialize it
    FB.init({
        appId      : '464296957352010',
        xfbml      : true,
        version    : 'v2.2'
    });
};

//load the JavaScript SDK
(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.com/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


//add event listener to login button
document.getElementById('loginBtn').addEventListener('click', function() {
    //do the login
    FB.login(function(response) {

        if (response.authResponse) {
            //user just authorized your app
            //document.getElementById('loginBtn').style.display = 'none';
            getUserData();
        }

    }, {scope: 'email,public_profile', return_scopes: true});

}, false);
    

// Google Login

var googleUser = {};

var startApp = function() {

    gapi.load('auth2', function(){

      // Retrieve the singleton for the GoogleAuth library and set up the client.
        auth2 = gapi.auth2.init({
            client_id: '987439329837-ocoraaqng2ju4b7j9dipcn5bejl9i4d8.apps.googleusercontent.com',
            cookiepolicy: 'single_host_origin',
            // Request scopes in addition to 'profile' and 'email'
            //scope: 'additional_scope'
        });

        attachSignin(document.getElementById('customBtn'));

    });
};

function attachSignin(element) {
    
    auth2.attachClickHandler(element, {},

        function(googleUser) {

            data =  googleUser.getBasicProfile();

            var gData = {
                name:  data.ig,
                socialId: data.Eea,
                email: data.U3,
                socialType: "gmail",
                profileImage:data.Paa
            };
            sessionStorage.setItem("fullName", gData.name);
            sessionStorage.setItem("socialId", gData.socialId);
            sessionStorage.setItem("email", gData.email);
            sessionStorage.setItem("socialType", gData.socialType);
            sessionStorage.setItem("profileImage", gData.profileImage);
            socialLogin(gData);
            
        }, function(error) {

        //toastr.error(JSON.stringify(error, undefined, 2));
    });
}
startApp();

//forgot form validation

$(".forgot-password").click(function(){
    
    var forgot_form = $("#forgot_form");  
    forgot_form.validate({ 
        rules: {           
            email: {
                required: true, 
                email: true
            }               
        },
        messages:{
            email: {
                required: emailReq, 
                email: emailValid
            }
        }
        
    }); //End forgot validation

    if (forgot_form.valid() === true){
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

                    $('#frgt-pswrd').modal('hide');
                    toastr.success(data.message);

                }else if(data.status == 2) {

                    toastr.error(data.message);

                }else if(data.status == 3) {

                    toastr.error(data.message);

                }else if(data.status == 4) {

                    toastr.error(data.message);

                }else{

                    toastr.error(data.message);                        
                } 
            },

            error:function (){
                hide_loader(); 
                toastr.error(commonMsg);
            }
        });  
    }
});

/*================= END LOGIN SECTION =================*/


/*================= Form Wizar Js For Registration SECTION =================*/

(function( $ ) {
        
    //registration form validation
    var reg_form = $("#myform");

    function scroll_to_class(element_class, removed_height) {
        var scroll_to = $(element_class).offset().top - removed_height;
        if($(window).scrollTop() != scroll_to) {
            $('.form-wizard').stop().animate({scrollTop: scroll_to}, 0);
        }
    }

    jQuery(document).ready(function() {        
        /*
            Form
        */
        $('.form-wizard fieldset:first').fadeIn('slow');
        
        $('.form-wizard .required').on('focus', function() {
            $(this).removeClass('input-error');
        });
        
        // next step
        $('.form-wizard .btn-next').on('click', function() {

            var parent_fieldset = $(this).parents('fieldset');

            var get_current_step = $(this).parents('fieldset').attr('data-step');

            var next_step = true;
            // navigation steps / progress steps
            var current_active_step = $(this).parents('.form-wizard').find('.form-wizard-step.active');
            var progress_line = $(this).parents('.form-wizard').find('.form-wizard-progress-line');

            var radioValue = $("input[name='gender']:checked").val();
            // fields validation
            /*parent_fieldset.find('.required').each(function() {

                if( $(this).val() == "" ) {

                    $(this).addClass('input-error');
                    next_step = false;

                } else {

                    $(this).removeClass('input-error');
                }
            });*/

            // fields validation
            if( reg_form.valid() ) {
                
                parent_fieldset.fadeOut(400, function() {
                    // change icons
                    current_active_step.removeClass('active').addClass('activated').next().addClass('active');
                    // progress bar
                    //bar_progress(progress_line, 'right');

                    var steps = $(this).next();

                    if(radioValue == "1" || radioValue == "3"){
                        
                        if(get_current_step == '3' && (socialId == '' || socialId == null)){
                            steps = $(this).next().next();
                        }

                        if((socialId != '' && socialId != null) && get_current_step == "3"){
                            steps = $(this).next().next().next().next();
                        }

                    }else if(socialId != '' && socialId != null && get_current_step == "4"){
                        steps = $(this).next().next().next();
                    }
                    steps.fadeIn();

                    // scroll window to beginning of the form
                    scroll_to_class( $('.form-wizard'), 20 );
                });
            }else{
                //toastr.error('error');
            }
        });
        
        // previous step
        $('.form-wizard .btn-previous').on('click', function() {
            // navigation steps / progress steps
            var current_active_step = $(this).parents('.form-wizard').find('.form-wizard-step.active');
            var progress_line = $(this).parents('.form-wizard').find('.form-wizard-progress-line');
            var radioValue = $("input[name='gender']:checked").val();
            var get_current_step = $(this).parents('fieldset').attr('data-step');

            $(this).parents('fieldset').fadeOut(400, function() {
                // change icons
                current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
                // progress bar
                //bar_progress(progress_line, 'left');
                // show previous step
                
                var steps = $(this).prev();

                if(radioValue == "1" || radioValue == "3"){
                    
                    if(get_current_step == '5' && (socialId == '' ||socialId == null)){
                        
                        steps = $(this).prev().prev();
                    }

                    if(socialId != '' && socialId != null && get_current_step == "7"){
                        steps = $(this).prev().prev().prev().prev();
                    }

                }else if(socialId != '' && socialId != null && get_current_step == "7"){

                    steps = $(this).prev().prev().prev();
                }
                steps.fadeIn();

                // scroll window to beginning of the form
                scroll_to_class( $('.form-wizard'), 20 );
            });
        });
        
        // submit
        $('.form-wizard').on('submit', function(e) {
            
            // fields validation
            $(this).find('.required').each(function() {

                if( $(this).val() == "" ) {

                    e.preventDefault();
                    $(this).addClass('input-error');

                } else {
                    $(this).removeClass('input-error');
                }
            });
            // fields validation            
        });
    }); 

}( jQuery ));

/*=================End Form Wizar Js For Registration SECTION =================*/
