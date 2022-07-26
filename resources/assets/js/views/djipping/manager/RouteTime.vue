<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="gelen__main__wrapper">
          <div class="preloader" v-show="loading">
            <span>Загружается...</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <multiselect
            :close-on-select="false"
            :multiple="true"
            :options="options"
            @close="getManagers"
            deselect-label="Нажмите для удаления"
            label="name"
            placeholder="Выберите из списка"
            select-label="Нажмите для выбора"
            selected-label="Выбран"
            track-by="id"
            v-model="value">
          <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single"
                                                                                   v-if="values.length &amp;&amp; !isOpen">{{
              values.length
            }} пользователей выбрано</span></template>
        </multiselect>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-wrap">
          <div class="row">
            <h3 class="col-12 text-center">Отчёт по времени маршрутов</h3>
          </div>
          <div class="row">
            <div class="col-12">
              <button @click="loadExcel" class="btn btn-success float-right"><span class="fa fa-book"></span> Excel</button>
            </div>
          </div>

          <div class="search-table-outter">
            <table class="table">
              <thead>
              <tr>
                <th>№</th>
                <th>Менеджер</th>
                <th v-for="time in times" style="width: 50px">
  <!--                <div class="row d-flex justify-content-between">-->
  <!--                  <div v-for="time in times" style="margin-right: 5px; margin-left: 5px">{{ time }}</div>-->
  <!--                  <div class="col-sm-2">08:00</div>-->
  <!--                  <div class="col-sm-2">09:00</div>-->
  <!--                  <div class="col-sm-2">12:00</div>-->
  <!--                  <div class="col-sm-2">15:00</div>-->
  <!--                  <div class="col-sm-2">16:00</div>-->
  <!--                  <div class="col-sm-2">21:00</div>-->
                  {{ time }}
  <!--                </div>-->
                </th>
                <th>Всего</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(manager, key, index) in managers">
                <td> {{ key + 1 }}</td>
                <td> {{ manager.name }}</td>
                <td v-for="time in times" style="width: 50px">

  <!--              <td>-->
  <!--                <div class="row d-flex justify-content-between">-->
  <!--                  <div v-for="time in times" style="margin-right: 5px; margin-left: 5px">-->
                      {{ manager.orders[time + ":00"] !== undefined ? manager.orders[time + ":00"].count : 0 }}
  <!--                  </div>-->
  <!--                </div>-->
                </td>
                <td>{{ totalCount(manager.orders) }}</td>
              </tr>
              <tr>
                <td> Итого</td>
                <td></td>
                <td v-for="time in times" style="width: 50px">

  <!--              <td>-->
  <!--                <div class="row d-flex justify-content-between">-->
  <!--                  <div v-for="time in times" style="margin-right: 5px; margin-left: 5px">{{ getTotalCountByTime([time + ":00"]) }}</div>-->
                  {{ getTotalCountByTime([time + ":00"]) }}
  <!--                </div>-->
                </td>
                <td> {{ getTotalCountByTimeQ() }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import axios from 'axios';

export default {
  props: {
    dateStart: String,
    dateEnd: String,
  },
  data() {
    return {
      loading: true,
      times: [],
      managers: [],
      value: null,
      options: [],
      selectedIds: [],
    }
  },
  components: {Multiselect},
  created() {
    axios.get(`/reports/djipping/manager/time-for-route-time`).then(response => {
      console.log(response);
      this.times = response.data;
    }).finally(() => {
      this.loading = false
    });

    this.getManagers();
    this.getAllManagers();
  },
  beforeRouteUpdate(to, from, next) {
    this.loading = true;

    axios.get(`/reports/djipping/manager/route-time`, {
      params: {
        start: this.dateStart,
        end: this.dateEnd,
        managers: this.selectedIds
      }
    }).then(response => {
      this.managers = response.data;
    }).finally(() => {
      this.loading = false
    });
  },
  watch: {
    value(newValues) {
      this.selectedIds = newValues.map(obj => obj.id);
    },
    dateStart() {
      this.getManagers();
    },
    dateEnd() {
      this.getManagers();
    }
  },
  methods: {
    getTotalCountByTimeQ() {
      let result = 0;

      _.forEach(this.managers, manager => {
        _.forEach(manager.orders, order => {
          result += order.count;
        });
      });

      return result;
    },
    getTotalCountByTime(time) {
      let count = 0;

      _.forEach(this.managers, manager => {
        if (manager.orders[time] !== undefined) {
          count += manager.orders[time].count;
        }
      });

      return count;
    },
    totalCount(orders) {
      let total = 0;
      _.forEach(orders, function (order) {
        total += order.count
      });

      return total;
    },
    getAllManagers() {
      axios.get(`/lists/users/1/1`).then(response => {
        this.options = response.data.data;
      });
    },
    getManagers() {
      this.loading = true;
      axios.get(`/reports/djipping/manager/route-time`, {
        params: {
          start: this.dateStart,
          end: this.dateEnd,
          managers: this.selectedIds
        }
      }).then(response => {
        this.managers = response.data;
      }).finally(() => {
        this.loading = false
      })
    },
    loadExcel() {
      axios.post('/reports/djipping/manager/route-time-excel', {
            start: this.dateStart,
            end: this.dateEnd,
            managers: this.selectedIds
          }, {
            responseType: 'arraybuffer'
          }
      ).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'route-time.xlsx');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      });
    },
    getAmountOFPassengers(men, women, kids) {
      return (men + women + kids);
    },
    getAddress(text, length) {
      if (typeof text === 'object' && text != null) {
        if (text.name != null && text.name.length > length) {
          let cut_str = text.name.substring(0, length + 1);
          return cut_str.trim() + '...';
        } else return text.name;
      }

      if (text != null && text.length > length) {
        let cut_str = text.substring(0, length + 1);
        return cut_str.trim() + '...';
      } else if (text != null) {
        return text
      } else return 'Нет адреса';
    },
  }
}
</script>

<style lang="scss" scoped>
.table th p {
  margin-bottom: 1rem !important;
  text-align: center !important;
}

.table {

  th, td {
    &:nth-child(1) {
      width: 6%;
      min-width: 50px;
    }

    &:nth-child(2) {
      width: 9%;
      min-width: 50px;
    }

    &:last-child {
      width: 9%;
      min-width: 50px;
    }
  }
  //table-layout: fixed;
  //border-collapse: collapse;
}
.table {
  //thead tr{
  //  display:block;
  //  width: 100%;
  //}
  //tbody {
  //  display:block;
  //  overflow:auto;
  //  height:200px;
  //  width:100%;
  //}
}

.search-table-outter {
  height: 420px !important;
  overflow-x:auto;
  overflow-y:auto;
}
</style>
