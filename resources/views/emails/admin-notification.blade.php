<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .message {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            white-space: pre-wrap;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Auto Golf</h2>
    </div>
    
    <div class="content">
        <p>Dear {{ $userName }},</p>
        
        <div class="message">
            {{ $emailMessage }}
        </div>
        
        <p>Best Regards,<br>
        Auto Golf Admin Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from Auto Golf.</p>
    </div>
</body>

</html>

