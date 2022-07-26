@extends('layouts.new__layout')

@section('title', 'Список категорий возрастов')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <div class="row">
            <div class="col-lg-12">
                <a href="{{ route('lists.age-categories.create') }}">
                    <button class="btn btn-dark float-right">Добавить</button>
                </a>
            </div>
            <div class="col-lg-12">
                <div class="table-wrap">
                    <table class="edit-car-info">
                        <thead>
                        <tr>
                            <th class="col-1">№</th>
                            <th class="col-9">Название</th>
                            @if (request()->user()->isAdmin())
                                <th class="col-2">Редактирование</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($ageCategories))
                            @foreach($ageCategories as $key => $ageCategory)
                                @include('lists.age-categories.parts.row', ['item' => $ageCategory, 'key' => $key])
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
