<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title')</title>
    <!-- ... your existing head content ... -->
</head>
<style>
    /* public/css/styles.css */

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
