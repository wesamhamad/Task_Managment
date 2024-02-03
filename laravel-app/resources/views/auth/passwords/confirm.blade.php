@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-md">
        <div class="mb-6 text-xl font-semibold text-gray-700">{{ __('Confirm Password') }}</div>

        <p class="mb-4 text-sm text-gray-600">{{ __('Please confirm your password before continuing.') }}</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-input @error('password') border-red-500 @enderror"
                    name="password" required autocomplete="current-password">
                @error('password')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Confirm Password') }}
                </button>

                @if (Route::has('password.request'))
                <a class="text-gray-600 text-sm hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection