@extends('layouts.new__layout')

@section('title', 'Список категорий')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('cars.defaultSeatsList')
        </div>
    </div>
@endsection
