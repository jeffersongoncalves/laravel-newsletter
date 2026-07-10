@component('mail::message')
{!! $content !!}

@if ($unsubscribeUrl)
@component('mail::subcopy')
[{{ __('newsletter::emails.unsubscribe') }}]({{ $unsubscribeUrl }})
@endcomponent
@endif
@endcomponent
