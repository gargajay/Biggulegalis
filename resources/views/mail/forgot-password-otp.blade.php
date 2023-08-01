
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <p>Dear {{$data['userObject']->full_name}},</p>
    <p>We have received a request to reset the password for your account. To complete the process, please use the following one-time password (OTP) within {{$data['otpExpireTime']}}:</p>
    <h3 style="font-size: 36px; font-weight: bold; color: #3b3e42;">{{$data['otp']}}</h3>
    <p>If you did not request this password reset, please contact our support team immediately.</p>
    <p>Thank you</p>
</body>
</html>


