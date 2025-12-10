<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .otp-box {
            font-size: 30px;
            background: #f0f4ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 5px;
            font-weight: bold;
            color: #3b6af7;
        }
        .small-text {
            font-size: 14px;
            color: #666666;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>{{ $message }}</h2>
        <p>Hello,</p>
        <p>Your One-Time Password (OTP) for verification is:</p>

        <div class="otp-box">{{ $otp }}</div>

        <p class="small-text">
            This code is valid for 10 minutes.  
            If you did not request this, please ignore the message.
        </p>


    </div>
</body>
</html>