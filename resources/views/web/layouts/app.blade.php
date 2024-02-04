<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title')</title>
    <!-- ... your existing head content ... -->
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('public/assets/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('public/assets/css/animate.css')}}">
    <!-- Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('public/assets/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('public/assets/css/bootstrap-social.css')}}">

    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Data Table-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" />
</head>
<style>
    /* public/css/styles.css */

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

.privacy-policy-section {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.privacy-header {
    color: #333;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.privacy-content {
    font-size: 16px;
    line-height: 1.6;
    margin-top: 20px;
}

.privacy-content h2,
.privacy-content h3,
.privacy-content h4 {
    color: #333;
    margin-top: 20px;
}

.privacy-content p {
    margin-bottom: 15px;
}

.privacy-content ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.privacy-content li {
    margin-bottom: 10px;
}

.privacy-content a {
    color: #007bff;
    text-decoration: underline;
}

/* Add more styles as needed */
/* public/css/styles.css */

.delete-account-section {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.delete-account-header {
    color: #333;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.delete-account-content {
    font-size: 16px;
    line-height: 1.6;
    margin-top: 20px;
}

.delete-account-content p {
    margin-bottom: 15px;
}

.delete-account-content ol,
.delete-account-content ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.delete-account-content li {
    margin-bottom: 10px;
}

.delete-account-content a {
    color: #007bff;
    text-decoration: underline;
}

/* Add more styles as needed */

</style>
<body>
    <div >
        <!-- Your navigation bar or header content -->
        <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="{{ url('/') }}">Home</a>
            <a class="navbar-brand" href="{{ route('privacy-policy') }}">Privacy Policy</a>
        </nav> -->

        <main>
            @yield('content')
        </main>

        <!-- ... your existing body content ... -->
    </div>

    <!-- Essential javascripts for application to work-->
    <script src="{{dynamicCacheVersion('public/assets/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{dynamicCacheVersion('public/assets/js/popper.min.js')}}"></script>
    <script src="{{dynamicCacheVersion('public/assets/js/bootstrap.min.js')}}"></script>
    <script src="{{dynamicCacheVersion('public/assets/js/main.js')}}"></script>
    <script src="{{dynamicCacheVersion('public/assets/js/admin.js')}}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{dynamicCacheVersion('public/assets/js/plugins/pace.min.js')}}"></script>
    <script src="{{dynamicCacheVersion('public/assets/js/login.js')}}"></script>
</body>
</html>
