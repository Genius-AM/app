@extends('layouts.new__layout')

@section('title', 'Время проведения')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        @include('admin.times')
    </div>
@endsection
