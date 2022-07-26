<template>
  <div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Выберите категорию</label>
        <multiselect
          :multiple="true"
          v-model="selectedCategories"
          :options="categories"
          label="name"
          track-by="id"
          placeholder="Выберите из списка"
          select-label="Нажмите для выбора"
          selected-label="Выбран"
          deselect-label="Нажмите для удаления"
          :close-on-select="false"
        />
      </div>

      <div class="col-md-6 mb-3">
        <div class="form-item m-0">
          <label>Выберите период</label>
          <div class="row">
            <div class="col-sm-1 d-flex align-items-center">
              с:
            </div>
            <div class="col-sm-5">
              <input name="date" id="filter_date_start" type="date" v-model="selectedDateStart">
            </div>
            <div class="col-sm-1 d-flex align-items-center">
              по:
            </div>
            <div class="col-sm-5">
              <input name="date" id="filter_date_end" type="date" v-model="selectedDateEnd">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col mb-3">
        <label>Выберите менеджера</label>
        <multiselect
            :multiple="true"
            v-model="selectedManagers"
            :options="managers"
            label="name"
            track-by="id"
            placeholder="Выберите из списка"
            select-label="Нажмите для выбора"
            selected-label="Выбран"
            deselect-label="Нажмите для удаления"
            :close-on-select="false"
        />
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="check-item">
          <input name="days" id="days" type="checkbox" v-model="selectedDays" class="checkbox">
          <label for="days">Категоризация по дням</label>
        </div>
      </div>

      <div class="col-md-3">
        <div class="check-item">
          <input name="hours" id="hours" type="checkbox" v-model="selectedHours" class="checkbox">
          <label for="hours">Категоризация по часам</label>
        </div>
      </div>

      <div class="col-md-6" v-if="report.categories.length">
        <button class="btn btn-sm btn-success float-right" @click="loadExcel">
          <span class="fa fa-book"/>
          Excel
        </button>
      </div>
    </div>

    <div class="row">
      <template v-for="category in selectedCategories">
        <div class="col-md-6 mb-3" v-if="category.subcategories.length > 1">
          <div class="row category-filter">
            <div class="col-md-3 d-flex align-items-center font-weight-bolder">
              {{ category.name }}
            </div>
            <div class="col-md-9">
              <label>Выберите подкатегорию</label>
              <multiselect
                :multiple="true"
                v-model="selectedSubcategories[category.id]"
                :options="category.subcategories"
                label="name"
                track-by="id"
                placeholder="Выберите из списка"
                select-label="Нажмите для выбора"
                selected-label="Выбран"
                deselect-label="Нажмите для удаления"
                :close-on-select="false"
              />
            </div>
          </div>
        </div>

        <div class="col-md-6 mb-3" v-if="category.id === 3">
          <div class="row category-filter">
            <div class="col-md-3 d-flex align-items-center font-weight-bolder">
              {{ category.name }}
            </div>
            <div class="col-md-9">
              <label>Выберите компанию</label>
              <multiselect
                  :multiple="true"
                  v-model="selectedCompanies[category.id]"
                  :options="companies"
                  label="name"
                  track-by="id"
                  placeholder="Выберите из списка"
                  select-label="Нажмите для выбора"
                  selected-label="Выбран"
                  deselect-label="Нажмите для удаления"
                  :close-on-select="false"
              />
            </div>
          </div>
        </div>
      </template>
    </div>

    <AgeCategoryBaseTable
      v-if="!selectedDays && !selectedHours"
      :age-categories="ageCategories"
      :report="report"
    />

    <AgeCategoryDayTable
      v-if="selectedDays && !selectedHours"
      :age-categories="ageCategories"
      :report="report"
    />

    <AgeCategoryHourTable
      v-if="selectedHours"
      :age-categories="ageCategories"
      :report="report"
    />
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import AgeCategoryBaseTable from './BaseTable.vue'
import AgeCategoryDayTable from './DayTable.vue'
import AgeCategoryHourTable from './HourTable.vue'
import axios from "axios";

export default {
  components: {
    Multiselect,
    AgeCategoryBaseTable,
    AgeCategoryDayTable,
    AgeCategoryHourTable,
  },
  props: {
    categories: {
      type: Array,
      required: true,
    },
    managers: {
      type: Array,
      required: true,
    },
    companies: {
      type: Array,
      required: true,
    },
    ageCategories: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedCategories: [],
      selectedSubcategories: {},
      selectedManagers: [],
      selectedCompanies: {},
      selectedDateStart: null,
      selectedDateEnd: null,
      selectedDays: false,
      selectedHours: false,
      report: {
        ages: {},
        categories: [],
      },
      axiosCancelToken: axios.CancelToken,
      cancelToken: null,
    }
  },
  watch: {
    selectedCategories: {
      handler: function () {
        const subcategories = {};
        const companies = {};

        for (const category of this.selectedCategories) {
          if (this.selectedSubcategories.hasOwnProperty(category.id)) {
            subcategories[category.id] = this.selectedSubcategories[category.id];
          }
          if (this.selectedCompanies.hasOwnProperty(category.id)) {
            companies[category.id] = this.selectedCompanies[category.id];
          }
        }

        this.selectedSubcategories = subcategories;
        this.selectedCompanies = companies;
      },
      deep: true,
    },
    queryData: {
      handler: function () {
        this.getData();
      },
      deep: true,
    },
    selectedHours(value) {
      if (value)
        this.selectedDays = true;
    },
    selectedDays(value) {
      if (!value)
        this.selectedHours = false;
    },
  },
  computed: {
    queryData: function () {
      return {
        categories: this.getArrId(this.selectedCategories),
        subcategories: this.getObjId(this.selectedSubcategories),
        companies: this.getObjId(this.selectedCompanies),
        managers: this.selectedManagers.map(item => item.id),
        start: this.selectedDateStart,
        end: this.selectedDateEnd,
      };
    },
  },
  methods: {
    getArrId(arr) {
      return arr.map(item => item.id);
    },
    getObjId(obj) {
      const newObj = {};
      for (const el in obj) {
        newObj[el] = this.getArrId(obj[el]);
      }
      return newObj;
    },
    getData() {
      this.report = {
        ages: {},
        categories: [],
      };

      this.cancelRequest();

      axios
        .post(
          '/reports/age-category/data',
          this.queryData,
          {cancelToken: this.cancelRequestToken()}
        )
        .then(response => {
          this.report = response.data;
        })
    },
    loadExcel() {
      this.cancelRequest();

      axios
        .post(
          '/reports/age-category/excel',
          {
            report: this.report,
            days: this.selectedDays,
            hours: this.selectedHours,
          },
          {
            cancelToken: this.cancelRequestToken(),
            responseType: 'arraybuffer',
          }
        )
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'age-category.xlsx');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(url);
        })
    },
    cancelRequest() {
      if (this.cancelToken) {
        this.cancelToken.cancel('cancel');
      }
    },
    cancelRequestToken() {
      this.cancelToken = this.axiosCancelToken.source();

      return this.cancelToken.token;
    },
  }
}
</script>

<style scoped>
.form-item input[type=date] {
  min-height: 44px;
  background-color: white !important;
}

.category-filter {
  background-color: #e9e9e9;
  margin: 0;
  padding: 10px;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>