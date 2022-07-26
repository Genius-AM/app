@extends('layouts.new__layout')

@section('title', 'Новый адрес')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.addresses.update', $address->id) }}">
            @csrf
            <input name="_method" type="hidden" value="PATCH">

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-item">
                                <legend>Название</legend>
                                <input type="text" name="name" required autofocus value="{{ $address->name }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="float-right">
                                <button type="submit" class="btn btn-green">Изменить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
