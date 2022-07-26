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
                        v-model="filterSelectedManagers"
                        :options="filterAllManagers"
                        :close-on-select="false"
                        track-by="id"
                        label="name"
                        placeholder="Выберите из списка"
                        select-label="Нажмите для выбора"
                        selected-label="Выбран"
                        deselect-label="Нажмите для удаления"
                        @close="getManagers">
                    <template slot="selection" slot-scope="{ values, search, isOpen }">
                        <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} менеджеров выбрано</span>
                    </template>
                </multiselect>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-wrap">
                    <h3 class="text-center">Общий отчет по менеджерам</h3>
                    <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>№ </th>
                            <th>Менеджер</th>
                            <th>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <b>Маршрут</b>
                                    </div>
                                    <div class="col-sm-8">
                                        <b>Всего / Принятые</b>
                                    </div>
                                </div>
                            </th>
                            <th>Общее количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(manager, key, index) in managers.managers">
                            <td> {{ index + 1 }} </td>
                            <td> {{ manager.name }} </td>
                            <td>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div v-for="route in manager.routes">
                                            <span class="route__color__grid__wrapper">
                                                <span class="color__grid__item">
                                                    <span :class="route.color ? route.color : ''"></span>
                                                    <span class="test">{{ route.name }}</span>
                                                </span>
                                            </span> <br>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div v-for="route in manager.routes">
                                            <span align="center">
                                                <span class="">
                                                    <span>{{ route.rejectorder }}</span> / <span style="color: green">{{ route.accept }}</span>
                                                </span> <br>
                                            </span> <br>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span>{{ sumReject(manager) }}</span> / <span style="color: green">{{ sumAccept(manager) }}</span><br>
                            </td>
                        </tr>

                        <tr v-if="hasManagers()">
                            <td> Итого </td>
                            <td></td>
                            <td></td>
                            <td>
                                <span>{{ totalReject() }}</span> / <span style="color: green">{{ totalAccept() }}</span>
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
    import Multiselect from "vue-multiselect";

    export default {
        props: {
            dateStart: String,
            dateEnd: String,
        },
        data() {
            return {
                loading: true,
                managers: [],
                filterAllManagers: [],
                filterSelectedManagers: [],
                filterSelectedManagersIds: []
            }
        },
        components: { Multiselect },
        created() {
            this.getManagers();
            this.getAllManagersForSelect();
        },
        beforeRouteUpdate(to, from, next) {
            this.loading = true;
            axios.get(`/reports/quadro/manager/general`, {
                params: {
                    start: this.dateStart,
                    end: this.dateEnd,
                    managers: this.filterSelectedManagersIds
                }
            }).then(response => {
                this.managers = response.data;
            }).finally(() => {this.loading = false});
        },
        watch: {
            dateStart() {
                this.getManagers();
            },
            dateEnd() {
                this.getManagers();
            },
            filterSelectedManagers(managers) {
                this.filterSelectedManagersIds = managers.map(manager => manager.id);
            }
        },
        methods: {
            getAllManagersForSelect() {
                axios.get('/lists/users/managers')
                    .then(response => {
                        this.filterAllManagers = response.data.data;
                    })
            },
            getManagers() {
                this.loading = true;
                axios.get(`/reports/quadro/manager/general`, {
                    params: {
                        start: this.dateStart,
                        end: this.dateEnd,
                        managers: this.filterSelectedManagersIds
                    }
                }).then(response => {
                    this.managers = response.data;
                }).finally(() => {this.loading = false});
            },
            hasManagers() {
                return this.managers.length !== 0;
            },
            sumAccept(manager) {
                let count = 0;
                let array = manager.routes;
                _.forEach(array, function (res) {
                    count = Number(count) + Number(res.accept);
                });

                return count;
            },
            sumReject(manager) {
                let count = 0;
                let array = manager.routes;
                _.forEach(array, function (res) {
                    count = count + res.rejectorder;
                });

                return count;
            },
            loadExcel() {
                axios({
                    url: '/reports/quadro/manager/general-excel',
                    data: {
                        managers: JSON.stringify(this.managers)
                    },
                    method: 'POST',
                    responseType: 'arraybuffer',
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'manager-general.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            },
            totalAccept() {
                let total = this.managers.total;
                let count = 0;
                _.forEach(total, function (res) {
                    count = count + res.accept;
                });

                return count;
            },
            totalReject() {
                let total = this.managers.total;
                let count = 0;
                _.forEach(total, function (res) {
                    count = count + res.reject;
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