<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}" defer></script>
<link href="{{ mix('css/app.css') }}" rel="stylesheet">

{{--    <script>--}}
{{--        console.log("__________                    .___ ________                .__               \n" +--}}
{{--            "\\______   \\ _____   ____    __| _/ \\______ \\   ____   _____|__| ____   ____  \n" +--}}
{{--            " |       _//     \\ /    \\  / __ |   |    |  \\_/ __ \\ /  ___/  |/ ___\\ /    \\ \n" +--}}
{{--            " |    |   \\  Y Y  \\   |  \\/ /_/ |   |    `   \\  ___/ \\___ \\|  / /_/  >   |  \\\n" +--}}
{{--            " |____|_  /__|_|  /___|  /\\____ |  /_______  /\\___  >____  >__\\___  /|___|  /\n" +--}}
{{--            "        \\/      \\/     \\/      \\/          \\/     \\/     \\/  /_____/      \\/");--}}
{{--    </script>--}}

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>

</head>
<body>
<div id="app">
    @include('partials.nav')
    <section class="wave-container">
        <div class="p-5">
            <h1 class="display-1">Stad, land & dier!</h1>
        </div>
        <div class="wave"></div>
        <div id="wrapper">
            <div id="content-wrapper" class="content-wrapper">
                <div class="container-fluid" id="content">
                    @yield('content')
                </div>
            </div>
            {{--        @include('partials.footer.footer')--}}
        </div>
    </section>
</div>
@yield('scripts')
</body>
</html>
