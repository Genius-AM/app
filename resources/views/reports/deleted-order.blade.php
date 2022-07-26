@extends('layouts.new__layout')

@section('title', 'Отчет удаленные заявки')

@section('content')
    <div class="container-fluid">
        <deleted-order
                :categories="{{ $categories }}"
                :companies="{{ $companies }}"
        />
    </div>
@endsection
