var base_url = $('#tl_admin_main_body').attr('data-base-url');
hide_loader();
function show_loader(){
    $('#tl_admin_loader').show();
}

function hide_loader(){
    $('#tl_admin_loader').hide();
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#inputImage').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#upload-photo").change(function(event){
    readURL(this);
    $('#inputImage').text(event.target.files[0].name);
});
/** start script in application **/
var logout = function () { 
    bootbox.confirm('Are you sure want to logout?', function (isTrue) {
        if (isTrue) {
            $.ajax({
                url: base_url+'admin/logout',
                type: 'POST',
                dataType: "JSON",
                success: function (data) {
                    window.location.href = base_url+"admin/";
                }
            });
        }
    });
}


/** backend script **/


    $(document).ready(function(){   
        $("ul.reorder-gallery").sortable({      
            update: function( event, ui ) {
                updateOrder();
            }
        });  
    });


    function updateOrder() {    
        var item_order = new Array(); 
        $('ul.reorder-gallery li').each(function() {
            item_order.push($(this).attr("id"));
        }); 
        var order_string = 'order='+item_order; 
        $.ajax({
            type: "GET",
            url: base_url+'admin/category/updateOrder',
            data: order_string,
            cache: false,
            success: function(data){
                //alert(data);
            }
        });
    }  


    var addFormBoot = function (ctrl, method)
    {
        $(document).on('submit', "#add-form-common", function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + ctrl + "/" + method,
                data: formData, //only input
                processData: false,
                contentType: false,
                beforeSend: function () {
                    show_loader()
                },
                success: function (response, textStatus, jqXHR) {
                    hide_loader()
                    try {
                        var data = $.parseJSON(response);
                        if (data.status == 1)
                        {
//                            bootbox.alert({
//                                message: data.message,
//                                callback: function (
//
//
//                                        ) { /* your callback code */
//                                }
//                            });
                            $("#commonModal").modal('show');
                            toastr.success(data.message);


                            window.setTimeout(function () {
                                window.location.href = "<?php echo base_url(); ?>" + ctrl;
                            }, 2000);
                            

                        } else {
                            toastr.error(data.message);
                            $('#error-box').show();
                            $("#error-box").html(data.message);
                            
                            setTimeout(function () {
                                $('#error-box').hide(800);
                            }, 1000);
                        }
                    } catch (e) {
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        hide_loader()
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    }
                }
            });

        });
    }

    var updateFormBoot = function (ctrl, method)
    {
        $("#edit-form-common").submit(function (event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + ctrl + "/" + method,
                data: formData, //only input
                processData: false,
                contentType: false,
                beforeSend: function () {
                    show_loader()
                },
                success: function (response, textStatus, jqXHR) {
                    hide_loader()
                    try {
                        var data = $.parseJSON(response);
                        if (data.status == 1)
                        {
//                            bootbox.alert({
//                                message: data.message,
//                                callback: function (
//
//
//                                        ) { /* your callback code */
//                                }
//                            });
                            $("#commonModal").modal('hide');
                            toastr.success(data.message);
                            window.setTimeout(function () {
                                window.location.href = base_url + ctrl;
                            }, 2000);
                            

                        } else {
                            $('#error-box').show();
                            $("#error-box").html(data.message);
                            
                            setTimeout(function () {
                                $('#error-box').hide(800);
                            }, 1000);
                        }
                    } catch (e) {
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    }
                }
            });

        });
    }

    var editFn = function (ctrl, method, id) {
        $.ajax({
            url: base_url+ 'admin/' + ctrl + "/" + method,
            type: 'POST',
            data: {'id': id},
            beforeSend: function () {
                show_loader()
            },
            success: function (data, textStatus, jqXHR) {

                $('#form-modal-box').html(data);
                $("#commonModal").modal('show');
                addFormBoot();
                hide_loader()
            }
        });
    }


    var showFn = function (ctrl, method, id) {
        $.ajax({
            url: base_url + ctrl + "/" + method,
            type: 'POST',
            data: {'id': id}, 
            beforeSend: function () { 
                show_loader()
            },
            success: function (data, textStatus, jqXHR) {

                $('#form-modal-box').html(data);
                $("#commonModal").modal('show');
                addFormBoot();
                hide_loader()
            }
        });
    }



    /**** for updating admin profile ****/
    var base_url = $('#tl_admin_main_body').attr('data-base-url');
    $('body').on('click', ".update_admin_profile", function (event) { 
        var _that = $(this), 
            form = _that.closest('form'),      
            formData = new FormData(form[0]),
            f_action = form.attr('action');  
            
    //console.log(formData+'-----'+f_action);
        $.ajax({
            type: "POST",
            url: f_action,
            data: formData, //only input
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function () {
              show_loader()
            },
            success: function (data, textStatus, jqXHR) {
                    hide_loader()
                    if (data.status == 1){

                        toastr.success(data.message);
                            window.setTimeout(function () {
                                 window.location.href = data.url;
                            }, 2000);
                        $(".loaders").fadeOut("slow");

                    } else {

                        toastr.error(data.message);
                        //toastr.options.positionClass = 'toast-top-center'
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        $(".loaders").fadeOut("slow");
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    } 
            },
            error:function (){
                
            }
        });

    });


      /**** forgot password ****/
    var base_url = $('#tl_admin_main').attr('data-base-url');
    $('body').on('click', ".forgot_password", function (event) {  
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
              show_loader()
            },
            success: function (data, textStatus, jqXHR) {
                    hide_loader()
                    if (data.status == 1){

                        toastr.success(data.message);
                            window.setTimeout(function () {
                                 window.location.href = data.url;
                            }, 2000);
                        $(".loaders").fadeOut("slow");

                    } else {

                        toastr.error(data.message);
                        //toastr.options.positionClass = 'toast-top-center'
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        $(".loaders").fadeOut("slow");
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    } 
            },
            error:function (){
                
            }
        });

    });


     /**** for updating about_us content ****/
    var base_url = $('#tl_admin_main_body').attr('data-base-url');
    $('body').on('click', ".update_content", function (event) { 
        var _that = $(this), 
            form = _that.closest('form'),      
            formData = new FormData(form[0]),
            f_action = form.attr('action'); 
            
    //console.log(formData+'-----'+f_action);
        $.ajax({
            type: "POST",
            url: f_action,
            data: formData, //only input
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function () {
              show_loader()
            },
            success: function (data, textStatus, jqXHR) {
                    hide_loader()
                    if (data.status == 1){

                        toastr.success(data.message);
                            window.setTimeout(function () {
                                 window.location.href = data.url;
                            }, 2000);
                        $(".loaders").fadeOut("slow");

                    } else {

                        toastr.error(data.message);
                        //toastr.options.positionClass = 'toast-top-center'
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        $(".loaders").fadeOut("slow");
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    } 
            },
            error:function (){
                
            }
        });

    });


    /**** for changing admin password ****/
    var base_url = $('#tl_admin_main_body').attr('data-base-url');
    $('body').on('click', ".change_password", function (event) { 
        var _that = $(this), 
            form = _that.closest('form'),      
            formData = new FormData(form[0]),
            f_action = form.attr('action');  
            
    //console.log(formData+'-----'+f_action);
        $.ajax({
            type: "POST",
            url: f_action,
            data: formData, //only input
            processData: false,
            contentType: false,
            dataType: "JSON",
            beforeSend: function () {
              show_loader()
            },
            success: function (data, textStatus, jqXHR) {
                    hide_loader()
                    if (data.status == 1){

                        toastr.success(data.message);
                            window.setTimeout(function () {
                                 window.location.href = data.url;
                            }, 2000);
                        $(".loaders").fadeOut("slow");

                    } else {

                        toastr.error(data.message);
                        //toastr.options.positionClass = 'toast-top-center'
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        $(".loaders").fadeOut("slow");
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    } 
            },
            error:function (){
                
            }
        });

    });


    var viewFn = function (ctrl, method, id) {
        $.ajax({
            url: base_url + ctrl + "/" + method,
            type: 'POST',
            data: {'id': id},
            beforeSend: function () {
                show_loader()
            },
            success: function (data, textStatus, jqXHR) {

                hide_loader()
                $('#form-modal-box').html(data);
                $("#commonModal").modal('show');
                addFormBoot();
                hide_loader()
            }
        });
    }

     var viewModel = function (ctrl, method, id) {
        $.ajax({
            url: base_url + ctrl + "/" + method,
            type: 'POST',
            data: {'id': id},
            beforeSend: function () {
                show_loader()
            },
            success: function (data, textStatus, jqXHR) {
                hide_loader()
                $('#form-modal-box').html(data);
                $("#commonModals").modal('show');
                addFormBoot();
                hide_loader()
            }
        });
    }

    var open_modal = function (controller) {
        $.ajax({
            url: base_url + controller + "/open_model",
            type: 'POST',
            success: function (data, textStatus, jqXHR) {

                $('#form-modal-box').html(data);
                $("#commonModal").modal('show');

            }
        });
    }

    var deleteFn = function (table, field, id, ctrl, method) {
        if(typeof method == "undefined" || method==""){
            method = "users/delete";
        }
        else{
            method = ctrl+method;
        }
        bootbox.confirm({
            message: "Are you sure you want to delete this category?",
            buttons: {
                confirm: {
                    label: 'OK',
                    className: 'btn btn-warning'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    show_loader();
                    var url = base_url+method;
                    $.ajax({
                        method: "POST",
                        url: url,
                        dataType: "json",
                        data: {id: id, id_name: field, table: table},
                        success: function (data) {
                            hide_loader();
                            if (data.status == 1) {
                                toastr.success(data.message);
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                            else{
                                toastr.error(data.message);
                            }
                        },
                        error: function (error, ror, r) {
                            toastr.error(error);
                        },
                    });
                }
            }
        });

    }

     var deleteFunc = function (table, field, id, ctrl, method) {
        if(typeof method == "undefined" || method==""){
            method = "events/delete";
        }
        else{
            method = ctrl+method;
        }
        bootbox.confirm({
            message: "Are you sure you want to delete this event?",
            buttons: {
                confirm: {
                    label: 'OK',
                    className: 'btn btn-warning'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    show_loader();
                    var url = base_url+method;
                    $.ajax({
                        method: "POST",
                        url: url,
                        dataType: "json",
                        data: {id: id, id_name: field, table: table},
                        success: function (data) {
                            hide_loader(); 
                            if (data.status == 1) {
                                toastr.success(data.message);
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                            else{
                                toastr.error(data.message);
                            }
                        },
                        error: function (error, ror, r) {
                            toastr.error(error);
                        },
                    });
                }
            }
        });

    }

    var statusFnu = function (table, field, id, status) {
        var message = "";
        if (status == 1) {
            message = "inactive";
        } else if (status == 0) {
            message = "active";
        }

        bootbox.confirm({
            message: "Do you want to " + message + " this record?",
            buttons: {
                confirm: {
                    label: 'Ok',
                    className: 'v'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    var url = base_url+"admin/users/activeInactive";
                    $.ajax({
                        method: "POST",
                        url: url,
                        data: {id: id, id_name: field, table: table, status: status},
                        success: function (response) {
                            var data = JSON.parse(response);
                            if(data.status == 1){
                                window.location.reload();
                            }else {
                                window.location.reload();
                            }
                        },
                        error: function (error, ror, r) {
                            bootbox.alert(error);
                        },
                    });
                }
            }
        });
    }

    var eventStatusFnu = function (table, field, id, status) {
        var message = "";
        if (status == 1) {
            message = "block";
        } else if (status == 0) {
            message = "unblock";
        }

        bootbox.confirm({
            message: "Are you sure ? you want to " + message + " this event.",
            buttons: {
                confirm: {
                    label: 'Ok',
                    className: 'v'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    var url = base_url+"admin/users/eventBlockUnblock";
                    $.ajax({
                        method: "POST",
                        url: url,
                        data: {id: id, id_name: field, table: table, status: status},
                        success: function (response) {
                            var data = JSON.parse(response);
                            if(data.status == 1){
                                window.location.reload();
                            }else {
                                window.location.reload();
                            }
                        },
                        error: function (error, ror, r) {
                            bootbox.alert(error);
                        },
                    });
                }
            }
        });
    }

    var statusFn = function (table, field, id, status) {
        var message = "";
        if (status == 1) {
            message = "inactive";
        } else if (status == 0) {
            message = "active";
        }

        bootbox.confirm({
            message: "Do you want to " + message + " this record?",
            buttons: {
                confirm: {
                    label: 'Ok',
                    className: 'v'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-danger'
                }
            },
            callback: function (result) {
                if (result) {
                    var url = base_url+"admin/interest/activeInactive";
                    $.ajax({
                        method: "POST",
                        url: url,
                        data: {id: id, id_name: field, table: table, status: status},
                        success: function (response) {
                            var data = JSON.parse(response);
                            if(data.status == 1){
                                window.location.reload();
                            }else {
                                window.location.reload();
                            }
                        },
                        error: function (error, ror, r) {
                            bootbox.alert(error);
                        },
                    });
                }
            }
        });
    }   


  var add_interest= $("#myform");  
    add_interest.validate({ 
        rules: {
            interest: {
                required: true,                
                remote:BASE_URL+'admin/interest/checkRecord'
            }                    
        },
        messages: {
            interest: {
                required: "Please enter interest.",
                remote:"Interest already exist."
            }
        }
    });


    $('body').on('click', ".interest", function (event) {
       
        if (add_interest.valid()){ 
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

                        toastr.success(data.message);
                        window.setTimeout(function () {
                            window.location.href = BASE_URL+'admin/interest/interestList';
                        }, 2000); 

                    } else {
                            toastr.error(data.message);
                    } 
                },

                error:function (){
                    $(".loaders").fadeOut("slow");
                }
            });
        }else{ 
            toastr.error('Failed, Please try again');
        }
    });

    // education
    var add_education= $("#myformedu");  
    add_education.validate({ 
        rules: {
            education: {
                required: true,                
                remote:BASE_URL+'admin/interest/checkEduRecord'
            },
            eduInSpanish: {
                required: true,                
                remote:BASE_URL+'admin/interest/checkEduInSpRecord'
            }                    
        },
        messages: {
            education: {
                required: "Please enter education in english.",
                remote:"Education already exist."
            },
            eduInSpanish: {
                required: "Please enter education in spanish.",
                remote:"Education already exist."
            }
        }
    });


    $('body').on('click', ".education", function (event) {
       
        if (add_education.valid()){ 
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

                        toastr.success(data.message);
                        window.setTimeout(function () {
                            window.location.href = BASE_URL+'admin/interest/educationList';
                        }, 2000); 

                    } else {
                            toastr.error(data.message);
                    } 
                },

                error:function (){
                    $(".loaders").fadeOut("slow");
                }
            });
        }else{ 
            toastr.error('Failed, Please try again');
        }
    });

    // work
    var add_work= $("#myformwork");  
    add_work.validate({ 
        rules: {
            work: {
                required: true,                
                remote:BASE_URL+'admin/interest/checkWorkRecord'
            },
            nameInSpanish: {
                required: true,                
                remote:BASE_URL+'admin/interest/checkWorkSPRecord'
            }                    
        },
        messages: {
            work: {
                required: "Please enter work in english.",
                remote:"Work already exist."
            },
            nameInSpanish: {
                required: "Please enter work in spanish.",
                remote:"Work already exist."
            }
        }
    });


    $('body').on('click', ".work", function (event) {
       
        if (add_work.valid()){ 
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

                        toastr.success(data.message);
                        window.setTimeout(function () {
                            window.location.href = BASE_URL+'admin/interest/workList';
                        }, 2000); 

                    } else {
                            toastr.error(data.message);
                    } 
                },

                error:function (){
                    $(".loaders").fadeOut("slow");
                }
            });
        }else{ 
            toastr.error('Failed, Please try again');
        }
    });




$(document).ready(function () {
   var base_url = $('#tl_admin_main_body').attr('data-base-url'); 
  toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        "positionClass": "toast-top-right",
        timeOut: 2000,
        "fadeIn": 300,
    };
    
    $(document).on('submit', "#addFormAjax", function (event) {
        event.preventDefault();
        var _that = $(this),
        formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: _that.attr('action'),
            data: formData, //only input
            processData: false,
            contentType: false,
            beforeSend: function () {
                show_loader()
            },
            success: function (response, textStatus, jqXHR) {
                try {
                    var data = $.parseJSON(response);
                    if (data.status == 1)
                    {
                        $("#commonModal").modal('hide');
                        toastr.success(data.message);
                        if(data.url != ""){
                        window.setTimeout(function () {
                            window.location.href = data.url;
                        }, 2000);
                       }
                        hide_loader()

                    } else {
                        toastr.error(data.message);
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        hide_loader()
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    }
                } catch (e) {
                    //$('#error-box').show();
                    //$("#error-box").html(data.message);
                    console.log(data.message);
                    hide_loader()
                    setTimeout(function () {
                        $('#error-box').hide(800);
                    }, 1000);
                }
            }
        });

    });


/****for category list****/
var base_url = $('#tl_admin_main_body').attr('data-base-url');

/*$('#status').on('change', function(){  
    var catId = $(this).val();  
        $.ajax({
            type: "POST",
            url:  base_url+"admin/category/singleCategory",
            data : {catId : catId}

        }).done(function(response) {
            //console.log(response); 
            //$('#cat_list').append(response);
            $('#cat_list').html(response);

        });
});
*/
    $(document).on('submit', "#editFormAjax", function (event) {

            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: formData, //only input
                processData: false,
                contentType: false,
                beforeSend: function () {
                    show_loader();
                },
                success: function (response, textStatus, jqXHR) {
                    hide_loader();
                    try {
                        
                        var data = $.parseJSON(response);
                        if (data.status == 1)
                        {
                            $("#commonModal").modal('hide');
                            toastr.success(data.message);
                            
                            window.setTimeout(function () {
                                window.location.href = data.url;
                            }, 2000);
                            
                        }else {
                            toastr.error(data.message);
                            /*$('#error-box').show();
                            $("#error-box").html(data.message);*/
                            
                            setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                        }
                    } catch (e) {
                        $('#error-box').show();
                        $("#error-box").html(data.message);
                        
                        setTimeout(function () {
                            $('#error-box').hide(800);
                        }, 1000);
                    }
                }
            });

        });
});

jQuery('body').on('change', '.input_img2', function () {

        var file_name = jQuery(this).val(),
            fileObj = this.files[0],
            calculatedSize = fileObj.size / (1024 * 1024),
            split_extension = file_name.substr( (file_name.lastIndexOf('.') +1) ).toLowerCase(), //this assumes that string will end with ext
            ext = ["jpg", "png", "jpeg"];
            console.log(split_extension+'---'+file_name.split("."));
        if (jQuery.inArray(split_extension, ext) == -1){
            $(this).val(fileObj.value = null);
            $('.ceo_file_error').html('Invalid file format. Allowed formats: jpg, jpeg, png');
            return false;
        }
        
        if (calculatedSize > 5){
            $(this).val(fileObj.value = null);
            $('.ceo_file_error').html('File size should not be greater than 5MB');
            return false;
        }
        if (jQuery.inArray(split_extension, ext) != -1 && calculatedSize < 10){
            $('.ceo_file_error').html('');
            readURL(this);
        }
    });

    jQuery('body').on('change', '.input_img3', function () {

        var file_name = jQuery(this).val(),
            fileObj = this.files[0],
            calculatedSize = fileObj.size / (1024 * 1024),
            split_extension = file_name.substr( (file_name.lastIndexOf('.') +1) ).toLowerCase(), //this assumes that string will end with ext
            ext = ["jpg", "png", "jpeg"];
        if (jQuery.inArray(split_extension, ext) == -1){
            $(this).val(fileObj.value = null);
            $('.ceo_file_error').html('Invalid file format. Allowed formats: jpg,jpeg,png');
            return false;
        }
        if (calculatedSize > 5){
            $(this).val(fileObj.value = null);
            $('.ceo_file_error').html('File size should not be greater than 5MB');
            return false;
        }
        if (jQuery.inArray(split_extension, ext) != -1 && calculatedSize < 10){
            $('.ceo_file_error').html('');
            readURL(this);
        }
    });
    function readURL(input) {
        var cur = input;
        if (cur.files && cur.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(cur).hide();
                $(cur).next('span:first').hide();
                $(cur).next().next('img').attr('src', e.target.result);
                $(cur).next().next('img').css("display", "block");
                $(cur).next().next().next('span').attr('style', "");
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    jQuery('body').on('click', '.remove_img', function () {
        var img = jQuery(this).prev()[0];
        var span = jQuery(this).prev().prev()[0];
        var input = jQuery(this).prev().prev().prev()[0];
        jQuery(img).attr('src', '').css("display", "none");
        jQuery(span).css("display", "block");
        jQuery(input).css("display", "inline-block");
        jQuery(this).css("display", "none");
        jQuery(".image_hide").css("display", "block");
        jQuery("#user_image").val("");
    });

var dataTable = $('#common_datatable_users');    
if(dataTable.length !== 0){
    $('#common_datatable_users').dataTable({
        /*columnDefs: [{orderable: false, targets: [4, 6, 7]}]*/
        "pageLength": 10
    });
}

//common class for onkeypress validatenumber call
$('body').on('keypress','.number_only',validateNumbers);
/*To validate number only*/
function validateNumbers(event) {
  if (event.keyCode == 46){
    return false;
  }
  var key = window.event ? event.keyCode : event.which;
  if (event.keyCode == 9 || event.keyCode == 8 || event.keyCode == 46) {
      return true; //allow only number key and period key
  }
  else if ( (key < 48 || key > 57) && key != 190 ) {
      return false;
  }
  else return true;
};






$(function () {
    no_record_msg = '<h4 class="text-danger">No record found</h4>'; 
    //$("#example1").DataTable();
    $('#example2, #example1').DataTable({
     "processing": true, //Feature control the processing indicator. 
        "serverSide": true, //Feature control DataTables' servermside processing mode. 
    "order": [], //Initial no order. 
    "paging": true, "lengthChange": false, 
    "searching": true, 
    "ordering": true, 
    "info": true, 
    "autoWidth": false, 
    "blengthChange": false, 
    "iDisplayLength" :10, 
    "bPaginate": true, 
    "bInfo": true, 
    "bFilter": false,
      "columnDefs": [
   { orderable: false, targets: -1 },
   { orderable: false, targets: -2 }
]
    });
    
    //datatables initailize

    //users
        var user_list = $('#user_list').DataTable({ 
       "processing": true, //Feature control the processing indicator. 
        "serverSide": true, //Feature control DataTables' servermside processing mode. 
        "order": [], //Initial no order. 
        "paging": true, "lengthChange": false, 
        "searching": true, 
        "ordering": true, 
        "info": true, 
        "autoWidth": false, 
        "blengthChange": false,
        "iDisplayLength" :10,
        "bPaginate": true,
        "bInfo": true,
        "bFilter": false,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/get_user_list_ajax",
            "type": "POST",
            "dataType": "json",
            "dataSrc": function (jsonData) { 
              return jsonData.data;
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 }, 
        ]
    });

     //users
        var id_proof_list = $('#id_proof_list').DataTable({ 
       "processing": true, //Feature control the processing indicator. 
        "serverSide": true, //Feature control DataTables' servermside processing mode. 
        "order": [], //Initial no order. 
        "paging": true, "lengthChange": false, 
        "searching": true, 
        "ordering": true, 
        "info": true, 
        "autoWidth": false, 
        "blengthChange": false, 
        "iDisplayLength" :10, 
        "bPaginate": true, 
        "bInfo": true, 
        "bFilter": false, 
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/get_id_proof_list_ajax",
            "type": "POST",
            "dataType": "json",
            "dataSrc": function (jsonData) { 
              return jsonData.data;
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 }, 
        ]
    });   

    //interest
    table = $('#interest_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/interest/get_interest_list_ajax",
            "type": "POST",
            "dataType": "json",
            //"data": {user_type: $('#user_list').attr('data-user-type')},
            "dataSrc": function (jsonData) {
                return jsonData.data;
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
        ]

    });


    //education
    table = $('#education_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/interest/get_education_list_ajax",
            "type": "POST",
            "dataType": "json",
            //"data": {user_type: $('#user_list').attr('data-user-type')},
            "dataSrc": function (jsonData) {
                return jsonData.data;
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
        ]

    });

    //work
    table = $('#work_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/interest/get_work_list_ajax",
            "type": "POST",
            "dataType": "json",
            //"data": {user_type: $('#user_list').attr('data-user-type')},
            "dataSrc": function (jsonData) {
                return jsonData.data;
            }
        },
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
        ]

    });


    var date_inp = $("#datepicker");
    date_inp.datepicker({
        useCurrent: false,
        autoclose: true,
        keepOpen: false,
    });


    /*date_inp.datepicker().on('changeDate', function (ev) {
        var _that = $(this);
        table_post.search(_that.val()).draw();
        table_post.search('');
    });*/

/*    date_inp.datepicker().on('changeDate', function (ev) {
        table_post
            .columns(3)
            .search(this.value)
            .draw();
    });


    var cat_inp = $('#cat_status');
    cat_inp.change(function(){         
        table_post
            .columns(2)
            .search(this.value)
            .draw();
    });*/

    
    table = $('#favourit_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/my_favourite_list_ajax", 
            "type": "POST",
            "dataType": "json",
            "data": {user_id: $('#userId').val()},
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg }, 
        
        //Set column definition initialisation properties.
        "columnDefs": [
             { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
           
        ]
    });


    table = $('#event_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/my_events_list_ajax", 
            "type": "POST",
            "dataType": "json",
            "data": {user_id: $('#userId').val()},
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg }, 
        
        //Set column definition initialisation properties.
        "columnDefs": [
             { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
           
        ]
    });


    table22 = $('#appointment_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/allAppointment", 
            "type": "POST",
            "dataType": "json",
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg }, 
        
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
           
        ]
    });
    var app_date = $("#datepicker");
    app_date.datepicker({
        useCurrent: false,
        autoclose: true,
        keepOpen: false,
    });

    app_date.datepicker().on('changeDate', function (ev) {
        table22
            .columns(0)
            .search(this.value)
            .draw();
    });

     $('#clearAppDate').on('click', function () {
        var d = new Date();
        app_date.datepicker('update',d);
        app_date.datepicker('update','');
        table22
        .columns(0)
        .search(app_date.val())
        .draw();
    });

    var app_status = $('#status');
    app_status.change(function(){         
        table22
            .columns(1)
            .search(this.value)
            .draw();
    });


    table = $('#friend_list').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/friend_list_ajax", 
            "type": "POST",
            "dataType": "json",
            "data": {user_id: $('#userId').val()},
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg }, 
        
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
           
        ]
    });

    table44 = $('#all_event_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/get_event_list_ajax", 
            "type": "POST",
            "dataType": "json",
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg }, 
        
        //Set column definition initialisation properties.
        "columnDefs": [
             { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }
           
        ]
    });    

    var date_inp = $("#startdate");

    date_inp.datepicker({
        format: 'yyyy-m-d',
        todayBtn: "linked",
        autoclose: true,
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
        table44
        .columns(4)
        .search(this.value)
        .draw();
    });

    $('#clearStartDate').on('click', function () {
        var d = new Date();
        date_inp.datepicker('update',d);
        date_inp.datepicker('update','');
        table44
        .columns(4)
        .search(date_inp.val())
        .draw();
    });

    var date_inp1 = $("#enddate");
    date_inp1.datepicker({
        format: 'yyyy-m-d',
        todayBtn: "linked",
        autoclose: true,
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        date_inp.datepicker('setEndDate', minDate);
        table44
        .columns(5)
        .search(this.value)
        .draw();
    });

    $('#clearEndDate').on('click', function () {
        var d = new Date();
        date_inp1.datepicker('update',d);
        date_inp1.datepicker('update','');
        table44
        .columns(5)
        .search(date_inp1.val())
        .draw();
    });

    var payment = $('#payment');
    payment.change(function(){       
        table44
            .columns(2)
            .search(this.value)
            .draw();
    });

    var privacy = $('#privacy');
    privacy.change(function(){       
        table44
            .columns(1)
            .search(this.value)
            .draw();
    });    
    
    //if ($(location).attr('href').split("/")[6] == 'eventList'){
    if ($(location).attr('href').split("/")[5] == 'eventList'){

        function initialize() {
            var autocomplete = new google.maps.places.Autocomplete(document.getElementById("address"));
            
            autocomplete.addListener('place_changed', function() {
              
                var place = autocomplete.getPlace();           

                table44
                    .columns(3)
                    .search(place.formatted_address)
                    .draw();
            });
        }
        google.maps.event.addDomListener(window, 'load', initialize); //initialise google autocomplete API on load
    }

    $('#address').on('keyup', function () {

        if($('#address').val() == ''){
            table44
            .columns(3)
            .search($('#address').val())
            .draw();
        }       
    });

    table = $('#Payment_list').DataTable({ 
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "lengthChange": false,
        "order": [], //Initial no order.
        "iDisplayLength" :10,
        
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": base_url+"admin/users/get_payment_list_ajax", 
            "type": "POST",
            "data": {user_id: $('#userId').val()},
            "dataType": "json",
            "dataSrc": function (jsonData) { 
                return jsonData.data;
            } 
        },
        
        "oLanguage": { "sEmptyTable": no_record_msg },
        
        //Set column definition initialisation properties.
        "columnDefs": [
            { orderable: false, targets: -1 },
            { orderable: false, targets: -2 }  
        ]
    });

    $.ajax({
        url: base_url+"admin/users/userImages",
        type: "POST",
        data:{user_id: $('#userId').val()},              
        cache: false,   
        beforeSend: function() {
        
            show_loader()
        },                          
        success: function(data){                
            hide_loader()
            $('#userimg').html(data);                
        }
    });       
});

$("#uploadTc").submit(function(e){

    e.preventDefault();
    $(".error").html('');

    $.ajax({

        type:"POST",
        url:base_url+"admin/option/update_tc_page",
        cache:false,
        contentType: false,
        processData: false,
        data: new FormData(this),

        success:function(res){

            var obj = JSON.parse(res);

            if(obj.status == 1){

                toastr.success(obj.message);

                if(obj.url == 'pp_page'){

                    var surl = base_url+"admin/privacyPolicy";
                }else{
                    var surl = base_url+"admin/termCondition";
                }
                window.setTimeout(function() { window.location = surl; }, 2000);

            } else if(obj.status == 0){
                toastr.error(obj.message);
            }
        }
    });
});

$("#updateAbout").submit(function(e){

    for (instance in CKEDITOR.instances) {

        CKEDITOR.instances[instance].updateElement();
        var formData = new FormData(this);
        e.preventDefault();
        $(".error").html(''); 
        $.ajax({
            type:"POST",
            url:base_url+"admin/option/update_about_page",
            contentType: false,
            processData: false,
            data: formData,
            success:function(res){

                var obj = JSON.parse(res);

                if(obj.status == 1){

                    toastr.success(obj.message);
                    if(obj.url == "contact_type"){
                        var surl = base_url+"admin/contactUs";
                    }else{
                        var surl = base_url+"admin/aboutUs"; 
                    }

                    window.setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                    //window.setTimeout(function() { window.location = surl; }, 2000);

                } else if(obj.status == 0){

                    toastr.error(obj.message);;
                }
            }
        });
    }
});

//Private & policy or terms & condition
$(document).on('submit', "#privateEditFormAjax", function (event) {

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        type        : "POST",
        url         : $(this).attr('action'),
        data        : formData, //only input
        processData : false,
        contentType : false,
        beforeSend: function () {
            show_loader();
        },
        success: function (response, textStatus, jqXHR) {

            hide_loader();

            try {

                var data = $.parseJSON(response);

                if (data.status == 1){

                    $("#commonModal").modal('hide');
                    toastr.success(data.message);

                    window.setTimeout(function () {
                        window.location.reload();
                    }, 2000);

                }else {

                    toastr.error(data.message);
                    $('#error-box').show();
                    $("#error-box").html(data.message);

                    setTimeout(function () {
                        $('#error-box').hide(800);
                    }, 1000);
                }

            } catch (e) {

                $('#error-box').show();
                $("#error-box").html(data.message);

                setTimeout(function () {
                    $('#error-box').hide(800);
                }, 1000);
            }
        }
    });

}); //End