@extends('layouts.new__layout')

@section('title', 'Отчет фактическая запись')

@section('content')
    <div class="container-fluid">
        <actual-recording
                :categories="{{ $categories }}"
                :managers="{{ $managers }}"
                :companies="{{ $companies }}"
        />
    </div>
@endsection
