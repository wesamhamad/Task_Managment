@extends('layouts.app')
@section('title', 'Home')
@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-7xl">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-4 text-lg leading-6 font-medium text-gray-900">{{ __('Dashboard') }}</div>

            <div class="mb-4">

                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <div
                            class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900">
                            <div>
                                <a class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 mr-2 rounded"
                                    href="{{url('/user-home/create')}}">Add
                                    New
                                    +</a>
                            </div>
                            <label for="table-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="table-search-users"
                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search for users">
                            </div>
                        </div>
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                                <tr>
                                    <th scope="col" class="p-4">
                                        <div class="flex items-center">
                                            <input id="checkbox-all-search" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Task Name
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Assign To
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Deadlines
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Status
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Description
                                    </th>
                                    <th scope="col" class="px-4 py-2">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($user_tasks))
                                @forelse($user_tasks as $item)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input id="checkbox-table-search-1" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{$item->name}}
                                    </td>

                                    <td class="px-6 py-4 text-gray-900">
                                        {{$item->assignTo}}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{$item->deadlin}}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            {{$item->status}}
                                        </div>
                                    </td>

                                    <th scope="row"
                                        class="flex items-center px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="ps-3">

                                            <div class="font-normal text-gray-500">{{$item->description}}</div>
                                        </div>
                                    </th>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col mt-1">

                                            <div class="flex mb-2">

                                                <!-- View button -->
                                                <a class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 mr-2 rounded"
                                                    href="{{url('/user-home/'.$item->id)}}">View</a>
                                                <!-- Update button -->
                                                <a class="bg-green-500 hover:bg-green-400 text-white  py-1 px-2 mr-2 rounded"
                                                    href="{{url('/user-home/'.$item->id.'/edit')}}">Update</a>

                                                <!-- Delete button -->
                                                <form action="{{ route('tasks.destroy', $item->id)}}" method="post"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded">
                                                        Delete
                                                    </button>

                                                </form>
                                            </div>
                                    </td>

                                </tr>
                                @empty
                                <td class="px-6 py-4 text-gray-900">
                                    No tasks yet.
                                </td>
                                @endforelse
                                @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection