<template>
    <div>
        <modal-dispatcher-request
            v-if="showDispatcherModal"
            @close="orderChange()"
            :order-id="this.idDispatcherOrder"
        />
        <div class="gelen__main__wrapper gelen__template_columns">
            <div class="gelen__left__wrapper">
                <div class="d-flex justify-content-betwee">
                    <div class="d-flex mx-auto">
                        <h3>Заявки менеджеров</h3>
                        <h3 class="mx-2">{{ amount }}</h3>
                    </div>
                    <h3 class="mx-2">{{ successAmount }}</h3>
                </div>
                <draggable v-if="managers.length > 0" ghost-class="ghost" class="ndragArea"  tag="div" :list="managers" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                    <div v-for="(el, key, index) in managers" :key="index" v-if="el.tasks.length > 0" class="new_gelen__manager__wrap">
                        <div class="gelen__manager__name__wrap">
                            <h4>{{ el.region }}</h4>
                            <h5>{{ el.name }}</h5>
                            <h5>{{ el.point }}</h5>
                        </div>

                        <draggable ghost-class="ghost" class="new_dragArea" tag="div" :list="el.tasks" :group="{ name: 'g1', pull:true, put:false }" :sort="false">
                            <div v-for="el1 in el.tasks"
                                 class="new_gelen__order__table__wrap gelen__order__table__wrap gelen_manager_order" :key="el1.id"
                                :class="{ jeep__main__wrapper_decline: el1.status_id === 5 || el1.status_id === 8 }">

                                <div class="gelen__address__time__block">
                                    <span v-if="el1.food == 1" class="rest_span">
                                        <img src="/images/ic_restaurant_24px.svg" alt="">
                                    </span>
                                    <span class="address_span" :title="getAddressWithPrepayment(el1.address, el1.prepayment, el1.price)" v-text="getAddress(el1.address, 10)"></span>
                                    <div class="time_span date__time__wrapper">
                                        <span>{{ momentTime(el1.time) }}</span>
                                    </div>
                                </div>
                                <div class="gelen__address__time__block">
                                    <span class="route_span" v-text="getShortName(el1.route.name)"></span>
                                    <span class="comment_span" :title="el1.client.comment" v-text="getAddress(el1.client.comment, 9)"></span>
                                    <div class="date__time__wrapper">
                                        <span class="date">{{ momentShort(el1.date) }}</span>
                                    </div>
                                </div>

                                <div class="gelen__pax__edit__status__wrapper">
                                    <div class="gelen__pax__wrap">
                                        <div :class="el1.route.color">{{ el1.men }}</div>
                                        <div :class="el1.route.color">{{ el1.women }}</div>
                                        <div :class="el1.route.color">{{ el1.kids }}</div>
                                    </div>
                                    <div class="amount_apassengers">{{getAmountOFPassengers(el1.men, el1.women, el1.kids)}}</div>
                                    <div class="edit__wrap">
                                        <div class="payment__wrap">
                                            <div class="phone-block-order">
                                                <div>{{ getCleanText( el1.client.phone )}}</div>
                                                <div>{{getCleanText( el1.client.phone_2 )}}</div>
                                            </div>
                                            <div class="money-block">
                                              <div><strong>П {{ el1.prepayment }}</strong></div>
                                              <div><strong>Ц {{ el1.price }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div align="center" class="gelen__buttons__block">
                                    <button title="Редактировать" class="btn-primary fa fa-pen-square btn-border" @click="showEditOrderModal(el1.id)">
                                    </button>

                                    <button title="Отменить" class="btn-danger fa fa-ban btn-border" @click="cancelOrderFromManager(el1.id)">
                                    </button>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </draggable>
            </div>
            <div class="gelen__right__wrapper">
                <h3 class="text-center">Наши машины</h3>
                <draggable v-if="cars.length > 0" ghost-class="ghost" class="right_new_dragArea"  tag="div" :list="cars" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                    <div v-for="el in cars" :key="el.name">
                        <div class="jeep__main__wrapper_driver jeep__main__wrapper">
                            <span>
                                {{ el.car_number }}
                            </span>
                        </div>

                        <draggable ghost-class="ghost" class="new_dragArea"  tag="div" :list="el.tasks" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                            <div v-for="(el1, iIndex) in el.tasks" :key="el1.name" class="jeep__main__wrapper"
                                 :class="{
                                    jeep__main__wrapper_empty: el1.excursion === undefined,
                                    jeep__main__wrapper_sent: el1.excursion !== undefined && el1.excursion.status_id === 3,
                                    jeep__main__wrapper_reservation: el1.excursion !== undefined && el1.excursion.status_id === 6,
                                    jeep__main__wrapper_decline: el1.excursion !== undefined && el1.excursion.status_id === 5,
                                    }">
                                <div class="buttons"></div>
                                <div class="jeep__contents">
                                    <div class="exc__timing">
                                        <div v-if="el1.excursion !== undefined && el1.excursion.capacity - el1.excursion.people>=0" class="jeep__excurssion__left">
                                            {{ el1.excursion.people }}
                                        </div>
                                        <div class="jeep__excurssion__left" v-else>
                                            0
                                        </div>

                                        <div v-if="el1.excursion !== undefined" class="jeep__excurssion__date">{{ moment(el1.excursion.date) }}</div>
                                        <div v-if="el1.excursion !== undefined" class="jeep__excurssion__time">{{ momentTime(el1.excursion.time) }}</div>
                                        <div v-else class="jeep__excurssion__time">00:00</div>

                                        <div class="car_buttons_block">
                                            <div v-if="el1.excursion !== undefined && (el1.excursion.status_id === 6 || el1.excursion.status_id !== 6)">
                                                <button @click.prevent="send(el1.excursion)" :title="el1.excursion.status_id === 3?'Вернуть':'Отправить'" class="jeep__send__btn btn_on_dnd">
                                                    <img width="18" src="/images/plane.svg" alt="">
                                                </button>
                                            </div>
                                            <div v-if="el1.excursion !== undefined && el1.excursion.status_id !== 3 && (el1.excursion.status_id === 6 || el1.excursion.status_id.status_id !== 6)">
                                                <button @click.prevent="book(el1.excursion)" :title="el1.excursion.status_id === 6?'Открепить':'Закрепить'" class="jeep__reserve__btn btn_on_dnd">
                                                    <img width="18" src="/images/save.svg" alt="">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sub__content">
                                        <div v-show="el1.tasks.length>0" v-for="order in el1.tasks" class="jeep__main__group">
                                            <div class="jeep__family__group">
                                                <div class="jeep__client__address">
                                                    <div class="cancel__link" v-if="el1.excursion !== undefined && el1.excursion.status_id !== 3 && el1.excursion.status_id !== 6">
                                                        <a href="#" class="btn_on_dnd fas fa-times-circle" @click.prevent="cancelOrder(order.id,el1.excursion.id)"></a>
                                                    </div>
                                                    <span :title="getAddressWithPrepayment(order.address, order.prepayment, order.price)" :class="{driver_agreed_order: order.status_id == 4}" v-text="getAddress(order.address, 3)"></span>
                                                </div>
                                                <div class="jeep__pax">
                                                    <div :class="order.route.color">{{ order.men }}</div>
                                                    <div :class="order.route.color">{{ order.women }}</div>
                                                    <div :class="order.route.color">{{ order.kids }}</div>
                                                </div>
                                            </div>
                                            <div class="jeep__btn__group">
                                                <button title="Редактировать" class="btn-primary fa fa-pen-square btn-border" style="align-self: flex-start" @click="showEditOrderModal(order.id, el1.excursion.id)"></button>
                                                <button title="Отменить" class="btn-danger fa fa-ban btn-border" style="align-items: flex-end" @click="cancelOrderFromManager(order.id, order.id, el1.excursion.id, el1.excursion.status_id)"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <draggable ghost-class="ghost" class="lastDragArea"  tag="div" :list="el1.tasks" :group="{ name: 'g1', pull:false, put:true }">
                                    <div v-for="el11 in el1.tasks" :key="el11.name"></div>
                                </draggable>
                            </div>
                        </draggable>
                    </div>
                </draggable>

                <h3 class="text-center">Машины партнеров</h3>
                <draggable v-if="partnerCars.length > 0" ghost-class="ghost" class="right_new_dragArea"  tag="div" :list="partnerCars" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                    <div v-for="el in partnerCars" :key="el.name">
                        <div class="jeep__main__wrapper_driver jeep__main__wrapper">
                            <span>
                                {{ el.car_number }}
                            </span>
                        </div>

                        <draggable ghost-class="ghost" class="new_dragArea"  tag="div" :list="el.tasks" :group="{ name: 'g1', pull:false, put:false }" :sort="false">
                            <div v-for="(el1, iIndex) in el.tasks" :key="el1.name" class="jeep__main__wrapper jeep__main__wrapper_empty">
                                <div class="buttons"></div>
                                <div class="jeep__contents">
                                    <div class="exc__timing">
                                        <div v-if="el1.excursion !== undefined && el1.excursion.capacity - el1.excursion.people>=0" class="jeep__excurssion__left">
                                            {{ el1.excursion.people }}
                                        </div>
                                        <div class="jeep__excurssion__left" v-else>
                                            0
                                        </div>

                                        <div v-if="el1.excursion !== undefined" class="jeep__excurssion__date">{{ moment(el1.excursion.date) }}</div>
                                        <div v-else class="jeep__excurssion__date">00.00.0000</div>
                                    </div>
                                    <div class="sub__content">
                                        <div v-show="el1.tasks.length>0" v-for="order in el1.tasks" class="jeep__main__group">
                                            <div class="jeep__family__group">
                                                <div class="jeep__client__address">
                                                    <div class="cancel__link" v-if="el1.excursion !== undefined && el1.excursion.status_id !== 3 && el1.excursion.status_id !== 6">
                                                        <a href="#" class="btn_on_dnd fas fa-times-circle" @click.prevent="cancelOrder(order.id,el1.excursion.id)"></a>
                                                    </div>
                                                    <span :title="momentTime(order.time) + ', ' + getAddressWithPrepayment(order.address, order.prepayment, order.price)" v-text="getAddress(order.address, 6)"></span>
                                                </div>
                                                <div class="jeep__pax">
                                                    <div :class="order.route.color">{{ order.men }}</div>
                                                    <div :class="order.route.color">{{ order.women }}</div>
                                                    <div :class="order.route.color">{{ order.kids }}</div>
                                                </div>
                                            </div>
                                            <div class="jeep__btn__group">
                                                <button title="Редактировать" class="btn-primary fa fa-pen-square btn-border" style="align-self: flex-start" @click="showEditOrderModal(order.id, el1.excursion.id)"></button>
                                                <button title="Отменить" class="btn-danger fa fa-ban btn-border" style="align-items: flex-end" @click="cancelOrderFromManager(order.id, order.id, el1.excursion.id)"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <draggable ghost-class="ghost" class="lastDragArea"  tag="div" :list="el1.tasks" :group="{ name: 'g1', pull:false, put:true }">
                                    <div v-for="el11 in el1.tasks" :key="el11.name"></div>
                                </draggable>
                            </div>
                        </draggable>
                    </div>
                </draggable>
            </div>
        </div>
    </div>
</template>
<script>
    import draggable from 'vuedraggable'
    import nestedDraggable from "./Nested";
    import modalDispatcherRequest from './modals/modal-edit-dispatcher-request';

    export default {
        props: ['managers', 'cars', 'partnerCars'],
        components: {
            draggable,
            nestedDraggable,
            modalDispatcherRequest
        },
        data() {
            return {
                showDispatcherModal: false,
                idDispatcherOrder: '',
            };
        },
        watch: {
            //следим за изменением наших машин
            carsForWatcher(newValue, oldValue) {
                let chosenCarId = null;
                let chosenRaceNumber = null;
                let chosenOrder = null;

                if(oldValue.length === 0) return;

                for(let item in newValue){
                    for(let iItem in newValue[item].tasks){
                        if( newValue[item].tasks[iItem].tasks.length !== oldValue[item].tasks[iItem].tasks.length){
                            chosenCarId = newValue[item].id;
                            chosenRaceNumber = +iItem+1;

                            //если 0, то значит запись тут первая
                            if(oldValue[item].tasks[iItem].tasks.length === 0){
                                chosenOrder = newValue[item].tasks[iItem].tasks[0];
                            } else {
                                let new_tasks = newValue[item].tasks[iItem].tasks;
                                let old_tasks = oldValue[item].tasks[iItem].tasks;

                                for(let inside_index in new_tasks){
                                    if(old_tasks[inside_index] === undefined){
                                        chosenOrder = newValue[item].tasks[iItem].tasks[inside_index];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                //если все переменные есть, то отправляем на перемещение заявки
                if(chosenCarId !== null && chosenRaceNumber !== null && chosenOrder !== null){
                    this.sendNewOrderInCar(chosenCarId, chosenRaceNumber, chosenOrder);
                }
            },
            //следим за изменением наших машин
            partnerCarsForWatcher(newValue, oldValue) {
                let chosenCarId = null;
                let chosenRaceNumber = null;
                let chosenOrder = null;

                if(oldValue.length === 0) return;

                for(let item in newValue){
                    for(let iItem in newValue[item].tasks){
                        if( newValue[item].tasks[iItem].tasks.length !== oldValue[item].tasks[iItem].tasks.length){
                            chosenCarId = newValue[item].id;
                            chosenRaceNumber = +iItem+1;

                            //если 0, то значит запись тут первая
                            if(oldValue[item].tasks[iItem].tasks.length === 0){
                                chosenOrder = newValue[item].tasks[iItem].tasks[0];

                            } else {
                                let new_tasks = newValue[item].tasks[iItem].tasks;
                                let old_tasks = oldValue[item].tasks[iItem].tasks;

                                for(let inside_index in new_tasks){
                                    if(old_tasks[inside_index] === undefined){
                                        chosenOrder = newValue[item].tasks[iItem].tasks[inside_index];

                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                //если все переменные есть, то отправляем на перемещение заявки
                if(chosenCarId !== null && chosenRaceNumber !== null && chosenOrder !== null){
                    this.sendNewOrderInPartnerCar(chosenCarId, chosenRaceNumber, chosenOrder);
                }
            },
        },
        methods: {
            cancelOrderFromManager(id, order_id = null, exc_id = null, exc_status_id = null) {
                if (order_id && exc_id) {
                    if (exc_status_id === 3) {
                        axios.post('/dispatcher/order/push-notification/' + order_id).then(response => {});
                    }
                }

                axios.post('/dispatcher/order/cancel/' + id, {
                    'with_excursion': exc_id ? true : false,
                    'order' : order_id,
                    'exc' : exc_id
                }).then(response => {
                    this.$emit('orderCanceled');
                });
            },
            orderChange() {
                this.showDispatcherModal = false;
                this.$emit('orderCanceled');
            },
            showEditOrderModal(id, exc_id = null) {
                this.idDispatcherOrder = id;
                this.showDispatcherModal = true;
            },
            /**
             * Возвращает сумму принятые объектов
             */
            getAmountOFPassengers(men, women, kids){
                return (men+women+kids);
            },
            /**
             * Убираем лишние пробелы с текста
             */
            getCleanText(text){
                let new_text = '';
                if(text != null){
                    new_text = text.replace(/ /g, '');
                    new_text = new_text.replace(/\(/g, '');
                    new_text = new_text.replace(/\)/g, '');
                    new_text = new_text.replace(/-/g, '');
                }
                else new_text = '';

                return new_text;
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
            /**
             * Режет строку с адресов
             */
            getAddress(text, length) {
                if (typeof text === 'object' && text != null) {
                    if (text.name != null && text.name.length > length) {
                        let cut_str = text.name.substring(0, length + 1);
                        return cut_str.trim() + '...';
                    } else return text.name;
                }

                if (text != null && text.length > length) {
                    let cut_str = text.substring(0, length + 1);
                    return cut_str.trim() + '...';
                } else return text;
            },
            /**
             * Возврат адреса + предоплаты
             * формат: "ул. Весеняя, д.1, предоплата 300"
             */
            getAddressWithPrepayment(address, prepayment, price) {
                if (typeof address === 'object' && address != null) {
                    return `${address.name}, предоплата ${prepayment}, цена ${price}`;
                }

                return `${address}, предоплата ${prepayment}, цена ${price}`;
            },
            //Формирование новой заявки
            sendNewOrderInCar(chosenCarId, chosenRaceNumber, chosenOrder) {
                axios.post('/dispatcher/assign-order-to-car',{
                    order: chosenOrder,
                    chosenCarId: chosenCarId,
                    chosenRaceNumber: chosenRaceNumber,
                }).then(response=>{
                    if(response.data.success === 201){
                        this.showMessage = response.data.message;
                        this.$emit('orderAssigninCar', response.data.message, 201);
                    } else {
                        this.$emit('orderAssigninCar', null, 200);
                        this.showMessage = false;
                    }
                })
            },
            //Формирование новой заявки для партнера
            sendNewOrderInPartnerCar(chosenCarId, chosenRaceNumber, chosenOrder) {
                axios.post('/dispatcher/assign-order-to-partner-car',{
                    order: chosenOrder,
                    chosenCarId: chosenCarId,
                    chosenRaceNumber: chosenRaceNumber,
                }).then(response=>{
                    if(response.data.success === 201){
                        this.showMessage = response.data.message;
                        this.$emit('orderAssigninCar', response.data.message, 201);
                    } else {
                        this.$emit('orderAssigninCar', null, 200);
                        this.showMessage = false;
                    }
                })
            },
            //Отправка экскурсии
            send(excursion) {
                let confirmation = confirm("Вы в этом уверены?");
                if(confirmation){
                    this.loading = true;
                    axios.post('/dispatcher/excursion/send', {
                        excursion: excursion.id
                    }).then(response => {
                        this.$emit('orderCanceled');
                        this.$notify({
                            group: 'success',
                            type: 'success',
                            title: 'All OK',
                            text: response.data.message ? response.data.message : 'Экскурсия отправлена',
                            duration: 3000,
                        });
                        this.loading = false;
                    }).catch(err => {
                    })
                }
            },
            //Отправка экскурсии
            book(excursion) {
                let confirmation = confirm("Вы в этом уверены?");
                if(confirmation){
                    this.loading = true;
                    axios.post('/dispatcher/excursion/book', {
                        excursion: excursion.id
                    }).then(response => {
                        // console.log(response.data);
                        this.$emit('orderCanceled');
                        this.$notify({
                            group: 'success',
                            type: 'success',
                            title: 'All OK',
                            text: response.data.message,
                            duration: 3000,
                        });
                        this.loading = false;
                    }).catch(err => {
                    })
                }
            },
            //Отмена единичной заявки у экскурсии
            cancelOrder(orderId, excId) {
                let confirmation = confirm("Вы в этом уверены?");
                if(confirmation){
                    axios.post('/dispatcher/cancel/order/' + orderId + '/' + excId)
                        .then(response => {
                        if (response.data.success === 200) {
                            this.$emit('orderCanceled');
                            this.$notify({
                                group: 'success',
                                type: 'success',
                                title: 'Успех',
                                text: response.data.message,
                                duration: 3000,
                            });
                        }
                    })
                }
            }
        },
        computed: {
            /**
             * Функция для слежки от lodash
             *  это дает:
             *      1. глубокий смотр(видит изменения внутренних элементов тоже)
             *      2. отдает новые и старые значения
             * @returns {*}
             */
            carsForWatcher() {
                return _.cloneDeep(this.cars);
            },
            /**
             * Функция для слежки от lodash
             *  это дает:
             *      1. глубокий смотр(видит изменения внутренних элементов тоже)
             *      2. отдает новые и старые значения
             * @returns {*}
             */
            partnerCarsForWatcher() {
                return _.cloneDeep(this.partnerCars);
            },

            amount: function () {
                return this.managers.reduce((sum, manager) => {
                    return sum + manager.tasks.reduce((sumTask, task) => sumTask + task.men + task.women + task.kids, 0);
                }, 0);
            },

            carsAmount: function () {
                return this.cars.reduce((amount, car) => {
                    return amount + car.tasks.reduce((summa, task) => {
                        return summa + (task.excursion ? task.excursion.people : 0);
                    }, 0);
                }, 0);
            },

            partnerCarsAmount: function () {
                return this.partnerCars.reduce((amount, car) => {
                    return amount + car.tasks.reduce((summa, task) => {
                        return summa + (task.excursion ? task.excursion.people : 0);
                    }, 0);
                }, 0);
            },

            successAmount: function () {
                return this.carsAmount + this.partnerCarsAmount;
            },
        },
    };
</script>

<style lang="scss" scoped>
    .sortable-chosen{
        position: relative !important;
        z-index: 9999 !important;
        opacity:1 !important;
        .gelen__address__time__block, .gelen__pax__edit__status__wrapper{
            position: relative !important;
            z-index: 9999 !important;
            top: 0;
            left: 0;
        }
    }

    .lastDragArea{
        .sortable-chosen{
            position: relative;
            z-index: 10;
        }
    }
</style>
