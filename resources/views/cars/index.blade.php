@extends('layouts.new__layout')

@section('title', 'Список транспортных средств')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <div class="row">
            <div class="col-lg-12">
                <div class="float-right">
                    <a href="{{ route('admin.cars.cars.create') }}">
                        <button type="button" class="btn btn-dark">Создать ТС</button>
                    </a>
                    <a href="{{ route('admin.cars.defaultSeats.index') }}">
                        <button type="button" class="btn btn-dark">Категории ТС</button>
                    </a>
                </div>
            </div>

            @include('cars.list')
        </div>
    </div>
@endsection
