<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('lms::messages.credentials_email_subject', ['app_name' => $appName ?? 'Noor Alhuda LMS']) }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .credentials-box {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-row {
            display: flex;
            margin-bottom: 10px;
        }
        .credential-label {
            font-weight: bold;
            width: 100px;
            color: #555;
        }
        .credential-value {
            font-family: monospace;
            font-size: 16px;
            color: #333;
        }
        .password {
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: monospace;
            letter-spacing: 2px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $appName ?? 'Noor Alhuda LMS' }}</h1>
        <p>{{ __('lms::messages.welcome_message') }}</p>
    </div>

    <div class="content">
        <h2>{{ __('lms::messages.credentials_email_greeting', ['name' => $user->name]) }}</h2>

        <p>{{ __('lms::messages.credentials_email_intro') }}</p>

        <div class="credentials-box">
            <div class="credential-row">
                <span class="credential-label">{{ __('Email') }}:</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">{{ __('User ID') }}:</span>
                <span class="credential-value">{{ $user->user_id }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">{{ __('Password') }}:</span>
                <span class="credential-value password">{{ $password }}</span>
            </div>
        </div>

        <p><strong>{{ __('lms::messages.credentials_email_note') }}</strong></p>

        <a href="{{ url('/login') }}" class="btn">{{ __('lms::messages.login_now') }}</a>

        <p>{{ __('lms::messages.credentials_email_thanks') }}</p>
    </div>

    <div class="footer">
        <p>{{ __('lms::messages.credentials_email_footer') }}</p>
    </div>
</body>
</html>
