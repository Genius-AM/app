@auth
    @switch(Auth::user()->role_id)
        @case(2)
            <ul>
                <li><a href="{{ route('dispatcher.orders.formed') }}">Сформированные заказы</a></li>
                <li><a href="">Отчетность</a></li>
                <li><a href="{{ route('dispatcher.staff') }}">Персонал</a></li>
            </ul>
            @break
    @endswitch
@endauth
