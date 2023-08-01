<!DOCTYPE html>
<html>

<head>

    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{config('app.settings.app_icon')}}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --my-primary-color: <?php echo !empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#009688';  ?>;
            --my-secondary-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#007065', 33);  ?>;
            --my-third-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#00635a', 55);  ?>;
            --my-forth-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#007d71', 45);  ?>;
            --my-fifth-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#278663', 65);  ?>;
            --my-sixth-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#004a43', 145);  ?>;
            --my-seventh-color: <?php echo colorDarken(!empty(config('app.settings.app_color')) ? config('app.settings.app_color') : '#004a43', 10);  ?>;

            --sidebar-primary-color: <?php echo !empty(config('app.settings.sidebar_color')) ? config('app.settings.sidebar_color') : '#222d32';  ?>;
            --sidebar-secondary-color: <?php echo colorDarken(!empty(config('app.settings.sidebar_color')) ? config('app.settings.sidebar_color') : '#0d1214', 30);  ?>;
        }
    </style>
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('assets/css/main.css')}}">
    <!-- Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('assets/css/custom.css')}}">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
    <div class="web-loader">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <main>
        @yield('content')
    </main>
</body>
<!-- Essential javascripts for application to work-->
<script src="{{dynamicCacheVersion('assets/js/jquery-3.3.1.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/popper.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/bootstrap.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/main.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/admin.js')}}"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="{{dynamicCacheVersion('assets/js/plugins/pace.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/login.js')}}"></script>

</html>
