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
          <h3 class="text-center">Взрослые / дети</h3>
          <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span>Excel</button>
          <table class="table">
            <thead>
              <tr>
                <th>Менеджер</th>
                <th>Взрослые</th>
                <th>Дети</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="order in orders">
                <tr class="border-top">
                  <td>{{ order.manager }}</td>
                  <td>{{ order.adults }}</td>
                  <td>{{ order.children }}</td>
                </tr>
              </template>

              <tr class="border-top">
                <td>Итого</td>
                <td>{{ allAdults }}</td>
                <td>{{ allChildren }}</td>
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
    subcategory: {
      type: [String, Number],
      default: null,
    },
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
  computed: {
    allAdults () {
      return this.orders.reduce((accumulator, item) => accumulator + item.adults, 0);
    },
    allChildren () {
      return this.orders.reduce((accumulator, item) => accumulator + item.children, 0);
    },
  },
  watch: {
    dateStart() {
      this.getOrders();
    },
    dateEnd() {
      this.getOrders();
    },
    subcategory() {
      this.getOrders();
    },
  },
  methods: {
    getOrders() {
      this.$nextTick(function () {
        this.orders = [];
        this.loading = true;
        axios
            .get(`/reports/sea/manager/adults-children`, {
              params: {
                start: this.dateStart,
                end: this.dateEnd,
                subcategory: this.subcategory,
              }
            })
            .then(response => {
              this.orders = response.data;
            })
            .finally(() => {
              this.loading = false
            });
      })
    },
    loadExcel() {
      axios({
        url: '/reports/sea/manager/adults-children-excel',
        data: {
          orders: this.orders
        },
        method: 'POST',
        responseType: 'arraybuffer',
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'adults-children.xlsx');
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