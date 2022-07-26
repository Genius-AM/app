@extends('layouts.new__layout')

@section('title', 'Редактирование подкатегории')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.subcategories.update', $subcategory->id) }}">
            @csrf
            <input name="_method" type="hidden" value="PATCH">

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-item">
                                <legend>Категория</legend>
                                <select type="text" name="category_id" class="nice-select" disabled>
                                    <option value="{{ $subcategory->category_id }}" selected> {{ $subcategory->category->name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-item">
                                <legend>Название</legend>
                                <input type="text" name="name" required autofocus value="{{ $subcategory->name }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Мужчины (Взрослые)</legend>
                                <input type="number" name="men" required value="{{$subcategory->men}}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Женщины</legend>
                                <input type="number" name="women" required value="{{$subcategory->women}}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-item">
                                <legend>Дети</legend>
                                <input type="number" name="kids" required value="{{$subcategory->kids}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="float-left">
                                <a href="{{ route('lists.subcategories.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                            </div>
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
