<table>
    <thead>
        <tr>
            <th rowspan="2">№</th>
            <th rowspan="2">Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th colspan="{{ count($days) }}">{{ $ageCategory }}</th>
            @endforeach
            <th rowspan="2">Общее количество</th>
            <th rowspan="2">Менеджер</th>
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    <th>{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($managers ?? [] as $index => $manager)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $manager['name'] }}</td>
                @foreach($ageCategories ?? [] as $key => $ageCategory)
                    @foreach($days ?? [] as $day)
                        <td>{{ $result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day] ?: '' }}</td>
                    @endforeach
                @endforeach
                <td>{{ $result['m-' . $manager['id']] ?: '' }}</td>
                <td>{{ $manager['name'] }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="2">Итого</td>
            @foreach($ageCategories ?? [] as $key => $ageCategory)
                @foreach($days ?? [] as $day)
                    <td>{{ $result['a-' . $key . '_d-' . $day] ?: '' }}</td>
                @endforeach
            @endforeach
            <td>{{ $result['amount'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th rowspan="2">№</th>
            <th rowspan="2">Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    <th>{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
            <th rowspan="2">Общее количество</th>
            <th rowspan="2">Менеджер</th>
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th colspan="{{ count($days) }}">{{ $ageCategory }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
