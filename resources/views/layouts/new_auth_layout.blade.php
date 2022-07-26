<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <!-- <base href="/"> -->

    <!-- CSRF Token -->

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

    <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,700&display=swap&subset=cyrillic" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--        <link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
    {{--        <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">--}}

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">



</head>
<body class="new_pack_styles">
<a href="{{ url('http://smartpetrol.ru') }}"></a>

<div id="app" class="main-containter @if (request()->getRequestUri() == '/' or request()->getRequestUri() == '/auth') main-background @endif">

    <section class="content">
        @yield('content')
        <notifications group="success" position="bottom right"/>
    </section>

    <footer>
        <div class="copyright" style="width: 100%; background: black">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 copyright-vs" style="margin-left: 900px;">
                        <a class="copyright text" style=" background:black; color: rgb(175, 175, 175); font-family: Roboto Regular; font-style: normal; font-size: 10pt;">РАЗРАБОТАНО КОМПАНИЕЙ</a>
                        <img src="{{asset('img/Group 2.png')}}" class="img-copyright">
                        <a href="{{url('https://smartpetrol.ru/')}}">
                            <img src="{{ asset('img/СМАРТПЕТРОЛ.png') }}" alt="СМАРТПЕТРОЛ">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
</body>

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>

@yield('js')
</html>
