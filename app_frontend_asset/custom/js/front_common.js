    
    /* Toastr settings */
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": true,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    hide_loader();
    function show_loader(){
        $('#tl_admin_loader').show();
    }

    function hide_loader(){
        $('#tl_admin_loader').hide();
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && charCode !=46 && (charCode < 48 || charCode > 57)) {           
            return false;
        }       
        return true;
    }

    jQuery.validator.addMethod("email", function(value, element) {
        return this.optional( element ) || ( /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/.test( value ) && /^(?=.{1,64}@.{4,64}$)(?=.{6,100}$).*/.test( value ) );
    }, 'Please enter valid email address.');

    jQuery.validator.addMethod("lettersonly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
    }, "Numbers and special characters are not allowed."); 

    $(document).ready(function(){
        window.setTimeout(function() {
            $(".success").fadeTo(1500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 5000);
    });

    function initialize() {
        
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById("address"));
        
        autocomplete.addListener('place_changed', function() {
          
            var place = autocomplete.getPlace();
            var lat = place.geometry.location.lat(),
                lng = place.geometry.location.lng();
             
            if(lat != '' && lng != ''){
                $("#lat").val(lat);
                $("#long").val(lng);
                
                // uri segment for loaclhost should be 6 and for server 5
                if ($(location).attr('href').split("/")[5] == 'createAppointment' || $(location).attr('href').split("/")[5] == 'updateAppointment' || $(location).attr('href').split("/")[5] == 'createEvent' || $(location).attr('href').split("/")[5] == 'updateEvent') {

                    var address   =    document.getElementById("address").value;
                    $("#setHLat").val(lat);
                    $("#setHLong").val(lng);
                    $("#setHAdd").val(address);
                    $("#setHBizId").val('');
                    $('.slider-sec').removeClass('slider-active');
                    if($(location).attr('href').split("/")[5] == 'createEvent'){
                        $("#appMeet").html('<div class="appiomnt_info mb-15 mt-20 evnt-othr-info"> <div class="media-body"><p id="setBizName" class="media-heading"> <i class="fa fa-map-marker"></i> '+address+'</p></div></div>'); 
                    }else{
                        $("#appMeet").html(' <div class="media-body"><p id="setBizName" class="media-heading"> <i class="fa fa-map-marker"></i> '+address+'</p></div>');  
                    }                  
                }
            }
          // place.geometry  -- this is used to detect whether User entered the name of a Place that was not suggested and pressed the Enter key, or the Place Details request failed.
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize); //initialise google autocomplete API on load

    function checkAddress(){

        var address = $('#address').val();
        if(address == ''){
            $("#lat").val('');
            $("#long").val('');        
        }
    }

    /* start notification list */
    $("#notification").click(function(){
        openClose();
    });
    $(".sidebar_overlay_noti").click(function(){
        openClose();
    });
    $("#sidebar_close_icon_noti").click(function(){
        openClose();
    });

    function openClose(){

        $("#sidebar-right-noti").toggleClass("sidebar-open3");
        $(".sidebar_overlay_noti").toggleClass("sidebar_overlay_active3"); 
        $("body").toggleClass("hide_overflow"); 
    }

    function ajax_notifications(url)
    {       
        $('#totalCount').hide();    
        $.ajax({
            url: url,
            type: "POST",
            data:{},              
            beforeSend: function() {
                show_loader();                                 
            },                       
            success: function(data){

                hide_loader();
                $("#notificationList").html(data);
            }
        });        
    }
    /* end notification list */

    $(document).ready(function(){

        if(getAddress == '' && getCity == '' && getLat == '' && getLong == ''){
            getLocation();
        }
        
        function getLocation() {

            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(showPosition);

            } else { 

                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        function showPosition(position) {

            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            var google_map_position = new google.maps.LatLng( lat, lng );

            var google_maps_geocoder = new google.maps.Geocoder();

            google_maps_geocoder.geocode(

                { 'latLng': google_map_position },

                function( results, status ) {
                    
                    if (status == google.maps.GeocoderStatus.OK) {

                        if (results[1]) {

                            var address = (results[0].formatted_address);

                            var country = '', city = '', cityAlt = ''; state = '';

                            var c, lc, component;

                            for (var r = 0, rl = results.length; r < rl; r += 1) {

                                var result = results[r];

                                if (!city && result.types[0] === 'locality') {

                                    for (c = 0, lc = result.address_components.length; c < lc; c += 1) {

                                        component = result.address_components[c];

                                        if (component.types[0] === 'locality') {

                                            city = component.long_name;
                                            break;
                                        }
                                    }

                                } else if (!city && !cityAlt && result.types[0] === 'administrative_area_level_1') {

                                    for (c = 0, lc = result.address_components.length; c < lc; c += 1) {

                                        component = result.address_components[c];

                                        if (component.types[0] === 'administrative_area_level_1') {

                                            city = component.long_name;
                                            break;
                                        }
                                    }

                                } else if (!state && result.types[0] === 'administrative_area_level_1') {

                                    state = result.address_components[0].long_name;

                                } else if (!country && result.types[0] === 'country') {

                                    country = result.address_components[0].long_name;
                                }

                                if (city && state && country) {
                                    break;
                                }
                            }
                            $('#curAdddress').val(address);
                            $('#curCity').val(city);
                            $('#curState').val(state);
                            $('#curCountry').val(country);
                            $('#curLat').val(lat);
                            $('#curLong').val(lng);

                            $('#addr').val(address);
                            $('#latitude').val(lat);
                            $('#longitude').val(lng);
                            $('#city').val(city);
                            $('#state').val(state);
                            $('#country').val(country);
                            //console.log("City: " + city + ", State: " + state + ", Country: " + country);
                        }
                    }
                }
            );
        }
    });
