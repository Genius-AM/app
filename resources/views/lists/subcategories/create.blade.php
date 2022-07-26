@extends('layouts.new__layout')

@section('title', 'Новая подкатегория')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.subcategories.store') }}">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-item">
                                <legend>Категория</legend>
                                <select id="category_id" class="nice-select" name="category_id" required>
                                    <option value="">Не выбрана</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-item">
                                <legend>Название</legend>
                                <input type="text" name="name" required autofocus>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Мужчины (Взрослые)</legend>
                                <input type="number" name="men" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Женщины</legend>
                                <input type="number" name="women" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Дети</legend>
                                <input type="number" name="kids" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="float-left">
                                <a href="{{ route('lists.subcategories.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                            </div>
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
