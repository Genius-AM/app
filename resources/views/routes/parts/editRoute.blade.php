<form id="order-form" method="POST" action="{{ route('admin.route.update') }}">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="form-item">
                        <legend>Категория</legend>
                        <select id="category" class="nice-select" disabled>
                            <option value="">{{ $route->subcategory->category->name }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-1 col-md-1">
                    <div class="form-item">
                        <legend>Длина</legend>
                        <input type="time" name="duration" class="custom-date-picker mini-input-date" value="{{ $route->duration }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Подкатегория</legend>
                        <select id="subcategory" class="nice-select" disabled>
                            <option value="">{{ $route->subcategory->name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Название маршрута</legend>
                        <input type="text" name="name" value="{{ $route->name }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Цена за мужчин (Соло)</legend>
                        <input type="text" name="price_men" value="{{ $route->price_men }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Цена за женщин (Тандем)</legend>
                        <input type="text" name="price_women" value="{{ $route->price_women }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Цена за детей (Трио)</legend>
                        <input type="text" name="price_kids" value="{{ $route->price_kids }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Цена</legend>
                        <input type="text" name="price" value="{{ $route->price }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Предоплата</legend>
                        <input type="text" name="prepayment" value="{{ $route->prepayment }}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                    <legend>Цвет маршрута</legend>
                    <select class="nice-select" name="color">
                        <option value="" selected>Нет цвета</option>
                        @foreach($colors as $key => $color)
                        <option value="{{$key}}" @if($key == $route->color) selected @endif>{{ $color }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4">

                <div class="form-item">
                    <legend>Зачислять на баланс</legend>
                    @if($route->is_payable)
                        <input type="checkbox" name="is_payable" checked>
                    @else
                        <input type="checkbox" name="is_payable">
                    @endif
                </div>
                <input type="hidden" name="route" value="{{ $route->id }}">
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-left">
                        <a href="{{ route('admin.routes.list') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Применить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>