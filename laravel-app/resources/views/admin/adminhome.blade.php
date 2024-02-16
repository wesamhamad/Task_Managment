@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-3xl">
        <div class="bg-white py-8 px-6 sm:rounded-lg sm:px-10 shadow-xl">
            <div class="mb-8 text-3xl leading-8 font-bold text-gray-900 flex items-center justify-center">
                {{ __('Admin Dashboard') }}
            </div>

            @if (session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <div class="flex items-center justify-between flex-wrap md:flex-nowrap">
                <a href="{{route('projects.create')}}"
                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Add New Project +</a>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Project Name</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">Tasks</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $project->title }}</td>
                            <td class="px-6 py-4">{{ $project->description }}</td>
                            <td class="px-6 py-4">
                                @foreach ($project->tasks as $task)
                                <div>{{ $task->name }}</div>
                                @endforeach
                            </td>

                            <td class="px-6 py-4 ">
                                <form action="{{ route('projects.confirm-destroy', $project->id) }}" method="POST">
                                    @csrf
                                    <input type="text" name="project_name" placeholder="Enter project name">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection