@extends('web.layouts.admin')

@section('title', config('app.name'))

@section('content')

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa {{$page_icon}}"></i> {{$page_title}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="row">

                        <!-- APP Settings -->
                        <div class="col-lg-12 mb-4">
                            <div class="bs-component">
                                <div class="card">
                                    <?php
                                    $key = 'APP';
                                    $title = 'APP';
                                    $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                    ?>
                                    <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                        <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">App Name</label>
                                                    <input class="form-control" type="text" placeholder="App Name" name="app_name" value="{{!empty($value['app_name']) ? $value['app_name'] : ''}}">
                                                </div>


                                                <div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">App Icon</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input upload-file-input" name="app_icon" aria-describedby="inputGroupFileAddon01">
                                                            <label class="custom-file-label upload-file-label" for="image">Choose file</label>
                                                        </div>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text app-icon-link" data-url="{{!empty($value['app_icon']) ? $value['app_icon'] : ''}}">
                                                                <i class="fa fa-file" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <input class="form-control" type="hidden" name="app_icon_old" value="{{!empty($value['app_icon_old']) ? $value['app_icon_old'] : ''}}">
                                                </div>

                                                <div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">App Color</label>
                                                    <input class="form-control" type="color" placeholder="App Color" name="app_color" value="{{!empty($value['app_color']) ? $value['app_color'] : ''}}">
                                                </div>

                                                <div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">Sidebar Color</label>
                                                    <input class="form-control" type="color" placeholder="Sidebar Color" name="sidebar_color" value="{{!empty($value['sidebar_color']) ? $value['sidebar_color'] : ''}}">
                                                </div>

                                                <div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">Copyright</label>
                                                    <input class="form-control" type="text" placeholder="Copyright" name="copyright" value="{{!empty($value['copyright']) ? $value['copyright'] : ''}}">
                                                </div>

                                                {{--<div class="col-lg-4 col-sm-6 form-group">
                                                    <label class="control-label">OTP for Signup</label>
                                                    <select class="form-control" name="otp_signup">
                                                        <option value="0" {{selected($value["otp_signup"], 0)}}>Off</option>
                                                <option value="1" {{selected($value["otp_signup"], 1)}}>On</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">OTP for Login</label>
                                                <select class="form-control" name="otp_login">
                                                    <option value="0" {{selected($value["otp_login"], 0)}}>Off</option>
                                                    <option value="1" {{selected($value["otp_login"], 0)}}>On</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">OTP for Forgot Password</label>
                                                <select class="form-control" name="otp_forgot">
                                                    <option value="0" {{selected($value["otp_forgot"], 0)}}>Off</option>
                                                    <option value="1" {{selected($value["otp_forgot"], 1)}}>On</option>
                                                </select>
                                            </div>--}}

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Rate On Apple Store</label>
                                                <input class="form-control" type="text" placeholder="Rate On Apple Store" name="rate_on_apple_store" value="{{!empty($value['rate_on_apple_store']) ? $value['rate_on_apple_store'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Rate On Google Store</label>
                                                <input class="form-control" type="text" placeholder="Rate On Google Store" name="rate_on_google_store" value="{{!empty($value['rate_on_google_store']) ? $value['rate_on_google_store'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Terms Conditions</label>
                                                <input class="form-control" type="text" placeholder="Terms Conditions" name="terms_conditions" value="{{!empty($value['terms_conditions']) ? $value['terms_conditions'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Privacy Policy</label>
                                                <input class="form-control" type="text" placeholder="Privacy Policy" name="privacy_policy" value="{{!empty($value['privacy_policy']) ? $value['privacy_policy'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Help</label>
                                                <input class="form-control" type="text" placeholder="Help" name="help" value="{{!empty($value['help']) ? $value['help'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">About Us</label>
                                                <input class="form-control" type="text" placeholder="About Us" name="about_us" value="{{!empty($value['about_us']) ? $value['about_us'] : ''}}">
                                            </div>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--End APP Settings -->

                    <!-- SMTP Settings -->
                    <div class="col-lg-12 mb-4">
                        <div class="bs-component">
                            <div class="card">
                                <?php
                                $key = 'SMTP';
                                $title = 'SMTP';
                                $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                ?>
                                <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                    <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Email</label>
                                                <input class="form-control" type="text" placeholder="Email" name="email" value="{{!empty($value['email']) ? $value['email'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Password</label>
                                                <div class="input-group">
                                                    <input class="form-control" type="password" placeholder="Password" name="password" value="{{!empty($value['password']) ? $value['password'] : ''}}">
                                                    <div class="input-group-append password-input">
                                                        <span class="input-group-text"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Host</label>
                                                <input class="form-control" type="text" placeholder="Host" name="host" value="{{!empty($value['host']) ? $value['host'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Port</label>
                                                <input class="form-control" type="text" placeholder="Port" name="port" value="{{!empty($value['port']) ? $value['port'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">From Address</label>
                                                <input class="form-control" type="text" placeholder="From Address" name="from_address" value="{{!empty($value['from_address']) ? $value['from_address'] : ''}}">
                                            </div>

                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">From Name</label>
                                                <input class="form-control" type="text" placeholder="From Name" name="from_name" value="{{!empty($value['from_name']) ? $value['from_name'] : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--End SMTP Settings -->





                    <!-- PUSH_NOTIFICATION_SERVER_KEY Settings -->
                    <div class="col-lg-12 mb-4">
                        <div class="bs-component">
                            <div class="card">
                                <?php
                                $key = 'PUSH_NOTIFICATION_SERVER_KEY';
                                $title = 'Push Notification';
                                $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                ?>
                                <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                    <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-6 form-group">
                                                <label class="control-label">Push Notification Server Key</label>
                                                <input class="form-control" type="text" placeholder="Push Notification Server Key" name="push_notification_server_key" value="{{!empty($value['push_notification_server_key']) ? $value['push_notification_server_key'] : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--End PUSH_NOTIFICATION_SERVER_KEY Settings -->


                    <!-- TWILIO Settings -->
                    <div class="col-lg-12 mb-4">
                        <div class="bs-component">
                            <div class="card">
                                <?php
                                $key = 'TWILIO';
                                $title = 'Twilio';
                                $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                ?>
                                <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                    <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Twilio Sid</label>
                                                <input class="form-control" type="text" placeholder="Twilio Sid" name="twilio_sid" value="{{!empty($value['twilio_sid']) ? $value['twilio_sid'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Twilio Auth Token</label>
                                                <input class="form-control" type="text" placeholder="Twilio Auth Token" name="twilio_auth_token" value="{{!empty($value['twilio_auth_token']) ? $value['twilio_auth_token'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Twilio Number</label>
                                                <input class="form-control" type="text" placeholder="Twilio Number" name="twilio_number" value="{{!empty($value['twilio_number']) ? $value['twilio_number'] : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--End TWILIO Settings -->

                    <!-- S3 Bucket Settings -->
                    <div class="col-lg-12 mb-4">
                        <div class="bs-component">
                            <div class="card">
                                <?php
                                $key = 'S3_BUCKET';
                                $title = 'S3 Bucket Settings';
                                $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                ?>
                                <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                    <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Aws Access Key Id</label>
                                                <input class="form-control" type="input" placeholder="Aws Access Key Id" name="aws_access_key_id" value="{{!empty($value['aws_access_key_id']) ? $value['aws_access_key_id'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Aws Secret Access Key</label>
                                                <input class="form-control" type="input" placeholder="Aws Secret Access Key" name="aws_secret_access_key" value="{{!empty($value['aws_secret_access_key']) ? $value['aws_secret_access_key'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Aws Default Region</label>
                                                <input class="form-control" type="input" placeholder="Aws Default Region" name="aws_default_region" value="{{!empty($value['aws_default_region']) ? $value['aws_default_region'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Aws Bucket</label>
                                                <input class="form-control" type="input" placeholder="Aws Bucket" name="aws_bucket" value="{{!empty($value['aws_bucket']) ? $value['aws_bucket'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Aws Url</label>
                                                <input class="form-control" type="input" placeholder="Aws Url" name="aws_url" value="{{!empty($value['aws_url']) ? $value['aws_url'] : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--End S3 Bucket Settings -->

                    <!-- Stripe Settings -->
                    {{--<div class="col-lg-12 mb-4">
                        <div class="bs-component">
                            <div class="card">
                                <?php
                                $key = 'STRIPE';
                                $title = 'Stripe Settings';
                                $value = !empty($settings[$key]['value']) ?  $settings[$key]['value'] : [];
                                ?>
                                <form method="POST" action="{{ route('settings.save', ['settingName' => $key]) }}">
                                    <h5 class="card-header text-white bg-primary">{{$title}}</h5>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Secret Key</label>
                                                <input class="form-control" type="input" placeholder="Secret Key" name="secret_key" value="{{!empty($value['secret_key']) ? $value['secret_key'] : ''}}">
                                            </div>
                                            <div class="col-lg-4 col-sm-6 form-group">
                                                <label class="control-label">Public Key</label>
                                                <input class="form-control" type="input" placeholder="Public Key" name="public_key" value="{{!empty($value['public_key']) ? $value['public_key'] : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary submitBtn" type="button"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>--}}
                    <!--End Stripe Settings -->

                </div>
            </div>
        </div>
    </div>
    </div>
</main>


@stop

@section('custom-js')

@stop