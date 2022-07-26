<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <OrderFilter
                        :cat-id="catId"
                        @routechanged="routeChanged"
                        @datechanged="dateChanged"
                        @timechanged="timeChanged"
                        :managers="fetchedManagersWithOrders"
                        @managerchanged="managerChanged">
                </OrderFilter>
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
                <div v-show="!loading">
                    <div v-if="booked_time.length > 0">
                        <div class="booked_times_block">
                            <div class="booked_times_item" v-for="el in booked_time"
                                 :class="{
                                    isActiveBook:el.booked === 0,
                                    isCloseBook:el.booked === 1
                                    }">
                                <div class="time_and_route_block">
                                    <div class="change_color_blocks">{{getShortName(el.route_name)}}</div>
                                    <div class="change_color_blocks">{{el.time}}</div>
                                    <div class="booked_button" v-on:click="changeRouteData(el.category_id, el.subcategory_id, el.route_id, el.date, el.time)"
                                         v-text="el.booked === 0 ? 'Закрыть' : 'Открыть'"></div>
                                    <input v-if="el.edit" type="number" v-model="el.amount" class="form-control form-control-sm" @dblclick="saveAmount(el)">
                                    <div v-else @dblclick="openEditAmount(el)">{{el.amount ? el.amount : '-'}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div :class="{ dnd_disable: !filters.selectedDate }">
                        <draggable-nested-component
                                :managers="fetchedManagersWithOrders"
                                :cars="cars"
                                :partnerCars="partnerCars"
                                @orderAssigninCar="onOrderAssignedToCar"
                                @orderCanceled="onOrderCanceled">
                        </draggable-nested-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import OrderFilter from './OrderFilter.vue';
    import { EventBus } from '../app.js';
    import DraggableNestedComponent from "./DraggableNestedComponent";

    export default {
        props: ['catId'],
        components: {
            DraggableNestedComponent,
            OrderFilter,
        },
        data() {
            return {
                loading: false,
                category_id: this.catId,
                allDrivers: [],
                drivers: [],
                cars: [],
                partnerCars: [],
                orderToPassToModal: {},
                fetchedManagersWithOrders: [],
                filters: {
                    selectedRoute: '',
                    selectedDate: '',
                    selectedManager: '',
                    selectedTime: ''
                },
                booked_time : []
            }
        },
        created() {
            this.loading = true;
            EventBus.$on('ordercanceled', this.getManagers);
            this.getManagers();
            this.getCars();
            this.getPartnersCars();
        },
        watch: {
            filters: {
                handler() {
                    this.getManagers();
                    this.getCars();
                    this.getPartnersCars();
                    this.getBookedTime();
                },
                deep: true
            }
        },
        methods: {
            openEditAmount(el) {
                this.$set(el, "edit", true);
            },
            saveAmount(el) {
                axios.post('/dispatcher/booked_time_amount', {
                    'route_id': el.route_id,
                    'date': el.date,
                    'time': el.time,
                    'amount': el.amount,
                }).then(()=>{
                    this.getBookedTime();
                    el.edit = false;
                })
            },
            /**
             * Возвращает буквенные сокращения
             */
            getShortName(text){
                if(text.length > 0){
                    let result = '';
                    let arr_text = text.split(' ');
                    for(let i = 0; i < arr_text.length; i++){
                        result += arr_text[i].substr(0, 1);
                    }
                    return result.toUpperCase();
                } else return '';
            },
            onOrderAssignedToCar(somevar=null, status) {
                this.$notify({
                    group: 'success',
                    type: status===200?'success':'error',
                    title: status===200?'Успех':'Ошибка',
                    text: somevar !== null?somevar:'Заявка была назначена на машину!',
                    duration: 3000,
                });

                this.getManagers();
                this.getCars();
                this.getPartnersCars();
            },
            onOrderCanceled(){
                this.getManagers();
                this.getCars();
                this.getPartnersCars();
            },
            openDriverModal(order) {
                this.orderToPassToModal = order;
                this.showDriverModal = !this.showDriverModal;
            },
            closeModal() {
                this.showDriverModal = false;
            },
            getManagers() {
                this.loading = true;
                this.fetchedManagersWithOrders = [];

                axios.post('/dispatcher/managers', {
                    'catId': this.catId,
                    'route': this.filters.selectedRoute,
                    'date': this.filters.selectedDate,
                    'time': this.filters.selectedTime,
                    'managerId': this.filters.selectedManager,
                }).then(response => {
                    this.fetchedManagersWithOrders = response.data;
                    this.loading = false;
                }).catch(err => {})
            },
            getCars(){
                this.cars = [];
                axios.post('/dispatcher/cars', {
                    'date': this.filters.selectedDate,
                }).then(response => {
                    this.cars = response.data;
                })
            },
            getPartnersCars(){
                this.partnerCars = [];

                axios.post('/dispatcher/partner_cars', {
                    'date': this.filters.selectedDate,
                }).then(response => {
                    this.partnerCars = response.data;
                })
            },
            getBookedTime(){
                this.booked_time = [];
                axios.post('/dispatcher/booked_time', {
                    'date': this.filters.selectedDate,
                    'category_id': this.category_id,
                }).then(response=>{
                    this.booked_time = response.data;
                })
            },
            changeRouteData(category_id, subcategory_id, route_id, date, time){
                axios.post('/dispatcher/booked_time_change', {
                    'category_id': category_id,
                    'subcategory_id': subcategory_id,
                    'route_id': route_id,
                    'date': date,
                    'time': time,
                }).then(response => {
                    this.getBookedTime();
                })
            },
            routeChanged(selectedRoute) {
                this.filters.selectedRoute = selectedRoute;
            },
            dateChanged(selectedDate) {
                this.filters.selectedDate = selectedDate;
            },
            managerChanged(selectedManager) {
                this.filters.selectedManager = selectedManager;
            },
            timeChanged(selectedTime) {
                this.filters.selectedTime = selectedTime;
            }
        },
        mounted() {
            this.getCars();
            this.getPartnersCars();
        }
    }
</script>
