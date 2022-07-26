<table>
    <thead>
    <tr>
        <th>№ </th>
        <th>Менеджер</th>
        @foreach($managers[0]->routes ?? [] as $key => $route)
            <th>
                {{ $route->name }}
            </th>
            @foreach(current($managers[0]->routes)->days as $day => $value)
                <th>
                    {{ $day }}
                </th>
            @endforeach
        @endforeach
        <th>Общее количество</th>
    </tr>
    </thead>
    <tbody>
    @foreach($managers as $key => $manager)
    <tr>
        <td> {{ $key + 1 }} </td>
        <td> {{ $manager->name }} </td>
            @foreach($manager->routes ?? [] as $key => $route)
                <td>
                {{ $route->accept }} / {{ $route->rejectorder }}
                </td>
                @foreach($route->days as $day => $value)
                    <td>
                        {{ $value->accept }} / {{ $value->reject }}
                    </td>
                @endforeach
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
            @foreach($item->days as $day => $value)
                <td>
                    {{ $value->accept }} / {{ $value->reject }}
                </td>
            @endforeach
        @endforeach
        <td>
            {{ $totalaccept }} / {{ $totalreject }}
        </td>
    </tr>
    </tbody>
</table>
