<table>
    <thead>
        <tr>
            <th rowspan="2">№</th>
            <th rowspan="2">Менеджер</th>
            <th rowspan="2">Маршрут</th>
            @foreach($categories ?? [] as $category)
                <th colspan="{{ count($days) * 2 }}">{{ $category['name'] }}</th>
            @endforeach
            <th rowspan="2" colspan="2">Общее количество</th>
            <th rowspan="2">Менеджер</th>
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    <th colspan="2">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
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
                            <td>{{ $category['id'] === $route['category_id'] ? ($result['m-' . $manager['id'] . '_r-' . $route['id'] . '_c-' . $category['id'] . '_d-' . $day]['accept'] ?: '') : '' }}</td>
                            <td>{{ $category['id'] === $route['category_id'] ? ($result['m-' . $manager['id'] . '_r-' . $route['id'] . '_c-' . $category['id'] . '_d-' . $day]['reject'] ?: '') : '' }}</td>
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
                        <td>{{ $result['m-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day]['accept'] ?: '' }}</td>
                        <td>{{ $result['m-' . $manager['id'] . '_c-' . $category['id'] . '_d-' . $day]['reject'] ?: '' }}</td>
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
                    <td>{{ $result['c-' . $category['id'] . '_d-' . $day]['accept'] ?: '' }}</td>
                    <td>{{ $result['c-' . $category['id'] . '_d-' . $day]['reject'] ?: '' }}</td>
                @endforeach
            @endforeach
            <td>{{ $result['accept'] ?: '' }}</td>
            <td>{{ $result['reject'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th rowspan="2">№</th>
            <th rowspan="2">Менеджер</th>
            <th rowspan="2">Маршрут</th>
            @foreach($categories ?? [] as $category)
                @foreach($days ?? [] as $day)
                    <th colspan="2">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
            <th rowspan="2" colspan="2">Общее количество</th>
            <th rowspan="2">Менеджер</th>
        </tr>
        <tr>
            @foreach($categories ?? [] as $category)
                <th colspan="{{ count($days) * 2 }}">{{ $category['name'] }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
