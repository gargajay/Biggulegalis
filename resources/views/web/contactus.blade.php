<!-- resources/views/contact-us.blade.php -->
@extends('web.layouts.app')

@section('title', 'Contact Us')

@section('content')



<section class="contact-us-section" style="margin-top: 10px;">
   
    <div class="row">
        <div class="col-md-12">
     
            <div class="tile">
            <div class="app-title">
        <div>
            <h1>Contact us</h1>
        </div>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-primary modal-link" id="backButton" >back</button>
        </div>
    </div>
                <div class="tile-body">
                <form action="" method="POST">
                    <!-- CSRF Token -->
                    @csrf

                    <!-- Name input -->
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Email input -->
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <!-- Message input -->
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>

                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                </div>
            </div>
        </div>
    </div>
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
</section>




@endsection

@section('scripts')
<script>
    // Add event listener for the back button
    document.getElementById("backButton").addEventListener("click", function() {
        // Send a message to Swift
        sendEvent("goBack", {});
    });
</script>
@endsection
