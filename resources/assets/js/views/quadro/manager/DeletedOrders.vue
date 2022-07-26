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

    <div class="row" v-if="!loading">
      <div class="col-sm-12">
        <div class="table-wrap">
          <h3 class="text-center">Удаленные заявки</h3>
          <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span> Excel</button>
          <table class="table">
            <thead>
              <tr>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Кол-во людей</th>
                <th>Время</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" class="border-top">
                <td>{{ order.client.name }}</td>
                <td>{{ order.client.phones }}</td>
                <td>{{ order.amount }}</td>
                <td>{{ order.date }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    dateStart: String,
    dateEnd: String,
  },
  data() {
    return {
      loading: true,
      orders: [],
    }
  },
  created() {
    this.getOrders();
  },
  beforeRouteUpdate(to, from, next) {
    this.getOrders();
  },
  watch: {
    dateStart() {
      this.getOrders();
    },
    dateEnd() {
      this.getOrders();
    }
  },
  methods: {
    getOrders() {
      this.loading = true;
      axios
          .get(`/reports/quadro/manager/deleted-orders`, {
            params: {
              start: this.dateStart,
              end: this.dateEnd,
            }
          })
          .then(response => {
            this.orders = response.data;
          })
          .finally(() => {
            this.loading = false
          });
    },
    loadExcel() {
      axios({
        url: '/reports/quadro/manager/deleted-orders-excel',
        data: {
          orders: this.orders
        },
        method: 'POST',
        responseType: 'arraybuffer',
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'deleted-orders.xlsx');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      });
    },
  }
}
</script>

<style lang="scss" scoped>
.preloader {
  height: unset;
}
</style>