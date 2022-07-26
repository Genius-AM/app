@extends('layouts.new__layout')

@section('title', '' . ($routeCar->id ? 'Изменение ' : 'Добавление новой ') . 'машины по маршруту "' . $route->name . '"')

@section('content')
    <div class="container-fluid">
        <new-route-car
                :cars="{{ $cars }}"
                :route="{{ $route }}"
                :route-car="{{ $routeCar }}"
        ></new-route-car>
    </div>
@endsection
