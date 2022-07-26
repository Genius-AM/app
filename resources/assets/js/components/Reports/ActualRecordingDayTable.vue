<template>
  <div class="table_wrapper">
    <table>
      <thead>
      <tr>
        <th rowspan="2">№</th>
        <th rowspan="2">Менеджер</th>
        <th rowspan="2">Маршрут</th>
        <th :colspan="days.length" v-for="category in sortedCategories">{{ category.name }}</th>
        <th rowspan="2">Общее количество</th>
      </tr>
      <tr>
        <template v-for="category in sortedCategories">
          <th v-for="day in days">{{ formatDate(day) }}</th>
        </template>
      </tr>
      </thead>
      <tbody>
      <template v-for="(manager, key) in managers">
        <tr v-for="(route, index) in getRoutes(manager.id)" :class="{'border-top': index === 0}">
          <td v-if="index === 0" :rowspan="getRoutes(manager.id).length + 1">{{ key + 1 }}</td>
          <td v-if="index === 0" :rowspan="getRoutes(manager.id).length + 1">{{ manager.name }}</td>
          <td>{{ route.name }}</td>
          <template v-for="category in sortedCategories">
            <td v-for="day in route.days">
              <div v-if="category.id === route.category_id">
                <span class="text-success">{{ day.accept }}</span> / <span class="text-danger">{{ day.reject }}</span>
              </div>
            </td>
          </template>
          <td>
            <span class="text-success">{{ route.accept }}</span> / <span class="text-danger">{{ route.reject }}</span>
          </td>
        </tr>

        <tr>
          <td>Итого</td>
          <template v-for="category in sortedCategories">
            <td v-for="day in days">
              <span class="text-success">{{ getManagerDaySumma(category.id, manager.id, day).accept }}</span> / <span class="text-danger">{{ getManagerDaySumma(category.id, manager.id, day).reject }}</span>
            </td>
          </template>
          <td>
            <span class="text-success">{{ getManagerSumma(manager.id).accept }}</span> / <span class="text-danger">{{ getManagerSumma(manager.id).reject }}</span>
          </td>
        </tr>
      </template>

      <tr class="border-top">
        <td colspan="3">Итого</td>
        <template v-for="category in sortedCategories">
          <td v-for="day in days">
            <span class="text-success">{{ getCategoryDaySumma(category.id, day).accept }}</span> / <span class="text-danger">{{ getCategoryDaySumma(category.id, day).reject }}</span>
          </td>
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
        return Object.keys(Object.values(this.report[0].data.managers[0].routes)[0].days);
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
            break;
          }
        }
      }

      return routes;
    },
    getManagerDaySumma(categoryId, managerId, day) {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        if (item.id === categoryId) {
          for (const manager of item.data.managers) {
            if (manager.id === managerId) {
              for (const route of Object.values(manager.routes)) {
                accept += route.days[day].accept;
                reject += route.days[day].reject;
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
            for (const route of Object.values(manager.routes)) {
              accept += route.accept;
              reject += route.reject;
            }
            break;
          }
        }
      }

      return {accept, reject}
    },
    getCategoryDaySumma(categoryId, day) {
      let accept = 0;
      let reject = 0;

      for (const item of this.report) {
        if (item.id === categoryId) {
          for (const manager of item.data.managers) {
            for (const route of Object.values(manager.routes)) {
              accept += route.days[day].accept;
              reject += route.days[day].reject;
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
          for (const route of Object.values(manager.routes)) {
            accept += route.accept;
            reject += route.reject;
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
}

table td,
table th {
  white-space: nowrap;
  padding: 10px;
}
</style>