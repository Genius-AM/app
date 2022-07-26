<table>
    <thead>
    <tr>
        <th>№ </th>
        <th>Менеджер</th>
        @foreach($times as $time)
            <th>{{ $time }}</th>
        @endforeach
        <th>Всего</th>
    </tr>
    </thead>
    <tbody>
    @php
        foreach ($times as $time_key => $time) {
            $varend[$time_key] = 0;
        }
    @endphp
    @foreach($managers as $key => $manager)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $manager['name'] }}</td>
            @foreach($times as $time_key => $time)
                <td>{{ $var[$time_key] = isset($manager['orders']->resolve()[$time . ':00']['count']) ? $manager['orders']->resolve()[$time . ':00']['count'] : 0 }}</td>
            @endforeach
            <td>
                @php
                    $test = 0;
                    foreach ($times as $time_key => $time) {
                        $test += $var[$time_key];
                    }

                    echo $test;
                @endphp
            </td>
        </tr>

        @php
            foreach ($times as $time_key => $time) {
                $varend[$time_key] += $var[$time_key];
            }
        @endphp
    @endforeach
        <tr>
            <td> </td>
            <td>Всего</td>
            @foreach($times as $time_key => $time)
                <td>{{ $varend[$time_key] }}</td>
            @endforeach
            <td>
                @php
                    $varendtest = 0;
                    foreach ($times as $time_key => $time) {
                        $varendtest += $varend[$time_key];
                    }

                    echo $varendtest;
                @endphp
            </td>
        </tr>
    </tbody>
</table>
