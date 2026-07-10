@component('mail::message')
# {{ __('newsletter::emails.confirm_heading') }}

{{ __('newsletter::emails.confirm_body') }}

@component('mail::button', ['url' => $confirmUrl])
{{ __('newsletter::emails.confirm_button') }}
@endcomponent
@endcomponent
