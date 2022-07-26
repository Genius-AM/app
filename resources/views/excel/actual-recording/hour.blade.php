<table>
    <thead>
        <tr>
            <th rowspan="3">№</th>
            <th rowspan="3">Менеджер</th>
            <th rowspan="3">Маршрут</th>
            @foreach($categories ?? [] as $category)
                <th colspan="{{ count($days) * count($hours) * 2 }}">{{ $category['name'] }}</th>
            @endforeach
            <th rowspan="3" colspan="2">Общее количество</th>
            <th rowspan="3">Менеджер</th>
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    <th colspan="{{ count($hours) * 2 }}">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    @foreach($hours ?? [] as $hour)
                        <th colspan="2">{{ $hour }}</th>
                    @endforeach
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($managers ?? [] as $key => $manager)
            @foreach($manager['routes'] ?? [] as $index => $route)
                <tr>
                    @if ($index === 0)
                        <td rowspan="{{ count($manager['routes']) + 1 }}">{{ $key + 1 }}</td>
                        <td rowspan="{{ count($manager['routes']) + 1 }}">{{ $manager['name'] }}</td>
                    @endif
                    <td>{{ $route['name'] }}</td>
                    @foreach($categories ?? [] as $category)
                        @foreach($days ?? [] as $day)
                            @foreach($hours ?? [] as $hour)
                                <td>{{ $category['id'] === $route['category_id'] ? ($result['m-' . $manager['id'] . '_r-' . $route['id'] . '_c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['accept'] ?: '') : '' }}</td>
                                <td>{{ $category['id'] === $route['category_id'] ? ($result['m-' . $manager['id'] . '_r-' . $route['id'] . '_c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['reject'] ?: '') : '' }}</td>
                            @endforeach
                        @endforeach
                    @endforeach
                    <td>{{ $route['accept'] ?: '' }}</td>
                    <td>{{ $route['reject'] ?: '' }}</td>
                    @if ($index === 0)
                        <td rowspan="{{ count($manager['routes']) + 1 }}">{{ $manager['name'] }}</td>
                    @endif
                </tr>
            @endforeach

            <tr>
                @if (count($manager['routes'] ?? []) === 0)
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $manager['name'] }}</td>
                @endif
                <td>Итого</td>
                @foreach($categories ?? [] as $category)
                    @foreach($days ?? [] as $day)
                        @foreach($hours ?? [] as $hour)
                            <td>{{ $result['m-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['accept'] ?: '' }}</td>
                            <td>{{ $result['m-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['reject'] ?: '' }}</td>
                        @endforeach
                    @endforeach
                @endforeach
                <td>{{ $result['m-' . $manager['id']]['accept'] ?: '' }}</td>
                <td>{{ $result['m-' . $manager['id']]['reject'] ?: '' }}</td>
                @if (count($manager['routes'] ?? []) === 0)
                    <td>{{ $manager['name'] }}</td>
                @endif
            </tr>
        @endforeach

        <tr>
            <td colspan="3">Итого</td>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    @foreach($hours ?? [] as $hour)
                        <td>{{ $result['c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['accept'] ?: '' }}</td>
                        <td>{{ $result['c-' . $category['id'] . '_d-' . $day . '_h-' . $hour]['reject'] ?: '' }}</td>
                    @endforeach
                @endforeach
            @endforeach
            <td>{{ $result['accept'] ?: '' }}</td>
            <td>{{ $result['reject'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th rowspan="3">№</th>
            <th rowspan="3">Менеджер</th>
            <th rowspan="3">Маршрут</th>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    @foreach($hours ?? [] as $hour)
                        <th colspan="2">{{ $hour }}</th>
                    @endforeach
                @endforeach
            @endforeach
            <th rowspan="3" colspan="2">Общее количество</th>
            <th rowspan="3">Менеджер</th>
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    <th colspan="{{ count($hours) * 2 }}">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                <th colspan="{{ count($days) * count($hours) * 2 }}">{{ $category['name'] }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
