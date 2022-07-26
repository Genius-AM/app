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
                        v-model="filterSelectedAddresses"
                        :options="filterAllAddresses"
                        :close-on-select="false"
                        track-by="id"
                        label="name"
                        placeholder="Выберите из списка"
                        select-label="Нажмите для выбора"
                        selected-label="Выбран"
                        deselect-label="Нажмите для удаления"
                        @close="getAddresses">
                    <template slot="selection" slot-scope="{ values, search, isOpen }">
                        <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} адресов выбрано</span>
                    </template>
                </multiselect>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-wrap">
                    <h3 class="text-center">Общий отчёт по адресам менеджеров</h3>
                    <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>№ </th>
                            <th>Адрес</th>
                            <th>Менеджер</th>
                            <th>
                                Человек на адрес
                            </th>
                            <th>Общее количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(address, key) in addresses">
                            <td> {{ key + 1 }}</td>
                            <td>
                                {{ address.name }}
                            </td>
                            <td>
                                <span v-for="(manager) in address.managers">
                                    {{ manager.name }} <br>
                                </span>
                            </td>
                            <td>
                                <span v-for="(manager) in address.managers">
                                    {{ manager.total }} <br>
                                </span>
                            </td>
                            <td>
                                {{ totalSum(address.managers) }}
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
                addresses: [],
                filterAllAddresses: [],
                filterSelectedAddresses: [],
                filterSelectedAddressesIds: []
            }
        },
        components: { Multiselect },
        created() {
            this.getAddresses();
            this.getAllAddressesForSelect();
        },
        beforeRouteUpdate(to, from, next) {
            this.loading = true;
            axios.get(`/reports/djipping/manager/address`, {
                params: {
                    start: this.dateStart,
                    end: this.dateEnd,
                    addresses: this.filterSelectedAddressesIds
                }
            }).then(response => {
                this.addresses = response.data;
            }).finally(() => {this.loading = false});
        },
        methods: {
            getAllAddressesForSelect() {
                axios.get('/lists/pointes/info')
                    .then(response => {
                        this.filterAllAddresses = response.data.data;
                        this.filterAllAddresses.push({'id': null, 'name': 'Без адреса'});
                    })
            },
            getAddresses() {
                this.loading = true;
                axios.get(`/reports/djipping/manager/address`, {
                    params: {
                        start: this.dateStart,
                        end: this.dateEnd,
                        addresses: this.filterSelectedAddressesIds
                    }
                }).then(response => {
                    this.addresses = response.data;
                }).finally(() => {this.loading = false})
            },
            totalSum(managers) {
                let count = 0;
                _.forEach(managers, manager => count += manager.total);

                return count;
            },
            loadExcel() {
                axios({
                    url: '/reports/djipping/manager/address-excel',
                    data: {
                        addresses: JSON.stringify(this.addresses)
                    },
                    method: 'POST',
                    responseType: 'arraybuffer',
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'address-general.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                });
            },
        },
        watch: {
            filterSelectedAddresses(addresses) {
                this.filterSelectedAddressesIds = addresses.map(address => address.id);
            },
            dateStart() {
                this.getAddresses();
            },
            dateEnd() {
                this.getAddresses();
            }
        },
    }
</script>

<style scoped>

</style>