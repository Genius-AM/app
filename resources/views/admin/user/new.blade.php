@extends('layouts.new__layout')

@section('title', 'Новый пользователь')

@section('content')
    <div class="container-fluid">
        <new-user
            :roles="{{ $roles }}"
            :categories="{{ $categories }}"
            :companies="{{ $companies }}"
        ></new-user>
    </div>
@endsection