<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Логин</legend>
                            <input type="text" name="login" v-model="user.login">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Пароль</legend>
                            <input type="password" name="password" v-model="user.password">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Повторите пароль</legend>
                            <input type="password" name="password_confirmation" v-model="user.password_confirmation">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Имя</legend>
                            <input type="text" name="name" autocomplete="false"  v-model="user.name">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Регион</legend>
                            <input type="text" name="region" autocomplete="false"  v-model="user.region">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-item">
                            <legend>Телефон</legend>
                            <input type="text" class="input" name="phone" v-model="user.phone">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-item">
                            <legend>Адрес</legend>
                            <input type="text" name="address" autocomplete="false" v-model="user.address">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-item">
                            <legend>Роль</legend>
                            <selectize placeholder="Выберите роль" v-model="user.role_id">
                                <option v-for="(role, index) in roles" :value="role.id" :key="index"> {{ role.name }} </option>
                            </selectize>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-item">
                            <legend>Категория</legend>
                            <selectize placeholder="Выберите категорию" v-model="user.category_id">
                                <option v-for="(category, index) in categories" :value="category.id" :key="index"> {{ category.name }} </option>
                            </selectize>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="user.category_id == 3">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-item">
                            <legend>Компания</legend>
                            <selectize placeholder="Выберите компанию" v-model="user.company_id">
                                <option v-for="(company, index) in companies" :value="company.id" :key="index"> {{ company.name }} </option>
                            </selectize>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="float-left">
                            <a href="/admin/users"><button type="button" class="btn btn-danger">Назад</button></a>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-green" @click="createUser">Создать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Selectize from 'vue2-selectize'

    export default {
        props: ['roles', 'categories', 'companies'],
        components: {
            Selectize
        },
        data() {
            return {
                user : {
                    login: "",
                    password: "",
                    password_confirmation: "",
                    name: "",
                    region: "",
                    phone: "",
                    address: "",
                    role_id: "",
                    category_id: "",
                    company_id: ""
                }
            }
        },
        created() {
        },
        methods: {
            createUser() {
                axios.post('/admin/user/create', this.user).then(response => {
                    this.$notify({
                        group: 'success',
                        type: 'success',
                        title: 'Успех',
                        text: response.data.message,
                        duration: 3000,
                    });

                    window.location.href = '/admin/users';
                }).catch(err => {
                    this.catchErrors(err);
                })
            }
        },
    }
</script>

<style scoped>

</style>