@extends('layouts.new__layout')

@section('title', 'Список рассылок')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if(Session::has('success'))
                    <div class="alert alert_new alert-success">
                        {{Session::get('success')}}
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert_new alert-danger">
                        {{Session::get('danger')}}
                    </div>
                @endif
            </div>

            <div class="col-lg-12">
                <a href="{{ route('lists.promotions.create') }}"><button class="btn btn-dark float-right">Добавить</button></a>
            </div>
            <div class="col-lg-12">
                <div class="table-wrap">
                    <table class="edit-car-info">
                        <thead>
                        <tr>
                            <th>№</th>
                            <th>Категория</th>
                            <th>Подкатегория</th>
                            <th>Текст</th>
                            <th>Количество</th>
                            <th>Дата отправки</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($promotions))
                            @foreach($promotions as $key => $promotion)
                                @include('lists.promotions.parts.row', ['item' => $promotion, 'key' => $key])
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">Нет данных</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
