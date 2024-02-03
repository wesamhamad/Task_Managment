@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-4 text-lg leading-6 font-medium text-gray-900">{{ __('Verify Your Email Address') }}</div>

            <div class="mb-4">
                @if (session('resent'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
                @endif

                <p class="text-sm text-gray-600">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                </p>

                <form method="POST" action="{{ route('verification.resend') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                        {{ __('click here to request another') }}
                    </button>.
                </form>
            </div>
        </div>
    </div>
</div>
@endsection