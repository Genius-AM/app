<template>
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
            <div class="modal-header">
                <h5> Редактирование заявки</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="$emit('close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Дата заявки</label>
                            <div class="">
                            <input type="date" class="form-control" v-model="selectedDate" @change="getBookedTime">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Время заявки</label>
                            <div>
                                <select name="" class="form-control" v-model="selectedTime">
                                    <option value="" disabled>Выберите время</option>
                                    <option v-for="time in this.booked_time" :value="time.time_id" :key="time.time_id">{{ time.time }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Точка заявки</label>
                            <div>
                                <select name="" class="form-control" v-model="selectedPoint">
                                    <option value="" disabled>Выберите точку</option>
                                    <option v-for="point in this.pointes" :value="point.id" :key="point.id">{{ point.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Адрес заявки</label>
                            <div>
                                <input type="text" v-model="selectedAddress" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="category_id === 1">
                        <div class="form-group col-sm-6">
                            <label>Предоплата</label>
                            <div class="">
                                <input type="text" v-model="prepayment" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Цена</label>
                            <div class="">
                                <input type="text" v-model="price" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row" v-else>
                        <div class="form-group col-sm-12">
                            <label>Предоплата</label>
                            <div class="">
                                <input type="text" v-model="prepayment" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>Женщин </label>
                            <div class="">
                                <input type="number" v-model="women" class="form-control" @change="changeWomen">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Мужчин</label>
                            <div class="">
                                <input type="number" v-model="men" class="form-control" @change="changeMen">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Детей</label>
                            <div class="">
                                <input type="number" v-model="kids" class="form-control" @change="changeKids">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Номер телефона (осн.) </label>
                            <div class="">
                                <input type="text" v-model="phone" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Номер телефона (доп.) </label>
                            <div class="">
                                <input type="text" v-model="second_phone" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="$emit('close')">Закрыть</button>
                    <button type="button" class="btn btn-primary" @click="saveOrder">Сохранить</button>
                </div>
            </div>
        </div>
      </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['orderId'],
        components: {
        },
        data() {
            return {
                pointes : [],
                category_id: 1,
                times: [],
                selectedDate:'',
                selectedTime: '',
                selectedPoint: '',
                selectedAddress: '',
                prepayment: 0,
                price: 0,
                phone: "",
                second_phone: "",
                men: 0,
                women: 0,
                kids: 0,
                order: {},
                booked_time : []
            }
        },
        mounted() {
            this.getAddress();
            this.getOrder(this.orderId);
        },
        methods: {
            getAddress() {
                axios.get('/lists/pointes/info')
                    .then(response => {
                        this.pointes = response.data.data;
                    });
            },
            changeWomen() {
                if (this.women === '') {
                    this.women = 0;
                }
            },
            changeMen() {
                if (this.men === '') {
                    this.men = 0;
                }
            },
            changeKids() {
                if (this.kids === '') {
                    this.kids = 0;
                }
            },
            getBookedTime(){
                this.booked_time = [];
                let vm = this;
                vm.selectedTime = '';
                axios.post('/dispatcher/booked_time', {
                    'date': this.selectedDate,
                    'category_id': this.category_id,
                }).then(response => {
                    var array = [];
                    response.data.forEach(function (el) {
                        if (el.booked === 0) {
                            array.push(el);

                            if (el.time == vm.momentTime(vm.order.time)) {
                                vm.selectedTime = el.time_id;
                            }
                        }
                    });

                    this.booked_time = array;
                })
            },
            getOrder(id) {
                axios.post('/dispatcher/order/info/' + id).then(response => {
                    this.order = response.data.order;
                    this.men = this.order.men;
                    this.women = this.order.women;
                    this.kids = this.order.kids;
                    this.prepayment = this.order.prepayment;
                    this.price = this.order.price;
                    this.phone = this.order.client.phone;
                    this.second_phone = this.order.client.phone_2;
                    this.selectedPoint = this.order.point_id;
                    this.selectedAddress = this.order.address;
                    this.selectedDate = response.data.order.date;
                    this.getBookedTime();
                });
            },
            saveOrder() {
                // определяем маршрут
                let route = '';
                let vm = this;
                if (this.selectedTime) {
                    this.booked_time.forEach(function (el) {
                        if (el.time_id == vm.selectedTime) {
                            route = el.route_id;
                        }
                    })
                } else {
                    this.$notify({
                        group: 'success',
                        type: 'error',
                        title: 'Ошибка',
                        text: 'Не выбрано время!',
                        duration: 3000,
                    });

                    return;
                }

                if (this.amountOFPassengers() > 8) {
                    this.$notify({
                        group: 'success',
                        type: 'error',
                        title: 'Ошибка',
                        text: 'Количество пассажиров больше 8!',
                        duration: 3000,
                    });

                    return;
                }

                axios.post('/dispatcher/order/change/' + this.orderId, {
                    'date': this.selectedDate,
                    'time': this.selectedTime,
                    'route': route,
                    'men': this.men,
                    'women': this.women,
                    'kids': this.kids,
                    'prepayment': this.prepayment,
                    'price': this.price,
                    'phone' : this.phone,
                    'second_phone' : this.second_phone,
                    'address' : this.selectedAddress,
                    'point' : this.selectedPoint
                }).then(response => {
                    this.$notify({
                        group: 'success',
                        type: 'success',
                        title: 'Успех',
                        text: 'Заявка была изменена!',
                        duration: 3000,
                    });
                    this.$emit('close');
                }).catch(response => {
                    let error = 'Не удалось сохранить заявку, неверные параметры!';
                    if (response.response.data) {
                        error = response.response.data.message;
                    }

                    this.$notify({
                        group: 'success',
                        type: 'error',
                        title: 'Ошибка!',
                        text: error,
                        duration: 3000,
                    });
                });
            },

            amountOFPassengers() {
                return Number(this.men) + Number(this.women) + Number(this.kids);
            }
        }
    }
</script>

<style scoped>
  * {
    box-sizing: border-box;
  }

  .modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .5);
    transition: opacity .3s ease;
  }

  .modal-container {
    width: 60%;
    margin: 40px auto 0;
    /*padding: 20px 30px;*/
    background-color: #fff;
    background-clip: padding-box;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
    transition: all .3s ease;
    font-family: Helvetica, Arial, sans-serif;
  }

  .modal-header {
      padding-bottom: 0;
  }


  .modal-footer {
      padding-left: 0;
      padding-right: 0;
  }

  .modal-header ul {
      border: 0;
      margin: 0;
  }

  .modal-header h3 {
    margin-top: 0;
    color: #42b983;
  }

  .modal-body {
    margin: 20px 0;
    max-height: 600px;
    overflow-y: auto;
  }

  .text-right {
    text-align: right;
  }

  .form-label {
    display: block;
    margin-bottom: 1em;
  }

  .form-label > .form-control {
    margin-top: 0.5em;
  }

  .form-control {
    display: block;
    width: 100%;
    padding: 0.5em 1em;
    line-height: 1.5;
    border: 1px solid #ddd;
  }

  .modal-enter {
    opacity: 0;
  }

  .modal-leave-active {
    opacity: 0;
  }

  .modal-enter .modal-container,
  .modal-leave-active .modal-container {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
  }
</style>