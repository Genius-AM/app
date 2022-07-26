<table>
    <thead>
    <tr>
        <th>№ </th>
        <th>Адрес</th>
        <th>Менеджер</th>
        <th>Человек на адрес</th>
    </tr>
    </thead>
    <tbody>
        @foreach($addresses as $addressKey => $address)
            @foreach($address->managers as $managerKey => $manager)
                <tr>
                    <td> {{ $addressKey }} </td>
                    <td> {{ $address->name }} </td>
                    <td> {{ $manager->name }} </td>
                    <td> {{ $manager->total }} </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right"> Общее количество: </td>
                <td> {{ $address->total }} </td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
        @endforeach
    </tbody>
</table>