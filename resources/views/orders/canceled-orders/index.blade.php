@extends('layouts.new__layout')

@section('title', 'Отказанные заявки')

@section('content')
    <div class="container-fluid">
        <canceled-orders :cat-id="1">

        </canceled-orders>
    </div>
@endsection
