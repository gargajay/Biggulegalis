@extends('web.layouts.auth')

@section('title', config('app.name'))

@section('content')
<div class="page-error">
    <h1><i class="fa fa-exclamation-circle"></i> Error {{$status_code}}: {{$title}}</h1>
    <p>{{$message}}</p>
    <p><a class="btn btn-primary" href="{{route('home')}}">Go Home</a></p>
</div>
@stop