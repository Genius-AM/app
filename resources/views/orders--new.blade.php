@extends('layouts.new__layout')

@section('title', 'Заказы')
@section('content')
    <div class="container-fluid">
    @if($mainCategory == \App\Category::DJIPPING)
        <order-proccesing :cat-id="{{ $mainCategory }}"></order-proccesing>
    @elseif($mainCategory == \App\Category::QUADBIKE)
        <order-proccesing-quadbike :cat-id="{{ $mainCategory }}"></order-proccesing-quadbike>
    @elseif($mainCategory == \App\Category::SEA)
        <order-proccesing-sea :cat-id="{{ $mainCategory }}"></order-proccesing-sea>
    @elseif($mainCategory == \App\Category::DIVING)
        <order-proccesing-diving :cat-id="{{ $mainCategory }}"></order-proccesing-diving>
    @endif
    </div>
 @endsection
