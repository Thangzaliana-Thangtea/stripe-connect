<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
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
        <h4>Buy Book price $80</h4>


        <form action="/purchase" method="POST">
            {{csrf_field()}}
            <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="{{config('services.stripe.key')}}"
                    data-amount="8000"
                    data-name="Buy book"
                    data-description="price is $80"
                    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                    data-locale="auto">
            </script>
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
</div>
</body>
</html>
