@extends('layouts.new__layout')


@section('title', 'История закрепленний')

@section('content')
    <div class="container-fluid">
        @include('cars.attach_list')
    </div>
@endsection
