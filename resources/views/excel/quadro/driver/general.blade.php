<table>
    <thead>
    <tr>
        <th>Водитель</th>
        <th>Количество принятых человек</th>
    </tr>
    </thead>
    <tbody>
    @foreach($drivers as $driver)
        <tr>
            <td>{{ $driver->name }}</td>
            <td>{{ $driver->accept_count }}</td>
        </tr>
    @endforeach
    </tbody>
</table>