@if(count($cars))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table class="edit-car-info">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Гос. номер</th>
                    <th>Кол-во мест</th>
                    <th>Владелец ТС</th>
                    <th>Имя партнера (если не наше)</th>
                    <th>Водитель (если закреплен)</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cars as $car)
                    <tr>
                        <td data-label="ID">{{ $car->id }}</td>
                        <td data-label="Название">{{ $car->name }}</td>
                        <td data-label="Гос.номер">{{ $car->car_number }}</td>
                        <td data-label="Телефон">{{ $car->passengers_amount }}</td>
                        <td data-label="Логин">{{ $car->owner === 'our' ? 'Наша' : 'Партнер' }}</td>
                        <td data-label="Регион">{{ $car->owner_name }}</td>
                        <td data-label="Водитель">{{ !empty($car->driver->name) ? '#'.$car->driver->id.'. '.$car->driver->name." (".$car->driver->region.")" : '' }} {{isset($car->driver->company) ? "| Компания: ".$car->driver->company->name : ""}} </td>
                        <td class="edit" data-label="Редактирование">
                            <a href="{{ route('admin.cars.cars.edit', $car->id) }}">
                                <i class="far fa-edit"></i></a>

                            <a href="{{ route('admin.cars.attach.index', $car->id) }}">
                                <i class="fa fa-male"></i></a>

                            <a href="{{  route('admin.cars.attach.list', $car->id) }}">
                                <i class="fa fa-history"></i></a>

                            <form action="{{route('admin.cars.cars.destroy', $car['id'])}}" method="post">
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit" onclick="return confirm('Вы уверены?')"><i class="fas fa-times-circle"></i></button>
                            </form>
                            <input type="checkbox" name ="checkbox" id = "{{$car['id']}}" @if($car['active']) checked @endif>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
<script>
    document.addEventListener('change', function () {
        var chk = event.target
        if (chk.tagName === 'INPUT' && chk.type === 'checkbox') {
            axios.post(`/admin/cars/cars/${chk.id}/status`, {status: chk.checked})
                .then(res => {
                })
                .catch(err => {

                })
        }
    })
</script>
