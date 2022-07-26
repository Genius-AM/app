@extends('layouts.new__layout')

@section('title', 'Список подкатегорий')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <div class="row">
            <div class="col-lg-12">
                <a href="{{ route('lists.subcategories.create') }}">
                    <button class="btn btn-dark float-right"><span class="fa fa-address-book"></span> Добавить</button>
                </a>
            </div>
            <div class="col-lg-12">
                <div class="table-wrap">
                    <table class="edit-car-info">
                        <thead>
                        <tr>
                            <th>№</th>
                            <th>Категория</th>
                            <th>Название</th>
                            @if (request()->user()->isAdmin())
                                <th>Редактирование</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($subcategories))
                            @foreach($subcategories as $key => $subcategory)
                                @include('lists.subcategories.parts.row', ['item' => $subcategory, 'key' => $key])
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">Нет данных</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
