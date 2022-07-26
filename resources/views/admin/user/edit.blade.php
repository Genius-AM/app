@extends('layouts.new__layout')

@section('title', 'Редактирование пользователя')

@section('content')
    <div class="container-fluid">
        @include('admin.user.edit_form')
    </div>
@endsection