/*start stripe payment section*/
function openStripeModel(e){

    $("#stripepopup").modal();
    $('#getPaymentType').val($(e).data("ptype"));   // use for payment type bcoz use one ajex for multiple payment 
    $('#getPageType').val($(e).data("pagetype"));   // only for map payment redirection
    $('#showTitle').text($(e).data("title"));       // use single popup for all payment but title different accordingly

    /* For event payment */
    $('#eventIdPay').val($(e).data("eid"));         // only for map payment redirection
    $('#memberIdPay').val($(e).data("mid"));        // only for map payment redirection
    $('#eventMemIdPay').val($(e).data("emid"));
    $('#eventAmtPay').val($(e).data("eamt"));
    $('#compIdPay').val($(e).data("compid"));
    $('#compMemIdPay').val($(e).data("compmemid"));
    $('#groupChat').val($(e).data("groupchat"));

    /* For appointment payment*/
    $('#appIdPay').val($(e).data("appid"));
    $('#appAmount').val($(e).data("payamt"));
    $('#appForId').val($(e).data("appforid"));
}

var stripe = Stripe(publish_key);
var elements = stripe.elements();
var base_url = $('#biz_body').attr('data-site-url');
// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
    base: {
        color: '#32325d',
        lineHeight: '18px',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Create a token or display an error when the form is submitted.
var form = document.getElementById('payment-form');

form.addEventListener('submit', function(event) {

    event.preventDefault();

    $('#tl_admin_loader').show(); //show loader 

    stripe.createToken(card).then(function(result) {

        if (result.error) {
            $('#tl_admin_loader').hide();
            // Inform the customer that there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;

        } else {

            var ptype = $('#getPaymentType').val();

            if(ptype == 4){

                eventMemPayment(result.token); // to pay for event

            }else if(ptype == 5){

                eventCompPayment(result.token); // to pay for companion member 

            }else if(ptype == 7){

                appointmentFinalPayment(result.token); // to pay for appointment payment 

            }else{
                // Send the token to your server.
                stripeTokenHandler(result.token); // to pay for one time payments and subscription 
            }
        }
    });
});

function stripeTokenHandler(token) {

    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);

    form.appendChild(hiddenInput);
    
    var paymentType = $('#getPaymentType').val();
    var pageType    = $('#getPageType').val();

    switch(paymentType) {

        case '1':
            var url = BASE_URL+'home/payment/paymentForShowTop';
            break;

        case '2':
            var url = BASE_URL+'home/payment/viewOnMapPayment';
            break;

        case '3':
            var url = BASE_URL+'home/subscription/subsPaymentProcess';
            break;

        case '6':
            var url = BASE_URL+'home/business/businessSubscriptionData';
            break;
    }
    
    $.ajax({
        type: "POST",
        url: url,
        data: {'stripeToken':token.id,pageType:pageType}, //only input
        dataType: "json",
        beforeSend: function () {
            $('#tl_admin_loader').show(); 
        },
        success: function (data, textStatus, jqXHR) {

            $('#tl_admin_loader').hide();
            
            switch(data.status) {

                case 1:
                    $('#stripepopup').modal('hide');  //hide payment modal
                   // $('#successpayment').modal('show'); //show success modal
                    card.clear(); //clear card values
                    toastr.success(data.msg);
                    if(data.url){
                        window.setTimeout(function () {
                            window.location.href = data.url;
                            //window.location.href = BASE_URL+'home';

                        }, 200);
                    }
                    break;
                case -1:
                    toastr.error(data.msg);
                    window.setTimeout(function () {
                        window.location.href = data.url;
                    }, 200);
                    break;
                default:
                    toastr.error(data.msg);
            }
        },
        complete:function(){
           
        },
        error: function (jqXHR, textStatus, errorThrown){
            $('#tl_admin_loader').hide();
            toastr.error(commonMsg);
        }
    });
}
/*end stripe payment section*/