require('./bootstrap');

import Vue from 'vue'
import Notifications from 'vue-notification'
import VueRouter from 'vue-router'
import moment from 'moment';
import router from './router'

Vue.router = router;

window.Vue = require('vue');

Vue.use(Notifications);
Vue.use(VueRouter);

export const EventBus = new Vue();

Vue.mixin({
    methods: {
        moment: date =>  moment(date).format("DD. MM. YYYY"),
        momentShort: date =>  moment(date).format("DD. MM. YY"),
        momentTime: time => moment(time, 'HH:mm:ss').format("HH:mm"),
        catchErrors(err) {
            let vm = this;
            if (err.response.data.errors instanceof Object) {
                _.forEach(err.response.data.errors, function (error) {
                    vm.$notify({
                        group: 'success',
                        type: 'error',
                        title: 'Ошибка',
                        text: error[0],
                        duration: 3000,
                    });
                });
            } else {
                vm.$notify({
                    group: 'success',
                    type: 'error',
                    title: 'Ошибка',
                    text: err.response.data.errors,
                    duration: 3000,
                });
            }
        },
        notifyResponse(response) {
            this.$notify({
                group: 'success',
                type: 'success',
                title: 'Успех',
                text: response.data.message,
                duration: 3000,
            });
        }
    }
});

Vue.component('order-proccesing', require('./components/OrderProccesing.vue'));
Vue.component('order-proccesing-quadbike', require('./components/OrderProccesingQuadBike.vue'));
Vue.component('order-proccesing-sea', require('./components/OrderProccesingSea.vue'));
Vue.component('order-proccesing-diving', require('./components/OrderProccesingDiving.vue'));
Vue.component('order-proccesing-other', require('./components/OrderProccesingOther.vue'));
Vue.component('canceled-orders', require('./components/CanceledOrder/CanceledOrders.vue'));
Vue.component('main-report', require('./components/Reports/Main.vue'));
Vue.component('new-user', require('./components/NewUser'));
Vue.component('new-route', require('./components/NewRoute'));
Vue.component('new-route-car', require('./components/RouteCar'));
Vue.component('car-timetable', require('./components/CarTimetable'));
Vue.component('actual-recording', require('./components/Reports/ActualRecording/Index.vue'));
Vue.component('age-category', require('./components/Reports/AgeCategory/Index.vue'));
Vue.component('deleted-order', require('./components/Reports/DeletedOrder/Index.vue'));

const app = new Vue({
    el: "#app",
    router,
});