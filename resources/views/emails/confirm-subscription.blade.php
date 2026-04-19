<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirm your subscription</title>
<style>
    body { margin: 0; padding: 0; background: #f5f3ef; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .wrap { max-width: 560px; margin: 40px auto; }
    .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .head { background: #1a1a1a; padding: 28px 36px; }
    .head img { height: 32px; }
    .head-text { color: #fff; font-size: 18px; font-weight: 600; }
    .body { padding: 36px; }
    .body p { color: #374151; font-size: 16px; line-height: 1.65; margin: 0 0 16px; }
    .btn { display: inline-block; background: #e85d04; color: #fff !important; text-decoration: none; font-size: 16px; font-weight: 700; padding: 14px 32px; border-radius: 10px; margin: 8px 0 20px; }
    .link { color: #6b7280; font-size: 13px; word-break: break-all; }
    .foot { padding: 20px 36px; border-top: 1px solid #f3f4f6; }
    .foot p { color: #9ca3af; font-size: 13px; margin: 0; line-height: 1.6; }
    .foot a { color: #9ca3af; }
</style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="head">
            @php
                try {
                    $siteName = \Contensio\Services\WhitelabelService::isActive()
                        ? \Contensio\Services\WhitelabelService::siteName()
                        : config('app.name');
                } catch (\Throwable) {
                    $siteName = config('app.name');
                }
            @endphp
            <span class="head-text">{{ $siteName }}</span>
        </div>
        <div class="body">
            <p>
                @if($subscriber->name)
                    Hi {{ $subscriber->name }},
                @else
                    Hello,
                @endif
            </p>
            <p>
                Thanks for subscribing! Click the button below to confirm your email address and activate your subscription.
            </p>
            <a href="{{ $confirmUrl }}" class="btn">Confirm my subscription</a>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p class="link">{{ $confirmUrl }}</p>
            <p>If you didn't sign up for this newsletter, you can safely ignore this email.</p>
        </div>
        <div class="foot">
            <p>
                You're receiving this email because someone (hopefully you) entered your address on
                <a href="{{ url('/') }}">{{ url('/') }}</a>.
            </p>
        </div>
    </div>
</div>
</body>
</html>
