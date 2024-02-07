@extends('layouts.app')
@section('title', 'Create Project')
@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-3xl">
        <div class="bg-white py-8 px-6 sm:rounded-lg sm:px-10 shadow-xl">
            <div class="mb-8 text-3xl leading-8 font-bold text-gray-900 flex items-center justify-center">
                {{ __('Create Project') }}
            </div>

            @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="projectTitle" class="block text-gray-700 text-sm font-bold mb-2">Project Title:</label>
                    <input type="text" name="title" id="projectTitle"
                        class="w-full px-4 py-3 rounded-md border focus:outline-none focus:ring focus:border-blue-500 transition duration-300"
                        placeholder="Enter project title" required>
                </div>
                <div class="mb-4">
                    <label for="projectDescription" class="block text-gray-700 text-sm font-bold mb-2">Project
                        Description:</label>
                    <textarea name="description" id="projectDescription" rows="4"
                        class="w-full px-4 py-3 rounded-md border focus:outline-none focus:ring focus:border-blue-500 transition duration-300"
                        placeholder="Enter project description" required></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Select Tasks:</label>
                    @foreach ($tasks as $task)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="tasks[]" id="task{{ $task->id }}" value="{{ $task->id }}"
                            class="mr-2">
                        <label for="task{{ $task->id }}">{{ $task->name }}</label>
                    </div>
                    @endforeach
                </div>


                <button type="submit"
                    class="px-8 py-4 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-300">Create
                    Project</button>
            </form>
        </div>
    </div>
</div>
@endsection