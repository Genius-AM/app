<table>
    <thead>
        <tr>
            <th>Маршрут</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Кол-во людей</th>
            <th>Время</th>
            <th>Дата создания</th>
            <th>Статус</th>
            <th>Подкатегория</th>
            <th>Менеджер</th>
            <th>Водитель</th>
            <th>Цена</th>
            <th>Предоплата</th>
            <th>Мужчины</th>
            <th>Женщины</th>
            <th>Дети</th>
            <th>Адрес</th>
            <th>Комментарий</th>
            <th>Отклонил</th>
            <th>Причина отмены</th>
            @foreach($orders[0]['ageCategories'] as $key => $value)
                <th>{{ $key }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order['route'] }}</td>
                <td>{{ $order['client']['name'] }}</td>
                <td>{{ $order['client']['phones'] }}</td>
                <td>{{ $order['amount'] }}</td>
                <td>{{ $order['date'] }}</td>
                <td>{{ $order['created_at'] }}</td>
                <td>{{ $order['status'] }}</td>
                <td>{{ $order['subcategory'] }}</td>
                <td>{{ $order['manager'] }}</td>
                <td>{{ $order['driver']['name'] }} {{ $order['driver']['phone'] }}</td>
                <td>{{ $order['price'] }}</td>
                <td>{{ $order['prepayment'] }}</td>
                <td>{{ $order['men'] }}</td>
                <td>{{ $order['women'] }}</td>
                <td>{{ $order['kids'] }}</td>
                <td>{{ $order['address'] }}</td>
                <td>{{ $order['client']['comment'] }}</td>
                <td>{{ $order['refuser'] }}</td>
                <td>{{ $order['reason'] }}</td>
                @foreach($order['ageCategories'] as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>