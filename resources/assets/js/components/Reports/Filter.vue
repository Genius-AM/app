<template>
  <div>
    <div class="row">
      <div class="col-sm">
        <div class="select-mini">
          <label>Выберите категорию</label>
          <selectize v-model="selectedCategory" placeholder="Выберите категорию">
            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
          </selectize>
        </div>
      </div>

      <div class="col-sm" v-if="selectedCategory == 4">
        <div class="select-mini">
          <label>Выберите подкатегорию</label>
          <selectize v-model="selectedSubcategory" placeholder="Выберите подкатегорию">
            <option v-for="subcategory in subcategories" :value="subcategory.id">{{ subcategory.name }}</option>
          </selectize>
        </div>
      </div>

      <div class="col-sm">
        <div class="form-item m-0">
          <label>Выберите период</label>
          <div class="row">
            <div class="col-sm-2">
              с:
            </div>
            <div class="col-sm-10">
              <input name="date" id="filter_date_start" :type="dateType" v-model="selectedDateStart"
                     class="mini-input-date mini-date">
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm d-flex align-items-end">
        <div class="form-item m-0 w-100">
          <div class="row">
            <div class="col-sm-2">
              по:
            </div>
            <div class="col-sm-10" style="margin-bottom: 7px;">
              <input name="date" id="filter_date_end" :type="dateType" v-model="selectedDateEnd"
                     class="mini-input-date mini-date">
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm">
        <div class="select-mini">
          <label>Выберите отчет</label>
          <selectize v-model="selectedReport" placeholder="Выберите отчет">
            <option v-for="(report, key) in reports" :value="key">{{ report }}</option>
          </selectize>
        </div>
      </div>
    </div>

    <div class="row" v-if="selectedCategory == 4">
      <div class="form-group col-lg-4 col-md-4">
        <div class="check-item">
          <input name="days" id="filter_days" type="checkbox" v-model="selectedDays" class="checkbox">
          <label for="filter_days">Категоризация по дням</label>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="color__grid__wrapper">
          <div class="color__grid__item mb-2" v-for="route in routes">
            <span :class="route.color ? route.color : ''"></span>
            <span>{{ route.name }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'selectize/dist/css/selectize.css'
import Selectize from 'vue2-selectize'

export default {
  props: [],
  components: {
    Selectize
  },
  data() {
    return {
      categories: [],
      subcategories: [],
      routes: [],
      reports: [],
      managers: [],
      drivers: [],
      selectedDateStart: null,
      selectedDateEnd: null,
      selectedManager: '',
      selectedDriver: '',
      selectedCategory: '',
      selectedSubcategory: '',
      selectedReport: '',
      selectedDays: false
    }
  },
  created() {
    this.getCategories();
  },
  computed: {
    dateType: function rootContainer() {
      if (
          this.selectedReport.indexOf('manager.orders') !== -1 ||
          this.selectedReport.indexOf('manager.adultsChildren') !== -1
      ) {
        return 'datetime-local';
      }
      return 'date';
    },
  },
  watch: {
    dateType(value) {
      if (this.selectedDateStart) {
        if (value === 'datetime-local') {
          this.selectedDateStart += 'T00:00';
        } else {
          this.selectedDateStart = this.selectedDateStart.split('T')[0];
        }
      }
      if (this.selectedDateEnd) {
        if (value === 'datetime-local') {
          this.selectedDateEnd += 'T23:59';
        } else {
          this.selectedDateEnd = this.selectedDateEnd.split('T')[0];
        }
      }
    },
    selectedReport() {
      this.$emit('changeRoute', this.selectedReport);
    },
    selectedCategory() {
      this.getSubcategories();
      this.getRoutes();
      this.getReports();
      this.$emit('changeCategory');
    },
    selectedSubcategory() {
      this.$emit('changeSubcategory', this.selectedSubcategory);
    },
    selectedDateStart() {
      this.$emit('changeDate', this.selectedDateStart, this.selectedDateEnd);
    },
    selectedDateEnd() {
      this.$emit('changeDate', this.selectedDateStart, this.selectedDateEnd);
    },
    selectedDays() {
      this.$emit('changeDays', this.selectedDays);
    },
  },
  methods: {
    getReports() {
      axios.get(`/dispatcher/${this.selectedCategory}/reports/`)
          .then(response => {
            this.reports = response.data;
          })
    },
    getCategories() {
      axios.get(`/dispatcher/categories/`)
          .then(response => {
            this.categories = response.data;
          })
    },
    getSubcategories() {
      axios.get(`/dispatcher/${this.selectedCategory}/subcategories/`)
          .then(response => {
            this.subcategories = response.data;
          })
    },
    getRoutes() {
      axios.get(`/dispatcher/routes/${this.selectedCategory}`)
          .then(response => {
            this.routes = response.data;
          })
    },
  },
}
</script>

<style lang="scss" scoped>
.color__grid__wrapper {
  display: flex;
  padding-right: 30px;

  .color__grid__item {
    display: flex;
    margin-left: 20px;

    span {
      &:first-child {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 10px;
      }

      &:last-child {
        font-size: 14px;
      }
    }
  }
}
</style>