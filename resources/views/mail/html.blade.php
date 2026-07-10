<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $newsletter->subject }}</title>
</head>
<body>
    {!! $content !!}

    @if ($unsubscribeUrl)
        <hr>
        <p style="font-size: 12px; color: #6b7280;">
            <a href="{{ $unsubscribeUrl }}">{{ __('newsletter::emails.unsubscribe') }}</a>
        </p>
    @endif
</body>
</html>
