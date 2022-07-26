@extends('layouts.new__layout')

@section('title', 'Новый маршрут')

@section('content')
    <div class="container-fluid">

        <new-route
        :categories="{{ $categories }}"
        :colors="{{ $colors }}"
        ></new-route>

    </div>
@endsection
