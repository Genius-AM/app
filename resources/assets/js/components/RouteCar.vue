<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-5 col-md-5">
                        <div class="form-item">
                            <legend>Выбор машины</legend>
                            <select id="subcategory" name="subcategory" class="nice-select" v-model="route_car.car" :disabled="disabled" required>
                                <option value="">Не выбрана</option>
                                <option v-for="(car, index) in cars" :value="car.id" :key="index">
                                    {{ car.name }}
                                    {{ car.driver && car.driver.company ? '(' + car.driver.company.name + ')' : '' }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1">
                        <div class="form-item">
                            <legend>Длина</legend>
                            <input type="time" name="duration" class="custom-date-picker mini-input-date" v-model="route_car.duration">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-item">
                            <legend>Предоплата</legend>
                            <input type="text" name="prepayment" v-model="route_car.prepayment" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за мужчину (Соло)</legend>
                            <input type="text" name="price_men" v-model="route_car.price_men" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за женщину (Тандем)</legend>
                            <input type="text" name="price_women" v-model="route_car.price_women" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за ребёнка (Трио)</legend>
                            <input type="text" name="price_kids" v-model="route_car.price_kids" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-item">
                            <legend>Цена</legend>
                            <input type="text" name="price" v-model="route_car.price" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="check-item">
                            <input type="checkbox" class="checkbox" id="payable" v-model="route_car.payable" name="is_payable"/>
                            <label for="payable">Зачислять на баланс</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="float-left">
                            <a :href="'/admin/route/cars/' + route.id"><button type="button" class="btn btn-danger">Назад</button></a>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-green" @click="createRouteCar">{{ this.button_name }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['cars', 'route', 'routeCar'],
        data() {
            return {
                route_car : {
                    car: "",
                    prepayment: "",
                    payable: false,
                    price: null,
                    price_men: null,
                    price_women: null,
                    price_kids: null,
                    duration: null,

                },
                disabled: false,
                button_name: null,
            }
        },
        mounted() {
            if (Object.keys(this.routeCar).length > 0) {
                this.route_car.car = this.routeCar.car_id;
                this.route_car.prepayment = this.routeCar.prepayment;
                this.route_car.payable = this.routeCar.is_payable;
                this.route_car.price = this.routeCar.price;
                this.route_car.price_men = this.routeCar.price_men;
                this.route_car.price_women = this.routeCar.price_women;
                this.route_car.price_kids = this.routeCar.price_kids;
                this.route_car.duration = this.momentTime(this.routeCar.duration);
                this.disabled = true;
                this.button_name = 'Изменить';
            } else {
                this.route_car.prepayment = this.route.prepayment;
                this.route_car.payable = this.route.is_payable;
                this.route_car.price = this.route.price;
                this.route_car.price_men = this.route.price_men;
                this.route_car.price_women = this.route.price_women;
                this.route_car.price_kids = this.route.price_kids;
                this.route_car.duration = this.momentTime(this.route.duration);
                this.button_name = 'Создать';
            }
        },
        methods: {
            createRouteCar() {
                let route_car = '';
                if (Object.keys(this.routeCar).length > 0) {
                    route_car = this.routeCar.id;
                }
                axios.post('/admin/route/cars/' + this.route.id + '/create/' + route_car, this.route_car).then(response => {
                    this.notifyResponse(response);
                }).catch(err => {
                    this.catchErrors(err);
                })
            }
        },
    }
</script>

<style scoped>

</style>