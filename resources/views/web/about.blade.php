<!-- resources/views/about-us.blade.php -->

@extends('web.layouts.app')

@section('title', 'About Us')


@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">



<section class="delete-account-section">

    <div class="text-center">
        <h1>{{ config('app.name') }}</h1>
    </div>
    <h3 class="">About Us</h3>
    <div class="about-content">
    <p>Welcome to {{ config('app.name') }}! We are dedicated to fostering legal excellence and professional development within the legal community.</p>
        
        <h2>Our Story</h2>
        <p>{{ config('app.name') }} was established with the mission to provide a platform for legal professionals to connect, collaborate, and grow.</p>

        <h2>Our Team</h2>
        <p>Meet the dedicated individuals who make up the {{ config('app.name') }} team. From experienced attorneys to administrative staff, our team is committed to serving the needs of our members.</p>

        <h2>Our Vision</h2>
        <p>Our vision is to be the premier resource for legal professionals, offering unparalleled support, education, and networking opportunities.</p>

        <h2>Why Choose Us?</h2>
        <p>Joining {{ config('app.name') }} means gaining access to a wealth of resources, including professional development programs, networking events, and advocacy initiatives.</p>

       
       
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
