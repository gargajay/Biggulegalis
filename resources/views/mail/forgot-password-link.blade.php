<!DOCTYPE html>
<html>
<head>
</head>
<body>
    {{--<p>Dear {{$data['userObject']->full_name}},</p>--}}
    <p>We have received a request to reset the password for your account. To complete the process, please click on the following link:</p>
    <a href="{{$data['resetLink']}}" style="color: #0000ee;" >{{$data['resetLink']}}</a>
    <p>If you did not request this password reset, please disregard this email.</p>
    <p>Best regards</p>
    <p>Strengthen Customer Support Team</p>
</body>
</html>

