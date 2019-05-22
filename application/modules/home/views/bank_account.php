<?php 
    $frontend_assets =  base_url().'frontend_asset/';
?>
<div class="main-page-area">
    <div id="pt-main" class="pt-perspective">
        <!-- Near You Start-->
        <div class="pt-page">
            <div class="container-fluid display_none bg-main-image">
                <div class="page-fixed">
                    <div class="bg-main-image-overlay-fixed">
                        <div class="main-section">
                            <div class="col-md-10 col-md-offset-1 col-sm-12 width100">
                                <div class="page-title wow fadeInUp" data-class="fadeInUp">
                                    <h2>Payment <span><i class="glyphicon glyphicon-heart"></i></span></h2>
                                </div>
                                <div class="no-pymnt">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                                                <div class="accountAdd create-event-wrap">
                                                    <div class="changePwd">
                                                        <h2 class="head-rel"><?php echo ($this->session->userdata('bankAccountStatus') == 1) ? 'Update ' : 'Add ' ;?>Bank Account</h2>
                                                        <div class="brdr-btm"></div>
                                                    </div>
                                                    <div class="accAdd">
                                                        <form id="add_band_accP" class="csForm" action="<?php echo base_url('home/payment/addBankAccount');?>" method="post">
                                                            <div class="pymnt-home-icn">
                                                                <img src="<?php echo AWS_CDN_FRONT_IMG;?>bank-payment.png" />
                                                            </div>
                                                            <div class="formData">
                                                                <div id="PayModebank" class="bankdesc">
                                                                    
                                                                    <div class="form-group">
                                                                        <label class="info-label" >First Name</label>
                                                                        <input type="text" class="form-control" placeholder="First name" name="firstName" value="<?php if(!empty($bankDetail->firstName)){ echo $bankDetail->firstName; }else{ echo ($this->input->post('firstName') != '') ? $this->input->post('firstName') : ''; } ?>" required="">
                                                                    </div>
                                                               
                                                                    <div class="form-group">       
                                                                        <label>Last Name</label>
                                                                        <input type="text" name="lastName" value="<?php if(!empty($bankDetail->lastName)){ echo $bankDetail->lastName; }else{ echo ($this->input->post('lastName') != '') ? $this->input->post('lastName') : ''; } ?>" class="form-control" placeholder="Last name">
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label class="info-label">Account Number</label>
                                                                        <input type="text" onkeypress="return isNumberKey1(event);" name="accountNumber" value="<?php if(!empty($bankDetail->accountNumber)){ echo $bankDetail->accountNumber; }else{ echo ($this->input->post('accountNumber') != '') ? $this->input->post('accountNumber') : ''; } ?>" class="form-control" placeholder="Account number" >
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>DOB</label>
                                                                        <input type="text" id="date22" name="dob" value="<?php if(!empty($bankDetail->dob)){ echo $bankDetail->dob; }else{ echo ($this->input->post('dob') != '') ? $this->input->post('dob') : ''; } ?>" class="form-control" placeholder="Date of birth" readonly>
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label>Postal Code</label>
                                                                        <input type="text" onkeypress="return isNumberKey1(event);" name="postalCode" value="<?php if(!empty($bankDetail->postalCode)){ echo $bankDetail->postalCode; }else{ echo ($this->input->post('postalCode') != '') ? $this->input->post('postalCode') : ''; } ?>" class="form-control" placeholder="Postal code">
                                                                    </div>
                                                                
                                                                    <div class="form-group">
                                                                        <label>SSN Last</label>
                                                                        <input type="text" onkeypress="return isNumberKey1(event);" name="ssnLast" value="<?php if(!empty($bankDetail->ssnLast)){ echo $bankDetail->ssnLast; }else{ echo ($this->input->post('ssnLast') != '') ? $this->input->post('ssnLast') : ''; } ?>" class="form-control" placeholder="SSN last">
                                                                    </div>
                                                                    
                                                                    <div class="form-group">
                                                                        <label class="info-label">Routing Number</label>
                                                                        <input type="text" onkeypress="return isNumberKey1(event);" name="routingNumber" value="<?php if(!empty($bankDetail->routingNumber)){ echo $bankDetail->routingNumber; }else{ echo ($this->input->post('routingNumber') != '') ? $this->input->post('routingNumber') : ''; } ?>" class="form-control" placeholder="Routing number">
                                                                    </div>
                                                                    <div class="updBtn">
                                                                        <div class="pay-shre-btn acp-rjt-btn">
                                                                            <button type="button" class="btn-det shre-btn addBank addBankAccountP"><?php echo ($this->session->userdata('bankAccountStatus') == 1) ? 'Update Account' : 'Add Account' ;?></button>
                                                                            <a href="<?php echo base_url('home/user/userProfile');?>"><button type="button" class="btn-det shre-btn btn-res">Cancel</button></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Near You ends-->
    </div>
</div>
<script type="text/javascript">

function isNumberKey1(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {           
        return false;
    }    
    return true;
}

$(document).ready(function(){

    var todayDate = new Date();
    $('#date22').datetimepicker({
        format : 'YYYY-MM-DD',
        ignoreReadonly: true,
        maxDate: todayDate
    });
});
    
// for submit add bank account record
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
            routingNumber: {
                required: true
            },
            dob: {
                required: true
            },
            accountNumber : {
                required: true              
            }, 
            postalCode : {
                required: true
            },
            ssnLast : {
                required: true
            }                       
        },
        messages: {
            firstName : {
                required: "Please enter first name.",
                minlength : "First name should be atleast 3 characters long.",
                maxlength: "First name should not be 100 characters long."
            },
            lastName : {
                required: "Please enter last name.",
                minlength : "Last name should be atleast 3 characters long.",
                maxlength: "Last name should not be 100 characters long."
            },
            routingNumber : {
                required: "Please enter routing number."
            },
            dob : {
                required: "Please select date of birth."
            },
            accountNumber : {
                required: "Please enter account number."
            },
            postalCode : {
                required: "Please enter postal code."
            },
            ssnLast : {
                required: "Please enter ssn last."
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
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);

                } else if(data.status == 2) {
                    
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
                toastr.error('Failed! Please try again');
            }
        });
    }
});
</script>