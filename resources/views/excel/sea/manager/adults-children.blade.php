<table>
    <thead>
        <tr>
            <th>Менеджер</th>
            <th>Взрослые</th>
            <th>Дети</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order['manager'] }}</td>
                <td>{{ $order['adults'] }}</td>
                <td>{{ $order['children'] }}</td>
            </tr>
        @endforeach
        <tr class="border-top">
            <td>Итого</td>
            <td>{{ $adults }}</td>
            <td>{{ $children }}</td>
        </tr>
    </tbody>
</table>