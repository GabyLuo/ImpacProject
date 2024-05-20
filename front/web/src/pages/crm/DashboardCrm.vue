<template>
  <div class="row q-mt-sm">
      <div class="q-pa-md bg-grey-3" style="overflow-y: auto;">
        <div class="row q-col-gutter-xs">
          <div class="col-sm-3">
            <q-card class="kpi-card bg-blue text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="playlist_add_check" class="kpi-icon text-blue-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h kpi-data text-center">{{resumen[0].oportunidades_registradas}}</div>
                  <div class="text-subtitle2 text-center text-blue-2">Oportunidades registradas</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card class="kpi-card bg-blue text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="star_half" class="kpi-icon text-blue-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].oportunidades_cierre}}</div>
                  <div class="text-subtitle2 text-center text-blue-2">Oportunidades en cierre</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card class="kpi-card bg-blue text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="star" class="kpi-icon text-blue-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].oportunidades_logradas}}</div>
                  <div class="text-subtitle2 text-center text-blue-2">Oportunidades ganadas</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card class="kpi-card bg-deep-purple text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="fas fa-dollar-sign" class="kpi-icon text-deep-purple-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].meta_anual}}M</div>
                  <div class="text-subtitle2 text-center text-deep-purple-2">Meta anual</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>
        </div>

        <div class="row q-col-gutter-xs" style="margin-top: 15px;">
          <div class="col-sm-3">
            <q-card class="kpi-card bg-green text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].o_monto_registradas}}M</div>
                  <div class="text-subtitle2 text-center text-green-2">Oportunidades registradas</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card class="kpi-card bg-green text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].o_monto_cierre}}M</div>
                  <div class="text-subtitle2 text-center text-green-2">Oportunidades en cierre</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card class="kpi-card bg-green text-white">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="fas fa-dollar-sign" class="kpi-icon text-green-2 col-4" style="font-size: 3.5em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].o_monto_logradas}}M</div>
                  <div class="text-subtitle2 text-center text-green-2">Oportunidades ganadas</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>

          <div class="col-sm-3">
            <q-card :class="resumen[0].color">
              <q-card-section horizontal class="row">
                <q-card-section class="col-4">
                  <q-icon name="fas fa-percent" :class="resumen[0].color_icon" style="font-size: 3em;" />
                </q-card-section>
                <q-card-section class="col-8 items-center">
                  <div class="text-h4 kpi-data text-center">{{resumen[0].porcentaje}}</div>
                  <div :class="resumen[0].color_subtitle">Meta anual</div>
                </q-card-section>
              </q-card-section>
            </q-card>
          </div>
        </div>

        <div class="row q-col-gutter-xs" style="margin-top: 15px;">
          <apexchart class="bg-white" type="line" height="350" :options="chartOptions2" :series="series"></apexchart>
        </div>
      </div>
  </div>
</template>

<script>
import {VMoney} from 'v-money'
import { mapActions, mapGetters } from 'vuex'
// import ApexCharts from 'apexcharts'
import VueApexCharts from 'vue-apexcharts'
import axios from 'axios'
import Vue from 'vue'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  components: {},
  beforeRouteEnter (to, from, next) {
    next(vm => {
      // if (vm.$store.getters['user/rolesIds'].includes(vm.$roles.SUPER_ADMIN) || vm.$store.getters['user/privileges'].include[vm.$privileges.VER_patron]) {
      //   next()
      // } else {
      //   next(from.path === '/' ? '/dashboard' : from.path)
      // }
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
          condicion = true
        }
      }
      if (condicion) {
        next()
      } else {
        next(from.path === '/' ? '/dashboard' : from.path)
      }
    })
  },
  data () {
    return {
      series: [],
      options: {},
      data_facturas: [],
      total_facturas: [],
      resumen: [],
      vendedores: [],
      today: new Date(),
      tabPrincipal: false,
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      tiposOptions: [ { label: 'GDP', value: 'GDP' }, { label: 'RETAIL', value: 'RETAIL' } ],
      tipo_prospecto: [ { label: 'NUEVO', value: 'NUEVO' }, { label: 'EXISTENTE', value: 'EXISTENTE' } ],
      ordenOptions: [ { label: 'FEDERAL', value: 'FEDERAL' }, { label: 'ESTATAL', value: 'ESTATAL' }, { label: 'MUNICIPAL', value: 'MUNICIPAL' }, { label: '--Seleccione--', value: 'all' } ],
      enteOptions: [ { label: 'EJECUTIVO', value: 'EJECUTIVO' }, { label: 'DEPENDENCIA', value: 'DEPENDENCIA' }, { label: 'OPD', value: 'OPD' }, { label: '--Seleccione--', value: 'all' } ],
      adjudicacionOptions: [ { label: 'LICITACIÓN', value: 'LICITACIÓN' }, { label: 'ADJUDICACIÓN DIRECTA', value: 'ADJUDICACIÓN DIRECTA' }, { label: 'INVITACIÓN A 3', value: 'INVITACIÓN A 3' }, { label: '--Seleccione--', value: 'all' } ],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      carril_id: 1,
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      lorem: '',
      today_sold_: 0,
      today_claimed_: 0,
      week_sold_: 0,
      week_claimed_: 0,
      sold_week: ' 2,250k',
      sold_month: ' 2.4M',
      sold_year: ' 14M',
      claimed_today: ' 290,005',
      claimed_week: ' 2,311K',
      claimed_month: ' 2.1M',
      claimed_year: ' 13.7M',
      form: {
        fields: {
          id: 0,
          nombre: '',
          sector_id: 0,
          subsector_id: 0,
          email: '',
          telefono: '',
          nombre_contacto: '',
          prospecto: '',
          prospecto_id: 0,
          giro_comercial: 0,
          valor: 0,
          valor_final: 0,
          ejecutivo_id: 0,
          tipo: 'GDP',
          tipo_prospecto: 'EXISTENTE',
          empresa_id: 0,
          orden: 'all',
          ente: 'all',
          adjudicacion: 'all',
          estado_id: 0,
          municipio_id: 0,
          folio: '',
          procedimiento: '',
          valor_licitacion: 0
        },
        // data: [],
        columns: [
          { name: 'nickname', label: 'Vendedor', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'meta_anual', label: 'Meta anual', field: 'meta_anual', sortable: true, type: 'string', align: 'right' },
          { name: 'o_registradas', label: 'Oportunidades registradas', field: 'o_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_registradas', label: 'Monto regsitradas', field: 'o_monto_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_cierre', label: 'Oportunidades en cierre', field: 'o_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_cierre', label: 'Monto cierre', field: 'o_monto_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Avance', field: 'acciones', sortable: false, type: 'string', align: 'right' }
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
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      carrilesOptions: 'crm/crm/selectOptions'
    })
  },
  created () {
    this.loadAll()
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
      // this.graficas()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async getDataGrafica () {
      await axios.get('/graficas/getGraficaDataFactura').then(({data}) => {
        this.data_facturas = data.facturas_recaudadas
        this.total_facturas = data.facturas_totales
        this.series = [{ name: 'Vendido', type: 'area', stacked: false, data: Object.values(data.facturas_recaudadas).reverse() }, { name: 'Remisionado', type: 'column', data: Object.values(data.facturas_totales).reverse() }, { name: 'Facturado', type: 'column', data: Object.values(data.facturas_recaudadas).reverse() }, { name: 'Cobrado', type: 'line', data: Object.values(data.facturas_totales).reverse() }]
      }).catch(error => {
        console.error(error)
      })
      this.chartOptions2 = {
        colors: ['#d1c4e9', '#e91e63', '#2196f3', '#4caf50'],
        labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
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
            return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + 'k'
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
            return max * 0.6
          },
          title: {
            text: 'Miles de pesos'
          },
          labels: {
            type: 'String',
            show: true,
            formatter: function (val) {
              if (Math.trunc(val) === 0) {
                return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + 'k'
              } else {
                return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + 'k'
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
    }
  }
}

</script>

<style scoped>
.kpi-card {
  height: 110px;
  min-width: 300px;
  margin-right: 20px;
}

.kpi-data{
  height: 80px;
  vertical-align: middle;
  line-height: 90px;
  font-size: 2.8em;
  font-weight: bold;
}

.kpi-icon{
  height: 110px;
  vertical-align: middle;
  line-height: 100px;
  padding-left: 15px;
}
</style>
