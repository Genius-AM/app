@extends('layouts.new__layout')

@section('title', 'Расписание для водителя ' . $car->driver->name . ' на маршрут "' . $route->name . '"')

@section('content')
    <div class="container-fluid">
        <car-timetable
                :route-car="{{ $routeCar }}"
        >
        </car-timetable>
    </div>
@endsection
