<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <canceled-order-filter
                    :cat-id = "catId"
                    @routechanged = "routeChanged"
                    @managerchanged = "managerChanged"
                    @driverchanged = "driverChanged"
                    @datechanged = 'dateChanged'
                    @timechanged = "timeChanged"
                    >
                </canceled-order-filter>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div v-show="loading" class="preloader">
                    <span>Загружается...</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-wrap">
                    <h3 class="text-center">Отказанные заявки</h3>
                    <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary active back-front-btn" @click="filters.sortOrders = [5,8]">
                            <input type="radio" name="options" checked> Все
                        </label>
                        <label class="btn btn-secondary back-front-btn" @click="filters.sortOrders = [5]">
                            <input type="radio" name="options"> Отказ
                        </label>
                        <label class="btn btn-secondary back-front-btn" @click="filters.sortOrders = [8]">
                            <input type="radio" name="options"> После принятия
                        </label>
                    </div>
                    <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
                    <div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>№ </th>
                                    <th>Менеджер</th>
                                    <th>Водитель</th>
                                    <th>Отказ от заявки</th>
                                    <th>Информация</th>
                                    <th>
                                        Пассажиры
                                    </th>
                                    <th>
                                        Маршрут
                                    </th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(order, key) in orders">
                                    <td> {{ key + 1}} </td>
                                    <td> {{ order.manager.name }}</td>
                                    <td> {{ order.driver != null ? order.driver.name : 'Нет закрепленного водителя' }} </td>
                                    <td> {{ order.refuser ? order.refuser.name : '' }}</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <b>Адрес</b>
                                            </div>
                                            <div class="col-sm-4">
                                                <b>Контакты</b>
                                            </div>
                                            <div class="col-sm-4">
                                                <b>Предоплата</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                {{ getAddress(order.address, 20)}} - {{ momentShort(order.date) }} в {{ momentTime(order.time) }}
                                            </div>
                                            <div class="col-sm-4">
                                                <div>Основной: {{ order.client.phone }}</div>
                                                <div>{{ order.client.phone_2 ? 'Дополнительный: ' + order.client.phone_2 : '' }}</div>
                                            </div>
                                            <div class="col-sm-4">
                                                {{ order.prepayment }} р.
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-sm-3"><b>М</b></div>
                                            <div class="col-sm-3"><b>Ж</b></div>
                                            <div class="col-sm-3"><b>Д</b></div>
                                            <div class="col-sm-3"><b>Общ.</b></div>
                                        </div>
                                        <div class="row">
                                        <div class="col-sm-3">{{ order.men }}</div>
                                        <div class="col-sm-3">{{ order.women }}</div>
                                        <div class="col-sm-3">{{ order.kids }}</div>
                                        <div class="col-sm-3">{{ getAmountOFPassengers(order.men, order.women, order.kids) }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div align="center">
                                            <div class="color__grid__item">
                                                <span :class="order.route.color"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td> {{ order.status_id == 5 ? 'Отказ' : 'Отказ после принятия' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CanceledOrderFilter from "./CanceledOrderFilter";

    export default {
        props: ['catId'],
        components: {
            CanceledOrderFilter,
        },
        data() {
            return {
                orders: [],
                loading: false,
                test: '',
                filters: {
                    selectedRoute: '',
                    selectedManager: '',
                    selectedDriver: '',
                    selectedDateStart: '',
                    selectedDateEnd: '',
                    selectedTime: '',
                    sortOrders: [5, 8],
                },
            }
        },
        created() {
            this.getOrders();
        },
        watch: {
            filters: {
                handler() {
                    this.getOrders();
                },
                deep: true
            }
        },
        methods: {
            getOrders() {
                this.loading = true;
                this.orders = [];

                // this.catId Заглушка для джиппинга
                axios.post('/dispatcher/canceled-orders/get-orders', {
                    'category_id' : this.catId,
                    'route' : this.filters.selectedRoute,
                    'manager' : this.filters.selectedManager,
                    'driver' : this.filters.selectedDriver,
                    'start_date' : this.filters.selectedDateStart,
                    'end_date' : this.filters.selectedDateEnd,
                    'time' : this.filters.selectedTime,
                    'sort_orders' : this.filters.sortOrders
                }).then(response => {
                    this.orders = response.data;
                    this.loading = false;
                }).catch(err => {

                })
            },
            managerChanged(selectedManager) {
                this.filters.selectedManager = selectedManager;
            },
            driverChanged(selectedDriver) {
                this.filters.selectedDriver = selectedDriver;
            },
            routeChanged(selectedRoute) {
                this.filters.selectedRoute = selectedRoute;
            },
            dateChanged(selectedDateStart, selectedDateEnd) {
                this.filters.selectedDateStart = selectedDateStart;
                this.filters.selectedDateEnd = selectedDateEnd;
            },
            timeChanged(selectedTime) {
                this.filters.selectedTime = selectedTime;
            },
            getAddress(text, length){
                if( text != null && text.length > length ){
                    let cut_str = text.substring(0, length+1);
                    return cut_str.trim()+'...';
                } else return text;
            },
            getAmountOFPassengers(men, women, kids){
                return (men+women+kids);
            },
            loadExcel() {
                axios({
                    url: '/dispatcher/canceled-orders/general-excel',
                    data: {
                        orders: JSON.stringify(this.orders)
                    },
                    method: 'POST',
                    responseType: 'arraybuffer',
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'canceled-orders.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .color__grid__item{
        width:20px;
        height:20px;
        span{
            &:first-child{
                display:inline-block;
                width:20px;
                height:20px;
                margin-right:10px;
            }
        }
    }
    .back-front-btn {
        z-index: 0 !important;
    }
</style>