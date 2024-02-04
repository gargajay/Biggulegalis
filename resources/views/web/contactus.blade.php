<!-- resources/views/contact-us.blade.php -->
@extends('web.layouts.app')

@section('title', 'Contact Us')

@section('content')



<section class="contact-us-section" style="margin-top: 10px;">
    <div class="container">
   
    <div class="row">
        <div class="col-md-12">
     
            <div class="tile">
            <div class="app-title">
        <div>
            <h1>Contact us</h1>
        </div>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-primary modal-link" >back</button>
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
    </div>
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
