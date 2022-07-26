<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <main-filter
            @changeCategory="categoryChanged"
            @changeSubcategory="subcategoryChanged"
            @changeRoute="routeChanged"
            @changeDate="dateChanged"
            @changeDays="daysChanged">
        </main-filter>
      </div>
    </div>

    <router-view
        :dateStart="dateStart"
        :dateEnd="dateEnd"
        :days="days"
        :subcategory="subcategory">
    </router-view>
  </div>
</template>

<script>
import MainFilter from "./Filter";

export default {
  components: {
    MainFilter
  },
  props: [],
  data() {
    return {
      route: '',
      subcategory: null,
      dateStart: null,
      dateEnd: null,
      days: false,
    }
  },
  methods: {
    categoryChanged() {
      if (this.$router.currentRoute.path != '/reports/show') {
        this.$router.push({path: '/reports/show'})
      }
    },
    subcategoryChanged(subcategory) {
      this.subcategory = subcategory;
    },
    routeChanged(route) {
      if (this.route != route) {
        this.route = route;
        this.$router.push({name: route})
      }
    },
    dateChanged(selectedDateStart, selectedDateEnd) {
      this.dateStart = selectedDateStart != "" ? selectedDateStart : null;
      this.dateEnd = selectedDateEnd != "" ? selectedDateEnd : null;
    },
    daysChanged(days) {
      this.days = days;
    }
  }
}
</script>

<style scoped>

</style>