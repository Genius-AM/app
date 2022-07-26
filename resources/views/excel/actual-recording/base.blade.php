<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Менеджер</th>
            @foreach($categories ?? [] as $category)
                <th colspan="2">{{ $category['name'] }}</th>
            @endforeach
            <th colspan="2">Общее количество</th>
            <th>Менеджер</th>
        </tr>
    </thead>
    <tbody>
        @foreach($managers as $key => $manager)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $manager['name'] }}</td>
                @foreach($categories ?? [] as $category)
                    <td>{{ $result[$manager['id'] . '_' . $category['id']]['accept'] ?: '' }}</td>
                    <td>{{ $result[$manager['id'] . '_' . $category['id']]['reject'] ?: '' }}</td>
                @endforeach
                <td>{{ $result[$manager['id']] ? ($result[$manager['id']]['accept'] ?: '') : '' }}</td>
                <td>{{ $result[$manager['id']] ? ($result[$manager['id']]['reject'] ?: '') : '' }}</td>
                <td>{{ $manager['name'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Итого</td>
            @foreach($categories ?? [] as $category)
                <td>{{ $result[$category['id']] ? ($result[$category['id']]['accept'] ?: '') : '' }}</td>
                <td>{{ $result[$category['id']] ? ($result[$category['id']]['reject'] ?: '') : '' }}</td>
            @endforeach
            <td>{{ $result['accept'] ?: '' }}</td>
            <td>{{ $result['reject'] ?: '' }}</td>
            <td>Итого</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>№</th>
            <th>Менеджер</th>
            @foreach($categories ?? [] as $category)
                <th colspan="2">{{ $category['name'] }}</th>
            @endforeach
            <th colspan="2">Общее количество</th>
            <th>Менеджер</th>
        </tr>
    </tfoot>
</table>
