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
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                    class="delete-project-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="project_name" value="{{ $project->title }}">
                                    <button type="button"
                                        class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded delete-project-btn">
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
<script>
// Show a prompt to the user asking to confirm the deletion by typing the project name.

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.delete-project-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            const form = this.closest('form');
            const projectName = form.querySelector('input[name="project_name"]').value;
            const userInput = prompt(
                `Please confirm the deletion by typing the name of the project: '${projectName}'`
            );

            if (userInput === projectName) {
                form.submit();
            } else {
                alert("The entered name did not match. Deletion cancelled.");
            }
        });
    });
});
</script>
@endsection