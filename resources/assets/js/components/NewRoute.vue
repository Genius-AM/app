<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-2 col-md-2">
                        <div class="form-item">
                            <legend>Название маршрута</legend>
                            <input type="text" name="name" v-model="route.name" required>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1">
                        <div class="form-item">
                            <legend>Длина</legend>
                            <input type="time" name="duration" class="custom-date-picker mini-input-date" v-model="route.duration">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-item">
                            <legend>Категория</legend>
                            <select id="category" name="category" class="nice-select" v-model="route.category_id" @change="getSubcategoies()" required>
                                <option value="">Не выбрана</option>
                                <option v-for="(category, index) in categories" :value="category.id" :key="index"> {{ category.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-item">
                            <legend>Подкатегория</legend>
                            <select id="subcategory" name="subcategory" class="nice-select" v-model="route.subcategory_id" required>
                                <option value="">Не выбрана</option>
                                <option v-for="(subcategory, index) in subcategories" :value="subcategory.id" :key="index"> {{ subcategory.name }}</option>
                            </select>

                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-item">
                            <legend>Предоплата</legend>
                            <input type="text" name="prepayment" v-model="route.prepayment" required>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1">
                        <div class="form-item">
                            <legend>Цвет</legend>
                            <select id="color" name="color" class="nice-select" v-model="route.color" required>
                                <option v-for="(color, index) in colors" :value="index" :key="index"> {{ color }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="route.category_id != 3">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за мужчину (Соло)</legend>
                            <input type="text" name="price_men" v-model="route.price_men" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за женщину (Тандем)</legend>
                            <input type="text" name="price_women" v-model="route.price_women" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Цена за ребёнка (Трио)</legend>
                            <input type="text" name="price_kids" v-model="route.price_kids" required>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="route.category_id == 3">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-item">
                            <legend>Цена</legend>
                            <input type="text" name="price" v-model="route.price" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="check-item">
                            <input type="checkbox" class="checkbox" id="payable" v-model="route.payable" name="is_payable"/>
                            <label for="payable">Зачислять на баланс</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="float-left">
                            <a href="/admin/routes/all"><button type="button" class="btn btn-danger">Назад</button></a>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-green" @click="createRoute">Создать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['categories', 'colors'],
        data() {
            return {
                subcategories: [],
                route : {
                    name: "",
                    category_id: "",
                    subcategory_id: "",
                    prepayment: "",
                    payable: false,
                    price: null,
                    price_men: null,
                    price_women: null,
                    price_kids: null,
                    color: null,
                    duration: null
                }
            }
        },
        methods: {
            getSubcategoies() {
                this.route.subcategory_id = "";
                axios.get('/admin/new/route/subcategories', {
                    params: {
                        id: this.route.category_id
                    }
                }).then(response => {
                    this.subcategories = response.data;
                }).catch(err => {
                    this.catchErrors(err);
                })
            },
            createRoute() {
                axios.post('/admin/new/route', this.route).then(response => {
                    this.notifyResponse(response);

                    window.location.href = '/admin/routes/all';
                }).catch(err => {
                    this.catchErrors(err);
                })
            }
        },
    }
</script>

<style scoped>

</style>