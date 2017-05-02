<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <script src="https://js.stripe.com/v2/"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Styles -->
    <style>
        .StripeElement {
            background-color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">


    <div class="content">
        <div class="title m-b-md">
            Stripe Demo
        </div>

        <div class="flex-center">
            @foreach($errors->all() as $err)
                <p style="color: red;font-weight: bold">{{$err}}</p>
            @endforeach
        </div>
        <div>
            <a style="color: #2e6da4;font-weight: bold"
               href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{config('services.stripe.client_id')}}&scope=read_write">connect
                me</a>
        </div>
        <div>
            @if(Session::has('success'))
                <p style="color: #0d3625;font-weight: bold">{{Session::get('success')}}</p>
            @endif

        </div>
        <h4>Buy something</h4>

        <br/>
        <form action="purchase" method="post" id="payment-form">
            <div class="form-row">
                {{csrf_field()}}
                <label for="price">Amount</label>
                <div id>
                    <input type="number" name="price" id="price" value="10"/>
                </div>
                <br/>

                <label for="card-number">Card Number</label>
                <div id="card-number"></div>

                <label for="expire-date">
                    Expire date
                </label>
                <div id="expire-date">

                </div>
                <label for="cvc">CVC</label>
                <div id="cvc"></div>

                <label for="postalCode">Postal Code</label>
                <div id="postal-code"></div>

                <!-- Used to display form errors -->
                <div id="card-errors"></div>
            </div>

            <button>Submit Payment</button>
        </form>

        {{-- <form action="/deposit" method="POST">
             {{csrf_field()}}
             <script
                     src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                     data-key="{{config('services.stripe.key')}}"
                     data-amount="10000"
                     data-name="Deposit $100"
                     data-description="Deposit $100"
                     data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                     data-locale="auto">
             </script>
             <script>
                 document.getElementsByClassName("stripe-button-el")[0].style.display = 'none';
             </script>
             <button type="submit" class="yourCustomClass">Deposit $100</button>
         </form>
        --}}
    </div>
    <br/>
</div>
<script>
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)

    // Create an instance of Elements

    // Create a Stripe client
    var stripe = Stripe('{{config('services.stripe.key')}}');
    var elements = stripe.elements();
    var style = {
        base: {
            color: 'black',
            lineHeight: '24px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#82868c'
            }
        },
        invalid: {
            color: 'red',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element

    var card = elements.create('cardNumber', {style: style});
    var exp = elements.create('cardExpiry', {style: style});
    var cvc=elements.create('cardCvc',{style:style});
    var postalCode=elements.create('postalCode',{style:style});

    // Add an instance of the card Element into the `card-element` <div>

    card.mount('#card-number');
    exp.mount('#expire-date');
    cvc.mount('#cvc');
    postalCode.mount('#postal-code');

    var card_info=[card,exp,cvc,postalCode];
    for (var i=0;i<card_info.length;i++){
        card_info[i].addEventListener('change',function (event) {
            var displayError=document.getElementById('card-errors');
            if(event.error) {
                displayError.textContent=event.error.message;
            }else {
                displayError.textContent='';
            }
        })
    }
    // Handle form submission
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var arr=[card,exp,cvc,postalCode];

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server
                stripeTokenHandler(result.token);
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

        // Submit the form
        form.submit();
    }
</script>
</body>
</html>
