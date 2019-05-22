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

    // to accept or reject event request for member
    $('body').on('click', ".eventRequestStatus", function (event) {
           
        var eventId         = $(this).data('eid'),
            memberId        = $(this).data('mid'),
            eventMemberId   = $(this).data('evtmemid'),
            status          = $(this).data('status'),
            payment         = $(this).data('payment'),
            groupChat       = $(this).data('groupchat'),
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
               
                if (data.status == 1){ 
                    toastr.success(data.msg);  

                    if((payment == 'Free') && (groupChat == 1)){

                        joinEventMember(eventId,memberId,eventMemberId); // sending member while accepting request acc to event in firebase
                    } 

                    window.setTimeout(function () {                   
                        location.reload();
                    }, 2000);

                }else if (data.status == 2){ 
                    toastr.error(data.msg);   
                    window.setTimeout(function () {                   
                        window.location.href = data.url;
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
    
    // to payment for event
    function eventMemPayment(token){
        
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);

        form.appendChild(hiddenInput);
        
        var paymentType     = $('#getPaymentType').val(),
            eventIdPay      = $('#eventIdPay').val(),
            memberIdPay     = $('#memberIdPay').val(),
            eventMemIdPay   = $('#eventMemIdPay').val(),
            eventAmount     = $('#eventAmtPay').val(),
            groupChat       = $('#groupChat').val();
        
        if(paymentType == 4){

            var url = BASE_URL+'home/event/eventPayment';

        }else if(paymentType == 5){

            var url = BASE_URL+'home/event/eventPayment';
        }

        $.ajax({

            type: "POST",
            url: url,
            data: {'stripeToken':token.id,eventIdPay:eventIdPay,memberIdPay:memberIdPay,eventMemIdPay:eventMemIdPay,eventAmount:eventAmount}, //only input
            dataType: "json",
            beforeSend: function () {
                $('#tl_admin_loader').show(); 
            },
            complete:function(){
               
            },
            success: function (data, textStatus, jqXHR) {               

                switch(data.status) {
                    case 1:
                        $('#stripepopup').modal('hide');  //hide payment modal
                       // $('#successpayment').modal('show'); //show success modal
                        card.clear(); //clear card values
                        toastr.success(data.msg);

                        if(groupChat == 1){

                            joinEventMember(eventIdPay,memberIdPay,eventMemIdPay); // sending member while accepting request acc to event in firebase
                        }                        

                        if(data.url){
                            window.setTimeout(function () {
                                window.location.href = data.url;
                            }, 2000);
                        }
                        break;
                    default:
                        $('#tl_admin_loader').hide();
                        toastr.error(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                $('#tl_admin_loader').hide();
                toastr.error(commonMsg);
            }
        });
    }

/*// for check / uncheck all
function checkUncheckAll(){

    var status=$("#select_all_1").is(':checked');
    if(status==true){
        $('.friend_checkbox').each(function(){ //iterate all listed checkbox items
            this.checked = true; //change ".checkbox" checked status
        });
    }else{
        $('.friend_checkbox').each(function(){ //iterate all listed checkbox items
            this.checked = false; //change ".checkbox" checked status
        });
    }
}

// manage check / uncheck all if all select and one not select & vice versa
function updateSelection(obj){
    
    if(obj.checked == false){ //if this item is unchecked
        $("#select_all_1").prop('checked', false);
    }
    //check "select all" if all checkbox items are checked
    if ($('.friend_checkbox:checked').length == $('.friend_checkbox').length ){ 
       $("#select_all_1").prop('checked', true);
    }
}*/

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
function shareMembers(is_scroll=0,apply=0){  

    var scroll_loader = $('#showLoader');
    var gender      = $('#getGender').val(),
        uprivacy    = $("#getPrivacy").val(),
        name        = ($('#searchName').val()).trim(),
        eventId     = $('#eventId').val(),
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

      /*  if(gType == 'Male'){
            var gender = 1;
        }else if(gType == 'Female'){
            var gender = 2;
        }else if(gType == 'Transgender'){
            var gender = 3;
        }else{
            var gender = '';
        }*/

        if(uprivacy == 'Public'){
            var privacy = '1';
        }else if(uprivacy == 'Private'){
            var privacy = '2';
        }else{
            var privacy = '';
        }

        //for new request, reset isNext and offset
        if(is_scroll==0){
            scroll_loader.attr('data-isNext',1);
            scroll_loader.attr('data-offset',0); //set new offset
        }
        
        var offset = scroll_loader.attr('data-offset'),
            isNext = scroll_loader.attr('data-isNext'), //to see if we have next record to load
            list_cont = $('#share-members'); //container where list will be appended

        //abort request if previous request is in progress OR next record is not available
        if(isFunctionCall || (isNext==0 && is_scroll==1)){
            return false;
        }

       

        isFunctionCall = true;
        
    $.ajax({

        url: BASE_URL+'home/event/allSharemember',
        type: "POST",
        data:{gender:gender,privacy:privacy,name:name,rating:arr,latitude:latitude,longitude:longitude,city:city,state:state,country:country,eventId:eventId,address:address,page:offset,memberId:memberId},   
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
             
        $('#filter-invite').slideUp();
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
    shareMembers(0);
}

function resetAll(){
    $('#img-flter').click();
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
       
    shareMembers(0);
}

// share member for event
$('body').on('click', "#share-event", function (event) {

    var  total=$('.friend_checkbox:checked').length;

    if(total==0){
        toastr.error(shareEvent);
        return false;
    } 
       
    var eventId = $(this).data('eventid'),
        memberId = $("#memberId").val(),
        eventType = $(this).data('eventp'),
        eventMemId = $(this).data('evenmemid'),
        gType = $('#genderType'+memberId).val(),
        invitation = $('#invitation'+memberId).val(),
        url = BASE_URL+'home/event/shareMember';

        if(typeof memberId === "undefined" || memberId == ''){
            //toastr.error('Please select friend to share event');   
            return false;
        }
        // gender = 1:Male,2:Female , eventInvitation = 1:Public,2:Private,3:Both
        /* if(eventType === "Public" && gType == '2' && invitation == '2'){
            toastr.error('You cannot share public event with private event members.');   
            return false;
        }

        if(eventType === "Private" && gType == '2' && invitation == '1'){
            toastr.error('You cannot share private event with public event members.');   
            return false;
        }*/

    $.ajax({

        type: "POST",
        url:url,
        data: {eventId:eventId,memberId:memberId,eventMemId:eventMemId}, //only input               
        dataType: "JSON", 
        beforeSend: function () {
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  
            hide_loader();        
           
            if (data.status == 1){ 
                $('#share-event').hide();
                toastr.success(data.msg);
                window.setTimeout(function () {
                    location.reload();
                }, 500); 

            }else if(data.status == 2) {

                toastr.error(data.msg);

            }else if(data.status == 3) {

                toastr.error(data.msg);

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
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });          
});

// companion status accept/reject for event
$('body').on('click', ".comp-status", function (event) {
       
    var eventId     = $(this).data('eid'),
        status      = $(this).data('status'),
        eventMemId  = $(this).data('eventmemid'),
        compId      = $(this).data('compid'),
        memberId    = $(this).data('userid'),
        payment     = $(this).data('payment'),
        groupChat   = $(this).data('groupchat'),
        url         = BASE_URL+'home/event/companionMemberStatus';

    $.ajax({
        type: "POST",
        url:url,
        data: {eventId:eventId,status:status,eventMemId:eventMemId}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {

                  
           
            if (data.status == 1){

                $('.comp-status').hide();
                toastr.success(data.msg);

                if((payment == 'Free') && (groupChat == 1)){
                    joinEventMember(eventId,memberId,eventMemId,compId);  // sending member while accepting request acc to event in firebase
                }
                  
                window.setTimeout(function () {                   
                    location.reload();
                }, 2000);

            }else if(data.status == 2) {
                hide_loader();  
                toastr.error(data.msg);                

            }else if(data.status == 3) {

                $('.comp-status').hide();
                hide_loader();  
                toastr.error(data.msg);
                window.setTimeout(function () {                   
                    location.reload();
                }, 500);
                

            }else if(data.status == 4) {
                hide_loader();  
                toastr.error(data.msg);
                

            }else if(data.status == 5) {
                hide_loader();  
                toastr.error(data.msg);
                

            }else if(data.status == -1) {
                hide_loader();  
                toastr.error(data.msg);
                window.setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);

            }else {
                window.location.href  = data.url;
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });       
});



// show joined members list on event request detail
var joinOffset=0;
var joinLimit=6;
var eventId = $('#eventId').val();
var joinedPagionationUrl=BASE_URL+"home/event/joinedMembersReqEvent/";
var type = 'request';
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
        }
    });        
}


// show companion members list on event request detail
var compOffset=0;
var compLimit=6;
var eventId = $('#eventId').val();
var compPagionationUrl=BASE_URL+"home/event/companionMembersReqEvent/";
var type = 'request';
load_companion_member();
function load_companion_member()
{    
    $.ajax({
        url: compPagionationUrl,
        type: "POST",
        data:{compOffset:compOffset,compLimit:compLimit,eventId:eventId,type:type},              
        cache: false,  
        beforeSend: function() {
            $('#tl_admin_loader').show();                           
        },                       
        success: function(data){
            $('#tl_admin_loader').hide();

            if(compOffset>0){
                $('#appendDataForComp').append(data);
            }else{
                $('#companion-member').html(data);
            }
            var totalComp = $("#compMemCount-count").val();
            var totalDataLoadedComp = $('.compVisitCount').length;
            if(totalComp>totalDataLoadedComp){
                $("#load_more_btn_companion").show();
            }else{
                $("#load_more_btn_companion").hide();
            }
            compOffset+=compLimit;
        }
    });        
}

    // to payment for event's companion payment
    function eventCompPayment(token){
        
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);

        form.appendChild(hiddenInput);
        
        var paymentType = $('#getPaymentType').val(),
            eventIdPay = $('#eventIdPay').val(),
            compIdPay = $('#compIdPay').val(),
            eventMemIdPay = $('#eventMemIdPay').val(),
            eventAmount = $('#eventAmtPay').val(),
            compMemId = $('#compMemIdPay').val(),
            groupChat       = $('#groupChat').val() ? $('#getGroupChat').val() : $('#getGroupChat').val();
        
        if(paymentType == 4){

            var url = BASE_URL+'home/event/eventPayment';

        }else if(paymentType == 5){

            var url = BASE_URL+'home/event/companionPayment';
        }

        $.ajax({ 
            type: "POST",
            url: url,
            data: {'stripeToken':token.id,eventIdPay:eventIdPay,compIdPay:compIdPay,eventMemIdPay:eventMemIdPay,eventAmount:eventAmount}, //only input
            dataType: "json",
            beforeSend: function () {
                $('#tl_admin_loader').show(); 
            },
            complete:function(){
               
            },
            success: function (data, textStatus, jqXHR) {

                switch(data.status) {
                    case 1:
                        $('#stripepopup').modal('hide');  //hide payment modal
                       // $('#successpayment').modal('show'); //show success modal
                        card.clear(); //clear card values
                        toastr.success(data.msg);

                        if(groupChat == 1){

                            joinEventMember(eventIdPay,compMemId,eventMemIdPay,compIdPay);  // sending member while accepting request acc to event in firebase
                        }

                        if(data.url){
                            window.setTimeout(function () {
                                window.location.href = data.url;
                            }, 2000);
                        }
                        break;
                    default:
                        $('#tl_admin_loader').hide();
                        toastr.error(data.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                $('#tl_admin_loader').hide();
                toastr.error(commonMsg);
            }
        });
    }

// if event's payment type is paid for companion
/*$('body').on('click', "#payForComp", function (event) {
       
    var eventId = $(this).data('eid'),
        compId = $(this).data('compid'),
        eventMemId = $(this).data('eventmemid'),
        url = BASE_URL+'home/event/companionPayment';

    $.ajax({
        type: "POST",
        url:url,
        data: {eventId:eventId,compId:compId,eventMemId:eventMemId}, //only input               
        dataType: "JSON", 
        beforeSend: function () { 
            show_loader(); 
        },
        success: function (data, textStatus, jqXHR) {  
            hide_loader();        
           
            if (data.status == 1){ 
                $('#payForComp').hide();
                toastr.success(data.msg);   
                window.setTimeout(function () {                   
                    location.reload();
                }, 500);

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
                }, 2000);

            }else {
                window.location.href  = data.url;
            } 
        },

        error:function (){
            hide_loader(); 
            toastr.error(commonMsg);
        }
    });       
});*/

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