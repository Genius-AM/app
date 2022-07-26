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
                    <div :class="{ dnd_disable: !filters.selectedDate }">
                        <draggable-nested-component
                                :managers="fetchedManagersWithOrders">
                        </draggable-nested-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import OrderFilter from './OrderFilterQuadBike.vue';
    import { EventBus } from '../app.js';
    import DraggableNestedComponent from "./DraggableNestedQuadbikeComponent.vue";

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
        },
        watch: {
            filters: {
                handler() {
                    this.getManagers();
                    this.getBookedTime();
                },
                deep: true
            }
        },
        methods: {
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
            onOrderAssigned() {
                this.$notify({
                    group: 'success',
                    type: 'success',
                    title: 'Успех',
                    text: 'Заявка была назначена на водителя!',
                    duration: 3000,
                });

                this.getManagers();
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
                }).then(response=>{
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
    }
</script>