@extends('newsletter::layout')

@section('title', __('newsletter::emails.subscribe_title'))

@section('content')
    <h1 class="mb-4 text-xl font-semibold text-gray-900">{{ __('newsletter::emails.subscribe_title') }}</h1>

    @if (session('status'))
        <p class="mb-4 text-sm text-green-600">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                {{ __('newsletter::emails.subscribe_email_label') }}
            </label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full rounded-md bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700">
            {{ __('newsletter::emails.subscribe_button') }}
        </button>
    </form>
@endsection
