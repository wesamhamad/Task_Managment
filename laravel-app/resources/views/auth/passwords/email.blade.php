@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-md">
        <div class="mb-6 text-xl font-semibold text-gray-700">{{ __('Reset Password') }}</div>

        @if (session('status'))
        <div class="mb-4 text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email"
                    class="block text-gray-700 text-sm font-medium mb-2">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-input @error('email') border-red-500 @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection