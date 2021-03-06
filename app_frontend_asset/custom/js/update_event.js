function isNumberKey1(evt) {

    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {           
        return false;
    }    
    return true;
}
function showImage(e,id) {

    $("#address").val('');   
    $('.slider-section').removeClass('image-active');
    $(e).addClass('image-active');

    var img = $('#bizId'+id).data('img');
    var name = $('#bizId'+id).data('name');
    var dis = $('#bizId'+id).data('dis');
    var add = $('#bizId'+id).data('add');
    var lat = $('#bizId'+id).data('lat');
    var long = $('#bizId'+id).data('long');
   
    $("#appMeet").html('<div class="media-left"><img id="setBizImg" src="'+img+'"></div><div class="media-body"><h4 id="setBizName" class="media-heading">'+name+'</h4><p id="setBizAdd">'+add+'</p><p id="setBizDis">'+dis+'</p></div>');

    $('#setBizName').text(name);
    $('#setBizImg').attr('src', img);
    $('#setHAdd').val(add);
    $('#setHBizId').val(id);
    $('#setHLat').val(lat);
    $('#setHLong').val(long);
}
$(function () {  

    $('.first').click();
    $('.first').addClass('image-active');

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
                lng = place.geometry.location.lng(),
                addr = place.formatted_address;
             
                $("#usrsadd").val(addr);
                $("#usrsearchlat").val(lat);
                $("#usrsearchlong").val(lng);
                $('#eventCity').val(city);
                $('#eventState').val(state);
                $('#eventCountry').val(country);
       
          // place.geometry  -- this is used to detect whether User entered the name of a Place that was not suggested and pressed the Enter key, or the Place Details request failed.
        });
    }
    google.maps.event.addDomListener(window, 'load', initializeAdd); //initialise google autocomplete API on load
    
    function initMap() {

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
            animation: google.maps.Animation.DROP,
            title: 'Uluru (Ayers Rock)'
        });

        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
    }

//$(window).load(function() {

    initMap();
//}); 

    var d = new Date();
    d = new Date(d.getFullYear()+"/"+(d.getMonth()+1)+"/"+(d.getDate()));
    var startDate = new Date($('#datetimepicker6').val());
    var endDate = new Date($('#datetimepicker7').val());
    
    if(d.getHours()>=20){
        d.setDate(d.getDate() + 1);
    }
    d.setHours(08,00,00);

    if(startDate<d){
       d=startDate;
    }    
    $('#datetimepicker6').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD hh:00 A',
        enabledHours: [20],
        //inline: true,
        sideBySide: true,
        ignoreReadonly: true,
        //defaultDate:startDate,
        minDate: d
        //minDate: moment().format('L') 
    });
    d.setHours(20,30,00);
    $('#datetimepicker7').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD hh:mm A',
        ignoreReadonly: true,
        //inline: true,
        sideBySide: true,
        //ignoreReadonly: true,
        //defaultDate:endDate,
        minDate: d
        //minDate: moment().format('L') 
    });
    $("#datetimepicker6").on("dp.change", function (e) {
        $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
        $(this).data("DateTimePicker").hide();
    });

    var todayDate = new Date().getYear();

    $('#date').datetimepicker({
        format          : 'YYYY-MM-DD',
        ignoreReadonly  : true,
        maxDate         : new Date(new Date().setYear(todayDate + 1887))
    });
});

function formatDate(date) {
    
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [month,day,year].join('/');
}

$(".makeFree").click(function(){
    $('#cur').slideUp();
    $("#freeEvent").prop("checked", true);
});

$(".getPay").click(function(){

    var selValue = $('input[name=payment]:checked').val(); 
    if(selValue == '1'){
        var accStatus = $('#getAccStatus').val();
        
        if(accStatus == 0){
            $("#myModalCheckPayment").modal({backdrop: 'static', keyboard: false});
            $('#myModalCheckPayment').modal('show');
        }else{
            $('#myModalShowPayment').modal('show');
        }
        
        $('#cur').slideDown();
    }else{
        $('#cur').slideUp();
    }
});

function getCheckUncheck(e){
    
    var newMemId = $(e).val();
    let getOldIds = $("#memberId").val();

    var ids = [];
    if($('#box-'+newMemId).is(':checked')) {

        // checked        
        var ids = getOldIds ? (getOldIds+','+newMemId) : newMemId;    
        $("#memberId").val(ids);

    }else {

        // unchecked        
        var array = getOldIds.split(',');
        var index = array.indexOf(newMemId);
        if (index > -1) {

            array.splice(index, 1);
        }
        array = array.toString();
        $("#memberId").val(array);
    }
}

var isFunctionCall = false;
// for showing friend list on popup
function allFriends(is_scroll=0,apply=0)
{  
    var scroll_loader = $('#showLoader');
    //var gender      = $("input[name=eventUserType]:checked").val(),
    var privacy     = $("input[name=privacy]:checked").val(),
        name        = ($('#searchName').val()).trim(),
        latitude    = $('#usrsearchlat').val(),
        longitude   = $('#usrsearchlong').val(),
        address     = $('#usrsadd').val(),
        city        = $('#eventCity').val(),
        state       = $('#eventState').val(),
        country     = $('#eventCountry').val(),
        memberId    = $('#memberId').val(),
        arr         = [],
        i           = 0;

        $('.rate_Checkbox:checked').each(function () {
           arr[i++] = $(this).val();
        });

        var gender = [];
        $('.gender_checkbox:checked').each(function(){ //iterate all listed checkbox items
            gender.push(this.value);
        });

        //for new request, reset isNext and offset
        if(is_scroll==0){
            scroll_loader.attr('data-isNext',1);
            scroll_loader.attr('data-offset',0); //set new offset
        }
        
        var offset = scroll_loader.attr('data-offset'),
            isNext = scroll_loader.attr('data-isNext'), //to see if we have next record to load
            list_cont = $('#all-friends'); //container where list will be appended

        //abort request if previous request is in progress OR next record is not available
        if(isFunctionCall || (isNext==0 && is_scroll==1)){
            return false;
        }

        isFunctionCall = true;
        
    $.ajax({

        url: BASE_URL+'home/event/getInvitationUserList',
        type: "POST",
        data:{gender:gender,privacy:privacy,name:name,rating:arr,latitude:latitude,longitude:longitude,city:city,state:state,country:country,memberId:memberId,address:address,page:offset},   
        dataType: "json",           
        beforeSend: function(){
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
                toastr.error(wrongMsg);
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
    if(apply == 1){
             
        $('#img-flter').click();
    }     
}

var typingTimerr;                //timer identifier
var doneTypingIntervall = 300 ;  //time in ms, 5 second for example
var $input = $('#searchName');

//on keyup, start the countdown
$input.on('keyup', function () {
    clearTimeout(typingTimerr);
    typingTimerr = setTimeout(doneTypingg, doneTypingIntervall);
});

//on keydown, clear the countdown 
$input.on('keydown', function () {
    clearTimeout(typingTimerr);
});

//user is "finished typing," do something
function doneTypingg () {
    allFriends(0);
}

function resetAll(){
    $('#img-flter').click();
    //$('#tl_admin_loader').show();  
    document.getElementById("5-str").checked = false;
    document.getElementById("4-str").checked = false;
    document.getElementById("3-str").checked = false;
    document.getElementById("2-str").checked = false;
    document.getElementById("1-str").checked = false;
    $('#searchName').val('');
    $('#usrsearchlat').val('');
    $('#usrsearchlong').val('');
    $('#usraddress').val('');
    $('#eventCity').val('');
    $('#eventState').val('');
    $('#eventCountry').val('');
    $('#usrsadd').val('');
       
    allFriends(0);
}

// for check / uncheck all
function checkUncheckAll(){

    var status=$("#awesome-item-11").is(':checked');
    if(status==true){
        $('.gender_checkbox').each(function(){ //iterate all listed checkbox items
            this.checked = true; //change ".checkbox" checked status
        });
    }else{
        $('.gender_checkbox').each(function(){ //iterate all listed checkbox items
            this.checked = false; //change ".checkbox" checked status
        });
    }
}

// manage check / uncheck all if all select and one not select & vice versa
function updateSelection(obj){
    
    if(obj.checked == false){ //if this item is unchecked
        $("#awesome-item-11").prop('checked', false);
    }
    //check "select all" if all checkbox items are checked
    if ($('.gender_checkbox:checked').length == $('.gender_checkbox').length ){ 
       $("#awesome-item-11").prop('checked', true);
    }
}


// for getting checked member's ids to save
function save(){

    var total = $('.friend_checkbox:checked').length;

    if(total > 0){
        /*var ids = [];
        $('.friend_checkbox:checked').each(function(){ //iterate all listed checkbox items
            ids.push(this.value);
        });
        $("#memberId").val(ids);*/
        $('.updateEvent').click();

    }else{
        toastr.error(eInvfrnd);
        return false;
    }
}

// to add bank account
$('body').on('click', ".addBankAccount", function (event) {

    var form = $("#add_band_acc");

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
           /* postalCode : {
                required: true
            },
            ssnLast : {
                required: true
            }   */                    
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
            $('.addBank').removeClass('addBankAccount');

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

                    $('#myModalCheckPayment').modal('hide');
                    toastr.success(data.msg);

                } else if(data.status == 2) {

                    $('#myModalCheckPayment').modal('hide');
                    $('#getAccStatus').val('1');
                    toastr.success(data.msg);
                    
                } else if(data.status == 3) {
                    $('.addBank').addClass('addBankAccount');
                    toastr.error(data.msg);
                    
                } else if(data.status == -1) {

                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 2000);

                }else if(data.status == 0) {
                    $('.addBank').addClass('addBankAccount');
                    toastr.error(data.msg);
                    
                }else {
                   
                    $('.addBank').addClass('addBankAccount');
                    toastr.error(data.message[0]);
                }           
            },

            error:function (){
                hide_loader();
                toastr.error(commonMsg);
            }
        });
    }
});

var eventForm = $("#eventUpdateForm");

    eventForm.validate({

        rules: {
            eventName : {
                required: true, 
                minlength: 3,
                maxlength:100
            },
            /*eventPlace: {
                required: true
            },*/
            eventStartDate : {
                required: true              
            }, 
            eventEndDate : {
                required: true
            },
            userLimit : {
                required: true,  
                min: 1             
            },
            eventUserTypeG : {
                required: true               
            }                    
        },
        messages: {
            eventName : {
                required: eventName,
                minlength : eNameMinLen,
                maxlength: eNameMaxLen
            },
            /*eventPlace : {
                required: eLocReq
            },*/
            eventStartDate : {
                required: eStartDate
            },
            eventEndDate : {
                required: eEndDate
            },
            userLimit : {
                required: eMaxUser,
                min:eMaxUserVal
            },             
            eventUserTypeG : {
                required: eUserType
            }
        },
        errorPlacement: function(error, element){

            //if input parent elemnt has 'form-group' class then place error element after that parent div
            if (element.parent().hasClass('form-group')) {
                error.insertAfter(element.parent());
            }else{
                error.insertAfter(element);
            }
            if($('input[name=eventUserTypeG]:checked').length==0)
            {
                error.appendTo( element.parents().next('.genderError') );

            }else{
                error.insertAfter( element );
            }
        }
    });

// for submit update event record
$('body').on('click', ".updateEvent", function (event){

    var add = $('#setHAdd').val();

    if(add == '' || typeof add === "undefined"){

        toastr.error(eLocReq);
        return false;
    }

    var currentDate = new Date();
    var dt1=Date.parse($('#datetimepicker6').val());
    var dt2=Date.parse($('#datetimepicker7').val());

    var validDAteTime=true;

    if(dt1 && dt2){
    
        var dateObj1 = new Date(dt1);
        var dateObj2 = new Date(dt2);

        var date1 = dateObj1.getDate()+"/"+dateObj1.getMonth()+"/"+dateObj1.getFullYear();
        var date2 = dateObj2.getDate()+"/"+dateObj2.getMonth()+"/"+dateObj2.getFullYear();

        var timeDiff = ((dateObj2.getTime() - dateObj1.getTime()) / 1000)/60;
        var diffWithCurrentTime = ((dateObj1.getTime() - currentDate.getTime()) / 1000)/60;

        /* if start and end date is same */
        if(date1==date2){
            /* Check if date 1 time is grate than 3 hours */
           /* if(diffWithCurrentTime<180){
                toastr.error("Event start time should be greater than 3 hours from current time.");
                validDAteTime=false;
            }
             check time is less than 30 min 
            else if(timeDiff<30){
                toastr.error("Event end time should be 30 minutes greater from event start time.");
                validDAteTime=false;
            }*/
            if(timeDiff<30){
                toastr.error(eEndTimeMsg);
                validDAteTime=false;
            }
        }else{
            if(timeDiff<30){
                toastr.error(eEndTimeMsg);
                validDAteTime=false;
            }
        }
    }

    if (eventForm.valid() === true && validDAteTime){
        
        $('#myModal').modal('hide');
        var _that = $(this), 
            form = _that.closest('form'),      
            formData = new FormData(form[0]),
            f_action = form.attr('action');
            groupChat = (typeof $('input[name=groupChat]:checked').val()!="undefined") ? 1 : 0;
            formData.append('groupChat',groupChat);
            memberId    = $('#memberId').val();
            formData.append('memberId',memberId);

            var gender = [];
            $('.gender_checkbox:checked').each(function(){ //iterate all listed checkbox items
                gender.push(this.value);
            });
            formData.append('eventUserTypeG',gender);
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

                    let img = IMG_BASE_URL+'uploads/event/thumb/'+data.imgData.event.eventImage;
                    let eventName = data.imgData.eventName;

                    formData.append('eventImg',img);
                    
                    if(groupChat == 1){
                        createEvent(formData); // sending data in firebase
                    }

                    $('#setEventVal').data('myval','1');
                    $('#setEventName').text(eventName);
                    $('#setEventVal').click();

                }else if(data.status == 2) {

                    toastr.error(data.msg);
                    
                }else if(data.status == 3) {

                    toastr.error(data.msg);
                    
                }else if(data.status == 4) {

                    toastr.error(data.msg);
                    
                }else {

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

(function( $ ) {

    var eventForm = $("#eventUpdateForm");
    
    function scroll_to_class(element_class, removed_height) {
        var scroll_to = $(element_class).offset().top - removed_height;
        if($(window).scrollTop() != scroll_to) {
            $('.form-wizard').stop().animate({scrollTop: scroll_to}, 0);
        }
    }

    function bar_progress(progress_line_object, direction) {

        var number_of_steps = progress_line_object.data('number-of-steps');
        var now_value = progress_line_object.data('now-value');
        var new_value = 0;
        if(direction == 'right') {
            new_value = now_value + ( 100 / number_of_steps );
        }
        else if(direction == 'left') {
            new_value = now_value - ( 100 / number_of_steps );
        }
        progress_line_object.attr('style', 'width: ' + new_value + '%;').data('now-value', new_value);
    }

    jQuery(document).ready(function() {
       
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
            
            var add = $('#setHAdd').val();

            if(get_current_step == '2' && add == '' ){
                toastr.error(eLocReq);
                return false;
            }
            
            if( eventForm.valid() ) {

                var eventImage = $('#file-1').val();

                if(get_current_step == '1'){

                    var currentDate = new Date();
                    var dt1=Date.parse($('#datetimepicker6').val());
                    var dt2=Date.parse($('#datetimepicker7').val());

                    if(dt1 && dt2){
                    
                        var dateObj1 = new Date(dt1);
                        var dateObj2 = new Date(dt2);

                        var date1 = dateObj1.getDate()+"/"+dateObj1.getMonth()+"/"+dateObj1.getFullYear();
                        var date2 = dateObj2.getDate()+"/"+dateObj2.getMonth()+"/"+dateObj2.getFullYear();

                        var timeDiff = ((dateObj2.getTime() - dateObj1.getTime()) / 1000)/60;
                        var diffWithCurrentTime = ((dateObj1.getTime() - currentDate.getTime()) / 1000)/60;

                        /* if start and end date is same */
                        if(date1==date2){
                            if(timeDiff<30){
                                toastr.error(eEndTimeMsg);
                                return false;
                            }
                        }else{
                            if(timeDiff<30){
                                toastr.error(eEndTimeMsg);
                                return false;
                            }
                        }
                    }
                }

                var eventVal = $('#setEventVal').data('myval');
                if(get_current_step == '3' && eventVal == '0'){
                    $('#myModal').modal('show'); 
                    allFriends(0);
                    return false;
                }

                parent_fieldset.fadeOut(400, function() {
                    
                    // change icons
                    current_active_step.removeClass('active').addClass('activated').next().addClass('active');
                    // progress bar
                    bar_progress(progress_line, 'right');
                    // show next step
                    // scroll window to beginning of the form
                    $(this).next().fadeIn();
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
            
            $(this).parents('fieldset').fadeOut(400, function() {
                // change icons
                current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
                // progress bar
                bar_progress(progress_line, 'left');
                // show previous step
                $(this).prev().fadeIn();
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
                }
                else {
                    $(this).removeClass('input-error');
                }
            });
            // fields validation            
        });
    });

    // image uploader scripts 
    var $dropzone = $('.image_picker'),
        $droptarget = $('.drop_target'),
        $dropinput = $('#inputFile'),
        $dropimg = $('.image_preview'),
        $remover = $('[data-action="remove_current_image"]');

    $dropzone.on('dragover', function() {
        $droptarget.addClass('dropping');
        return false;
    });

    $dropzone.on('dragend dragleave', function() {
        $droptarget.removeClass('dropping');
        return false;
    });

    $dropzone.on('drop', function(e) {

        $droptarget.removeClass('dropping');
        $droptarget.addClass('dropped');
        $remover.removeClass('disabled');
        e.preventDefault();
      
        var file = e.originalEvent.dataTransfer.files[0],
            reader = new FileReader();

        reader.onload = function(event) {
            $dropimg.css('background-image', 'url(' + event.target.result + ')');
        };
      
        console.log(file);
        reader.readAsDataURL(file);

        return false;
    });

    $dropinput.change(function(e) {

        $droptarget.addClass('dropped');
        $remover.removeClass('disabled');
        $('.image_title input').val('');
      
        var file = $dropinput.get(0).files[0],
            reader = new FileReader();
      
        reader.onload = function(event) {
            $dropimg.css('background-image', 'url(' + event.target.result + ')');
        }
      
        reader.readAsDataURL(file);
    });

    $remover.on('click', function() {
        $dropimg.css('background-image', '');
        $droptarget.removeClass('dropped');
        $remover.addClass('disabled');
        $('.image_title input').val('');
    });

    $('.image_title input').blur(function() {
        if ($(this).val() != '') {
            $droptarget.removeClass('dropped');
        }
    });
    // image uploader scripts

}( jQuery ));

// to upload image using ajax
function addMoreEventImg(e){

    var numTotalImg = $('li.img_count_no').length;

    if(numTotalImg >= 5) {
 
        toastr.error(eMoreThanFiveImg);  
        return false;         
    }

    var file_data = $('#newImgMy').prop("files")[0],
        eventId = $('#eventId').val(),
        form_data = new FormData();
        form_data.append("eventImage", file_data);
        form_data.append("eventId", eventId);

    $.ajax({ 

        url: BASE_URL+'home/event/addEventImage',
        cache: false,
        dataType: 'json',
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',

        beforeSend: function () { 
            $('#tl_admin_loader').show();  
        },

        success: function(data) {

            $('#tl_admin_loader').hide();

            if(data.status==1){  
                
                $("#uploadedEvImgs").html(data.html);
                
            } else if(data.status == -1) {
                
                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            }else{                   
                toastr.error(data.msg);
            }
            $("#newImgMy").val('');
        },
        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });                    
}

// to delete image
function deleteEventImg(eventImageId){

    var hid_inp = $('#imgCount');

    $.ajax({

        url: BASE_URL+'home/event/deleteEventImages',
        data: {eventImageId:eventImageId,eventId:eventId},
        dataType: 'json',
        type: 'post',

        beforeSend: function () { 
            $('#tl_admin_loader').show();  
        },

        success: function(data) {

            $('#tl_admin_loader').hide();

            if(data.status  ==  1){ 
                $("#uploadedEvImgs").html(data.html);
                //$("#dImg"+eventImageId).html('');
                $("#newImgMy").val('');

            }if(data.status == -1) {

                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            }else{
                toastr.error(data.msg);
            }
        },
        error:function (){
            $('#tl_admin_loader').hide();
            toastr.error(commonMsg);
        }
    });
}