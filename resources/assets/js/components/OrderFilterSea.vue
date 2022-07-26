<template>
    <div>
        <div class="row">
            <div class="col-sm-3">
                <div class="select-mini">
                    <label>Выберите маршрут</label>
                    <selectize v-model="selectedRoute" placeholder="Выберите маршрут">
                        <option v-for="route in getRoutesArray(routes)" :value="route">{{ route }}</option>
                    </selectize>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-item m-0">
                    <label>Выберите дату</label>
                    <input name="date" id="filter_date" type="date" v-model="selectedDate" class="mini-input-date mini-date">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="select-mini">
                    <label>Выберите менеджера</label>
                    <selectize v-model="selectedManager" placeholder="Выберите менеджера">
                        <option v-for="manager in getManagersArray(managers)" :value="manager">{{ manager }}</option>
                    </selectize>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="select-mini" v-if="selectedDate">
                    <label>Выберите время</label>
                    <selectize v-model="selectedTime" placeholder="Выберите время">
                        <option v-for="time in times" :value="time">{{ time }}</option>
                    </selectize>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="color__grid__wrapper">
                    <div class="color__grid__item mb-2" v-for="route in routes">
                        <span :class="route.color ? route.color : ''"></span>
                        <span>{{ route.name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import 'selectize/dist/css/selectize.css'
import Selectize from 'vue2-selectize'
import axios from 'axios';

export default {
    name: 'OrderFilter',
    props:['catId'],
    components: {
        Selectize
    },
    data () {
        return {
            routes:[],
            managers:[],
            times:[],
            selectedRoute:'',
            selectedDate:'',
            selectedManager:'',
            selectedTime:'',
        }
    },
    created(){
        this.getRoutes();
        this.getManagers();
    },
    watch: {
        selectedRoute(){
            let id_route = '';
            if(this.selectedRoute !== null){
                if(this.selectedRoute === 'Все'){
                    id_route = '';
                } else {
                    id_route = this.selectedRoute.substring(
                        this.selectedRoute.indexOf("(#")+2,
                        this.selectedRoute.indexOf(")")
                    );
                }
            }
            // console.log(id_route);
            this.$emit('routechanged', id_route);
        },
        selectedDate(){
            this.$emit('datechanged',this.selectedDate);
            this.getTimes();
        },
        selectedManager(){
            let id_manager = '';
            if(this.selectedManager !== null){
                if(this.selectedManager === 'Все'){
                    id_manager = '';
                } else {
                    id_manager = this.selectedManager.substring(
                        this.selectedManager.indexOf("(#")+2,
                        this.selectedManager.indexOf(")")
                    );
                }
            }

            this.$emit('managerchanged',id_manager);
        },
        selectedTime(){
            if(this.selectedTime === 'Все'){
                this.$emit('timechanged','');
            } else {
                this.$emit('timechanged',this.selectedTime);
            }
        }
    },
    methods:{
        getTimes(){
            if(this.selectedDate !== ''){
                this.times = [];
                axios.post('/dispatcher/timesByDate', {
                    'date': this.selectedDate,
                    'category_id': this.catId,
                }).then(response => {
                    this.times = response.data;
                    this.times.unshift('Все');
                })
            } else {
                this.selectedTime = '';
                this.times = [];
            }
        },
        getRoutes(){
            axios.get(`/dispatcher/routes/${this.catId}`)
                .then(response=>{
                    this.routes = response.data;
                })
        },
        getManagers(){
            axios.get(`/dispatcher/managers/all`)
                .then(response=>{
                    this.managers = response.data;
                })
        },
        /*
         * Возвращает нам необходимый менеджеров данные для select
         */
        getManagersArray(managers){
            let new_array_managers = [];
            new_array_managers.push('Все');
            if(managers.length > 0){
                for (let manager in managers){
                    new_array_managers.push(`(#${managers[manager].id}) ${ managers[manager].name } `);
                }
            }

            return new_array_managers;
        },
        /*
         * Возвращает нам необходимый маршрута данные для select
         */
        getRoutesArray(routes){
            let new_array_routes = [];
            new_array_routes.push('Все');
            if(routes.length > 0){
                for (let route in routes){
                    new_array_routes.push(`(#${routes[route].id}) ${ routes[route].name } `);
                }
            }

            return new_array_routes;
        },
        /*
         * Возвращает нам необходимые данные по времени для select
         */
        getTimesArray(times){
            let new_array_times = [];
            new_array_times.push('Все');
            if(times.length > 0){
                for (let time in times){
                    new_array_times.push(`(#${times[time].id}) ${ times[time].name } `);
                }
            }

            return new_array_times;
        }

    },
    computed:{

    }
}
</script>

<style lang="scss" scoped>
    .color__grid__wrapper{
        display:flex;
        padding-right: 30px;
        .color__grid__item{
            display:flex;
            margin-left:20px;
            span{
                &:first-child{
                    display:inline-block;
                    width:20px;
                    height:20px;
                    margin-right:10px;
                }
                &:last-child{
                    font-size: 14px;
                }
            }
        }
    }
</style>