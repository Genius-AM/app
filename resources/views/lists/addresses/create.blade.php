@extends('layouts.new__layout')

@section('title', 'Новый адрес')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.addresses.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-item">
                                <legend>Название</legend>
                                <input type="text" name="name" required autofocus>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="float-right">
                                <button type="submit" class="btn btn-green">Создать</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
