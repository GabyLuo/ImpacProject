<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Reporte de contratos" to="/" />
            </q-breadcrumbs>
          </div>
        </div>
        <!-- -->
        <div class="col-sm-6 pull-right">
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
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select v-model="form.fields.estado" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-map-pin" icon-color="dark">
                <q-select v-model="form.fields.municipio" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form.fields.programa" inverted color="dark" float-label="Programa" :options="programasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.contrato" inverted color="dark" float-label="Contrato" :options="contratosOptions" filter/>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md" style="padding: 0;">
          <div class="col q-pa-md border-panel">
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
                    <q-td key="year" :props="props">{{ props.row.year }}</q-td>
                    <q-td key="num_contrato" :props="props">{{ props.row.num_contrato }}</q-td>
                    <q-td key="codigo" :props="props">{{ props.row.codigo }}</q-td>
                    <q-td key="proyecto" :props="props">{{ props.row.proyecto }}</q-td>
                    <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                    <q-td  key="municipio" :props="props" style="text-align: left;">{{ props.row.municipio }}</q-td>
                    <q-td key="estado" :props="props">{{ props.row.estado}}</q-td>
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                    <q-td key="lider" :props="props">{{ props.row.lider }}</q-td>
                    <q-td key="contrato" :props="props">${{ props.row.contrato }}</q-td>

                    <q-td key="acad" :props="props">{{ props.row.acad }}</q-td>
                    <q-td key="academia" :props="props">${{ props.row.academia }}</q-td>
                    <q-td key="ingresos_impact" :props="props">${{ props.row.ingresos_impact }}</q-td>
                    <q-td key="created_by" :props="props">{{ props.row.created_by }}</q-td>
                    <q-td key="facturado" :props="props">${{ props.row.facturado }}</q-td>
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
import { mapActions } from 'vuex'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      reportes: [],
      // proyectosOptions2: [],
      // proveedoresOptions2: [],
      // empresasOptions2: [],
      municipiosOptions: [],
      views: {
        grid: true,
        create: false,
        update: false,
        grid_direcciones: false,
        create_direccion: false,
        update_direccion: false
      },
      form: {
        fields: {
          recurso: 0,
          cliente: 0,
          empresa: 0,
          estado: 0,
          municipio: 0,
          programa: 0,
          contrato: 0
        },
        columns: [
          { name: 'year', label: 'Año', field: 'year', sortable: true, type: 'string', align: 'left' },
          { name: 'num_contrato', label: '# Contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Programa', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'lider', label: 'Lider', field: 'lider', sortable: true, type: 'string', align: 'left' },
          { name: 'contrato', label: 'Contrato', field: 'contrato', sortable: true, type: 'string', align: 'right' },
          { name: 'acad', label: 'Acad', field: 'acad', sortable: true, type: 'string', align: 'left' },
          { name: 'academia', label: 'Academia', field: 'academia', sortable: true, type: 'string', align: 'right' },
          { name: 'ingresos_impact', label: 'Ingresos IMPACT', field: 'ingresos_impact', sortable: true, type: 'string', align: 'right' },
          { name: 'created_by', label: 'Creador contrato', field: 'created_by', sortable: true, type: 'string', align: 'left' },
          { name: 'facturado', label: 'Facturado', field: 'facturado', sortable: true, type: 'string', align: 'left' }
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
    // this.todos()
  },
  computed: {
    /* ...mapGetters({
      proyectosOptions: 'pmo/proyecto/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions'
    }), */
    programasOptions () {
      let programas = this.$store.getters['vnt/subprograma/selectOptions']
      programas.push({ 'label': 'Todos', 'value': 0 })
      return programas
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({ 'label': 'Todos', 'value': 0 })
      return empresas
    },
    estadosOptions () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.push({ 'label': 'Todos', 'value': 0 })
      return estados
    },
    contratosOptions () {
      let contratos = this.$store.getters['vnt/contrato/selectOptions']
      contratos.push({ 'label': 'Todos', 'value': 0 })
      return contratos
    }
  },
  methods: {
    ...mapActions({
      getProgramas: 'vnt/subprograma/refresh',
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getEstados: 'vnt/estado/refresh',
      getContratos: 'vnt/contrato/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getProgramas()
      // this.proyectosOptions2 = this.getProyectos()
      await this.getEmpresas()
      await this.getEstados()
      await this.getContratos()
      this.municipiosOptions.push({label: 'Todos', value: 0})
      await this.cargarDatosReporte()
      this.form.loading = false
    },
    async cargarDatosReporte () {
      this.form.loading = true
      this.form.data = []
      await axios.get(`/reportes/getReporteProyectos/${this.form.fields.estado}/${this.form.fields.municipio}/${this.form.fields.programa}/${this.form.fields.empresa}/${this.form.fields.contrato}`).then(({data}) => {
        this.form.data = data.reportes_contratos
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    exportCSV () {
      let uri = process.env.API + `reportes/exportCSV_proyectos/${this.form.fields.estado}/${this.form.fields.municipio}/${this.form.fields.programa}/${this.form.fields.empresa}/${this.form.fields.contrato}}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.recurso = 0
      this.form.fields.empresa = 0
      this.form.fields.cliente = 0
      this.form.fields.estado = 0
      this.form.fields.municipio = 0
      this.form.fields.programa = 0
      this.form.fields.contrato = 0
    },
    cargaMunicipios () {
      this.municipiosOptions = []
      this.form.fields.municipio = 0
      this.getMunicipiosByEstado({estado_id: this.form.fields.estado}).then(({data}) => {
        this.municipiosOptions.push({label: 'Todos', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    }
  }
}
</script>

<style scoped>
</style>
