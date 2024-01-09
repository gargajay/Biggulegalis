<!-- resources/views/delete-account.blade.php -->

@extends('web.layouts.app')

@section('title', 'Delete Account')

@section('content')

<!-- Include the link to your CSS file -->
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<section class="delete-account-section">

    <div class="text-center">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <h3 class="delete-account-header">Request Account Deletion</h3>

    <div class="delete-account-content">

        <p>If you wish to delete your account, please follow the steps below:</p>

        <ol>
            <li>Compose an email from the email address associated with your account.</li>
            <li>In the subject line, write "Account Deletion Request."</li>
            <li>In the body of the email, include the following information:
                <ul>
                    <li>Your full name</li>
                    <li>Username or email address associated with your account</li>
                    <li>Reason for account deletion (optional)</li>
                </ul>
            </li>
            <li>Send the email to: info@biggulegalis.com</li>
        </ol>

        <p>Please allow us some time to process your request. Once your request is processed, you will receive a confirmation email.</p>

        <p>If you encounter any issues or need assistance, feel free to contact us:</p>

        <ul>
            <li>By email: info@biggulegalis.com</li>
        </ul>

    </div>

</section>

@endsection
