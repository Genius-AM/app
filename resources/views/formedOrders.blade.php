@extends('layouts.new__layout')
<header>
    <div class="top-panel closable-topnav-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2">
                    <a href="{{ route('index') }}" class="float-left"><img src="{{ asset('img/logo.png') }}" style="width: 95px;position: relative;margin-right: 70px;" alt=""></a>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1>@yield('title')</h1>
            </div>
        </div>
    </div>
</section>

@section('title', 'Сформированные заказы')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="form-item">
                    <legend>Услуга</legend>
                    <select name="filter_category" id="filter_category" class="filters">
                        <option value="">Все услуги</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <legend>Время</legend>
                    <select name="filter_time" id="filter_time" class="filters">
                        <option value="">Весь день</option>
                        <option value="00:00">Утро</option>
                        <option value="12:00">Полдень</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-item">
                    <legend>Маршрут</legend>
                    <select name="filter_route" id="filter_route" class="filters">
                        <option value="">Все маршруты</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-item">
                    <legend>Дата</legend>
                    <input type="date" name="filter_date" id="filter_date" class="filters">
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="form-item">
                    <legend>Статус</legend>
                    <select name="filter_status" id="filter_status" class="filters">
                        <option value="">Все статусы</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="formed_orders">
                @include('shared.formedOrders')
            </div>
        </div>
    </div>
    @isset($orders)
        <script>
            $(document).ready(function() {
                $('.filters').change(function () {
                    $.ajax({
                        url: '{{ route('dispatcher.orders.formedShort') }}',
                        type: "GET",
                        data: {category: $('#filter_category').val(),
                            route: $('#filter_route').val(),
                            status: $('#filter_status').val(),
                            time: $('#filter_time').val(),
                            date: $('#filter_date').val()},
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('#formed_orders').html(response);
                        }
                    });
                });
            });
        </script>
    @endisset
@endsection

