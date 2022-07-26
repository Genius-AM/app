<template>
    <div class="row">
<!--        <div class="mini-list-top">-->
<!--            <div class="filters">-->
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-2">
                    <div class="select-mini">
                        <label>Выберите маршрут</label>
                        <selectize v-model="selectedRoute" placeholder="Выберите маршрут">
                            <option value="">Все</option>
                            <option v-for="route in routes" :value="route.id">{{ route.name }}</option>
                        </selectize>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="select-mini">
                        <label>Выберите менеджера</label>
                        <selectize v-model="selectedManager" placeholder="Выберите менеджера">
                            <option value="">Все</option>
                            <option v-for="manager in managers" :value="manager.id">{{ manager.name }}</option>
                        </selectize>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="select-mini">
                        <label>Выберите водителя</label>
                        <selectize v-model="selectedDriver" placeholder="Выберите водителя">
                            <option value="">Все</option>
                            <option v-for="driver in drivers" :value="driver.id">{{ driver.name }}</option>
                        </selectize>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-item">
                        <label>Выберите период</label>
                        <div class="row">
                            <div class="col-sm-1">
                                с:
                            </div>
                            <div class="col-sm-5">
                                <input name="date" id="filter_date_start" type="date" v-model="selectedDateStart" class="mini-input-date mini-date">
                            </div>
                            <div class="col-sm-1">
                                по:
                            </div>
                            <div class="col-sm-5">
                                <input name="date" id="filter_date_end" type="date" v-model="selectedDateEnd" class="mini-input-date mini-date">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="select-mini" v-if="selectedDateStart && (selectedDateStart == selectedDateEnd)">
                        <label>Выберите время</label>
                        <selectize v-model="selectedTime" placeholder="Выберите время">
                          <option v-for="time in times" :value="time">{{ time }}</option>
                        </selectize>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="color__grid__wrapper float-right">
                <div class="color__grid__item" v-for="route in routes">
                    <span :class="route.color ? route.color : ''"></span>
                    <span>{{ route.name }}</span>
                </div>
            </div>
        </div>
<!--          </div>-->
<!--        </div>-->
    </div>
</template>

<script>
import 'selectize/dist/css/selectize.css'
import Selectize from 'vue2-selectize'
import axios from 'axios';

export default {
    name: 'CanceledOrderFilter',
    props:['catId'],
    components: {
        Selectize
    },
    data () {
        return {
            routes:[],
            managers:[],
            drivers:[],
            times:[],
            selectedRoute:'',
            selectedDateStart:'',
            selectedDateEnd:'',
            selectedManager:'',
            selectedDriver:'',
            selectedTime:'',
        }
    },
    created() {
        this.getRoutes();
        this.getManagers();
        this.getDrivers();
    },
    watch: {
        selectedRoute() {
            this.$emit('routechanged', this.selectedRoute);
        },
        selectedDateStart() {
            this.selectedDateEnd = this.selectedDateStart;
            this.$emit('datechanged',this.selectedDateStart, this.selectedDateEnd);
            this.getTimes();
        },
        selectedDateEnd() {
            this.$emit('datechanged',this.selectedDateStart, this.selectedDateEnd);
            this.getTimes();
        },
        selectedManager() {
            this.$emit('managerchanged', this.selectedManager);
        },
        selectedDriver() {
            this.$emit('driverchanged', this.selectedDriver);
        },
        selectedTime() {
            if(this.selectedTime === 'Все'){
                this.$emit('timechanged','');
            } else {
                this.$emit('timechanged',this.selectedTime);
            }
        }
    },
    methods:{
        getTimes(){
            if((this.selectedDateStart !== '') && (this.selectedDateStart == this.selectedDateEnd)){
                this.times = [];
                axios.post('/dispatcher/timesByDate', {
                    'date': this.selectedDateStart,
                    'category_id': 1,
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
        getDrivers(){
            axios.get('/dispatcher/drivers')
                .then( response => {
                this.drivers = response.data;
            })
        },
    },
    computed:{

    }
}
</script>

<style lang="scss" scoped>
.mini-input-date {
    /*width: 180px !important*/
}
.mini-input-time {
    /*width: 180px !important*/
}

/*.mini-list-top{*/
/*    z-index: 0;*/
/*	display:flex;*/
/*	justify-content:space-between;*/
/*	align-items:center;*/
/*	.filters{*/
/*		display: flex;*/
/*	}*/
/*}*/
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