<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-4">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Cobranza" to="/cobranza" />
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
        <div class="col q-pa-md" style="padding: 0;">
          <div class="row q-col-gutter-xs">
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form.fields.recurso" inverted color="dark" float-label="Recurso" :options="programasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.cliente" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md">
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
                    <q-td  key="tipo" :props="props" style="text-align: left;">{{ props.row.tipo }}</q-td>
                    <q-td  key="anio" :props="props" style="text-align: left;">{{ props.row.anio }}</q-td>
                    <q-td key="municipio" :props="props">{{ props.row.municipio}}</q-td>
                    <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                    <q-td key="aliado" :props="props">{{ props.row.aliado }}</q-td>
                    <q-td key="num_contrato" :props="props">{{ props.row.num_contrato }}</q-td>
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="recurso" :props="props">{{ props.row.recurso }}</q-td>
                    <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                    <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                    <q-td key="monto_total" :props="props">${{ props.row.monto_total }}</q-td>
                    <q-td key="contratos_impact" :props="props">${{ props.row.contratos_impact }}</q-td>
                    <q-td key="facturado_impact" :props="props">${{ props.row.facturado_impact }}</q-td>
                    <!-- <q-td key="facturado_notas" :props="props">${{ props.row.facturado_notas }}</q-td>
                    <q-td key="facturado_total" :props="props">${{ props.row.facturado_total }}</q-td> -->
                    <q-td key="porcentaje_facturado" :props="props">{{ props.row.porcentaje_facturado }}%</q-td>
                    <q-td key="falta_facturar" :props="props">${{ props.row.falta_facturar }}</q-td>
                    <q-td key="cobrado" :props="props">${{ props.row.cobrado }}</q-td>
                    <q-td key="falta_cobrar" :props="props">${{ props.row.falta_cobrar }}</q-td>
                    <q-td key="facturado_notas" :props="props">${{ props.row.facturado_notas }}</q-td>
                    <q-td key="actions" :props="props"><q-btn small flat @click="filtrar(props.row)" color="blue-6" icon="visibility" v-if="props.row.tipo !== 'REMISIONES'">
                          <q-tooltip>Ver</q-tooltip>
                        </q-btn></q-td>
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

    <q-modal no-backdrop-dismiss v-if="modal_facturas" style="background-color: rgba(0,0,0,0.7);" v-model="modal_facturas" :content-css="{width: '80vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Facturas
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="modal_facturas = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <div class="row q-mt-sm" style="margin-top:70px;margin-left:20px;margin-right:20px;margin-bottom:20px;">
        <div class="row" style="width:60vw;">
          <q-search hide-underline color="secondary" v-model="form.filter_factura"/>
        </div>
        <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
          <q-table id="sticky-table"
            :data="form.data_facturas"
            :columns="form.columnsFacturas"
            :selected.sync="form.selected"
            :filter="form.filter_factura"
            color="positive"
            title=""
            :dense="true"
            :pagination.sync="form.pagination"
            :loading="form.loading"
            :rows-per-page-options="form.rowsOptions">
            <template slot="body" slot-scope="props">
              <q-tr :props="props">
                <q-td key="codigo" :props="props" style="cursor: pointer;">{{ props.row.codigo }}</q-td>
                <q-td key="cliente" :props="props" style="cursor: pointer;">{{ props.row.cliente }}</q-td>
                <q-td key="name" :props="props" style="cursor: pointer;">{{ props.row.name }}</q-td>
                <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                <!-- <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td> -->
                <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
                <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                <q-td key="cancelada" :props="props"><q-checkbox readonly v-model="props.row.cancelada" color="green-10"/></q-td>
              </q-tr>
            </template>
          </q-table>
          <q-inner-loading :visible="form.loading">
            <q-spinner-dots size="64px" color="primary" />
          </q-inner-loading>
        </div>
      </div>
    </q-modal>
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      reportes: [],
      // proyectosOptions2: [],
      // proveedoresOptions2: [],
      // empresasOptions2: [],
      views: {
        grid: true,
        create: false,
        update: false,
        grid_direcciones: false,
        create_direccion: false,
        update_direccion: false
      },
      modal_facturas: false,
      form: {
        data: [],
        data_facturas: [],
        fields: {
          recurso: 0,
          cliente: 0,
          empresa: 0
        },
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'anio', label: 'Año', field: 'anio', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Contacto', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'num_contrato', label: 'Número de contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'recurso', label: 'Recurso', field: 'recurso', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_total', label: 'Monto total', field: 'monto_total', sortable: true, type: 'string', align: 'right' },
          { name: 'contratos_impact', label: 'Contratos IMPACT', field: 'contratos_impact', sortable: true, type: 'string', align: 'right' },
          { name: 'facturado_impact', label: 'Facturado IMPACT', field: 'facturado_impact', sortable: true, type: 'string', align: 'right' },
          // { name: 'facturado_notas', label: 'Notas de crédito', field: 'facturado_notas', sortable: true, type: 'string', align: 'right' },
          // { name: 'facturado_total', label: 'Facturado - notas', field: 'facturado_total', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_facturado', label: '% Facturado', field: 'porcentaje_facturado', sortable: true, type: 'string', align: 'right' },
          { name: 'falta_facturar', label: 'Falta facturar', field: 'falta_facturar', sortable: true, type: 'string', align: 'right' },
          { name: 'cobrado', label: 'Cobrado', field: 'cobrado', sortable: true, type: 'string', align: 'right' },
          { name: 'falta_cobrar', label: 'Falta cobrar', field: 'falta_cobrar', sortable: true, type: 'string', align: 'right' },
          { name: 'facturado_notas', label: 'Descontado', field: 'facturado_notas', sortable: true, type: 'string', align: 'right' },
          { name: 'actions', label: 'Ver', field: 'actions', sortable: true, type: 'string', align: 'right' }
        ],
        columnsFacturas: [
          { name: 'codigo', label: 'Proyecto', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Factura', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_factura', label: 'Monto factura', field: 'monto_factura', sortable: true, type: 'string', align: 'right' },
          // { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_total_abonado', label: 'Monto abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Monto restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'cancelada', label: 'Cancelada', field: 'cancelada', sortable: true, type: 'string', align: 'left' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        filter_factura: '',
        loading: false
      }
    }
  },
  mounted () {
    this.loadAll()
  },
  computed: {
    programasOptions () {
      let programas = this.$store.getters['vnt/programa/selectOptions']
      programas.push({ 'label': 'Todos', 'value': 0 })
      return programas
    },
    clientesOptions () {
      let clientes = this.$store.getters['ventas/clientes/selectOptions']
      clientes.push({ 'label': 'Todos', 'value': 0 })
      return clientes
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({ 'label': 'Todos', 'value': 0 })
      return empresas
    }
  },
  methods: {
    ...mapActions({
      getProgramas: 'vnt/programa/refresh',
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getFacturas: 'vnt/contratoFactura/getFacturasByContratoandId'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getProgramas()
      // this.proyectosOptions2 = this.getProyectos()
      await this.getEmpresas()
      await this.getClientes()
      await this.cargarDatosReporte()
      this.form.loading = false
    },
    async cargarDatosReporte () {
      this.form.loading = true
      this.form.data = []
      await axios.get(`/reportes/getReporteCobranza/${this.form.fields.recurso}/${this.form.fields.cliente}/${this.form.fields.empresa}/${this.year}`).then(({data}) => {
        this.form.data = data.reportes_cobranza
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    exportCSV () {
      let uri = process.env.API + `reportes/exportCSV_cobranza/${this.form.fields.recurso}/${this.form.fields.cliente}/${this.form.fields.empresa}/${this.year}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.recurso = 0
      this.form.fields.empresa = 0
      this.form.fields.cliente = 0
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    filtrar (row) {
      this.form.loading = true
      this.getFacturas({proyecto: 0, cliente: 0, contrato: row.id, factura: 0, year: this.year}).then(({data}) => {
        if (data.facturas.length > 0) {
          data.facturas.forEach(function (element) {
            if (element.status === 'EMITIDO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'ABONADO') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            }
          })
          this.form.data_facturas = data.facturas
        } else {
          this.form.data_facturas = data.facturas
        }
        this.modal_facturas = true
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
