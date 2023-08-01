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
        <form class="login-form" id="reset-form" action="{{route('reset-password' , ['token'=>$token])}}">
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-key"></i>Reset Password</h3>
            <div class="alert alert-danger hide" role="alert"></div>

            <div class="col-lg-12 form-group">
                <label class="control-label">PASSWORD</label>
                <div class="input-group">
                <input class="form-control" type="password" placeholder="Password" name="password" id="password">
                <div class="input-group-append password-input">
                    <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                </div>
                </div>
            </div>
            <div class="col-lg-12 form-group">
                <label class="control-label">CONFIRM PASSWORD</label>
                <div class="input-group">
                <input class="form-control" type="password" placeholder="Confirm Password" name="password_confirmation" id="password_confirmation">
                <div class="input-group-append password-input">
                    <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                </div>
                </div>
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block" id="reset-password-btn"><i class="fa fa-save fa-lg fa-fw"></i>SAVE</button>
            </div>
        </form>
    </div>
</section>
@stop
