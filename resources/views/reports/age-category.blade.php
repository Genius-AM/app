@extends('layouts.new__layout')

@section('title', 'Отчет категории возрастов')

@section('content')
    <div class="container-fluid">
        <age-category
                :categories="{{ $categories }}"
                :managers="{{ $managers }}"
                :companies="{{ $companies }}"
                :age-categories="{{ $ageCategories }}"
        />
    </div>
@endsection
