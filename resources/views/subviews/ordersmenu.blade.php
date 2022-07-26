<ul>
    <li><a href="javascript:void(0);">Списки</a>
        <ul>
            <li><a href="{{ route('lists.addresses.index') }}">Точки</a></li>
            <li><a href="{{ route('lists.companies.index') }}">Компании</a></li>
            <li><a href="{{ route('dispatcher.staff') }}">Персонал</a></li>
            <li><a href="{{ route('lists.age-categories.index') }}">Категории возрастов</a></li>

            @if(request()->user()->isAdmin())
                <li><a href="{{ route('admin.cars.cars.index') }}">Транспортные средства</a></li>
                <li><a href="{{ route('admin.users.index') }}">Пользователи</a></li>
                <li><a href="{{ route('admin.routes.list') }}">Маршруты</a></li>
                <li>
                    <a href="">Расписание</a>
                    <ul>
                        <li><a href="{{ route('admin.route.times.index') }}">Маршруты</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('lists.tracking-operations.index') }}">Отслеживание операций</a></li>
            @endif

            @if(request()->user()->isRole('dispatcher'))
                <li>
                    <a href="">Расписание</a>
                    <ul>
                        <li><a href="{{ route('route.times.index') }}">Маршруты</a></li>
                    </ul>
                </li>
            @endif

            @if(request()->user()->isAdmin() || (request()->user()->isRole('dispatcher') && request()->user()->category_id == \App\Category::DJIPPING))
                <li><a href="{{ route('lists.promotions.index') }}">Рассылки</a></li>
            @endif
        </ul>
    </li>
    <li><a href="{{ route('dispatcher.canceled-orders.index') }}">Отказанные заявки</a></li>
    <li><a href="javascript:void(0);">Виды экскурсий</a>
        <ul>
            <li><a href="{{ route('dispatcher.all-orders', 1) }}"><i class="demo-icon icon-jipping"></i> Джиппинг</a></li>
            <li><a href="{{ route('dispatcher.all-orders', 2) }}"><i class="demo-icon icon-diving"></i> Сокровища Геленджика</a></li>
            <li><a href="{{ route('dispatcher.all-orders', 3) }}"><i class="demo-icon icon-motorcycle"></i> Квадроциклы</a></li>
            <li><a href="{{ route('dispatcher.all-orders', 4) }}"><i class="demo-icon icon-ship"></i> Море</a></li>
{{--            <li><a href=""><i class="demo-icon icon-ship"></i>Море</a>--}}
{{--                <ul>--}}
{{--                    <li><a href="{{ route('dispatcher.all-orders', 4) }}">Прогулка</a></li>--}}
{{--                    <li><a href="{{ route('dispatcher.all-orders', 5) }}">Рыбалка</a></li>--}}
{{--                    <li><a href="{{ route('dispatcher.all-orders', 6) }}">Аренда</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}
{{--            <li><a href=""><i class="demo-icon icon-bus"></i>Автобусные экскурсии</a>--}}
{{--                <ul>--}}
{{--                    <li><a href="{{ route('dispatcher.all-orders', 7) }}">Лесные угодья</a></li>--}}
{{--                    <li><a href="{{ route('dispatcher.all-orders', 8) }}">Меридиан</a></li>--}}
{{--                </ul>--}}
{{--            </li>--}}
{{--            <li><a href="{{ route('dispatcher.all-orders', 9) }}"><i class="demo-icon icon-parachute"></i>Парашют</a></li>--}}
        </ul>
    </li>
    <li><a href="javascript:void(0);">Отчеты</a>
        <ul>
            <li><a href="{{ route('reports.index') }}">Отчетность</a></li>
            <li><a href="{{ route('reports.actual-recording.index') }}">Фактическая запись</a></li>
            <li><a href="{{ route('reports.age-category.index') }}">Категории возрастов</a></li>
{{--            <li><a href="{{ route('reports.deleted-order.index') }}">Удаленные заявки</a></li>--}}
        </ul>
    </li>
</ul>
