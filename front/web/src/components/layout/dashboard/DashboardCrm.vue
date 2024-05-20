<template>
  <div class="row q-col-gutter-lg">
    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-blue text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-4">
            <q-icon name="playlist_add_check" class="kpi-icon text-blue-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-8 items-center">
            <div class="kpi-data text-center">{{resumen[0].oportunidades_registradas}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 bg-blue text-center text-blue-2 margint">Oportunidades registradas</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-blue text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-4">
            <q-icon name="star_half" class="kpi-icon text-blue-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-8 items-center">
            <div class="kpi-data text-center">{{resumen[0].oportunidades_cierre}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 bg-blue text-center text-blue-2 margint">Oportunidades en cierre</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-blue text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-4">
            <q-icon name="star" class="kpi-icon text-blue-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-8 items-center">
            <div class="kpi-data text-center">{{resumen[0].oportunidades_logradas}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 bg-blue text-center text-blue-2 margint">Oportunidades ganadas</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-deep-purple text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-2">
            <q-icon name="fas fa-dollar-sign" class="kpi-icon text-deep-purple-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-10 items-center">
            <div class="text-subtitle2 kpi-data-medium text-center">{{resumen[0].meta_anual}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 bg-deep-purple text-center text-deep-purple-2 margint">Meta anual</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-green text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-2">
            <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-10 items-center">
            <div class="text-h4 kpi-data-medium text-center">{{resumen[0].o_monto_registradas}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 text-center text-green-2 margint">Oportunidades registradas</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-green text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-2">
            <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-10 items-center">
            <div class="text-h4 kpi-data-medium text-center">{{resumen[0].o_monto_cierre}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 text-center text-green-2 margint">Oportunidades en cierre</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card class="kpi-card bg-green text-white">
        <q-card-section horizontal class="row">
          <q-card-section class="col-2">
            <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4"/>
          </q-card-section>
          <q-card-section class="col-10 items-center">
            <div class="text-h4 kpi-data-medium text-center">{{resumen[0].o_monto_logradas}}</div>
          </q-card-section>
        </q-card-section>
        <div class="text-subtitle2 text-center text-green-2 margint">Oportunidades ganadas</div>
      </q-card>
    </div>

    <div class="col-sm-3 q-mb-md">
      <q-card :class="resumen[0].color">
        <q-card-section horizontal class="row">
          <q-card-section class="col-2">
            <q-icon name="fas fa-percent" :class="resumen[0].color_icon" style="font-size: 3em;" />
          </q-card-section>
          <q-card-section class="col-10 items-center">
            <div class="text-h4 kpi-data text-center">{{resumen[0].porcentaje}}</div>
          </q-card-section>
        </q-card-section>
        <div :class="resumen[0].color_subtitle">Meta anual</div>
      </q-card>
    </div>

    <div class="col-sm-12 q-mb-md">
      <q-card>
        <apexchart class="bg-white" type="line" height="350" :options="chartOptions2" :series="series" width="100%"></apexchart>
      </q-card>
    </div>

    <div class="col-sm-12 q-mt-md">
      <q-card>
        <div class="col-sm-12">
          <q-search color="primary" v-model="form.filter" class="q-pt-md"/>
        </div>
        <div class="col-md-12" id="sticky-table-scroll">
          <q-table id="sticky-table-newstyle"
            :data="vendedores"
            :columns="form.columns"
            :selected.sync="form.selected"
            :filter="form.filter"
            color="positive"
            title=""
            :dense="true"
            :pagination.sync="form.pagination"
            :loading="form.loading"
            :rows-per-page-options="form.rowsOptions">
            <template slot="body" slot-scope="props">
              <q-tr :props="props">
                <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                <q-td key="meta_anual" :props="props">${{ currencyFormat(props.row.meta_anual) }}</q-td>
                <q-td key="o_registradas" :props="props">{{ props.row.o_registradas }}</q-td>
                <q-td key="o_monto_registradas" :props="props">${{ currencyFormat(props.row.o_monto_registradas) }}</q-td>
                <q-td key="o_cierre" :props="props">{{ props.row.o_cierre }}</q-td>
                <q-td key="o_monto_cierre" :props="props">${{ currencyFormat(props.row.o_monto_cierre) }}</q-td>
                <q-td key="o_ganadas" :props="props">{{ props.row.o_ganadas }}</q-td>
                <q-td key="o_monto_ganadas" :props="props">${{ currencyFormat(props.row.o_monto_ganadas) }}</q-td>
              </q-tr>
            </template>
          </q-table>
          <q-inner-loading :visible="form.loading">
            <q-spinner-dots size="64px" color="primary" />
          </q-inner-loading>
        </div>
      </q-card>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import VueApexCharts from 'vue-apexcharts'
import axios from 'axios'
import Vue from 'vue'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  name: 'DashboardCrm',
  data () {
    return {
      series: [],
      options: {},
      data_gdp: [],
      dta_retail: [],
      resumen: [],
      vendedores: [],
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        // data: [],
        columns: [
          { name: 'nickname', label: 'Vendedor', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'meta_anual', label: 'Meta anual', field: 'meta_anual', sortable: true, type: 'string', align: 'right' },
          { name: 'o_registradas', label: 'Op. registradas', field: 'o_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_registradas', label: 'Monto registradas', field: 'o_monto_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_cierre', label: 'Op. en cierre', field: 'o_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_cierre', label: 'Monto cierre', field: 'o_monto_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'o_ganadas', label: 'Op. ganadas', field: 'o_ganadas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_ganadas', label: 'Monto ganadas', field: 'o_monto_ganadas', sortable: true, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      modal: {
        x: 0,
        y: 0,
        transition: null
      },
      chartOptions2: {}
    }
  },
  computed: {
    ...mapGetters({
      carrilesOptions: 'crm/crm/selectOptions'
    })
  },
  async created () {
    await this.loadAll()
  },
  methods: {
    ...mapActions({
      getResumen: 'crm/oportunidades/getResumen'
    }),
    async cargarResumen () {
      this.getResumen().then(({data}) => {
        this.resumen = data.resumen
        this.vendedores = data.vendedores
      }).catch(error => {
        console.error(error)
      })
    },
    async loadAll () {
      this.form.loading = true
      await this.cargarResumen()
      await this.getDataGrafica()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async getDataGrafica () {
      await axios.get('/graficas/getDataCRM').then(({data}) => {
        this.series = [{ name: 'Ventas de gobierno y directas', type: 'bar', stacked: true, data: data.gdp }]
      }).catch(error => {
        console.error(error)
      })
      this.chartOptions2 = {
        colors: ['#2196f3', '#4caf50'],
        // labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
        chart: {
          height: 350,
          type: 'line',
          stacked: true,
          fontFamily: 'Nunito',
          toolbar: {
            show: true,
            tools: {
              download: true,
              selection: false,
              pan: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              reset: false
            }
          }
        },
        stroke: {
          curve: ['smooth', 'straight', 'straight', 'straight'],
          width: [4, 0, 0, 4]
        },
        dataLabels: {
          enabled: true,
          enabledOnSeries: [0, 1, 2, 3],
          formatter: function (val) {
            return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
          }
        },
        xaxis: {
          categories: [],
          labels: {
            type: 'String',
            show: true
          }
        },
        yaxis: {
          max: function (max) {
            return max * 1
          },
          title: {
            text: 'Miles de pesos'
          },
          labels: {
            type: 'String',
            show: true,
            formatter: function (val) {
              if (Math.trunc(val) === 0) {
                return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
              } else {
                return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
              }
            }
          }
        },
        series: this.series,
        grid: {
          borderColor: '#c6c2cF',
          row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.8
          }
        }
      }
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
  }
}

</script>

<style scoped>
.kpi-card {
  margin-right: 20px;
  min-width: 150 px;
}

.kpi-data{
  // height: 80px;
  vertical-align: middle;
  line-height: 90px;
  font-size: 2.8em;
  font-weight: bold;
  margin-top: 10px;
}

.kpi-data-medium{
  height: 80px;
  vertical-align: middle;
  line-height: 90px;
  font-size: 1.6em;
  font-weight: bold;
}

.kpi-icon{
  height: 110px;
  vertical-align: middle;
  line-height: 100px;
  padding-left: 15px;
  font-size: 3.5em;
}

.margint {
   margin-top: -15px;
}

</style>
