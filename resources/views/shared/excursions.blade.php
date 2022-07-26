@foreach($drivers as $driver)
@if(count($driver->excursions))
        <div class="div4">
            @foreach($driver->excursions as $excursion)
                <table class="table-02" border="1" id="excursion{{ $excursion->id }}">
                    <tr>
                        <td class="header-table" <!--colspan="7"--><button class="btn-table" onclick="send({{ $excursion->id }});">Отправить</button>
                           </td> {{-- onclick="send({{ $excursion->id }}); --}}
                        <td class="header-table" <!--colspan="7"-->  <button class="btn-table" onclick="sendclose({{ $excursion->id }});">Отмена </button></td>
                        <td class="header-table" colspan="15"><a class="btn-table" href="">Резервировать</a></td>
                    </tr>
                    <tr>
                        <th valign="bottom" class="time-2">{{ \Carbon\Carbon::parse($excursion->time)->format('H:i') }}</th>
                        @foreach($excursion->orders as $order)
                            <td colspan="3" class="adress-1">
                                <p class="vertical">{{ $order->address }}</p>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <th class="carnumber">{{ $excursion->driver->id }}</th> {{-- айди тачки --}}
                        @foreach($excursion->orders as $order)
                            <th colspan="3" class="idmanager-2" id="idmanager">{{ $order->manager->id }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th class="allpeople">{{ $excursion->people }}</th>
                        @foreach($excursion->orders as $order)
                            <td class="man-1 people blue">{{ $order->men }}</td>
                            <td class="girl-1 people blue">{{ $order->women }}</td>
                            <td class="child-1 people blue">{{ $order->kids }}</td>
                        @endforeach
                    </tr>
                </table>
            @endforeach
        </div>
    @endif
@endforeach



<script>
    function sendclose(excursion) {

        $.ajax({
            url: '<?php echo e(route('dispatcher.excursion.sendclose')); ?>',
            type: "POST",
            data: {id: excursion},

            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response){
                location.reload();
                //console.log(response);

            }
        });
    }
</script>
