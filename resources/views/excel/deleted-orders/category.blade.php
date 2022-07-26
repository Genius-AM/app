<table>
    <thead>
        <tr>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Кол-во людей</th>
            <th>Время</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order['client']['name'] }}</td>
                <td>{{ $order['client']['phones'] }}</td>
                <td>{{ $order['amount'] }}</td>
                <td>{{ $order['date'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>