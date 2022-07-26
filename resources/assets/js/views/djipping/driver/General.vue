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
                    <h3 class="text-center">Общий отчет по водителям</h3>
                    <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>№ </th>
                            <th>Водитель</th>
                            <th>Количество отказанных человек</th>
                            <th>Количество отказанных человек после принятия</th>
                            <th>Количество принятых человек</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(driver, key) in drivers">
                            <td> {{ key + 1}} </td>
                            <td> {{ driver.name }}</td>
                            <td> {{ driver.reject_count }} </td>
                            <td> {{ driver.reject_after_accept_count }}</td>
                            <td> {{ driver.accept_count }}</td>
                        </tr>
                        <tr>
                            <td> Итого </td>
                            <td> {{  }}</td>
                            <td> {{ rejectCount() }} </td>
                            <td> {{ rejectAfterAcceptCount() }}</td>
                            <td> {{ acceptCount() }}</td>
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
        created() {
            this.getDrivers();
            this.getAllDrivers();
        },
        beforeRouteUpdate(to, from, next) {
            this.loading = true;
            axios.get(`/reports/djipping/driver/general`, {
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
                axios.get(`/reports/djipping/driver/general`, {
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
            rejectCount() {
                let count = 0;
                    this.drivers.forEach(function(res) {
                   return count = count + res.reject_count
                });

                return count;
            },
            rejectAfterAcceptCount() {
                let count = 0;
                    this.drivers.forEach(function(res) {
                   return count = count + res.reject_after_accept_count
                });

                return count;
            },
            acceptCount() {
                let count = 0;
                    this.drivers.forEach(function(res) {
                   return count = count + res.accept_count
                });

                return count;
            },
            loadExcel() {
                axios({
                    url: '/reports/djipping/driver/general-excel',
                    data: {
                        drivers: JSON.stringify(this.drivers)
                    },
                    method: 'POST',
                    responseType: 'arraybuffer',
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'driver-general.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            }
        }
    }
</script>

<style scoped>

</style>