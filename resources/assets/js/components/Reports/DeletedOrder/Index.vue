<template>
  <div>
    <div class="row align-items-end">
      <div class="mb-3" :class="orders.length ? 'col-md-5' : 'col-md-6'">
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

      <div class="mb-3" :class="orders.length ? 'col-md-5' : 'col-md-6'">
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

      <div class="col-md-2 mb-3" v-if="orders.length">
        <button class="btn btn-success float-right" @click="loadExcel">
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

    <Table
      v-if="orders.length"
      :orders="orders"
    />
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import Table from './Table.vue'
import axios from "axios";

export default {
  components: {
    Multiselect,
    Table,
  },
  props: {
    categories: {
      type: Array,
      required: true,
    },
    companies: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedCategories: [],
      selectedSubcategories: {},
      selectedCompanies: {},
      selectedDateStart: null,
      selectedDateEnd: null,
      axiosCancelToken: axios.CancelToken,
      cancelToken: null,
      orders: [],
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
  },
  computed: {
    queryData: function () {
      return {
        categories: this.getArrId(this.selectedCategories),
        subcategories: this.getObjId(this.selectedSubcategories),
        companies: this.getObjId(this.selectedCompanies),
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
      this.orders = [];

      this.cancelRequest();

      axios
        .post(
          '/reports/deleted-order/data',
          this.queryData,
          {cancelToken: this.cancelRequestToken()}
        )
        .then(response => {
          this.orders = response.data;
        })
    },
    loadExcel() {
      this.cancelRequest();

      axios
        .post(
          '/reports/deleted-order/excel',
          {
            orders: this.orders,
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
          link.setAttribute('download', 'deleted-order.xlsx');
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
</style>