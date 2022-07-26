@extends('layouts.new__layout')

@section('title', 'Машины по маршруту "' . $route->name . '"')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="float-right">
                    <a href="{{ route('admin.route.cars.add', $route) }}">
                        <button type="button" class="btn btn-dark"> Добавить машину</button>
                    </a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Машина</th>
                            <th>Водитель</th>
                            <th>Цена за мужчин (Соло)</th>
                            <th>Цена за женщин (Тандем)</th>
                            <th>Цена за детей (Трио)</th>
                            <th>Цена</th>
                            <th>Предоплата</th>
                            <th>Продолжительность</th>
                            <th>Зачисляется на баланс</th>
                            <th>Редактирование</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($route_cars as $route_car)
                            <tr>
                                <td data-label="ID">{{ $route_car->id }}</td>
                                <td data-label="Машина">{{ $route_car->car->name }}</td>
                                <td data-label="Водитель">{{ $route_car->car->driver->name }}</td>
                                <td data-label="Цена за мужчин (Соло)">{{ $route_car->price_men }}</td>
                                <td data-label="Цена за женщин (Тандем)">{{ $route_car->price_women }}</td>
                                <td data-label="Цена за детей (Трио)">{{ $route_car->price_kids }}</td>
                                <td data-label="Цена">{{ $route_car->price }}</td>
                                <td data-label="Предоплата">{{ $route_car->prepayment }} руб.</td>
                                <td data-label="Продолжительность">{{ $route_car->duration }}</td>
                                @if ($route_car->is_payable)
                                    <td data-label="Зачисляется на баланс"><i class="fa fa-check" aria-hidden="true"></i></td>
                                @else
                                    <td data-label="Зачисляется на баланс"><i class="fa fa-times" aria-hidden="true"></i></td>
                                @endif
                                <td class="edit" data-label="Редактирование">
                                    <a href="{{ route('admin.cars.timetables.index', $route_car) }}"><i class="fa fa-list-alt"></i></a>&nbsp
                                    <a href="{{ route('admin.route.cars.add', [$route, $route_car]) }}"><i class="far fa-edit"></i></a>&nbsp
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
