let loginJs;

$(document).ready(function () {
    loginJs = new LoginJs();
    loginJs.init();
});


function LoginJs() {
    let that = this;
    that.init = function () {
        // hide loader
        $('.web-loader').addClass('hide');


        // login user
        $('#login-btn').on('click', function (e) {
            e.preventDefault();
            that.checkLogin();
        });


        // forgot password user
        $('#forgot-password-btn').on('click', function (e) {
            e.preventDefault();
            that.forgotPassword();
        });


        // reset password user
        $('#reset-password-btn').on('click', function (e) {
            e.preventDefault();
            that.resetPassword();
        });

    }

    // check login user details
    that.checkLogin = function () {
        try {
            if (!$('#email').val()) { throw 'Email is required.' }
            if (!$('#password').val()) { throw 'Password is required.' }
            $.ajax({
                url: $('#login-form').attr('action'),
                type: "POST",
                data: $('#login-form').serialize(),
                cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('#login-form .alert').addClass('hide');
                    $('.web-loader').removeClass('hide');
                },
                complete: function () {
                    $('.web-loader').addClass('hide');
                },
                success: function (jsonData) {
                    if (jsonData.success === true) {
                        window.location = "dashboard";
                    }
                    if (jsonData.success === false) {
                        $('#login-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jsonData.message);

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.message) {
                        $('#login-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jqXHR.responseJSON.message);
                    }
                }
            });
        }
        catch (err) {
            $('#login-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + err);

        }
    }


    // forgot user password
    that.forgotPassword = function () {
        try {
            if (!$('#email').val()) { throw 'Email is required.' }
            $.ajax({
                url: $('#forgot-form').attr('action'),
                type: "POST",
                data: $('#forgot-form').serialize(),
                cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('#forgot-form .alert').addClass('hide');
                    $('.web-loader').removeClass('hide');
                },
                complete: function () {
                    $('.web-loader').addClass('hide');
                },
                success: function (jsonData) {
                    if (jsonData.success === true) {
                        $('#forgot-form')[0].reset();
                        $('#forgot-form .alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success').html('<b>Success: </b>' + jsonData.message);
                    }
                    if (jsonData.success === false) {
                        $('#forgot-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jsonData.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.message) {
                        $('#forgot-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jqXHR.responseJSON.message);
                    }
                }
            });
        }
        catch (err) {
            $('#forgot-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + err);
        }
    }


    // reset user password
    that.resetPassword = function () {
        try {
            if (!$('#password').val()) { throw 'Password is required.' }
            if (!$('#password_confirmation').val()) { throw 'Confirm Password is required.' }
            if ($('#password').val() != $('#password_confirmation').val()) { throw 'Both password not same.' }

            $.ajax({
                url: $('#reset-form').attr('action'),
                type: "POST",
                data: $('#reset-form').serialize(),
                cache: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('#reset-form .alert').addClass('hide');
                    $('.web-loader').removeClass('hide');
                },
                complete: function () {
                    $('.web-loader').addClass('hide');
                },
                success: function (jsonData) {
                    if (jsonData.success === true) {
                        $('#reset-form')[0].reset();
                        $('#reset-form .alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success').html('<b>Success: </b>' + jsonData.message);
                    }
                    if (jsonData.success === false) {
                        $('#reset-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jsonData.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.message) {
                        $('#reset-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + jqXHR.responseJSON.message);
                    }
                }
            });
        }
        catch (err) {
            $('#reset-form .alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger').html('<b>Error: </b>' + err);
        }
    }
}
