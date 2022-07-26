@if(count($orders))
    <div class="col-lg-12">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Услуга</th>
                    <th>Маршрут</th>
                    <th>Форма заказа</th>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Кол-во человек</th>
                    <th>Сумма</th>
                    <th>Машина</th>
                </tr>
                </thead>
                <tbody>





                </tbody>
            </table>
        </div>
        <div class="text-right total">
            <p><span class="big-green">Итого:</span><span class="total-person">Человек:  <span class="big-green">{{ $people }}</span></span> <span class="total-sum">Сумма:   <span class="big-green">{{ $summ }} руб.</span></span> </p>
        </div>
    </div>
@endif