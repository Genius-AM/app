@if(count($categories))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table class="edit-car-info">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Мест по умолчанию</th>
                    <th>Редактирование</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td data-label="ID">{{ $category->id }}</td>
                        <td data-label="Название">{{ $category->name }}</td>
                        <td data-label="Мест по умолчанию">{{ $category->default_seats_on_vehicle }}</td>
                        <td class="edit" data-label="Редактирование">
                            <a href="{{ route('admin.cars.defaultSeats.edit', $category->id) }}">
                                <i class="far fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif