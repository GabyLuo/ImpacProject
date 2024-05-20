<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-4">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Reporte de Licitaciones" to="/" />
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
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form.fields.recurso" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select v-model="form.fields.estado" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
              </q-field>
            </div>
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-select v-model="form.fields.status" inverted color="dark" float-label="Status" :options="statusOptions" filter></q-select>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="q-px-md bg-grey-3">
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
                    <q-td  key="folio" :props="props">{{ props.row.folio }}</q-td>
                    <q-td key="proyecto" :props="props">{{ props.row.proyecto}}</q-td>
                    <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                    <q-td key="observaciones_licitacion" :props="props">{{ props.row.observaciones_licitacion }}</q-td>
                    <q-td key="entidad" :props="props">{{ props.row.entidad }}</q-td>
                    <q-td key="procedimiento" :props="props">{{ props.row.procedimiento }}</q-td>
                    <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>

                    <q-td key="contrato" :props="props">{{ props.row.contrato }}</q-td>
                    <q-td key="orden_compra" :props="props">{{ props.row.orden_compra }}</q-td>
                    <q-td key="monto_inicial" :props="props">${{ props.row.monto_inicial }}</q-td>
                    <q-td key="monto_adjudicado" :props="props">${{ props.row.monto_adjudicado }}</q-td>
                    <q-td key="comprador" :props="props">{{ props.row.comprador }}</q-td>
                    <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                    <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                    <q-td key="respaldo_1" :props="props">{{ props.row.respaldo_1 }}</q-td>
                    <q-td key="respaldo_2" :props="props">{{ props.row.respaldo_2 }}</q-td>
                    <q-td key="respaldo_3" :props="props">{{ props.row.respaldo_3 }}</q-td>
                    <q-td key="respaldo_4" :props="props">{{ props.row.respaldo_4 }}</q-td>
                    <q-td key="fecha_inicio" :props="props">{{ props.row.fecha_inicio }}</q-td>
                    <q-td key="fecha_presentacion" :props="props">{{ props.row.fecha_presentacion }}</q-td>
                    <q-td key="fecha_fallo" :props="props">{{ props.row.fecha_fallo }}</q-td>
                    <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                    <q-td key="responsable" :props="props">{{ props.row.responsable }}</q-td>
                    <q-td key="responsable_gdp" :props="props">{{ props.row.responsable_gdp }}</q-td>
                    <q-td key="oportunidad" :props="props">{{ props.row.oportunidad }}</q-td>
                    <q-td key="status_crm" :props="props">{{ props.row.status_crm }}</q-td>
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
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
      statusOptions: [ { 'label': 'CREADA', 'value': 'CREADA' }, { 'label': 'EN PROCESO', 'value': 'EN PROCESO' }, { 'label': 'PRESENTADA', 'value': 'PRESENTADA' }, { 'label': 'ADJUDICADA', 'value': 'ADJUDICADA' }, { 'label': 'NO ADJUDICADA', 'value': 'NO ADJUDICADA' }, { 'label': 'CANCELADA', 'value': 'CANCELADA' }, { 'label': 'Todos', 'value': 0 } ],
      form: {
        fields: {
          recurso: 0,
          cliente: 0,
          empresa: 0,
          estado: 0,
          municipio: 0,
          programa: 0,
          contrato: 0,
          status: 0
        },
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'observaciones_licitacion', label: 'Observaciones', field: 'observaciones_licitacion', sortable: true, type: 'string', align: 'left' },
          { name: 'entidad', label: 'Entidad', field: 'entidad', sortable: true, type: 'string', align: 'left' },
          { name: 'procedimiento', label: 'Procedimiento', field: 'procedimiento', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'contrato', label: 'Contrato', field: 'contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'orden_compra', label: 'Orden de compra', field: 'orden_compra', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_inicial', label: 'Monto inicial', field: 'monto_inicial', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_adjudicado', label: 'Monto adjudicado', field: 'monto_adjudicado', sortable: true, type: 'string', align: 'right' },
          { name: 'comprador', label: 'Comprador', field: 'comprador', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Empresa concursante', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa aliada', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'respaldo_1', label: 'Respaldo 1', field: 'respaldo_1', sortable: true, type: 'string', align: 'left' },
          { name: 'respaldo_2', label: 'Respaldo 2', field: 'respaldo_2', sortable: true, type: 'string', align: 'left' },
          { name: 'respaldo_3', label: 'Respaldo 3', field: 'respaldo_3', sortable: true, type: 'string', align: 'left' },
          { name: 'respaldo_4', label: 'Respaldo 4', field: 'respaldo_4', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_inicio', label: 'Fecha inicio', field: 'fecha_inicio', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_presentacion', label: 'Fecha presentación', field: 'fecha_presentacion', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_fallo', label: 'Fecha fallo', field: 'fecha_fallo', sortable: true, type: 'string', align: 'left' },
          { name: 'nickname', label: 'Usuario', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable', label: 'Responsable', field: 'responsable', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable_gdp', label: 'Responsable GDP', field: 'responsable_gdp', sortable: true, type: 'string', align: 'left' },
          { name: 'oportunidad', label: 'Oportunidad', field: 'oportunidad', sortable: true, type: 'string', align: 'left' },
          { name: 'status_crm', label: 'Status CRM', field: 'status_crm', sortable: true, type: 'string', align: 'left' }
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
    recursosOptions () {
      let recursos = this.$store.getters['vnt/recurso/selectOptions']
      recursos.push({ 'label': 'Todos', 'value': 0 })
      return recursos
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
      getRecursos: 'vnt/recurso/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form.loading = true
      // await this.getProgramas()
      // this.proyectosOptions2 = this.getProyectos()
      await this.getRecursos()
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
      await axios.get(`/reportes/reporteLicitaciones/${this.form.fields.estado}/${this.form.fields.recurso}/${this.form.fields.status}/${this.form.fields.empresa}/${this.year}`).then(({data}) => {
        this.form.data = data.licitaciones
      }).catch(error => {
        console.error(error)
      })
      /* await axios.get(`/reportes/reporteLicitaciones`).then(({data}) => {
        this.form.data = data.licitaciones
      }).catch(error => {
        console.error(error)
      }) */
      this.form.loading = false
    },
    exportCSV () {
      let uri = process.env.API + `/reportes/exportCSV_reporteLicitaciones/${this.form.fields.estado}/${this.form.fields.recurso}/${this.form.fields.status}/${this.form.fields.empresa}/${this.year}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.status = 0
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
