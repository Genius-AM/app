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
            @close="getManagers">
          <template slot="selection" slot-scope="{ values, search, isOpen }">
            <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">
              {{ values.length }} пользователей выбрано
            </span>
          </template>
        </multiselect>
      </div>
    </div>

    <div class="row" v-if="!loading">
      <div class="col-sm-12">
        <div class="table-wrap">
          <h3 class="text-center">Фактическая запись</h3>
          <button class="btn btn-success float-right" @click="loadExcel">
            <span class="fa fa-book"></span> Excel
          </button>
          <table class="table">
            <thead>
            <tr>
              <th>№</th>
              <th>Менеджер</th>
              <th>Маршрут</th>
              <th>Принятые / Отказанные</th>
              <th>Общее количество</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(manager, key) in managers.managers">
              <td>{{ key + 1 }}</td>
              <td>{{ manager.name }}</td>
              <td :class="{'v-top': days}">
                <div class="col-sm-4" :class="{'div_days': days}">
                  <div v-for="route in manager.routes">
                      <span class="route__color__grid__wrapper">
                        <span class="color__grid__item">
                          <span :class="route.color ? route.color : ''"></span>
                          <span class="test">{{ route.name }}</span>
                        </span>
                      </span>
                  </div>
                </div>
              </td>
              <td>
                <div v-if="days && manager.routes && Object.keys(manager.routes).length" class="overflow-auto" :class="{'w-50vw': days}">
                  <table class="table">
                    <thead>
                      <tr>
                        <th v-for="(day, key) in manager.routes[Object.keys(manager.routes)[0]].days">
                          {{ formatDate(key) }}
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="route in manager.routes">
                        <td v-for="day in route.days" style="text-align: center">
                          <span style="color: green">{{ day.accept }}</span> / <span style="color: red">{{ day.reject }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-else>
                  <div v-for="route in manager.routes" style="text-align: center; padding: 16px 0;">
                      <span style="color: green">{{ route.accept }}</span> / <span style="color: red">{{ route.rejectorder }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span style="color: green">{{ sumAccept(manager) }}</span> / <span style="color: red">{{ sumReject(manager) }}</span>
              </td>
            </tr>

            <tr v-if="hasManagers">
              <td> Итого</td>
              <td></td>
              <td></td>
              <td></td>
              <td>
                <span style="color: green">{{ totalAccept() }}</span> / <span style="color: red">{{ totalReject() }}</span>
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
import moment from "moment";

export default {
  props: {
    dateStart: String,
    dateEnd: String,
    days: Boolean,
    subcategory: {
      type: [String, Number],
      default: null,
    },
  },
  data() {
    return {
      loading: true,
      managers: [],
      value: null,
      options: [],
      selectedIds: [],
    }
  },
  components: {Multiselect},
  created() {
    this.getManagers();
    this.getAllManagers();
  },
  beforeRouteUpdate(to, from, next) {
    this.loading = true;
    axios.get(`/reports/sea/manager/general`, {
      params: {
        start: this.dateStart,
        end: this.dateEnd,
        days: this.days,
        subcategory: this.subcategory,
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
    },
    days() {
      this.getManagers();
    },
    subcategory() {
      this.getManagers();
    },
  },
  methods: {
    getAllManagers() {
      axios.get(`/lists/users/1/1`).then(response => {
        this.options = response.data.data;
      });
    },
    getManagers() {
      this.loading = true;
      axios.get(`/reports/sea/manager/general`, {
        params: {
          start: this.dateStart,
          end: this.dateEnd,
          days: this.days,
          subcategory: this.subcategory,
          managers: this.selectedIds
        }
      }).then(response => {
        this.managers = response.data;
        this.loading = false;
      })
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
        url: '/reports/sea/manager/general-excel',
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
    },
    formatDate(value) {
      return moment(value).format('DD.MM');
    },
  }
}
</script>

<style lang="scss" scoped>
.route__color__grid__wrapper {
  display: flex;
  padding-right: 30px;
  padding-top: 16px;
  padding-bottom: 15px;

  .color__grid__item {
    display: flex;
    align-items: center;
    margin-left: 20px;

    span {
      &:first-child {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 10px;
      }

      &:last-child {
        font-size: 15px;
        white-space: nowrap;
      }
    }
  }
}
.div_days {
  margin-top: 68px;
}
.w-50vw {
  width: 50vw;
}
.v-top {
  vertical-align: top !important;
}
</style>
