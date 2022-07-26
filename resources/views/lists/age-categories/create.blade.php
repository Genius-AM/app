@extends('layouts.new__layout')


@section('title', 'Новая категория возраста')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.age-categories.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-item">
                        <legend>От</legend>
                        <input type="text" name="from" autofocus>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-item">
                        <legend>До</legend>
                        <input type="number" name="to">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Создать</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
