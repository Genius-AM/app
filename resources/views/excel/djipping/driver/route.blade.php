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
    @foreach($drivers as $key => $driver)
    <tr>
        <td> {{ $key }} </td>
        <td> {{ $driver->name }} </td>
            @foreach($driver->routes as $key => $route)
                <td>
                {{ $route->accept }} / {{ $route->rejectorder }} / {{ $route->rejectafteracceptorder }}
                </td>
            @endforeach
        <td>
            {{ $driver->accept }} / {{ $driver->reject }} / {{ $driver->rejectafteraccept }}
        </td>
    </tr>
    @endforeach
    <tr>
        <td></td>
        <td>Итого</td>
        @foreach($total as $key => $item)
            <td>
                {{ $item->accept }} / {{ $item->reject }}  / {{ $item->rejectafteraccept }}
            </td>
        @endforeach
        <td>
            {{ $totalaccept }} / {{ $totalreject }} / {{ $totalrejectafteraccept }}
        </td>
    </tr>
    </tbody>
</table>