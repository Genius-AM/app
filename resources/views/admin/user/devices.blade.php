@extends('layouts.new__layout')

@section('title', 'Список привязанных устройств')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.user.devices_table')
        </div>
    </div>
@endsection