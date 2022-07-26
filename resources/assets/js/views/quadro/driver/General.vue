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
                        v-model="filterSelectedDrivers"
                        :options="filterAllDrivers"
                        :close-on-select="false"
                        track-by="id"
                        label="name"
                        placeholder="Выберите из списка"
                        select-label="Нажмите для выбора"
                        selected-label="Выбран"
                        deselect-label="Нажмите для удаления"
                        @close="getDrivers">
                    <template slot="selection" slot-scope="{ values, search, isOpen }">
                        <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} водителей выбрано</span>
                    </template>
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
                            <th>Количество принятых человек</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(driver, key) in drivers">
                            <td> {{ key + 1}} </td>
                            <td> {{ driver.name }}</td>
                            <td> {{ driver.accept_count }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";

    export default {
        props: {
            dateStart: String,
            dateEnd: String,
        },
        data() {
            return {
                role_driver: 3,
                category_quadike: 3,
                loading: true,
                drivers: [],
                filterAllDrivers: [],
                filterSelectedDrivers: [],
                filterSelectedDriversIds: []
            }
        },
        components: { Multiselect },
        created() {
            this.getDrivers();
            this.getAllDriversForSelect();
        },
        beforeRouteUpdate(to, from, next) {
            this.loading = true;
            axios.get(`/reports/quadro/driver/general`, {
                params: {
                    start: this.dateStart,
                    end: this.dateEnd,
                    drivers: this.filterSelectedDriversIds
                }
            }).then(response => {
                this.drivers = response.data;
            }).finally(() => {this.loading = false});
        },
        watch: {
            filterSelectedDrivers(drivers) {
                this.filterSelectedDriversIds = drivers.map(driver => driver.id);
            },
            dateStart() {
                this.getDrivers();
            },
            dateEnd() {
                this.getDrivers();
            }
        },
        methods: {
            getAllDriversForSelect() {
                axios.get('/lists/users/' + this.role_driver + '/' + this.category_quadike)
                    .then(response => {
                        this.filterAllDrivers = response.data.data;
                })
            },
            getDrivers() {
                this.loading = true;
                axios.get(`/reports/quadro/driver/general`, {
                    params: {
                        start: this.dateStart,
                        end: this.dateEnd,
                        drivers: this.filterSelectedDriversIds
                    }
                }).then(response => {
                    this.drivers = response.data;
                }).finally(() => {this.loading = false});
            },
            loadExcel() {
                axios({
                    url: '/reports/quadro/driver/general-excel',
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