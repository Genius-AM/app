<table>
    <thead>
        <tr>
            <th colspan="{{ count($result['hours']) + 2 }}">{{ $result['period'] }}</th>
        </tr>
        <tr>
            <th>Менеджеры</th>
            <th>Всего</th>
            @foreach($result['periods'] ?? [] as $hour)
                <th>{{ (int)$hour }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($result['managers'] as $manager)
            <tr>
                <td>{{ $manager['name'] }}</td>
                <td>{{ $manager['all'] }}</td>
                @foreach($result['periods'] as $hour)
                    <th>{{ $manager['hours'][$hour] ? $manager['hours'][$hour] : null }}</th>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td>Итого</td>
            <td>{{ $result['all'] }}</td>
            @foreach($result['periods'] as $hour)
                <th>{{ $result['hours'][$hour] }}</th>
            @endforeach
        </tr>
    </tbody>
</table>
