@extends('newsletter::layout')

@section('title', __('newsletter::emails.unsubscribed_title'))

@section('content')
    <h1 class="mb-2 text-xl font-semibold text-gray-900">{{ __('newsletter::emails.unsubscribed_title') }}</h1>
    <p class="text-gray-600">{{ __('newsletter::emails.unsubscribed_body') }}</p>
@endsection
