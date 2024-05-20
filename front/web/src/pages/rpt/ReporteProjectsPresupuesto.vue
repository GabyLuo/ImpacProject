<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-4">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Utilidades projects" to="/" />
            </q-breadcrumbs>
          </div>
        </div>
        <!-- -->
        <div class="col-sm-4 pull-right" style="margin-top: 10px;">
          <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="cargarDatosReporte()"/>
        </div>
        <div class="col-sm-4 pull-right">
          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
           <q-btn @click="cargarDatosReporte()" color="green" icon="fas fa-search" :loading="loadingButton" style="margin-top:5px;"><q-tooltip>Buscar</q-tooltip></q-btn>
           <q-btn @click="exportCSV()" color="green" icon="fas fa-file-excel" style="margin-top:5px;"><q-tooltip>Generar CSV</q-tooltip></q-btn>
           <q-btn @click="borrar()" color="red" icon="loop" style="margin-top:5px;"><q-tooltip>Limpiar</q-tooltip></q-btn>
         </div>
       </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md">
          <div class="row q-col-gutter-xs">
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.lider_id" inverted color="dark" float-label="Líder" :options="usuariosOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions2" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-map-pin" icon-color="dark">
                <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md" style="padding: 0;">
          <div class="col q-pa-md">
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
                      <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                      <q-td key="sucursal" :props="props">{{ props.row.sucursal }}</q-td>
                      <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                      <q-td  key="programa" :props="props" style="text-align: left;">{{ props.row.programa }}</q-td>
                      <q-td  key="project" :props="props" style="text-align: left;">{{ props.row.project }}</q-td>
                      <q-td key="monto_bolsa" :props="props">{{ props.row.monto_bolsa }}</q-td>
                      <q-td key="utilidad_bruta" :props="props">{{ props.row.utilidad_bruta }}</q-td>
                      <q-td key="utilidad_operacion" :props="props">{{ props.row.utilidad_operacion }}</q-td>
                      <q-td key="presupuesto_actividad_principal" :props="props">{{ props.row.presupuesto_actividad_principal }}</q-td>
                      <q-td key="porcentaje_recurso_ejercido" :props="props">{{ props.row.porcentaje_recurso_ejercido }}%</q-td>
                      <q-td key="utilidad_neta" :props="props">{{ props.row.utilidad_neta }}</q-td>
                      <q-td key="porcentaje_utilidad" :props="props">{{ props.row.porcentaje_utilidad }}%</q-td>
                      <q-td key="recurso_ejercido" :props="props">${{ props.row.recurso_ejercido }}</q-td>
                      <q-td key="importe" :props="props">${{ props.row.importe }}</q-td>
                      <q-td key="porcentaje_importe" :props="props">{{ props.row.porcentaje_importe }}%</q-td>
                      <q-td key="status_project" :props="props">{{ props.row.status_project }}</q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form.loading">
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
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'

export default {
  components: {},
  beforeRouteEnter (to, from, next) {
    next(vm => {
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      reportes: [],
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        data: [],
        fields: {
          lider_id: 0,
          empresa_id: 0,
          estado_id: 0,
          municipio_id: 0
        },
        columns: [
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'sucursal', label: 'Sucursal', field: 'sucursal', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'programa', label: 'Programa', field: 'programa', sortable: true, type: 'string', align: 'left' },
          { name: 'project', label: 'Project', field: 'project', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_bolsa', label: 'Monto de la bolsa', field: 'monto_bolsa', sortable: true, type: 'string', align: 'right' },
          { name: 'utilidad_bruta', label: 'Utilidad bruta', field: 'utilidad_bruta', sortable: true, type: 'string', align: 'right' },
          { name: 'utilidad_operacion', label: 'Utilidad de operación', field: 'utilidad_operacion', sortable: true, type: 'string', align: 'right' },
          { name: 'presupuesto_actividad_principal', label: 'Recurso actual', field: 'presupuesto_actividad_principal', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_recurso_ejercido', label: '% de gasto', field: 'porcentaje_recurso_ejercido', sortable: true, type: 'string', align: 'right' },
          { name: 'utilidad_neta', label: 'Utilidad presupuestada', field: 'utilidad_neta', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_utilidad', label: '% de utilidad', field: 'porcentaje_utilidad', sortable: true, type: 'string', align: 'right' },
          { name: 'recurso_ejercido', label: 'Recurso ejercido', field: 'recurso_ejercido', sortable: true, type: 'string', align: 'right' },
          { name: 'importe', label: 'Utilidad Importe', field: 'importe', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_importe', label: '% utilidad real', field: 'porcentaje_importe', sortable: true, type: 'string', align: 'right' },
          { name: 'status_project', label: 'Status', field: 'status_project', sortable: true, type: 'string', align: 'left' }
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
  computed: {
    ...mapGetters({
      proyectosOptions: 'pmo/proyecto/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions'
    }),
    estadosOptions () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.push({label: '---Seleccione---', value: 0})
      estados.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return estados
    },
    proyectosOptions2 () {
      let proyectos2 = this.$store.getters['pmo/proyecto/selectOptions']
      proyectos2.push({ 'label': 'Todos', 'value': 0 })
      return proyectos2
    },
    empresasOptions2 () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({ 'label': 'Todos', 'value': 0 })
      return empresas
    },
    usuariosOptions () {
      let usuarios = this.$store.getters['sys/users/selectOptionsName']
      usuarios.push({ 'label': 'Todos', 'value': 0 })
      return usuarios
    }
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getEstados: 'vnt/estado/refresh',
      getUsers: 'sys/users/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form.loading = true
      await this.isAdmin()
      if (this.form.fields.user_id === 43 || this.form.fields.user_id === 1 || this.form.fields.user_id === 33) {
        await this.cargarDatosReporte()
        await this.getUsers()
        await this.getEmpresas()
        await this.getEstados()
      }
      this.form.loading = false
    },
    async isAdmin () {
      await this.getUser().then(({data}) => {
        this.form.fields.user_id = data.user.id
      }).catch(error => {
        console.log(error)
      })
    },
    async cargarDatosReporte () {
      this.form.loading = true
      this.form.data = []
      await axios.get(`/reportes/reporteProjectsMonto/${this.form.fields.lider_id}/${this.form.fields.empresa_id}/${this.form.fields.estado_id}/${this.form.fields.municipio_id}/${this.year}`).then(({data}) => {
        this.form.data = data.reportes_projects
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    exportCSV () {
      let uri = process.env.API + `reportes/exportCSV_reporteProjectsMonto/${this.form.fields.lider_id}/${this.form.fields.empresa_id}/${this.form.fields.estado_id}/${this.form.fields.municipio_id}/${this.year}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.empresa_id = 0
      this.form.fields.lider_id = 0
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
    },
    async cargaMunicipios () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
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
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
