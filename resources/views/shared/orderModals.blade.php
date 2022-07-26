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
{{--                        <select name="excursion" id="excursion">--}}
{{--                            <option value="">Не выбрана</option>--}}
{{--                            @foreach($excursions as $excursion)--}}
{{--                                <option value="{{ $excursion->id }}">{{ $excursion->driver->name }}{{ $excursion->driver->region }} ({{ $excursion->capacity }})</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-item">--}}
{{--                        <legend>Доступные водители</legend>--}}
{{--                        <select name="driver" id="driver">--}}
{{--                            <option value="">Не выбран</option>--}}
{{--                            @foreach($drivers as $driver)--}}
{{--                                <option value="{{ $driver->id }}">{{ $driver->region }} ({{ $driver->id }})</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <input type="hidden" name="order" value="{{ $order->id }}">--}}
{{--                    <input type="hidden" name="subcategory" value="{{ $order->subcategory_id }}">--}}
{{--                    <input type="hidden" name="route" value="{{ $order->route_id }}">--}}
{{--                    <input type="hidden" name="subcategory" value="{{ $order->subcategory_id }}">--}}
{{--                    <input type="hidden" name="date" value="{{ $order->date }}">--}}
{{--                    <input type="hidden" name="time" value="{{ $order->time }}">--}}
{{--                    <input type="hidden" name="people" value="{{ $order->men + $order->women + $order->kids }}">--}}
{{--                    <button type="submit" class="btn btn-green btn-detail-page">Отправить</button>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}