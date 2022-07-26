<template>
  <div class="table_wrapper">
    <table>
      <thead>
        <tr>
          <th rowspan="2">№</th>
          <th rowspan="2">Менеджер</th>
          <th :colspan="days.length" v-for="ageCategory in ageCategories">{{ ageCategory.name }}</th>
          <th rowspan="2">Всего</th>
        </tr>
        <tr>
          <template v-for="ageCategory in ageCategories">
            <th v-for="day in days">{{ formatDate(day) }}</th>
          </template>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(manager, index) in managers" class="border-top">
          <td>{{ index + 1 }}</td>
          <td>{{ manager.name }}</td>
          <template v-for="ageCategory in ageCategories">
            <td v-for="day in days">
                {{ getManagerDayValue(manager, ageCategory, day) }}
            </td>
          </template>
          <td>
            {{ getManagerAmount(manager) }}
          </td>
        </tr>

        <tr class="border-top">
          <td colspan="2">Итого</td>
          <template v-for="ageCategory in ageCategories">
            <td v-for="day in days">
              {{ getCategoryDaySumma(ageCategory, day) }}
            </td>
          </template>
          <td>
            {{ report.ages.amount }}
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
    ageCategories: {
      type: Array,
      required: true,
    },
    report: {
      type: Object,
      required: true,
    },
  },
  computed: {
    managers: function () {
      if (Object.keys(this.report.categories).length !== 0) {
        const managers = [];

        for (const item of this.report.categories) {
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
    days: function () {
      if (Object.keys(this.report.categories).length !== 0) {
        return Object.keys(this.report.categories[0].data.managers[0].dates);
      }

      return [];
    },
  },
  methods: {
    formatDate(value) {
      return moment(value).format('DD.MM');
    },
    getManagerDayValue(manager, ageCategory, day) {
      let value = 0;

      for (const categoryElement of this.report.categories) {
        for (const managerElement of categoryElement.data.managers) {
          if (managerElement.id === manager.id) {
            value += managerElement.dates[day].ages[ageCategory.id];
            break;
          }
        }
      }

      return value;
    },
    getManagerAmount(manager) {
      let value = 0;

      for (const categoryElement of this.report.categories) {
        for (const managerElement of categoryElement.data.managers) {
          if (managerElement.id === manager.id) {
            value += managerElement.ages.amount;
            break;
          }
        }
      }

      return value;
    },
    getCategoryDaySumma(ageCategory, day) {
      let value = 0;

      for (const categoryElement of this.report.categories) {
        for (const managerElement of categoryElement.data.managers) {
          value += managerElement.dates[day].ages[ageCategory.id];
        }
      }

      return value;
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