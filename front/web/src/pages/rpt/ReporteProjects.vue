<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Auditoría de presupuesto" to="/" v-if="tipoReporte===2"/>
              <q-breadcrumbs-el class="page-title" label="Auditoría de contratos" to="/" v-if="tipoReporte===1"/>
            </q-breadcrumbs>
          </div>
        </div>
      </div>
    </div>
    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md" style="padding: 0;">
          <div class="col q-pa-md">
            <div class="col q-pa-xs" v-if="tipoReporte === 1">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
                </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table-newstyle"
                :data="form.data"
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
                    <q-td  key="codigo" :props="props" style="text-align: left;">{{ props.row.codigo }}</q-td>
                    <q-td  key="nombre" :props="props" style="text-align: left;">{{ props.row.nombre }}</q-td>
                    <q-td key="total_proyectos" :props="props">{{ props.row.total_proyectos }}</q-td>
                    <q-td key="total_contratos" :props="props">{{ props.row.total_contratos }}</q-td>
                    <q-td key="total_contratos_firmados" :props="props">{{ props.row.total_contratos_firmados }}</q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
              </div>
            </div>

            <div class="col q-pa-xs" v-if="tipoReporte === 2">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="form_project.data"
                  :columns="form_project.columns"
                  :selected.sync="form_project.selected"
                  :filter="form_project.filter"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form_project.pagination"
                  :loading="form_project.loading"
                  :rows-per-page-options="form_project.rowsOptions">
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td  key="recurso" :props="props" style="text-align: left;">{{ props.row.recurso }}</q-td>
                      <q-td  key="razon_social" :props="props" style="text-align: left;">{{ props.row.razon_social }}</q-td>
                      <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
                      <q-td key="presupuesto" :props="props">${{ currencyFormat(props.row.presupuesto) }}</q-td>
                      <q-td key="presupuesto_actual" v-if="props.row.presupuesto === props.row.presupuesto_actual" :props="props">${{ currencyFormat(props.row.presupuesto_actual) }}</q-td>
                      <q-td key="presupuesto_actual" v-if="props.row.presupuesto !== props.row.presupuesto_actual" style="color: red;" :props="props">${{ currencyFormat(props.row.presupuesto_actual) }}</q-td>
                    </q-tr>
                  </template>
                </q-table>
              </div>
              <q-inner-loading :visible="form_project.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import axios from 'axios'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase()) {
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
      tipoReporte: 1,
      reportes: [],
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        data: [],
        fields: {
          id: 0,
          razon_social: '',
          rfc: '',
          rep_legal: '',
          curp_representante: '',
          regimen: '',
          nombre_corto: '',
          rfc_representante: ''
        },
        columns: [
          { name: 'codigo', label: 'Código del proyecto', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre del proyecto', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'total_proyectos', label: 'Número de projects registrados', field: 'total_proyectos', sortable: true, type: 'string', align: 'right' },
          { name: 'total_contratos', label: 'Número de contratos registrados', field: 'total_contratos', sortable: true, type: 'string', align: 'right' },
          { name: 'total_contratos_firmados', label: 'Número de contratos firmados', field: 'total_contratos_firmados', sortable: true, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_project: {
        data: [],
        columns: [
          { name: 'recurso', label: 'Código del proyecto', field: 'recurso', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Cliente', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_proyecto', label: 'Nombre del project', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'presupuesto', label: 'Presupuesto inicial', field: 'presupuesto', sortable: true, type: 'string', align: 'right' },
          { name: 'presupuesto_actual', label: 'Presupuesto actual', field: 'presupuesto_actual', sortable: true, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      }
    }
  },
  mounted () {
    this.loadAll()
  },
  watch: {
    'tipoReporte' (newValue, old) {
      if (newValue === 1) {
        this.loadAll()
      } else if (newValue === 2) {
        this.loadAll()
      }
    }
  },
  methods: {
    async loadAll () {
      this.form.loading = true
      await this.cargarDatosReporte()
      this.form.loading = false
    },
    async cargarDatosReporte () {
      if (this.tipoReporte === 1) {
        this.form.loading = true
        this.form.data = []
        await axios.get('/reportes/getReporte1').then(({data}) => {
          this.form.data = data.reportes_proyectos
        }).catch(error => {
          console.error(error)
        })
        this.form.loading = false
      }
      if (this.tipoReporte === 2) {
        this.form_project.loading = true
        this.form_project.data = []
        await axios.get('/proyectos/getAll').then(({data}) => {
          this.form_project.data = data.proyectos
        }).catch(error => {
          console.error(error)
        })
        this.form_project.loading = false
      }
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
