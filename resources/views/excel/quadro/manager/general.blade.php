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
        <td> {{ $key }} </td>
        <td> {{ $manager->name }} </td>
            @foreach($manager->routes as $key => $route)
                <td>
                    {{ $route->rejectorder }} / {{ $route->accept }}
                </td>
            @endforeach
        <td>
            {{ $manager->reject }} / {{ $manager->accept }}
        </td>
    </tr>
    @endforeach
    <tr>
        <td></td>
        <td>Итого</td>
        @foreach($total as $key => $item)
            <td>
                {{ $item->reject }} / {{ $item->accept }}
            </td>
        @endforeach
        <td>
            {{ $totalreject }} / {{ $totalaccept }}
        </td>
    </tr>
    </tbody>
</table>