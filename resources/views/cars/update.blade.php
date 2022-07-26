<form id="order-form" method="POST" action="{{ route('admin.cars.cars.update', $id) }}">
    @csrf
    <input name="_method" type="hidden" value="PATCH">

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Название</legend>
                        <input type="text" name="name" required autofocus value="{{$car->name}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Гос. номер</legend>
                        <input type="text" name="car_number" required value="{{$car->car_number}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Имя партнера (если наше, оставить пустым)</legend>
                        <input type="text" name="owner_name" value="{{$car->owner_name}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Владелец машины</legend>

                        <label>
                            <input type="radio" name="owner" value="our" required {{$car->owner === 'our' ? 'checked' : ''}}>
                            Наши
                        </label>
                        <label>
                            <input type="radio" name="owner" value="partner" required {{$car->owner === 'partner' ? 'checked' : ''}}>
                            Партнер
                        </label>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Категория ТС</legend>
                        <select name="category_id" class="nice-select" required>
                            <option value="">Пусто</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}" {{ $car->category_id==$category->id?'selected':'' }} data-default-seats-value="{{$category->default_seats_on_vehicle}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Сортировка</legend>
                        <input type="text" name="order" required value="{{$car->order}}" placeholder="Номер сортировки">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Максимальное число мест по умолчанию</legend>
                        <input type="text" required name="default_seats_on_vehicle">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Мужчины (Взрослые)</legend>
                        <input type="number" name="men" required value="{{$car->men}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Женщины</legend>
                        <input type="number" name="women" required value="{{$car->women}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-item">
                        <legend>Дети</legend>
                        <input type="number" name="kids" required value="{{$car->kids}}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-left">
                        <a href="{{ route('admin.cars.cars.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Изменить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@section('js')
<script>
    function insertCurrentDefaultSeats(){
        $('input[name=default_seats_on_vehicle]')
            .val(
                $('select[name=category_id] option:selected').attr('data-default-seats-value')
            );
    }

    $(document).ready(function() {
        insertCurrentDefaultSeats();
        $('select[name=category_id]').on('change', function(){
            insertCurrentDefaultSeats()
        });
    });
</script>
@endsection