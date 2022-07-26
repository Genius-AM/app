<table>
    <thead>
        <tr>
            <th rowspan="3">№</th>
            <th rowspan="3">Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th colspan="{{ count($days) * count($hours) }}">{{ $ageCategory }}</th>
            @endforeach
            <th rowspan="3">Общее количество</th>
            <th rowspan="3">Менеджер</th>
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    <th colspan="{{ count($hours) }}">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    @foreach($hours ?? [] as $hour)
                        <th>{{ $hour }}</th>
                    @endforeach
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
                        @foreach($hours ?? [] as $hour)
                            <td>{{ $result['m-' . $manager['id'] . '_a-' . $key . '_d-' . $day . '_h-' . $hour] ?: '' }}</td>
                        @endforeach
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
                    @foreach($hours ?? [] as $hour)
                        <td>{{ $result['a-' . $key . '_d-' . $day . '_h-' . $hour] ?: '' }}</td>
                    @endforeach
                @endforeach
            @endforeach
            <td>{{ $result['amount'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th rowspan="3">№</th>
            <th rowspan="3">Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    @foreach($hours ?? [] as $hour)
                        <th>{{ $hour }}</th>
                    @endforeach
                @endforeach
            @endforeach
            <th rowspan="3">Общее количество</th>
            <th rowspan="3">Менеджер</th>
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                @foreach($days ?? [] as $day)
                    <th colspan="{{ count($hours) }}">{{ \Carbon\Carbon::parse($day)->format('d.m') }}&nbsp;</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th colspan="{{ count($days) * count($hours) }}">{{ $ageCategory }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
