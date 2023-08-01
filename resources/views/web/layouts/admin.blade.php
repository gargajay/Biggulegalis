<!DOCTYPE html>
<html>

<head>
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{config('app.settings.app_icon')}}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Define a variable in your HTML file -->
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
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('assets/css/animate.css')}}">
    <!-- Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('assets/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{dynamicCacheVersion('assets/css/bootstrap-social.css')}}">

    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Data Table-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css" />


</head>

<body class="app sidebar-mini">
    <div class="web-loader">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Navbar-->
    <header class="app-header">
        <a class="app-header__logo" href="dashboard" style="font-size: 20px;">{{config('app.name')}}</a>
        <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
        <!-- Navbar Right Menu-->
        <ul class="app-nav">

            <!-- User Menu-->
            <li class="dropdown">
                <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
                <ul class="dropdown-menu settings-menu dropdown-menu-right">
                    <li><a class="dropdown-item modal-link" href="{{route('profile-update')}}"><i class="fa fa-user fa-lg"></i> Profile</a></li>
                    <li><a class="dropdown-item modal-link" href="{{route('change-password')}}"><i class="fa fa-lock fa-lg"></i> Change Password</a></li>
                    <li><a class="dropdown-item" href="{{route('logout')}}"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </header>

    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <div class="app-sidebar__user">
            <img class="app-sidebar__user-avatar" src="{{ auth()->user()->image }}" style="max-width: 50px;" alt="User Image">
            <div>
                <p class="app-sidebar__user-name">{{ auth()->user()->full_name }}</p>
                <p class="app-sidebar__user-designation">Admin</p>
            </div>
        </div>
        <ul class="app-menu">
            <li><a class="app-menu__item {{request()->route()->getName()=='dashboard' ? 'active' : ''}}" href="{{route('dashboard')}}"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
            <li><a class="app-menu__item {{request()->route()->getName()=='user' ? 'active' : ''}}" href="{{route('user')}}"><i class="app-menu__icon fa  fa-user"></i><span class="app-menu__label">User</span></a></li>
            <li><a class="app-menu__item {{request()->route()->getName()=='goal' ? 'active' : ''}}" href="{{route('goal')}}"><i class="app-menu__icon fa  fa-user"></i><span class="app-menu__label">Goals</span></a></li>
            <li><a class="app-menu__item {{request()->route()->getName()=='workout-hours' ? 'active' : ''}}" href="{{route('workout-hours')}}"><i class="app-menu__icon fa  fa-user"></i><span class="app-menu__label">Workout Hours</span></a></li>
            {{--<li><a class="app-menu__item {{request()->route()->getName()=='translate' ? 'active' : ''}}" href="{{route('translate')}}"><i class="app-menu__icon fa  fa-language"></i><span class="app-menu__label">Translate</span></a></li>--}}
            {{--<li><a class="app-menu__item {{request()->route()->getName()=='subscription-plan' ? 'active' : ''}}" href="{{route('subscription-plan')}}"><i class="app-menu__icon fa  fa-money"></i><span class="app-menu__label">Subscription Plan</span></a></li>--}}
            <li><a class="app-menu__item {{request()->route()->getName()=='settings' ? 'active' : ''}}" href="{{route('settings')}}"><i class="app-menu__icon fa  fa-cog"></i><span class="app-menu__label">Settings</span></a></li>
        </ul>
    </aside>
    <main>
        @yield('content')
    </main>
    <div class="modal fade" id="commonModal">
    </div>
</body>
<!-- Essential javascripts for application to work-->
<script src="{{dynamicCacheVersion('assets/js/jquery-3.3.1.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/popper.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/bootstrap.min.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/main.js')}}"></script>
<script src="{{dynamicCacheVersion('assets/js/jquery.validate.min.js')}}"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="{{dynamicCacheVersion('assets/js/plugins/pace.min.js')}}"></script>

<!-- Page specific javascripts-->
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/bootstrap-notify.min.js')}}"></script>
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/bootstrap-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/sweetalert.min.js')}}"></script>
<!-- Include the Chart.js library -->
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/chart.min.js')}}"></script>

<!-- Data table plugin-->
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/plugins/dataTables.bootstrap.min.js')}}"></script>



<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/admin.js')}}"></script>
<script type="text/javascript" src="{{dynamicCacheVersion('assets/js/helper.js')}}"></script>

@yield('custom-js')

</html>
