@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-4 text-lg leading-6 font-medium text-gray-900">{{ __('Dashboard') }}</div>

            <div class="mb-4">
                @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <p class="text-sm text-gray-600">
                    {{ __('You are logged in!') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection