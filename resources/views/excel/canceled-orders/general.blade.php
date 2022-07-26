<table>
    <thead>
    <tr>
        <th rowspan="2">№ </th>
        <th rowspan="2">Менеджер</th>
        <th rowspan="2">Водитель</th>
        <th rowspan="2">Отказ от заявки</th>
        <th rowspan="2">Адрес</th>
        <th rowspan="2">Дата</th>
        <th rowspan="2">Время</th>
        <th rowspan="2">Контакты</th>
        <th rowspan="2">Предоплата (р.)</th>
        <th colspan="4">Пассажиры</th>
        <th rowspan="2">Маршрут</th>
        <th rowspan="2">Статус</th>
    </tr>
    <tr>
        <th>М</th>
        <th>Ж</th>
        <th>Д</th>
        <th>Общ.</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        <tr>
            <td> {{ $loop->index + 1 }} </td>
            <td> {{ $order->manager->name }} </td>
            <td> {{ $order->driver !== null ? $order->driver->name : 'Нет закрепленного водителя' }} </td>
            <td> {{ $order->refuser ? $order->refuser->name : '' }} </td>
            <td> {{ $order->address }} </td>
            <td> {{ \Carbon\Carbon::parse($order->date)->format('d.m.Y')}} </td>
            <td> {{ $order->time }} </td>
            <td> Осн.: {{ $order->client->phone }}{{ $order->client->phone_2 ? ', доп.: ' . $order->client->phone_2 : ''}} </td>
            <td> {{ $order->prepayment }} </td>
            <td> {{ $order->men }} </td>
            <td> {{ $order->women }} </td>
            <td> {{ $order->kids }} </td>
            <td> {{ $order->men + $order->women + $order->kids }} </td>
            <td> {{ $order->route->name }} </td>
            <td> {{ $order->status_id === 5 ? 'Отказ' : 'Отказ после принятия' }} </td>
        </tr>
    @endforeach
    </tbody>
</table>