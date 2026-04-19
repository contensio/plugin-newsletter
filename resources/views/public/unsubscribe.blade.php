<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $success ? 'Unsubscribed' : 'Link not found' }}</title>
<style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; min-height: 100dvh; display: flex; align-items: center; justify-content: center; background: #f5f3ef; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 24px; }
    .card { background: #fff; border-radius: 20px; padding: 48px 40px; max-width: 440px; width: 100%; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .icon { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 32px; }
    .icon-ok { background: #f3f4f6; color: #6b7280; }
    .icon-err { background: #fee2e2; color: #dc2626; }
    h1 { font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 12px; }
    p { font-size: 16px; color: #6b7280; line-height: 1.65; margin: 0 0 28px; }
    a.btn { display: inline-block; background: #1a1a1a; color: #fff; text-decoration: none; font-size: 15px; font-weight: 600; padding: 12px 28px; border-radius: 10px; }
    a.btn:hover { background: #e85d04; }
</style>
</head>
<body>
<div class="card">
    @if($success)
    <div class="icon icon-ok">✓</div>
    <h1>You've been unsubscribed</h1>
    <p>Your email address has been removed from our mailing list. You won't receive any more emails from us.</p>
    <a href="{{ url('/') }}" class="btn">Back to site</a>
    @else
    <div class="icon icon-err">✗</div>
    <h1>Link not found</h1>
    <p>This unsubscribe link is invalid or has already been used. If you're still receiving emails, please contact us directly.</p>
    <a href="{{ url('/') }}" class="btn">Back to site</a>
    @endif
</div>
</body>
</html>
