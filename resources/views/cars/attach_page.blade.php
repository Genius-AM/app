<form id="order-form" method="POST" action="{{route('admin.cars.attach.attach', $id) }}">
    @csrf
    <input name="_method" type="hidden" value="PATCH">

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Машина</legend>
                        <h3>
                            #{{$car->id}}. {{$car->name}} - {{$car->car_number}}
                        </h3>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-item">
                        <legend>Водитель</legend>
                        <select name="driver_id" class="nice-select">
                            <option value="">Пусто</option>
                            @foreach($drivers as $driver)
                                <option value="{{$driver->id}}" {{ $car_attachment==$driver->id?'selected':'' }}>{{$driver->name}} ({{$driver->region}}) @if($driver->company_id) | Компания: ({{ $driver->company->name }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-left">
                        <a href="{{ route('admin.cars.cars.index') }}"><button type="button" class="btn btn-danger">Назад</button></a>
                    </div>
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Применить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>