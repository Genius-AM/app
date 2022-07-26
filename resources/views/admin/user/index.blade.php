@extends('layouts.new__layout')

@section('title', 'Список пользователей')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="float-right">
                    <a href="{{ route('admin.user.new') }}">
                        <button type="button" class="btn btn-dark">Создать пользователя</button>
                    </a>
                    <a href="{{ route('admin.users.devices') }}">
                        <button type="button" class="btn btn-dark">Список привязанных устройств</button>
                    </a>
                </div>
            </div>

            @include('admin.user.index_table')
        </div>
    </div>
@endsection