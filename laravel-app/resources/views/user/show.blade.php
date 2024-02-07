@extends('layouts.app')
@section('title', 'View Task')
@section('content')
<div class="flex items-center justify-center h-screen">
    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <div>
            <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Name:
                {{$tasksDetails->name}}</h5>
            <h5 class="mb-2 text-l font-semibold tracking-tight text-gray-700 dark:text-white">Assign To:
                {{$tasksDetails->assignTo}}</h5>
            <h5 class="mb-2 text-l font-semibold tracking-tight text-gray-700 dark:text-white">Deadlin:
                {{$tasksDetails->deadlin}}</h5>
            <h5 class="mb-2 text-l font-semibold tracking-tight text-gray-700 dark:text-white">Status:
                {{$tasksDetails->status}}</h5>
            <h5 class="mb-2 text-l font-semibold tracking-tight text-gray-700 dark:text-white">Description: <br>
                {{$tasksDetails->description}}</h5>
        </div>
    </div>
</div>
@endsection