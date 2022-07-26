<table>
    <thead>
    <tr>
        <th>№ </th>
        <th>Менеджер</th>
        @foreach($routes as $key => $route)
            <th>
                {{ $route->name }}
            </th>
        @endforeach
        <th>Общее количество</th>
    </tr>
    </thead>
    <tbody>
    @foreach($managers as $key => $manager)
    <tr>
        <td> {{ $key + 1 }} </td>
        <td> {{ $manager->name }} </td>
            @foreach($manager->routes as $key => $route)
                <td>
                {{ $route->accept }} / {{ $route->rejectorder }}
                </td>
            @endforeach
        <td>
            {{ $manager->accept }} / {{ $manager->reject }}
        </td>
    </tr>
    @endforeach
    <tr>
        <td></td>
        <td>Итого</td>
        @foreach($total as $key => $item)
            <td>
                {{ $item->accept }} / {{ $item->reject }}
            </td>
        @endforeach
        <td>
            {{ $totalaccept }} / {{ $totalreject }}
        </td>
    </tr>
    </tbody>
</table>
