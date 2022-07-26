<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="gelen__main__wrapper">
                    <div v-show="loading" class="preloader">
                        <span>Загружается...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <multiselect
                        :multiple="true"
                        v-model="value"
                        :options="options"
                        label="name"
                        track-by="id"
                        placeholder="Выберите из списка"
                        select-label="Нажмите для выбора"
                        selected-label="Выбран"
                        deselect-label="Нажмите для удаления"
                        :close-on-select="false"
                        @close="getDrivers">
                    <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} пользователей выбрано</span></template>
                </multiselect>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-wrap">
                    <h3 class="text-center">Отчет по маршрутам водителей</h3>
                    <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>№ </th>
                            <th>Водитель</th>
                            <th>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <b>Маршрут</b>
                                    </div>
                                    <div class="col-sm-8">
                                        <b>Принятые / Отказанные / Отказанные после принятия</b>
                                    </div>
                                </div>
                            </th>
                            <th>Общее количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(driver, key, index) in drivers.drivers">
                            <td> {{ index + 1 }} </td>
                            <td> {{ driver.name }} </td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div v-for="route in driver.routes">
                                            <span class="route__color__grid__wrapper">
                                                <span class="color__grid__item">
                                                    <span :class="route.color ? route.color : ''"></span>
                                                    <span class="test">{{ route.name }}</span>
                                                </span>
                                            </span> <br>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div v-for="route in driver.routes">
                                            <span align="center">
                                                <span class="">
                                                    <span style="color: green">{{ route.accept }}</span> / <span style="color: red">{{ route.rejectorder }}</span> / <span style="color: red">{{ route.rejectafteracceptorder }}</span>
                                                </span> <br>
                                            </span> <br>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="color: green">{{ sumAccept(driver) }}</span> / <span style="color: red">{{ sumReject(driver) }}</span> / <span style="color: red">{{ sumRejectAfterAccept(driver) }}</span> <br>
                            </td>
                        </tr>

                        <tr v-if="hasDrivers()">
                            <td> Итого </td>
                            <td></td>
                            <td></td>
                            <td>
                                <span style="color: green">{{ totalAccept() }}</span> / <span style="color: red">{{ totalReject() }}</span> / <span style="color: red">{{ totalRejectAfterAccept() }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    export default {
        props: {
            dateStart: String,
            dateEnd: String,
        },
        data() {
            return {
                loading: true,
                drivers: [],
                value: null,
                options: [],
                selectedIds: [],
            }
        },
        components: { Multiselect },
        mounted() {
            this.getDrivers();
            this.getAllDrivers();
        },
        beforeRouteUpdate(to, from, next) {
            this.loading = true;
            axios.get(`/reports/djipping/driver/route`, {
                params: {
                    start: this.dateStart,
                    end: this.dateEnd,
                    drivers: this.selectedIds

                }
            }).then(response => {
                this.drivers = response.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        watch: {
            value(newValues) {
                this.selectedIds = newValues.map(obj => obj.id);
            },
            dateStart() {
                this.getDrivers();
            },
            dateEnd() {
                this.getDrivers();
            }
        },
        methods: {
            getAllDrivers() {
                axios.get(`/lists/users/3/1`).then(response => {
                    this.options = response.data.data;
                });
            },
            getDrivers() {
                this.loading = true;
                axios.get(`/reports/djipping/driver/route`, {
                    params: {
                        start: this.dateStart,
                        end: this.dateEnd,
                        drivers: this.selectedIds
                    }
                }).then(response => {
                    this.drivers = response.data;
                }).finally(() => {
                    this.loading = false;
                });
            },
            hasDrivers() {
                return this.drivers.length !== 0;
            },
            sumAccept(driver) {
                let count = 0;
                let array = driver.routes;
                _.forEach(array, function (res) {
                    count = Number(count) + Number(res.accept);
                });

                return count;
            },
            sumReject(driver) {
                let count = 0;
                let array = driver.routes;
                _.forEach(array, function (res) {
                    count = count + res.rejectorder;
                });

                return count;
            },
            sumRejectAfterAccept(driver) {
                let count = 0;
                let array = driver.routes;
                _.forEach(array, function (res) {
                    count = count + res.rejectafteracceptorder;
                });

                return count;
            },
            loadExcel() {
                axios({
                    url: '/reports/djipping/driver/route-excel',
                    data: {
                        drivers: JSON.stringify(this.drivers)
                    },
                    method: 'POST',
                    responseType: 'arraybuffer',
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'driver-route.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            },
            totalAccept() {
                let total = this.drivers.total;
                let count = 0;
                _.forEach(total, function (res) {
                    count = count + res.accept;
                });

                return count;
            },
            totalReject() {
                let total = this.drivers.total;
                let count = 0;
                _.forEach(total, function (res) {
                    count = count + res.reject;
                });

                return count;
            },
            totalRejectAfterAccept() {
                let total = this.drivers.total;
                let count = 0;
                _.forEach(total, function (res) {
                    count = count + res.rejectafteraccept;
                });

                return count;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .route__color__grid__wrapper{
        display:flex;
        padding-right: 30px;
        .color__grid__item{
            display:flex;
            align-items: center;
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