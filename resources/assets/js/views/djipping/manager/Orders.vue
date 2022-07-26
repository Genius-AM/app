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
          <h3 class="text-center">Заявки</h3>
          <button class="btn btn-success float-right" @click="loadExcel"><span class="fa fa-book"></span>Excel</button>
          <table class="table">
            <thead>
            <tr>
              <th>Маршрут</th>
              <th>ФИО</th>
              <th>Телефон</th>
              <th>Кол-во людей</th>
              <th>Время</th>
              <th>Дата создания</th>
              <th>Статус</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(order, index) in orders">
              <tr class="border-top" @click="toggleRow(index)">
                <td>{{ order.route }}</td>
                <td>{{ order.client.name }}</td>
                <td>{{ order.client.phones }}</td>
                <td>{{ order.amount }}</td>
                <td>{{ order.date }}</td>
                <td>{{ order.created_at }}</td>
                <td>{{ order.status }}</td>
              </tr>
              <tr v-if="row === index">
                <td colspan="7" class="p-0" style="background-color: lightgray">
                  <div class="row">
                    <div class="col">
                      <div class="font-weight-bold">Менеджер</div>
                      <div>{{ order.manager }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Водитель</div>
                      <div>{{ order.driver.name }} {{ order.driver.phone }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Цена</div>
                      <div>{{ order.price }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Предоплата</div>
                      <div>{{ order.prepayment }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Мужчины</div>
                      <div>{{ order.men }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Женщины</div>
                      <div>{{ order.women }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Дети</div>
                      <div>{{ order.kids }}</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="font-weight-bold">Адрес</div>
                      <div>{{ order.address }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Комментарий</div>
                      <div>{{ order.client.comment }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Отклонил</div>
                      <div>{{ order.refuser }}</div>
                    </div>
                    <div class="col">
                      <div class="font-weight-bold">Причина отмены</div>
                      <div>{{ order.reason }}</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col" v-for="(value, key) in order.ageCategories">
                      <div class="font-weight-bold">{{ key }}</div>
                      <div>{{ value }}</div>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
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
      row: null,
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
    toggleRow(row) {
      if (this.row === row) {
        this.row = null;
      } else {
        this.row = row;
      }
    },
    getOrders() {
      this.$nextTick(function () {
        this.orders = [];
        this.loading = true;
        axios
            .get(`/reports/djipping/manager/orders`, {
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
      })
    },
    loadExcel() {
      axios({
        url: '/reports/djipping/manager/orders-excel',
        data: {
          orders: this.orders
        },
        method: 'POST',
        responseType: 'arraybuffer',
      }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'orders.xlsx');
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