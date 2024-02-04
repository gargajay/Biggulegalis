@extends('web.layouts.auth')

@section('title', 'Contact us')

@section('content')
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>{{config('app.name')}}</h1>
    </div>
    <div class="login-box">
        <form class="login-form" id="contact-form" action="{{url('contact')}}" method="POST">
            @csrf
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-envelope"></i>Contact us</h3>
            @if(Session::has('success'))
            <div class="alert alert-danger " role="alert">{{ Session::get('success') }}</div>
            @endif 
            <div class="form-group">
                <label class="control-label">Email</label>
                <input class="form-control" type="email" placeholder="Email" name="email" id="email" autofocus>
            </div>
            <div class="form-group">
                <label class="control-label">Phone</label>
                <input class="form-control" type="text" placeholder="Phone" name="phone" id="phone">
            </div>
            <div class="form-group">
                <label class="control-label">Description</label>
                <textarea class="form-control" placeholder="Description" name="description" id="description"></textarea>
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-paper-plane fa-lg fa-fw"></i>Submit</button>
            </div>
        </form>
    </div>
</section>
@stop