@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-7xl">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-4 text-lg leading-6 font-medium text-gray-900">{{ __('Dashboard') }}</div>

            <!-- Tasks Created by User -->
            <div class="mb-4">
                <!-- Add New Task Button -->
                <div>
                    <a class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 mr-2 rounded"
                        href="{{ route('tasks.create') }}">Add New Task</a>
                </div>

                <!-- Table to display tasks created by the user -->
                <h2 class="text-lg font-medium text-gray-900 mb-2">Tasks Created by You</h2>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <!-- Table Headers -->
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">Task Name</th>
                                <th scope="col" class="p-4">Deadlines</th>
                                <th scope="col" class="p-4">Status</th>
                                <th scope="col" class="p-4">Description</th>
                                <th scope="col" class="p-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through user's tasks and display them -->
                            @forelse($user_created_tasks as $task)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 text-gray-900">{{$task->name}}</td>
                                <td class="px-6 py-4">{{$task->deadlin}}</td>
                                <td class="px-6 py-4">{{$task->status}}</td>
                                <td class="px-6 py-4">{{$task->description}}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col mt-1">
                                        <div class="flex mb-2">
                                            <!-- View button -->
                                            <a class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 mr-2 rounded"
                                                href="{{ route('tasks.show', $task->id) }}">View</a>
                                            <!-- Update button -->
                                            <a class="bg-green-500 hover:bg-green-400 text-white  py-1 px-2 mr-2 rounded"
                                                href="{{ route('tasks.edit', $task->id) }}">Update</a>
                                            <!-- Delete button -->
                                            <form action="{{ route('tasks.destroy', $task->id)}}" method="post"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <!-- Display message if no tasks are created by the user -->
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 text-gray-900" colspan="5">No tasks created by you.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Projects Assigned to User -->
            <div>
                <!-- Table to display projects assigned to the user -->
                <h2 class="text-lg font-medium text-gray-900 mb-2">Projects Assigned to You</h2>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <!-- Table Headers -->
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-4">Project Title</th>
                                <th scope="col" class="p-4">Tasks</th>
                                <th scope="col" class="p-4">Assigned Users</th>


                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through projects assigned to the user and display them -->
                            @forelse($projects as $project)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 text-gray-900">
                                    {{ $project->title }}
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    <ul>
                                        <!-- Loop through tasks of the project and display them -->
                                        @foreach($project->tasks as $task)
                                        <li>{{ $task->name }}</li>
                                        <!-- Display other task details as needed -->
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 text-gray-900">
                                    <ul>
                                        <!-- Loop through assigned users of the task and display them -->
                                        @foreach($task->users as $user)
                                        <li>{{ $user->name }}</li>
                                        <!-- Display other user details as needed -->
                                        @endforeach
                                    </ul>
                                </td>

                            </tr>
                            @empty
                            <!-- Display message if no projects are assigned to the user -->
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 text-gray-900" colspan="2">No projects assigned to you.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection