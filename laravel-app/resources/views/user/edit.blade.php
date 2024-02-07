@extends('layouts.app')
@section('title', 'Edit Task')
@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-7xl">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-4 text-lg leading-6 font-medium text-gray-900">{{ __('Edit Task') }}</div>
            <form method="post" class="max-w-md mx-auto" action="{{ route('tasks.update', $tasksDetails->id) }}">
                @csrf
                @method('PATCH')
                <div class="relative z-0 w-full mb-5 group">
                    <label for="name">Task Name</label>
                    <input name="name" id="name"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        value="{{ $tasksDetails->name }}" required />
                </div>
                <div class="mb-4">
                    <label for="assignTo" class="block text-sm font-medium text-gray-700">Assign To</label>
                    <select id="assignTo" name="assignTo"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach($users as $userId => $userName)
                        <option value="{{ $userId }}" @if($userId==$tasksDetails->assignTo) selected @endif>{{ $userName
                            }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative z-0 w-full mb-5 group">
                    <input type="date" name="deadlin" id="deadlin"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        value="{{ \Carbon\Carbon::parse($tasksDetails->deadlin)->format('Y-m-d') }}" required />
                    <label for="deadlin"
                        class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                        DEADLINES</label>
                </div>

                <div class="relative z-0 w-full mb-5 group">
                    <select name="status" id="status"
                        class="block w-full p-2 mb-6 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="Not Work" @if($tasksDetails->status == 'Not Work') selected @endif>Not Work
                        </option>
                        <option value="Pending" @if($tasksDetails->status == 'Pending') selected @endif>Pending</option>
                        <option value="Completed" @if($tasksDetails->status == 'Completed') selected @endif>Completed
                        </option>
                    </select>
                </div>

                <div class="relative z-0 w-full mb-5 group">
                    <label for="description">DESCRIPTION</label>
                    <input name="description" id="description"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        value="{{ $tasksDetails->description }}" required />
                </div>

                <button type="submit" class="btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection