<table>
    <thead>
        <tr>
            <th>Категория</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Кол-во людей</th>
            <th>Время</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order['category'] }}</td>
                <td>{{ $order['client']['name'] }}</td>
                <td>{{ $order['client']['phones'] }}</td>
                <td>{{ $order['amount'] }}</td>
                <td>{{ $order['date'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>