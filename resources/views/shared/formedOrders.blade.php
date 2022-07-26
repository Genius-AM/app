@if(count($orders))
    <div class="col-lg-12">
        <div class="table-wrap">
             <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Услуга</th>
                    <th>Маршрут</th>
                    <th>Кол-во человек</th>
                    <th>Сумма</th>

                </tr>
                </thead>
                <tbody>
                @foreach($excursions as $indexKey => $excursion)
                    @foreach($excursion as  $indexKey2 => $excursio)
                        @foreach($excursio as  $indexKey3 =>$excurs)
                            <tr><td colspan="6">Машина -
                            @foreach($users as  $index => $user)
                                @if($users[$index]->id == $indexKey)
                                {{ $users[$index]->name }}
                                @endif
                            @endforeach
                            - дата {{ \Carbon\Carbon::parse($indexKey3)->format('d.m.Y') }} - время {{   \Carbon\Carbon::parse($indexKey2)->format('H:i')   }}</td></tr>

                            @php $sumpipl=0 @endphp
                            @php $summany=0 @endphp
                            @foreach($excurs as $exc)
                                @if($exc->orders->count())
                                <tr>
                                    <td>{{ $exc->orders['0']->id  }}</td>
                                    <td>
                                        @foreach($categories as $categor)
                                            @if($categor->id == $exc->orders['0']->category_id )
                                                {{ $categor->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>  {{ $exc->orders['0']->address  }}</td>
                                    <td data-label="Кол-во человек">{{ $exc->orders['0']->men + $exc->orders['0']->women + $exc->orders['0']->kids }}</td>
                                    @php    $sumpipl = $sumpipl + $exc->orders['0']->men + $exc->orders['0']->women + $exc->orders['0']->kids @endphp
                                    <td data-label="Сумма">{{ $exc->orders['0']->price }} руб.</td>
                                    @php $summany = $summany + $exc->orders['0']->price @endphp
                                </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="6">
                                    <div class="text-right total">
                                        <p><span class="big-green">Итого:</span><span class="total-person">Человек:  <span class="big-green">{{ $sumpipl }}</span></span> <span class="total-sum">Сумма:   <span class="big-green">{{ $summany }} руб.</span></span> </p>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
                </tbody>
             </table>
        </div>
    </div>
@endif