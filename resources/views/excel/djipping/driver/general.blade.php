<table>
    <thead>
    <tr>
        <th>Водитель</th>
        <th>Количество отказанных человек</th>
        <th>Количество отказанных человек после принятия</th>
        <th>Количество принятых человек</th>
    </tr>
    </thead>
    <tbody>
    @foreach($drivers as $driver)
        <tr>
            <td>{{ $driver->name }}</td>
            <td>{{ $driver->reject_count }}</td>
            <td>{{ $driver->reject_after_accept_count }}</td>
            <td>{{ $driver->accept_count }}</td>
        </tr>
    @endforeach
    </tbody>
</table>