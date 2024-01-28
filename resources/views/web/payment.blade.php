<!-- resources/views/privacy-policy.blade.php -->
<?php
?>
@extends('web.layouts.app')

@section('title', 'Payment')


@section('content')

<style>
    .razorpay-payment-button {
        background-color: #ff7529;
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        text-align: center;
        margin:auto;
    }

    .razorpay-payment-button:hover {
        background-color: #ff4500;
    }
</style>

<section class="privacy-policy-section">

    <div class="text-center">
    </div>
    <!-- <h3 class="">Payment</h3> -->
    <div class="privacy-content">
    <div class="container">
    <div class="card card-default" style="max-width: 400px; margin: 0 auto; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); border: 1px solid #ccc; padding: 20px; margin-top: 50px; position: relative; display: flex; flex-direction: column; align-items: center;">
        <a href="#" id="backButton" class="btn btn-sm btn-secondary" style="position: absolute; top: 10px; left: 10px;">Back</a>
        <div class="card-header" style="margin-bottom: 20px;">
        Biggulegalis Payment
        </div>
        <div class="card-body text-center">
        @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            
            <!-- Error message -->
            @if(Session::has('error'))
                <div class="alert alert-danger">
                    {{ Session::get('error') }}
                </div>
            @endif
            <form action="{{ route('razorpay.payment.store') }}" method="POST">
                
                <input type="hidden" name="user_id" value="{{$user_id}}" >
                <input type="hidden" name="document_id" value="{{$document->id}}" >
                <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZORPAY_KEY') }}" data-prefill.user_id="{{$user_id}}" data-amount="{{$document->price*100}}" data-buttontext="Pay {{$document->price}} INR" data-name="Biggulegalis official" data-description="Razorpay payment" data-image="{{asset('assets/images/lawyer-icon.png')}}" data-prefill.name="ABC" data-prefill.email="{{$user_id}}@gmail.com" data-theme.color="#ff7529">
                </script>
            </form>
        </div>
    </div>
</div>


    </div>

</section>


<script>
     document.getElementById("backButton").addEventListener("click", function() {
      // Send a message to Swift
      sendEvent("goBack", {});
    });
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

@endsection