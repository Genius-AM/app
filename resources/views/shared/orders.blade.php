{{--@foreach($managers as $manager)--}}
{{--    @if(count($manager->orders))--}}

{{--        <table class="table-01" id="orders-01">--}}
{{--            <tbody class="table__item-01">--}}
{{--                <tr>--}}
{{--                    <th rowspan="4" id="idmanager" class="idmanager-1">{{ $manager->id }}</th>--}}
{{--                    <th rowspan="4" class="manager">{{ $manager->name }}</th>--}}
{{--                    @foreach($manager->orders as $order)--}}

{{--                        <td rowspan="2" colspan="4" class="adress" data-toggle="modal" data-target="#excursionnew" onclick="associate({{ $order->id }})">--}}
{{--                           @if($order->status_id =='5')--}}
{{--                                <b style="color:red">Отказ </b>--}}
{{--                            @endif--}}
{{--                            {{ $order->address }}</td>--}}
{{--                        <td colwspan="3" onclick="location.href='{{ url('/dispatcher/orderedit/'.$order->id) }}'" >Edit</td>--}}
{{--                        <td rowspan="2" class="time" >{{ \Carbon\Carbon::parse($order->time)->format('H:i') }} </td>--}}
{{--                    @endforeach--}}
{{--                </tr>--}}
{{--                <tr></tr>--}}
{{--                <tr>--}}
{{--                    @php--}}
{{--                    $colors =[--}}
{{--                        'blue',--}}
{{--                        'violate',--}}
{{--                        'green',--}}
{{--                        'grey'--}}
{{--                    ];--}}
{{--                        $count = 0;--}}
{{--                    @endphp--}}
{{--                    @foreach($manager->orders as $order)--}}
{{--                        <td rowspan="2" class="man {{ $colors[$count] }}">{{ $order->men }} </td>--}}
{{--                        <td rowspan="2" class="girl {{ $colors[$count] }}">{{ $order->women }}</td>--}}
{{--                        <td rowspan="2" class="child {{ $colors[$count] }}">{{ $order->kids }}</td>--}}
{{--                        <td colspan="2" class="cooment">--}}
{{--                            @isset($order->comment[0])--}}
{{--                                {{ $order->comment[0]}}--}}
{{--                            @endisset--}}
{{--                        </td>--}}
{{--                        <td> </td>--}}
{{--                        @php--}}
{{--                            $count++;--}}
{{--                        @endphp--}}
{{--                        @if($count>3)--}}
{{--                            @php--}}
{{--                             $count = 0;--}}
{{--                            @endphp--}}
{{--                        @endif--}}

{{--                    @endforeach--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    @foreach($manager->orders as $order)--}}
{{--                        <td class="food">--}}
{{--                            @isset($order->comment[1])--}}
{{--                                {{ $order->comment[1]}}--}}
{{--                            @endisset--}}
{{--                        </td>--}}
{{--                        <td></td>--}}
{{--                        <td class="money price">{{ $order->price }}</td>--}}
{{--                    @endforeach--}}
{{--                </tr>--}}
{{--            </tbody>--}}
{{--        </table>--}}
{{--    @endif--}}
{{--@endforeach--}}
{{--<div class="modal fade" id="excursionnew2" tabindex="-1" role="dialog" aria-labelledby="excursionNewLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h4 class="modal-title " id="excursionNewLabel"> </h4>--}}
{{--            </div>--}}
{{--            <div class="modal-body text-center">--}}
{{--                 Уведомление об отказе клиента--}}
{{--                --}}{{--<form id="" class="" method="POST" action="{{ route('dispatcher.excursion2.create') }}" novalidate>--}}
{{--                    @csrf--}}

{{--                    <input name="driver_id" id="driver_id" type="hidden" value="">--}}

{{--                   <button type="submit" class="msgbotton btn btn-green btn-detail-page">Скрыть</button>--}}
{{--                --}}{{--</form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--       <script>--}}
{{--           function associate2(order) {--}}
{{--               console.log(order);--}}
{{--               $("#driver_id").val(order)--}}
{{--               $( ".msgbotton" ).click(function() {--}}
{{--               //     $('.msgbotton').change(function () {--}}
{{--                       console.log("++++");--}}
{{--                   $.ajax({--}}
{{--                      url: '{{ route('dispatcher.excursion.create2') }}',--}}
{{--                      type: "POST",--}}
{{--                       data: {id: order},--}}

{{--                       headers: {--}}
{{--                           'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')--}}
{{--                       },--}}
{{--                       success: function (response) {--}}
{{--                           // if (response) {--}}
{{--                           console.log(response);--}}
{{--                           // }--}}
{{--                       }--}}
{{--                   })--}}

{{--               });--}}

{{--           }--}}
{{--       </script>--}}

{{--<div class="modal fade" id="excursionnew" tabindex="-1" role="dialog" aria-labelledby="excursionNewLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h4 class="modal-title" id="excursionNewLabel">Отправить водителю</h4>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <form id="excursionnewform" class="form-horizontal" method="POST" action="{{ route('dispatcher.excursion.create') }}" novalidate>--}}
{{--                    @csrf--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Доступные экскурсии</legend>--}}
{{--                        <select name="excursion" id="excursion_select">--}}
{{--                            <option value="">Не выбрана</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Доступные водители</legend>--}}
{{--                        <select name="driver" id="driver_select">--}}
{{--                            <option value="">Не выбран</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <button type="submit" class="btn btn-green btn-detail-page">Отправить</button>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}