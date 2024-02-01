<!doctype html>
<html>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- @vite('resources/css/app.css') -->
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <title>@yield('title')</title>
    <script src="https://unpkg.com/flowbite@1.4.4/dist/flowbite.js"></script>
</head>

<body>
    @include('include.header')
    @yield('body')
</body>

</html>