@extends('web.layouts.auth')

@section('title', config('app.name'))

@section('content')
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>{{config('app.name')}}</h1>
    </div>
    <div class="login-box">
        <form class="login-form" id="forgot-form" action="{{route('forgot-password')}}">
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>Forgot Password ?</h3>
            <div class="alert alert-danger hide" role="alert"></div>

            <div class="form-group">
                <label class="control-label">EMAIL</label>
                <input class="form-control" type="text" placeholder="Email" name="email" id="email" autofocus>
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block" id="forgot-password-btn"><i class="fa fa-unlock fa-lg fa-fw"></i>RESET</button>
            </div>
            <div class="form-group mt-3">
                <p class="semibold-text mb-0"><a href="{{route('login')}}" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
            </div>
        </form>
    </div>
</section>
@stop