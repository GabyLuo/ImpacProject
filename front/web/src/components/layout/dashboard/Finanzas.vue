<template>
  <div class="row q-col-gutter-lg">
    <div class="col-sm-2">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.anio" inverted color="dark" float-label="Año" :options="aniosOptions" filter @input="getProyectosOpt()"/>
      </q-field>
    </div>
    <div class="col-sm-2">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.mes" inverted color="dark" float-label="Mes" :options="mesesOptions" filter/>
      </q-field>
    </div>
    <div class="col-sm-4">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Proyecto" :options="proyectosOptions" filter @input="getProjectsOpt()"/>
      </q-field>
    </div>
    <div class="col-sm-4">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.project_id" inverted color="dark" float-label="Presupuesto" :options="projectsOptions" filter/>
      </q-field>
    </div>
    <div class="col-sm-3">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.sucursal_id" inverted color="dark" float-label="Sucursal" :options="sucursalesOptions" filter/>
      </q-field>
    </div>
    <div class="col-sm-3">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.vendedor_id" inverted color="dark" float-label="Vendedor" :options="usuariosOptions" filter/>
      </q-field>
    </div>
    <div class="col-sm-3">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.concepto_id" inverted color="dark" float-label="Concepto" :options="conceptosOptions" filter @input="getSubconceptosGasto()"/>
      </q-field>
    </div>
    <div class="col-sm-3">
      <q-field icon="work" icon-color="dark">
        <q-select v-model="form.fields.subconcepto_id" inverted color="dark" float-label="Subconcepto" :options="subconceptosOptions" filter/>
      </q-field>
    </div>
    <div class="col-sm-3">
      <q-field icon="fas fa-dollar-sign" icon-color="dark">
        <q-input upper-case v-model="total" inverted color="dark" float-label="Total" suffix="MXN"/>
      </q-field>
    </div>
    <div class="col-sm-9 pull-right">
      <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
        <q-btn @click="getData()" color="green" icon="fas fa-search" :loading="loadingButton" style="margin-top:5px;"><q-tooltip>Buscar</q-tooltip></q-btn>
      </div>
    </div>
    <div class="col-xs-12 col-md-4 q-pa-md">
      <q-card class="col-xs-12">
        <q-card-title class="bg-primary text-white text-bold">
          <div class="text-h8"><center>GASTOS POR CONCEPTO</center></div>
        </q-card-title>
        <div class="col q-pr-xs">
          <div class="row q-col-gutter-xs">
            <div class="col-xs-12 col-sm-12 q-pt-sm q-pb-md">
              <donut :labelsD="grafica_concepto" :seriesD="grafica_concepto_series"/>
            </div>
          </div>
        </div>
      </q-card>
    </div>
    <div class="col-xs-12 col-md-8 q-pa-md">
      <q-card class="col-xs-12">
        <q-card-title class="bg-primary text-white text-bold">
          <div class="text-h8"><center>GASTOS 2022</center></div>
        </q-card-title>
        <div class="col q-pr-xs">
          <div class="row q-col-gutter-xs">
            <div class="col-xs-12 col-sm-12 q-pt-sm q-pb-md">
              <line-chart :seriesD="data_meses" v-if="data_meses.length > 0"/>
            </div>
          </div>
        </div>
      </q-card>
    </div>
    <div class="col-xs-12 col-md-4 q-pa-md" v-for="stt in data_sucursales" :key="stt.id">
      <q-card class="col-xs-12">
        <q-card-title class="bg-primary text-white text-bold">
          <div class="text-h7"><center>{{ stt.nombre }}</center></div>
        </q-card-title>
        <div class="col q-pr-xs">
          <div class="row q-col-gutter-xs">
            <div class="col-xs-12 col-sm-12 q-pt-sm q-pb-md">
              <donut :labelsD="stt.conceptos" :seriesD="stt.montos"/>
            </div>
          </div>
        </div>
      </q-card>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import Donut from '../../../components/layout/dashboard/Donut'
import LineChart from '../../../components/layout/dashboard/LineChart'

export default {
  name: 'DashboardCrm',
  components: {
    Donut,
    LineChart
  },
  data () {
    return {
      data: [],
      data_sucursales: [],
      data_meses: [],
      grafica_concepto: [],
      grafica_concepto_series: [],
      grafica_concepto_monto: [],
      proyectosOptions: [{label: '---Seleccione---', value: 0}],
      projectsOptions: [{label: '---Seleccione---', value: 0}],
      subconceptosOptions: [],
      aniosOptions: [{ 'label': '' + (new Date().getFullYear() - 2), 'value': '' + (new Date().getFullYear() - 2) }, { 'label': '' + (new Date().getFullYear() - 1), 'value': '' + (new Date().getFullYear() - 1) }, { 'label': '' + (new Date().getFullYear()), 'value': '' + (new Date().getFullYear()) }],
      mesesOptions: [{ 'label': 'Enero', 'value': 1 }, { 'label': 'Febrero', 'value': 2 }, { 'label': 'Marzo', 'value': 3 }, { 'label': 'Abril', 'value': 4 }, { 'label': 'Mayo', 'value': 5 }, { 'label': 'Junio', 'value': 6 }, { 'label': 'Julio', 'value': 7 }, { 'label': 'Agosto', 'value': 8 }, { 'label': 'Septiembre', 'value': 9 }, { 'label': 'Octubre', 'value': 10 }, { 'label': 'Noviembre', 'value': 11 }, { 'label': 'Diciembre', 'value': 12 }, { label: '---Seleccione---', value: 0 }],
      loadingButton: false,
      loading: false,
      total: 0,
      form: {
        fields: {
          anio: '' + (new Date().getFullYear()),
          mes: new Date().getMonth(),
          project_id: 0,
          proyecto_id: 0,
          sucursal_id: 0,
          vendedor_id: 0,
          concepto_id: 0,
          subconcepto_id: 0
        },
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
      }
    }
  },
  computed: {
    ...mapGetters({
      usuariosOptions: 'sys/users/selectOptionsName',
      sucursalesOptions: 'pmo/pmosucursales/selectOptions',
      conceptosOptions: 'vnt/concepto/selectOptions'
    })
  },
  async mounted () {
    await this.loadAll()
  },
  methods: {
    ...mapActions({
      getUsuarios: 'sys/users/refresh',
      getSucursales: 'pmo/pmosucursales/refresh',
      getConceptos: 'vnt/concepto/refresh',
      getSubconceptos: 'vnt/subconcepto/getByConcepto'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getData()
      await this.getProyectosOpt()
      await this.getUsuarios()
      await this.getSucursales()
      await this.getConceptos()
      this.form.loading = false
    },
    getSubconceptosGasto () {
      this.getSubconceptos({concepto_id: this.form.fields.concepto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.subconceptosOptions = data.subconceptos
          this.subconceptosOptions.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getData () {
      await axios.get('/graficas/getDataFinanzas/' + this.form.fields.anio + '/' + this.form.fields.mes + '/' + this.form.fields.proyecto_id + '/' + this.form.fields.project_id + '/' + this.form.fields.sucursal_id + '/' + this.form.fields.vendedor_id + '/' + this.form.fields.concepto_id + '/' + this.form.fields.subconcepto_id).then(({data}) => {
        this.data = data.data
        this.total = data.total
        this.grafica_concepto = data.grafica_concepto[0].grafica_concepto
        this.grafica_concepto_series = data.grafica_concepto[0].grafica_concepto_monto
        this.data_sucursales = data.data_sucursales
        this.data_meses = data.grafica_meses
      }).catch(error => {
        console.error(error)
      })
    },
    async getProyectosOpt () {
      await axios.get('/recursos/getByYear/' + this.form.fields.anio + '/all').then(({data}) => {
        this.proyectosOptions = []
        this.proyectosOptions.push({label: '---Seleccione---', value: 0})
        if (data.recursos.length > 0) {
          data.recursos.forEach(recurso => {
            this.proyectosOptions.push({label: recurso.codigo + ' - ' + recurso.nombre, value: recurso.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getProjectsOpt () {
      await axios.get('/proyectos/getByRecurso/' + this.form.fields.proyecto_id).then(({data}) => {
        this.projectsOptions = []
        this.projectsOptions.push({label: '---Seleccione---', value: 0})
        if (data.proyectos.length > 0) {
          this.projectsOptions = data.proyectos
        }
      }).catch(error => {
        console.error(error)
      })
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
  }
}

</script>

<style scoped>
</style>
