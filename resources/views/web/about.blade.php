<!-- resources/views/about-us.blade.php -->

@extends('web.layouts.app')

@section('title', 'About Us')


@section('content')

<section class="about-us-section">

    <div class="text-center">
        <h1>{{ config('app.name') }}</h1>
    </div>
    <h3 class="">About Us</h3>
    <div class="about-content">
        <p>Welcome to {{ config('app.name') }}! We are dedicated to providing [describe your company's mission or purpose].</p>
        
        <h2>Our Story</h2>
        <p>At {{ config('app.name') }}, our journey began with a vision to [describe how your company started and its mission].</p>

        <h2>Our Team</h2>
        <p>Meet the faces behind {{ config('app.name') }}. Our team consists of passionate individuals who are committed to [describe the team's expertise and dedication].</p>

        <h2>Our Vision</h2>
        <p>Our vision is to [describe your company's vision for the future and how you aim to make a difference in your industry or community].</p>

        <h2>Why Choose Us?</h2>
        <p>At {{ config('app.name') }}, we stand out because of our commitment to provide best information regarding assoications.</p>

       
       
        <!-- Add more testimonials as needed -->

        <h2>Contact Us</h2>
        <p>If you have any questions about {{ config('app.name') }}, feel free to reach out to us:</p>
        <ul>
            <li>By email: info@biggulegalis.com</li>
            <li>By phone: 94641-61808</li>
        </ul>
    </div>

</section>


@endsection
