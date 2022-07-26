<template>
<div>
<div class="gelen__main__wrapper">
    <div v-show="loading" class="preloader">
        <span>Загружается...</span>
    </div>
        <div class="gelen__left__wrapper">
                <div v-show="manager.orders.length > 0" v-for="(manager,index) in fetchedManagersWithOrders" class="gelen__manager__wrap">
                <div class="gelen__manager__name__wrap">
                    <h4>{{ manager.id }}</h4>
                    <h5>{{ manager.name }}</h5>
                </div>
            </div>
            </div>            
</div>
</div>
</template>

<script>
import OrderFilter from './OrderFilter.vue';
    import { EventBus } from '../app.js';
    export default {
        props: ['catId'],
        components:{
        OrderFilter,
        },
        data(){
            return{
                loading: true,
                allDrivers:[],
                orderToPassToModal:{},
                showDriverModal:false,
                fetchedManagersWithOrders:[],
                filters:{
                    selectedRoute:'',
                    selectedDate:'',
                    selectedManager:''
                }
            }
        },
        created(){
            this.loading=true;
             EventBus.$on('ordercanceled', this.getManagers);
            this.getManagers();
        },
        watch:{
            filters:{
                  handler(){
                    this.getManagers();
                     },
                     deep: true
            }
        },
        methods: {
            saveDrivers(drivers){
                this.allDrivers = drivers;
            },
            onOrderAssigned(){
                this.$notify({
                  group: 'success',
                  type: 'success',
                  title: 'All OK',
                  text: 'Order has been assigned to a driver!',
                  duration: 10000,
                });

                this.getManagers();
            },
            openDriverModal(order){
                this.orderToPassToModal = order;
                this.showDriverModal = ! this.showDriverModal;
            },
            closeModal(){
                this.showDriverModal = false;
            },
            getManagers(){
                this.loading = true;
                       axios.post('/dispatcher/managers',{
                        'catId':this.catId,
                        'route':this.filters.selectedRoute,
                        'date':this.filters.selectedDate,
                        'managerId':this.filters.selectedManager
                            }).then(response=>{
                               this.fetchedManagersWithOrders = response.data;
                               this.loading = false;
                            }).catch(err=>{}) 
            },
          routeChanged(selectedRoute){
            this.filters.selectedRoute = selectedRoute;
          },
          dateChanged(selectedDate){
            this.filters.selectedDate = selectedDate;
          },
            managerChanged(selectedManager){
            this.filters.selectedManager = selectedManager;
          }
        }
    }
</script>
<style lang="scss" scoped>
    .gelen__main__wrapper{
        position:relative;
        .preloader{
            position:absolute;
            width:100%;
            height:100vh;
            background: #F3F3F3;
            top:0;
            left:0;
            z-index:11;
            display:flex;
            justify-content: center;   
            span{
                padding-top:200px;
                color:#00b4ff;
            }
        }
    }
 
</style>
