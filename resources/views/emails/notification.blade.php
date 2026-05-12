{{-- Notification Email Template --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }} - Noor Alhuda LMS</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
        .content { padding: 30px 20px; background: #f8fafc; }
        .footer { background: #1f2937; color: white; padding: 20px; text-align: center; font-size: 12px; }
        .button { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .notification-card { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .notification-icon { font-size: 24px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Noor Alhuda LMS</h1>
            <p>Learning Management System</p>
        </div>

        <div class="content">
            <div class="notification-card">
                <div class="notification-icon">
                    @switch($notification->type)
                        @case('grade') 📊
                        @case('enrollment') 👥
                        @case('payment') 💳
                        @case('announcement') 📢
                        @case('reminder') ⏰
                        @case('system') ⚙️
                        @default 🔔
                    @endswitch
                </div>

                <h2>{{ $notification->title }}</h2>
                <p>{{ $notification->content }}</p>

                @if($notification->link)
                    <a href="{{ url($notification->link) }}" class="button">View Details</a>
                @endif

                <p style="color: #6b7280; font-size: 14px; margin-top: 20px;">
                    Received: {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                </p>
            </div>

            <p>If you no longer wish to receive these notifications, you can update your preferences in your account settings.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Noor Alhuda LMS. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>