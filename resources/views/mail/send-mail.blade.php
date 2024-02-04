<!DOCTYPE html>
<html>
<head>
</head>


<body>
    <h1>{{ $data['subject'] }}</h1>
    <p></p>
    
    <ul>
        <li><strong>Email:</strong> {{ $data['data']['email'] }}</li>
        <li><strong>Phone:</strong> {{ $data['data']['phone'] }}</li>
        <li><strong>Description:</strong> {{ $data['data']['description'] }}</li>
    </ul>
</body>
</html>
