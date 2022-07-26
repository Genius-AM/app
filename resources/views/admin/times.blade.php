<form id="order-form" method="POST" action="{{ route('admin.route.times.set') }}">
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Категория</legend>
                        <select id="category" class="nice-select" required>
                            <option value="">Не выбрана</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Подкатегория</legend>
                        <select id="subcategory" class="nice-select" required>
                            <option value="">Не выбрана</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Маршрут</legend>
                        <select id="route" name="route" class="nice-select" required>
                            <option value="">Не выбран</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg col-md car-block hidden">
                    <div class="form-item">
                        <legend>Транспортное средство</legend>
                        <select id="car" name="car" class="nice-select" required disabled>
                            <option value="">Не выбран</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Понедельник*</legend>
                        <input type="hidden" name="dates[]" value="monday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-monday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[monday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[monday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Вторник*</legend>
                        <input type="hidden" name="dates[]" value="tuesday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-tuesday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[tuesday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[tuesday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Среда*</legend>
                        <input type="hidden" name="dates[]" value="wednesday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-wednesday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[wednesday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[wednesday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Четверг*</legend>
                        <input type="hidden" name="dates[]" value="thursday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-thursday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[thursday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[thursday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Пятница*</legend>
                        <input type="hidden" name="dates[]" value="friday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-friday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[friday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[friday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Суббота*</legend>
                        <input type="hidden" name="dates[]" value="saturday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-saturday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[saturday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[saturday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md">
                    <div class="form-item">
                        <legend>Воскресенье*</legend>
                        <input type="hidden" name="dates[]" value="sunday" required>
                    </div>
                    <div class="form-item">
                        <div id="checkbox-container-sunday" class="checkbox-container">
                            @foreach($times as $time)
                                <div class="d-flex">
                                    <div class="col-6 p-0">
                                        <input type="checkbox" name="times[sunday][{{ $time->id }}]" value="{{ $time->id }}"> {{ $time->name }}
                                    </div>
                                    <div class="col-6 pl-0">
                                        <input type="number" name="amount[sunday][{{ $time->id }}]" class="form-control-sm" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-sm btn-green py-1 px-2 add-datetime">Добавить</button>
                </div>
            </div>

            <div class="block-datetime"></div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Сохранить</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@section('js')
<script>
    $(document).ready(function() {
        $('#category').click(function () {
            $('#subcategory').find('option').remove();
            if ($('#category').val() == 4) {
                $('.car-block').show();
                $('.car-block select').prop('disabled', false);
            } else {
                $('.car-block').hide();
                $('.car-block select').prop('disabled', true);
            }
            $.ajax({
                url: '{{ route('admin.route.subcategories') }}',
                type: "GET",
                data: {id: $('#category').val()},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response) {
                        $('#subcategory').append($('<option>', {
                            value: "",
                            text : "Не выбрана"
                        }));
                        jQuery.each(response, function (index, item) {
                            $('#subcategory').append($('<option>', {
                                value: item.id,
                                text : item.name
                            }));
                        });
                    }
                }
            });
        });
        $('#subcategory').click(function () {
            $('#route').find('option').remove();
            $.ajax({
                url: '{{ route('admin.routes') }}',
                type: "GET",
                data: {id: $('#subcategory').val()},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response) {
                        $('#route').append($('<option>', {
                            value: "",
                            text : "Не выбран"
                        }));
                        jQuery.each(response, function (index, item) {
                            $('#route').append($('<option>', {
                                value: item.id,
                                text : item.name
                            }));
                        });
                    }
                }
            });
        });
        $('#route').click(function () {
            $('#car').find('option').remove();
            $.ajax({
                url: '{{ route('admin.route.cars') }}',
                type: "GET",
                data: {id: $('#route').val()},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response) {
                        $('#car').append($('<option>', {
                            value: "",
                            text : "Не выбран"
                        }));
                        jQuery.each(response, function (index, item) {
                            $('#car').append($('<option>', {
                                value: item.id,
                                text : item.name
                            }));
                        });
                    }
                }
            });

            $('.checkbox-container input').prop('checked',false);
            $.ajax({
                url: '{{ route('admin.routeTimes') }}',
                type: "GET",
                data: {
                    id: $('#route').val(),
                    car_id: $('#car').val(),
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('input[name*="amount"]').prop('disabled', true).val(null);
                    if (response) {
                        if(
                            response.hasOwnProperty(0) &&
                            response[0].days.length > 0
                        ) {
                            jQuery.each(response[0].days, function (index, item) {
                                if( item.weekday !== '' && item.times.length > 0){

                                    jQuery.each(item.times, function (iIndex, iItem) {
                                        $('input[name="times['+item.weekday+']['+iItem.id+']"]').prop('checked', true);
                                        $('input[name="amount['+item.weekday+']['+iItem.id+']"]').prop('disabled', false).val(iItem.pivot.amount);
                                    });

                                }
                            });
                        }

                        $('div.datetime').remove();
                        if(
                            response.hasOwnProperty(0) &&
                            response[0].route_timetables.length > 0
                        ) {
                            jQuery.each(response[0].route_timetables, (index, item) => addDatetime(item.date + 'T' + item.time, item.amount));
                        }
                    }
                }
            });
        });
        $('#car').click(function () {
            $('.checkbox-container input').prop('checked',false);
            $.ajax({
                url: '{{ route('admin.routeTimes') }}',
                type: "GET",
                data: {
                    id: $('#route').val(),
                    car_id: $('#car').val(),
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('input[name*="amount"]').prop('disabled', true).val(null);
                    if (response) {
                        if(
                            response.hasOwnProperty(0) &&
                            response[0].days.length > 0
                        ) {
                            jQuery.each(response[0].days, function (index, item) {
                                if(
                                    item.weekday !== '' &&
                                    item.hasOwnProperty('times') &&
                                    item.times.length > 0
                                ) {

                                    jQuery.each(item.times, function (iIndex, iItem) {
                                        $('input[name="times['+item.weekday+']['+iItem.id+']"]').prop('checked', true);
                                        $('input[name="amount['+item.weekday+']['+iItem.id+']"]').prop('disabled', false).val(iItem.pivot.amount);
                                    });

                                }
                            });
                        }

                        $('div.datetime').remove();
                        if(
                            response.hasOwnProperty(0) &&
                            response[0].route_timetables.length > 0
                        ) {
                            jQuery.each(response[0].route_timetables, (index, item) => addDatetime(item.date + 'T' + item.time, item.amount));
                        }
                    }
                }
            });
        });

        $('input[name*="times"]').click(function () {
            if (this.checked) {
                $('input[name="'+this.name.replace('times', 'amount')+'"]').prop('disabled', false);
            } else {
                $('input[name="'+this.name.replace('times', 'amount')+'"]').prop('disabled', true).val(null);
            }
        });

        $('button.add-datetime').click(() => addDatetime());

        const addDatetime = (datetime = null, amount = null) => {
            const block = document.createElement("div");
            block.classList.add('row', 'datetime');

            const child = document.createElement("div");
            child.classList.add('col-12', 'mb-2');
            block.appendChild(child);

            const inputDatetime = document.createElement("input");
            inputDatetime.name = 'datetime[]';
            inputDatetime.type = 'datetime-local';
            inputDatetime.classList.add('form-control-sm');
            inputDatetime.setAttribute('required', 'required');
            inputDatetime.value = datetime;
            child.appendChild(inputDatetime);

            const inputAmount = document.createElement("input");
            inputAmount.name = 'datetime_amount[]';
            inputAmount.type = 'number';
            inputAmount.classList.add('form-control-sm');
            inputAmount.setAttribute('required', 'required');
            inputAmount.value = amount;
            child.appendChild(inputAmount);

            const button = document.createElement("button");
            button.type = 'button';
            button.classList.add('btn', 'btn-sm', 'btn-green', 'py-1', 'px-2', 'del-datetime');
            button.innerHTML = 'Удалить';
            button.addEventListener("click", () => block.remove());
            child.appendChild(button);

            document.getElementsByClassName('block-datetime')[0].appendChild(block);
        }
    });
</script>
@endsection