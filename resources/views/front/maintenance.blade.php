<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Under Maintenance | {{ setting('site_name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Plus+Jakarta+Sans:wght@800&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; font-family: Inter, sans-serif; background: linear-gradient(135deg, #06173a, #0b3d91); color: #fff; text-align: center; padding: 20px; }
        .box { max-width: 560px; } img { height: 80px; background: #fff; border-radius: 16px; padding: 8px 14px; }
        h1 { font-family: 'Plus Jakarta Sans'; font-size: 2.2rem; margin: 28px 0 10px; } p { color: rgba(255,255,255,.8); line-height: 1.7; }
        a { color: #ffc93c; }
    </style>
</head>
<body>
<div class="box">
    <img src="{{ img_url(setting('site_logo')) }}" alt="{{ setting('site_name') }}">
    <h1>We’ll be back soon</h1>
    <p>{{ setting('maintenance_message') }}</p>
    @if(setting('contact_phone'))<p>Call us: <a href="{{ tel_link(setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></p>@endif
</div>
</body>
</html>
