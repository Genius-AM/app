<template>
  <div class="table_wrapper">
    <table>
      <thead>
        <tr>
          <th>Категория</th>
          <th>ФИО</th>
          <th>Телефон</th>
          <th>Кол-во людей</th>
          <th>Время</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" class="border-top">
          <td>{{ order.category }}</td>
          <td>{{ order.client.name }}</td>
          <td>{{ order.client.phones }}</td>
          <td>{{ order.amount }}</td>
          <td>{{ order.date }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import moment from "moment";

export default {
  props: {
    orders: {
      type: Object,
      required: true,
    },
  },
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