<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th>{{ $ageCategory }}</th>
            @endforeach
            <th>Общее количество</th>
            <th>Менеджер</th>
        </tr>
    </thead>
    <tbody>
        @foreach($managers as $key => $manager)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $manager['name'] }}</td>
                @foreach($ageCategories ?? [] as $key => $ageCategory)
                    <td>{{ $result['m-' . $manager['id'] . '_a-' . $key] ?: '' }}</td>
                @endforeach
                <td>{{ $result['m-' . $manager['id']] ?: '' }}</td>
                <td>{{ $manager['name'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Итого</td>
            @foreach($ageCategories ?? [] as $key => $ageCategory)
                <td>{{ $result['a-' . $key] ?: '' }}</td>
            @endforeach
            <td>{{ $result['amount'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>№</th>
            <th>Менеджер</th>
            @foreach($ageCategories ?? [] as $ageCategory)
                <th>{{ $ageCategory }}</th>
            @endforeach
            <th>Общее количество</th>
            <th>Менеджер</th>
        </tr>
    </tfoot>
</table>
