@if(count($history))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Машина</th>
                    <th>Водитель</th>
                    <th>Дата закрепления</th>
                    <th>Дата открепления</th>
                </tr>
                </thead>
                <tbody>
                @foreach($history as $record)
                    <tr>
                        <td data-label="ID">{{ $record->id }}</td>
                        <td data-label="Машина">#{{ $record->car->id }}. {{$record->car->name}} ({{$record->car->car_number}})</td>
                        <td data-label="Водитель">
                            @if($record->driver)
                                #{{ $record->driver->id }}. {{$record->driver->name}} ({{$record->driver->region}})
                            @else
                                Водитель не найден или был удалён
                            @endif
                        </td>
                        <td data-label="Дата закрепления">{{ $record->begin_attach }}</td>
                        <td data-label="Дата открепления">{{ $record->end_attach }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif