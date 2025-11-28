<!DOCTYPE html>
<html class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">


    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Inertia --}}
    <script src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>

    {{-- Ping CRM --}}
    <script src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?features=String.prototype.startsWith" defer></script>


    <script src="https://cdn.jsdelivr.net/npm/@joint/core@4.0.1/dist/joint.js"></script>



    @vite('resources/js/app.js')

    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
