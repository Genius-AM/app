<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

    <head>
        <meta charset="utf-8">

        <title>Лесные угодья | @yield('title')</title>
        <meta name="description" content="@yield('description')">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- Template Basic Images Start -->
        <meta property="og:image" content="{{ asset('img/favicon/favicon.ico') }}">
        <link rel="icon" href="{{ asset('img/favicon/favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon-180x180.png') }}">
        <!-- Template Basic Images End -->

        <!-- Custom Browsers Color Start -->
        <meta name="theme-color" content="#00937c">
        <!-- Custom Browsers Color End -->

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700&display=swap&subset=cyrillic" rel="stylesheet">

        {{--<link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
{{--        <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">--}}
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="{{ asset('js/jquery-3.1.1.slim.min.js') }}"></script>
        <script src="{{ asset('js/tether-1.4.0.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-4.0.0-alpha.6.min.js') }}"></script>
    </head>
    <body class="new_pack_styles">
        <header>
            <div class="top-panel">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <nav class="top-panel__gel">
                                @include('subviews.ordersmenu')
                            </nav>
                            @include('subviews.authmenu')
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <section class="title-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>@yield('title')</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            @yield('content')
        </section>

        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>