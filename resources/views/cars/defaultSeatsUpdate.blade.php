@include('layouts.messages')


<form id="order-form" method="POST" action="{{ route('admin.cars.defaultSeats.update', $id)}}">
    @csrf
    <input name="_method" type="hidden" value="PATCH">

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Название типа категории</legend>
                        <input type="text" name="name" disabled value="{{$category->name}}">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Мест по умолчанию</legend>
                        <input type="text" name="default_seats_on_vehicle" autofocus required value="{{$category->default_seats_on_vehicle}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Мужчины (Взрослые)</legend>
                        <input type="number" name="men" required value="{{$category->men}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Женщины</legend>
                        <input type="number" name="women" required value="{{$category->women}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Дети</legend>
                        <input type="number" name="kids" required value="{{$category->kids}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-left">
                        <a href="{{ route('admin.cars.defaultSeats.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Изменить</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
