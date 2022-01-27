<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Register Activate Email</title>
</head>
<body>
    <h1>Hi! Dear <strong>{{ $name }}</strong></h1>
    <p>Your account has been created Successfully</p>
    <p>To verify Your account Click the button</p>
    <a href="{{ url('confirm/'.$code) }}" class="btn btn-primary">Activate Now</a>
    
</body>
</html>