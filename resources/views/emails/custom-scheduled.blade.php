@php
    $title = $subject ?? 'Scheduled Email';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #0f172a;
            background: #f8fafc;
            margin: 0;
            padding: 24px;
        }
        .email-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
            max-width: 640px;
            margin: 0 auto;
        }
        .email-title {
            font-size: 18px;
            margin: 0 0 12px;
        }
        .email-body {
            font-size: 14px;
            line-height: 1.6;
            color: #1f2937;
            white-space: normal;
        }
        .email-body p {
            margin: 0 0 10px;
        }
        .email-body p:last-child {
            margin-bottom: 0;
        }
        .email-body ul,
        .email-body ol {
            margin: 8px 0 8px 18px;
        }
    </style>
</head>
<body>
    <div class="email-card">
        @if(!empty($name))
            <h1 class="email-title">{{ $name }}</h1>
        @endif
        <div class="email-body">{!! $body_html ?: nl2br(e($body ?? '')) !!}</div>
    </div>
</body>
</html>
