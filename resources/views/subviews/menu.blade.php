@auth
    @switch(Auth::user()->role_id)
        @case(2)
            <ul>
                <li>
                    <span><i class="demo-icon icon-jipping"></i> Списки</span>
                    <ul>
                        <li><a href="{{ route('lists.addresses.index') }}">Адреса</a></li>
                    </ul>
                </li>
                <li><a href="/dispatcher/all-orders/1"><i class="demo-icon icon-jipping"></i> Джиппинг</a></li>
                <li><a href="{{ route('dispatcher.orders.get', ['category' => 2, 'subcategory' => 2]) }}"><i class="demo-icon icon-diving"></i> Сокровища Геленджика</a></li>
                <li><a href="{{ route('dispatcher.orders.get', ['category' => 3, 'subcategory' => 3]) }}"><i class="demo-icon icon-motorcycle"></i> Квадроциклы</a></li>
                <li><span><i class="demo-icon icon-ship"></i> Море</span>
                    <ul>
                        <li><a href="{{ route('dispatcher.orders.get', ['category' => 4, 'subcategory' => 4]) }}">Прогулка</a></li>
                        <li><a href="{{ route('dispatcher.orders.get', ['category' => 4, 'subcategory' => 5]) }}">Рыбалка</a></li>
                        <li><a href="{{ route('dispatcher.orders.get', ['category' => 4, 'subcategory' => 6]) }}">Аренда</a></li>
                    </ul>
                </li>
                <li><span><i class="demo-icon icon-bus"></i>Автобусные экскурсии</span>
                    <ul>
                        <li><a href="{{ route('dispatcher.orders.get', ['category' => 5, 'subcategory' => 7]) }}">Лесные угодья</a></li>
                        <li><a href="{{ route('dispatcher.orders.get', ['category' => 5, 'subcategory' => 8]) }}">Меридиан</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('dispatcher.orders.get', ['category' => 6, 'subcategory' => 9]) }}"><i class="demo-icon icon-parachute"></i> Парашют</a></li>
                {{--<li><a href="{{ route('packs.get') }}"><i class="demo-icon icon-pack"></i> Пакетные экскурсии</a></li>--}}
            </ul>
            @break
        @case(4)
            <ul>
                <li>
                    <span><i class="demo-icon icon-jipping"></i> Списки</span>
                    <ul>
                        <li><a href="{{ route('lists.addresses.index') }}">Адреса</a></li>
                    </ul>
                </li>
                <li><span><i class="demo-icon icon-jipping"></i> Транспортные средства</span>
                    <ul>
                        <li><a href="{{ route('admin.cars.cars.index') }}">Список ТС</a></li>
                        <li><a href="{{ route('admin.cars.cars.create') }}">Создать ТС</a></li>
                        <li><a href="{{ route('admin.cars.defaultSeats.index') }}">Категории ТС</a></li>
                    </ul>
                </li>
                <li><span><i class="demo-icon icon-user"></i> Пользователи</span>
                    <ul>
                        <li><a href="{{ route('admin.users.index') }}">Список пользователей</a></li>
                        <li><a href="{{ route('admin.user.new') }}">Создать пользователя</a></li>
                        <li><a href="{{ route('admin.users.devices') }}">Список привязанных устройств</a></li>
                    </ul>
                </li>
                <li><span><i class="demo-icon icon-jipping"></i> Маршруты</span>
                    <ul>
                        <li><a href="{{ route('admin.routes.list') }}">Список маршрутов</a></li>
                        <li><a href="{{ route('admin.route.new') }}">Создать маршрут</a></li>
{{--                        <li><a href="{{ route('admin.pack.new') }}">Создать пакет</a></li>--}}
                    </ul>
                </li>
                <li><span><i class="demo-icon icon-bus"></i> Расписание</span>
                    <ul>
                        <li><a href="{{ route('admin.route.times.index') }}">Маршруты</a></li>
                        <li><a href="{{ route('admin.route.busstops') }}">Остановки</a></li>
                    </ul>
                </li>
            </ul>
            @break
    @endswitch
@endauth