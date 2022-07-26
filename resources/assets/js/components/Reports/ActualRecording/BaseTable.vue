<template>
  <div class="table_wrapper">
    <table>
      <thead>
      <tr>
        <th>№</th>
        <th>Менеджер</th>
        <th v-for="category in sortedCategories">{{ category.name }}</th>
        <th>Общее количество</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(manager, key) in managers" class="border-top">
        <td>{{ key + 1 }}</td>
        <td>{{ manager.name }}</td>
        <td v-for="category in sortedCategories">
          <div v-if="result[`${manager.id}_${category.id}`].accept != null && result[`${manager.id}_${category.id}`].reject != null">
            <span class="text-success">{{ result[`${manager.id}_${category.id}`].accept }}</span> / <span class="text-danger">{{ result[`${manager.id}_${category.id}`].reject }}</span>
          </div>
        </td>
        <td>
          <div v-if="result[manager.id]">
            <span class="text-success">{{ result[manager.id].accept }}</span> / <span class="text-danger">{{ result[manager.id].reject }}</span>
          </div>
        </td>
      </tr>
      <tr class="border-top">
        <td colspan="2">Итого</td>
        <td v-for="category in sortedCategories">
          <div v-if="result[category.id]">
            <span class="text-success">{{ result[category.id].accept }}</span> / <span class="text-danger">{{ result[category.id].reject }}</span>
          </div>
        </td>
        <td>
          <span class="text-success">{{ result.accept }}</span> / <span class="text-danger">{{ result.reject }}</span>
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
    result: function () {
      const result = {accept: 0, reject: 0};

      for (const manager of this.managers) {
        result[manager.id] = {accept: 0, reject: 0};

        for (const category of this.sortedCategories) {
          if (!result.hasOwnProperty(category.id)) {
            result[category.id] = {accept: 0, reject: 0}
          }

          result[`${manager.id}_${category.id}`] = this.getValue(manager.id, category.id);

          result[manager.id].accept += result[`${manager.id}_${category.id}`].accept || 0;
          result[manager.id].reject += result[`${manager.id}_${category.id}`].reject || 0;

          result[category.id].accept += result[`${manager.id}_${category.id}`].accept || 0;
          result[category.id].reject += result[`${manager.id}_${category.id}`].reject || 0;
        }

        result.accept += result[manager.id].accept;
        result.reject += result[manager.id].reject;
      }

      return result;
    },
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
  },
  methods: {
    getValue(manager, category) {
      let accept = null;
      let reject = null;

      for (const categoryElement of this.report) {
        if (categoryElement.id === category) {
          for (const managerElement of categoryElement.data.managers) {
            if (managerElement.id === manager) {
              accept = 0;
              reject = 0;

              if (managerElement.routes) {
                for (const routeElement of Object.values(managerElement.routes)) {
                  accept += routeElement.accept;
                  reject += routeElement.reject;
                }
              }
              break;
            }
          }
          break;
        }
      }

      return {accept, reject};
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