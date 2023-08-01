@extends('web.layouts.admin')

@section('title', config('app.name'))

@section('content')

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa {{$page_icon}}"></i> {{$page_title}}</h1>
        </div>
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-primary modal-link" data-url="{{route('workout-hours.form')}}">ADD</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered data-table-class" id="myDataTable" data-url="{{ $table_url }}">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@stop

@section('custom-js')

@stop
