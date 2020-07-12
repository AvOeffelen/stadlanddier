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
    <section class="background">
        <div class="row">
            <div class="col-md-12 col-xl-12 d-sm-none d-none d-md-block d-xl-block">
                <div class="p-2 text-center">
                    <h1 class="display-1 sld-title">Stad, land & dier!</h1>
                </div>
            </div>
        </div>
        <div id="wrapper" class="py-3">
            <div id="content-wrapper" class="content-wrapper">
                <div class="container-fluid" id="content">
                    @yield('content')
                </div>
            </div>
            {{--        @include('partials.footer.footer')--}}
        </div>
    </section>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="#1e3799" fill-opacity="1" d="M0,128L17.1,160C34.3,192,69,256,103,272C137.1,288,171,256,206,229.3C240,203,274,181,309,165.3C342.9,149,377,139,411,117.3C445.7,96,480,64,514,69.3C548.6,75,583,117,617,117.3C651.4,117,686,75,720,96C754.3,117,789,203,823,240C857.1,277,891,267,926,245.3C960,224,994,192,1029,192C1062.9,192,1097,224,1131,218.7C1165.7,213,1200,171,1234,165.3C1268.6,160,1303,192,1337,197.3C1371.4,203,1406,181,1423,170.7L1440,160L1440,0L1422.9,0C1405.7,0,1371,0,1337,0C1302.9,0,1269,0,1234,0C1200,0,1166,0,1131,0C1097.1,0,1063,0,1029,0C994.3,0,960,0,926,0C891.4,0,857,0,823,0C788.6,0,754,0,720,0C685.7,0,651,0,617,0C582.9,0,549,0,514,0C480,0,446,0,411,0C377.1,0,343,0,309,0C274.3,0,240,0,206,0C171.4,0,137,0,103,0C68.6,0,34,0,17,0L0,0Z"></path>
    </svg>
</div>
@yield('scripts')
</body>
</html>
