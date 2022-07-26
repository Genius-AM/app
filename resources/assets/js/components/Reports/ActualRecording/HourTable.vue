<template>
  <div class="table_wrapper">
    <table>
      <thead>
      <tr>
        <th rowspan="3">№</th>
        <th rowspan="3">Менеджер</th>
        <th rowspan="3">Маршрут</th>
        <th :colspan="days.length * hours.length" v-for="category in sortedCategories">{{ category.name }}</th>
        <th rowspan="3">Общее количество</th>
      </tr>
      <tr>
        <template v-for="category in sortedCategories">
            <th :colspan="hours.length" v-for="day in days">{{ formatDate(day) }}</th>
        </template>
      </tr>
      <tr>
        <template v-for="category in sortedCategories">
          <template v-for="day in days">
              <th v-for="hour in hours">{{ hour }}</th>
          </template>
        </template>
      </tr>
      </thead>
      <tbody>
      <template v-for="(manager, key) in managers">
        <template v-if="getRoutes(manager.id).length">
          <tr v-for="(route, index) in getRoutes(manager.id)" :class="{'border-top': index === 0}">
            <td v-if="index === 0" :rowspan="getRoutes(manager.id).length + 1">{{ key + 1 }}</td>
            <td v-if="index === 0" :rowspan="getRoutes(manager.id).length + 1">{{ manager.name }}</td>
            <td>{{ route.name }}</td>
            <template v-for="category in sortedCategories">
              <template v-for="day in route.days">
                <td v-for="hour in hours">
                  <div v-if="category.id === route.category_id">
                    <span class="text-success">{{ day.times[hour].accept }}</span> / <span class="text-danger">{{ day.times[hour].reject }}</span>
                  </div>
                </td>
              </template>
            </template>
            <td>
              <span class="text-success">{{ route.accept }}</span> / <span class="text-danger">{{ route.reject }}</span>
            </td>
          </tr>

          <tr>
            <td>Итого</td>
            <template v-for="category in sortedCategories">
              <template v-for="day in days">
                <td v-for="hour in hours">
                  <span class="text-success">{{ getManagerHourSumma(category.id, manager.id, day, hour).accept }}</span> / <span class="text-danger">{{ getManagerHourSumma(category.id, manager.id, day, hour).reject }}</span>
                </td>
              </template>
            </template>
            <td>
              <span class="text-success">{{ getManagerSumma(manager.id).accept }}</span> / <span class="text-danger">{{ getManagerSumma(manager.id).reject }}</span>
            </td>
          </tr>
        </template>

        <tr v-else>
          <td>{{ key + 1 }}</td>
          <td>{{ manager.name }}</td>
          <td>Итого</td>
          <template v-for="category in sortedCategories">
            <template v-for="day in days">
              <td v-for="hour in hours">
                <span class="text-success">{{ getManagerHourSumma(category.id, manager.id, day, hour).accept }}</span> / <span class="text-danger">{{ getManagerHourSumma(category.id, manager.id, day, hour).reject }}</span>
              </td>
            </template>
          </template>
          <td>
            <span class="text-success">{{ getManagerSumma(manager.id).accept }}</span> / <span class="text-danger">{{ getManagerSumma(manager.id).reject }}</span>
          </td>
        </tr>
      </template>

      <tr class="border-top">
        <td colspan="3">Итого</td>
        <template v-for="category in sortedCategories">
          <template v-for="day in days">
            <td v-for="hour in hours">
              <span class="text-success">{{ getCategoryHourSumma(category.id, day, hour).accept }}</span> / <span class="text-danger">{{ getCategoryHourSumma(category.id, day, hour).reject }}</span>
            </td>
          </template>
        </template>
        <td>
          <span class="text-success">{{ getSumma().accept }}</span> / <span class="text-danger">{{ getSumma().reject }}</span>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import _ from "lodash";
import moment from "moment";

export default {
  props: {
    categories: {
      type: Array,
      required: true,
    },
    report: {
      type: Array,
      required: true,
    },
  },
  computed: {
    managers: function () {
      if (Object.keys(this.report).length !== 0) {
        const managers = [];

        for (const item of this.report) {
          for (const manager of item.data.managers) {
            managers.push({
              id: manager.id,
              name: manager.name,
            })
          }
        }

        return _.sortBy(_.uniqWith(managers, _.isEqual), ['name', 'id']);
      }

      return [];
    },
    sortedCategories: function () {
      return _.sortBy(this.categories, ['name', 'id']);
    },
    days: function () {
      if (Object.keys(this.report).length !== 0) {
        return this.report[0].data.days;
      }

      return [];
    },
    hours: function () {
      if (Object.keys(this.report).length !== 0) {
        return this.report[0].data.hours;
      }

      return [];
    },
  },
  methods: {
    formatDate(value) {
      return moment(value).format('DD.MM');
    },
    getRoutes(managerId) {
      const routes = [];

      for (const item of this.report) {
        for (const manager of item.data.managers) {
          if (manager.id === managerId) {
            if (manager.routes) {
              for (const route of Object.values(manager.routes)) {
                routes.push({
                  id: route.id,
                  name: route.name,
                  category_id: route.category_id,
                  accept: route.accept,
                  reject: route.reject,
                  days: route.days,
                })
              }
            }
            break;
          }
        }
      }

      return routes;
    },
    getManagerHourSumma(categoryId, managerId, day, hour) {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        if (item.id === categoryId) {
          for (const manager of item.data.managers) {
            if (manager.id === managerId) {
              if (manager.routes) {
                for (const route of Object.values(manager.routes)) {
                  accept += route.days[day].times[hour].accept;
                  reject += route.days[day].times[hour].reject;
                }
              }
              break;
            }
          }
          break;
        }
      }

      return {accept, reject}
    },
    getManagerSumma(managerId) {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        for (const manager of item.data.managers) {
          if (manager.id === managerId) {
            if (manager.routes) {
              for (const route of Object.values(manager.routes)) {
                accept += route.accept;
                reject += route.reject;
              }
            }
            break;
          }
        }
      }

      return {accept, reject}
    },
    getCategoryHourSumma(categoryId, day, hour) {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        if (item.id === categoryId) {
          for (const manager of item.data.managers) {
            if (manager.routes) {
              for (const route of Object.values(manager.routes)) {
                accept += route.days[day].times[hour].accept;
                reject += route.days[day].times[hour].reject;
              }
            }
          }
          break;
        }
      }

      return {accept, reject}
    },
    getSumma() {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        for (const manager of item.data.managers) {
          if (manager.routes) {
            for (const route of Object.values(manager.routes)) {
              accept += route.accept;
              reject += route.reject;
            }
          }
        }
      }

      return {accept, reject}
    },
  }
}
</script>

<style scoped>
.table_wrapper {
  overflow-x: auto;
  height: 90vh;
}

table td,
table th {
  white-space: nowrap;
  padding: 10px;
}

table thead {
  position: -webkit-sticky;
  position: -moz-sticky;
  position: -ms-sticky;
  position: -o-sticky;
  position: sticky;
  top: 0;
}
</style>