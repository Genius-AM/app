<template>
  <div class="table_wrapper">
    <table>
      <thead>
      <tr>
        <th>№</th>
        <th>Менеджер</th>
        <th v-for="ageCategory in ageCategories">{{ ageCategory.name }}</th>
        <th>Всего</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(manager, key) in managers" class="border-top">
        <td>{{ key + 1 }}</td>
        <td>{{ manager.name }}</td>
        <td v-for="ageCategory in ageCategories">
          {{ getManagerValue(manager, ageCategory) }}
        </td>
        <td>
          {{ getManagerAmount(manager) }}
        </td>
      </tr>
      <tr class="border-top" v-if="Object.keys(report.ages).length">
        <td colspan="2">Итого</td>
        <td v-for="ageCategory in ageCategories">
          {{ report.ages[ageCategory.id] }}
        </td>
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
  },
  methods: {
    getManagerValue(manager, ageCategory) {
      let value = 0;

      for (const categoryElement of this.report.categories) {
        for (const managerElement of categoryElement.data.managers) {
          if (managerElement.id === manager.id) {
            value += managerElement.ages[ageCategory.id];
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