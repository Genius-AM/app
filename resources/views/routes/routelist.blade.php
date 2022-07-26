@extends('layouts.new__layout')

@section('title', 'Список маршрутов')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">
                <div class="float-right">
                    <a href="{{ route('admin.route.new') }}">
                        <button type="button" class="btn btn-dark"> Создать маршрут</button>
                    </a>
                    <a href="{{ route('lists.subcategories.index') }}">
                        <button type="button" class="btn btn-success">Подкатегории</button>
                    </a>
                </div>
            </div>

            @if(count($routes))
                <div class="col-lg-12">
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Категория</th>
                                <th>Маршрут</th>
                                <th>Цена за мужчин (Соло)</th>
                                <th>Цена за женщин (Тандем)</th>
                                <th>Цена за детей (Трио)</th>
                                <th>Цена</th>
                                <th>Предоплата</th>
                                <th>Продолжительность</th>
                                <th>Зачисляется на баланс</th>
                                <th>Редактирование</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($routes as $route)
                                <tr>
                                    <td data-label="ID">{{ $route->id }}</td>
                                    <td data-label="Категория">{{ $route->subcategory->category->name }}</td>
                                    @if($route->subcategory->category->id != 4)
                                        <td data-label="Маршрут">{{ $route->name }}</td>
                                    @else
                                        <td data-label="Маршрут">{{ $route->subcategory->name }} - {{ $route->name }}</td>
                                    @endif
                                    <td data-label="Цена за мужчин (Соло)">{{ $route->price_men }}</td>
                                    <td data-label="Цена за женщин (Тандем)">{{ $route->price_women }}</td>
                                    <td data-label="Цена за детей (Трио)">{{ $route->price_kids }}</td>
                                    <td data-label="Цена">{{ $route->price }}</td>
                                    <td data-label="Предоплата">{{ $route->prepayment }} руб.</td>
                                    <td data-label="Продолжительность">{{ $route->duration }}</td>
                                    @if ($route->is_payable)
                                        <td data-label="Зачисляется на баланс"><i class="fa fa-check" aria-hidden="true"></i></td>
                                    @else
                                        <td data-label="Зачисляется на баланс"><i class="fa fa-times" aria-hidden="true"></i></td>
                                    @endif
                                    <td class="edit" data-label="Редактирование">
                                        <a href="{{ route('admin.route.cars.index', $route) }}"><i class="fa fa-car"></i></a>&nbsp
                                        <a href="{{ route('admin.route.show', ['id' => $route->id]) }}"><i class="far fa-edit"></i></a>&nbsp
                                        <i id="{{ $route->id }}" class="fas fa-times-circle" onclick="return confirm('Вы уверены?')"></i>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.fa-times-circle').click(function (e) {
                let id = e.target.id;
                $.ajax({
                    url: '{{ route('admin.route.delete') }}',
                    type: "POST",
                    data: {route : id},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response) {
                            location.reload();
                        }
                    }
                });
            });
        });
    </script>
@endsection
