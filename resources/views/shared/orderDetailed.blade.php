
{{--@switch($order->category->id)--}}
{{--    @case(3)--}}
{{--        <div class="col-lg-9">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Категория</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->category->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Маршрут</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->route->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дата</legend>--}}
{{--                        <input type="date" value="{{ \Carbon\Carbon::parse($order->date)->format('yyyy-MM-dd') }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Время</legend>--}}
{{--                        <select name="" disabled>--}}
{{--                            <option value="">{{ \Carbon\Carbon::parse($order->time)->format('H:i') }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>ФИО клиента</legend>--}}
{{--                        <input type="text" value="{{ $order->client->name }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дополнительный телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone_2 }}" disabled>--}}
{{--                    </div>--}}
{{--                    @isset($order->manager)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Менеджер</legend>--}}
{{--                            <input type="text" value="{{ $order->manager->name }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-12">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Комментарий</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->client->comment }}</textarea>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Адрес</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->address }}</textarea>--}}
{{--                    </div>--}}
{{--                    @isset($order->excursion[0]->driver)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Машина</legend>--}}
{{--                            <input type="text" value="{{ $order->excursion[0]->driver->region }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-6 col-md-6">--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <legend>Количество человек</legend>--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-man"></i></span>--}}
{{--                            <b>Соло</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->men }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-users"></i></span>--}}
{{--                            <b>Тандем</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->women }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-3">--}}
{{--            <div class="form-item">--}}
{{--                <legend>Предоплата*</legend>--}}
{{--                <input type="text" name="" value="{{ $order->prepayment }}" disabled>--}}
{{--            </div>--}}
{{--            <div class="form-item">--}}
{{--                <legend>Сумма</legend>--}}
{{--                <input type="text" name="" value="{{ $order->price }}" disabled>--}}
{{--            </div>--}}
{{--            @if($order->is_pack)--}}
{{--                <div class="form-item">--}}
{{--                    <legend>Пакет: {{ $order->pack->name }}</legend>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if($order->status_id == 1)--}}
{{--                <button class="btn btn-send btn-detail-page" data-toggle="modal" data-target="#excursionnew">Отправить водителю</button>--}}
{{--            @elseif($order->status_id == 2)--}}
{{--                <input type="hidden" id="order" value="{{ $order->id }}">--}}
{{--                <input type="hidden" id="excursion" value="{{ $order->excursion[0]->id }}">--}}
{{--                <button id="driverchange" class="btn btn-green btn-detail-page">Сменить водителя</button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        @break--}}
{{--    @case(5)--}}
{{--        <div class="col-lg-9">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Категория</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->category->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Маршрут</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->route->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дата</legend>--}}
{{--                        <input type="date" value="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}" disabled>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>ФИО клиента</legend>--}}
{{--                        <input type="text" value="{{ $order->client->name }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дополнительный телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone_2 }}" disabled>--}}
{{--                    </div>--}}
{{--                    @isset($order->manager)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Менеджер</legend>--}}
{{--                            <input type="text" value="{{ $order->manager->name }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-12">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Комментарий</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->client->comment }}</textarea>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Предоплата</legend>--}}
{{--                        <input type="text" name="" value="{{ $order->prepayment }}" disabled>--}}
{{--                    </div>--}}
{{--                    @isset($order->excursion[0]->driver)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Машина</legend>--}}
{{--                            <input type="text" value="{{ $order->excursion[0]->driver->region }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-6 col-md-6">--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <legend>Количество человек</legend>--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-man"></i></span>--}}
{{--                            <b>Мужчин</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->men }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-woman"></i></span>--}}
{{--                            <b>Женщин</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->women }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-child"></i></span>--}}
{{--                            <b>Детей</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->kids }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-3">--}}
{{--            <div class="form-item">--}}
{{--                <legend>Остановка</legend>--}}
{{--                <textarea name="" disabled>{{ $order->address  }}</textarea>--}}
{{--            </div>--}}
{{--            <div class="form-item">--}}
{{--                <legend>Сумма</legend>--}}
{{--                <input type="text" name="" value="{{ $order->price }}" disabled>--}}
{{--            </div>--}}
{{--            @if($order->is_pack)--}}
{{--                <div class="form-item">--}}
{{--                    <legend>Пакет: {{ $order->pack->name }}</legend>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if($order->status_id == 1)--}}
{{--                <button class="btn btn-send btn-detail-page" data-toggle="modal" data-target="#excursionnew">Отправить водителю</button>--}}
{{--            @elseif($order->status_id == 2)--}}
{{--                <input type="hidden" id="order" value="{{ $order->id }}">--}}
{{--                <input type="hidden" id="excursion" value="{{ $order->excursion[0]->id }}">--}}
{{--                <button id="driverchange" class="btn btn-green btn-detail-page">Сменить водителя</button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        @break--}}
{{--    @case(6)--}}
{{--        <div class="col-lg-9">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Категория</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->category->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Маршрут</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->route->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дата</legend>--}}
{{--                        <input type="date" value="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Время</legend>--}}
{{--                        <select name="" disabled>--}}
{{--                            <option value="">{{ \Carbon\Carbon::parse($order->time)->format('H:i') }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>ФИО клиента</legend>--}}
{{--                        <input type="text" value="{{ $order->client->name }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дополнительный телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone_2 }}" disabled>--}}
{{--                    </div>--}}
{{--                    @isset($order->manager)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Менеджер</legend>--}}
{{--                            <input type="text" value="{{ $order->manager->name }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-12">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Комментарий</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->client->comment }}</textarea>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Адрес</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->address }}</textarea>--}}
{{--                    </div>--}}
{{--                    @isset($order->excursion[0]->driver)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Машина</legend>--}}
{{--                            <input type="text" value="{{ $order->excursion[0]->driver->region }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-6 col-md-6">--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <legend>Количество человек</legend>--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-man"></i></span>--}}
{{--                            <b>Соло</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->men }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-users"></i></span>--}}
{{--                            <b>Тандем</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->women }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-usersgroup"></i></span>--}}
{{--                            <b>Трио</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->kids }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-3">--}}
{{--            <div class="form-item">--}}
{{--                <legend>Предоплата</legend>--}}
{{--                <input type="text" name="" value="{{ $order->prepayment }}" disabled>--}}
{{--            </div>--}}
{{--            <div class="form-item">--}}
{{--                <legend>Сумма</legend>--}}
{{--                <input type="text" name="" value="{{ $order->price }}" disabled>--}}
{{--            </div>--}}
{{--            @if($order->is_pack)--}}
{{--                <div class="form-item">--}}
{{--                    <legend>Пакет: {{ $order->pack->name }}</legend>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if($order->status_id == 1)--}}
{{--                <button class="btn btn-send btn-detail-page" data-toggle="modal" data-target="#excursionnew">Отправить водителю</button>--}}
{{--            @elseif($order->status_id == 2)--}}
{{--                <input type="hidden" id="order" value="{{ $order->id }}">--}}
{{--                <input type="hidden" id="excursion" value="{{ $order->excursion[0]->id }}">--}}
{{--                <button id="driverchange" class="btn btn-green btn-detail-page">Сменить водителя</button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        @break--}}
{{--    @default--}}
{{--        <div class="col-lg-9">--}}
{{--            <div class="row">--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Категория</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->category->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Маршрут</legend>--}}
{{--                        <select name="" id="" disabled>--}}
{{--                            <option value="">{{ $order->route->name }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дата</legend>--}}
{{--                        <input type="date" value="{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Время</legend>--}}
{{--                        <select name="" disabled>--}}
{{--                            <option value="">{{ \Carbon\Carbon::parse($order->time)->format('H:i') }}</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-6">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>ФИО клиента</legend>--}}
{{--                        <input type="text" value="{{ $order->client->name }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone }}" disabled>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Дополнительный телефон</legend>--}}
{{--                        <input type="text" class="tel-input" value="{{ $order->client->phone_2 }}" disabled>--}}
{{--                    </div>--}}
{{--                    @isset($order->manager)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Менеджер</legend>--}}
{{--                            <input type="text" value="{{ $order->manager->name }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-4 col-md-12">--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Комментарий</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->client->comment }}</textarea>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Адрес</legend>--}}
{{--                        <textarea name="" disabled>{{ $order->address }}</textarea>--}}
{{--                    </div>--}}
{{--                    @isset($order->excursion[0]->driver)--}}
{{--                        <div class="form-item">--}}
{{--                            <legend>Машина</legend>--}}
{{--                            <input type="text" value="{{ $order->excursion[0]->driver->region }}" disabled>--}}
{{--                        </div>--}}
{{--                    @endisset--}}
{{--                </div>--}}
{{--                <div class="col-lg-6 col-md-6">--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <legend>Количество человек</legend>--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-man"></i></span>--}}
{{--                            <b>Мужчин</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->men }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-woman"></i></span>--}}
{{--                            <b>Женщин</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->women }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="form-item person-sum-item">--}}
{{--                        <div class="title">--}}
{{--                            <span><i class="demo-icon icon-child"></i></span>--}}
{{--                            <b>Детей</b>--}}
{{--                        </div>--}}
{{--                        <div class="podbor">--}}
{{--                            <input type="text" value="{{ $order->kids }}" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-3">--}}
{{--            <div class="form-item">--}}
{{--                <legend>Предоплата</legend>--}}
{{--                <input type="text" name="" value="{{ $order->prepayment }}" disabled>--}}
{{--            </div>--}}
{{--            <div class="form-item">--}}
{{--                <legend>Сумма</legend>--}}
{{--                <input type="text" name="" value="{{ $order->price }}" disabled>--}}
{{--            </div>--}}
{{--            @if($order->is_pack)--}}
{{--                <div class="form-item">--}}
{{--                    <legend>Пакет: {{ $order->pack->name }}</legend>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            @if($order->status_id == 1)--}}
{{--                <button class="btn btn-send btn-detail-page" data-toggle="modal" data-target="#excursionnew">Отправить водителю</button>--}}
{{--            @elseif($order->status_id == 2)--}}
{{--                <input type="hidden" id="order" value="{{ $order->id }}">--}}
{{--                <input type="hidden" id="excursion" value="{{ $order->excursion[0]->id }}">--}}
{{--                <button id="driverchange" class="btn btn-green btn-detail-page">Сменить водителя</button>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        @break--}}
{{--@endswitch--}}