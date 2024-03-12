<!-- resources/views/privacy-policy.blade.php -->
@extends('web.layouts.auth')

@section('title', 'Contact us')
<style>
    .razorpay-payment-button {
        display: block;
    width: 100%;
    color: #FFF;
    background-color: var(--my-primary-color);
    border-color: var(--my-primary-color);
    }

   
</style>

@section('content')
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>{{config('app.name')}}</h1>
    </div>
    <div class="login-box">
        <a href="#goBack" id="backButton" class="btn btn-sm btn-secondary" style="position: absolute; top: 10px; left: 10px;">Back</a>

        <form class="login-form" action="{{ route('razorpay.payment.store') }}" method="POST">
            @csrf
            <h3 class="login-head">Payment</h3>
            @if(Session::has('success'))
            <div class="alert alert-danger " role="alert">{{ Session::get('success') }}</div>


            @endif

            <!-- Error message -->
            @if(Session::has('error'))
            <div class="alert alert-danger " role="alert"> {{ Session::get('error') }}</div>


            @endif
            <input type="hidden" name="user_id" value="{{$user_id}}">
            <input type="hidden" name="document_id" value="{{$document->id}}">


                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ config('settings.stripe.public_key') }}" data-prefill.user_id="{{$user_id}}" data-amount="{{$document->price*100}}" data-buttontext="Pay {{$document->price}} INR" data-name="Biggulegalis official" data-description="Razorpay payment" data-image="{{asset('assets/images/lawyer-icon.png')}}" data-prefill.name="ABC" data-prefill.email="{{$user_id}}@gmail.com" data-theme.color="#ff7529">
                </script>
        </form>
    </div>
    <script>
    //     document.getElementById("backButton").addEventListener("click", function() {
    //         var currentURL = window.location.href;
    
    // // Concatenate #goBack to the current URL
    // var newURL = currentURL + "#goBack";
    
    // // Redirect to the new URL
    // window.location.href = newURL;
            
    //     });

        function sendEvent(event, ...data) {
            try {
                console.info("event:" + event);
                console.info("data:");
                console.info(data);
                if (/Android/.test(navigator.userAgent)) {
                    console.info("Device is Android");
                    // The WebView is on an Android device
                    if (typeof Biggulegalis !== "undefined") {
                        if (data.length > 1) {
                            Biggulegalis.sendEvent(event, JSON.stringify(data))
                        } else {
                            Biggulegalis.sendEvent(event, data[0])
                        }
                    } else {
                        console.error("StraightTrippin object is not defined.");
                    }
                } else if (/iPhone|iPad|iPod|iOS|Mac|Apple/.test(navigator.userAgent)) {
                    console.info("Device is iOS");
                    // The WebView is on an iOS device
                    if (typeof webkit !== "undefined") {

                        if (data.length > 1) {
                            window.webkit.messageHandlers.Biggulegalis.postMessage({
                                event: event,
                                data: data
                            });
                        } else {
                            window.webkit.messageHandlers.Biggulegalis.postMessage({
                                event: event,
                                data: data[0]
                            });
                        }
                    } else {
                        console.error("Biggulegalis object is not defined.");
                    }
                }
            } catch (e) {
                console.error(e)
            }
        }
    </script>
</section>
@stop