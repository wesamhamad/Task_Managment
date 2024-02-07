<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel')}} | @yield('title','Home')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/flowbite@1.4.4/dist/flowbite.js"></script>
</head>

<body>
    <div id="app">
        <!-- Tailwind CSS Navigation Bar -->
        <nav class="bg-white border-gray-200 px-2 sm:px-4 py-2.5 dark:bg-gray-900">
            <div class="container flex flex-wrap justify-between items-center mx-auto">
                <a href="{{ url('/') }}" class="flex items-center">
                    <img src="https://flowbite.com/docs/images/logo.svg" class="mr-3 h-6 sm:h-9" alt="Flowbite Logo">
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">{{
                        config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex items-center md:order-2">
                    @guest
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Login</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                    @endif
                    @else
                    <!-- Dynamic Home Link -->
                    <button id="user-menu-button" data-dropdown-toggle="user-menu"
                        class="text-gray-500 bg-white dark:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center"
                        type="button">{{ Auth::user()->name }}<svg class="ml-2 w-4 h-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg></button>
                    <!-- Dropdown menu -->
                    <div id="user-menu"
                        class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700"
                        data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="top">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="user-menu-button">
                            <!-- Show Add Task for admin users -->
                            @if(Auth::user()->usertype == 'admin')
                            <li>
                                <a href="{{ route('tasks.create') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600
                                    dark:hover:text-white">Add Task</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endguest
                </div>

                <div class="hidden justify-between items-center w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
                    <ul
                        class="flex flex-col p-4 mt-4 bg-gray-50 rounded-lg border border-gray-100 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                        <li>
                            @guest
                            <!-- Direct guests to the login page -->
                            <a href="{{ route('login') }}"
                                class="block py-2 pr-4 pl-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700">Home</a>
                            @else
                            <!-- Direct logged-in users to the appropriate "Home" page based on their user type -->
                            <a href="{{ Auth::user()->usertype == 'admin' ? route('projects.index') : route('tasks.index') }}"
                                class="block py-2 pr-4 pl-3 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-gray-400 md:dark:hover:text-white dark:hover:bg-gray-700">Home</a>
                            @endguest
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        @if(session('success_message'))
        <div id="successMessage" class="bg-green-500 text-white p-4 mb-4">
            {{ session('success_message') }}
            <button onclick="dismissMessage('successMessage')"
                class="float-right text-sm focus:outline-none">Dismiss</button>
        </div>
        @endif

        @if(session('error_message'))
        <div id="errorMessage" class="bg-red-500 text-white p-4 mb-4">
            {{ session('error_message') }}
            <button onclick="dismissMessage('errorMessage')"
                class="float-right text-sm focus:outline-none">Dismiss</button>
        </div>
        @endif


        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script>
    function dismissMessage(messageId) {
        document.getElementById(messageId).style.display = 'none';
    }
    // Automatically dismiss after 3 seconds
    setTimeout(dismissMessage, 3000);
    </script>

</body>

</html>