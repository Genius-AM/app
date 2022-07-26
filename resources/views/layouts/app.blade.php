<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

    <head>
        <meta charset="utf-8">
        <base href="/">

        <title>Лесные угодья | @yield('title')</title>
        <meta name="description" content="@yield('description')">
        <a href="{{ url('http://smartpetrol.ru') }}"></a>

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

{{--        <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">--}}
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="{{ asset('js/jquery-3.1.1.slim.min.js') }}"></script>
        <script src="{{ asset('js/tether-1.4.0.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-4.0.0-alpha.6.min.js') }}"></script>
    </head>
    <body>
        <div class="main-containter">
            <header>
                <div class="top-panel closable-topnav-block">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2">
                                <a href="{{ route('index') }}"><img src="{{ asset('img/logo.png') }}" style="width: 95px;position: relative;margin-right: 70px;" alt=""></a>
                            </div>
                            <div class="col-lg-8" align="center">
                                <nav class="top-panel__gel">
                                    @if(request()->user())
                                        @include('subviews.ordersmenu')
                                    @endif
                                </nav>
                            </div>
                            <div class="col-lg-2">
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

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="f-menu">
                            @include('subviews.bottommenu', ['role' => 2])
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <a href="{{ url('http://smartpetrol.ru') }}" target="_blank">РАЗРАБОТАНО КОМПАНИЕЙ СМАРТПЕТРОЛ</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        </div>

        <script src="{{ asset('js/app.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>
</html>