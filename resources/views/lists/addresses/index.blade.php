@extends('layouts.new__layout')

@section('title', 'Список точек')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if(Session::has('success'))
                    <div class="alert alert_new alert-success">
                        <p>{{Session::get('success')}}</p>
                    </div>
                @endif
                @if(Session::has('danger'))
                    <div class="alert alert_new alert-danger">
                        <p>{{Session::get('danger')}}</p>
                    </div>
                @endif
            </div>

            <div class="col-lg-12">
                <a href="{{ route('lists.addresses.create') }}"><button class="btn btn-dark float-right"><span class="fa fa-address-book"></span> Добавить</button></a>
            </div>
            <div class="col-lg-12">
                    <div class="table-wrap">
                        <table class="edit-car-info">
                            <thead>
                            <tr>
                                <th>№</th>
                                <th>Название</th>
                                @if (request()->user()->isAdmin())
                                    <th>Редактирование</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($addresses))
                                @foreach($addresses as $key => $address)
                                    @include('lists.addresses.parts.row', ['item' => $address, 'key' => $key])
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
