<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Reporte de facturas" to="/" />
            </q-breadcrumbs>
          </div>
        </div>
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
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.cliente" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-2">
              <q-field icon="fas fa-folder-open" icon-color="dark">
                <q-input v-model="form.fields.folio" inverted color="dark" float-label="Folio" filter/>
              </q-field>
            </div>
            <div class="col-sm-2">
              <q-field icon="fas fa-folder" icon-color="dark">
                <q-input v-model="form.fields.ffiscal" inverted color="dark" float-label="Folio Fiscal" filter/>
              </q-field>
            </div>
            <div class="col-sm-2">
              <q-field icon="fas fa-clipboard" icon-color="dark">
                <q-input v-model="form.fields.remision" inverted color="dark" float-label="Remisión" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime mask="YYYY/MM/DD" v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime mask="YYYY/MM/DD" v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md" style="padding: 0;">
          <div class="col q-pa-mdl">
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
                    <q-td key="tipo" :props="props" style="text-align: left;">{{ props.row.tipo }}</q-td>
                    <q-td key="fecha_venta" :props="props">{{ props.row.fecha_venta }}</q-td>
                    <q-td key="year" :props="props">{{ props.row.year }}</q-td>
                    <q-td key="municipio" :props="props">{{ props.row.municipio }}</q-td>
                    <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                    <q-td key="num_contrato" :props="props">{{ props.row.num_contrato }}</q-td>
                    <q-td key="folio_remision" :props="props">{{ props.row.folio_remision }}</q-td>
                    <q-td key="codigo" :props="props">{{ props.row.codigo }}</q-td>
                    <q-td key="proyecto" :props="props">{{ props.row.proyecto }}</q-td>
                    <q-td key="monto_contrato" :props="props">${{ props.row.monto_contrato }}</q-td>
                    <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                    <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                    <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                    <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                    <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                    <q-td key="monto_factura" :props="props">${{ props.row.monto_factura }}</q-td>
                    <q-td key="amortizacion" :props="props">{{ props.row.amortizacion }}</q-td>
                    <q-td key="abonos" :props="props">${{ props.row.abonos }}</q-td>
                    <q-td key="falta_cobrar" :props="props">${{ props.row.falta_cobrar }}</q-td>
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
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      views: {
        grid: true
      },
      form: {
        data: [],
        fields: {
          cliente: 0,
          empresa: 0,
          folio: '',
          ffiscal: '',
          remision: '',
          fecha_inicio: '',
          fecha_fin: ''
        },
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_venta', label: 'Fecha factura', field: 'fecha_venta', sortable: true, type: 'string', align: 'left' },
          { name: 'year', label: 'Año', field: 'year', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'num_contrato', label: 'Número de contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_remision', label: 'Folio remisión', field: 'folio_remision', sortable: true, type: 'string', align: 'left' },
          { name: 'codigo', label: 'Código del proyecto', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Nombre del proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_contrato', label: 'Importe del contrato', field: 'monto_contrato', sortable: true, type: 'string', align: 'right' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_factura', label: 'Monto total', field: 'monto_factura', sortable: true, type: 'string', align: 'right' },
          { name: 'amortizacion', label: 'Amortizacion', field: 'amortizacion', sortable: true, type: 'string', align: 'left' },
          { name: 'abonos', label: 'Abonos', field: 'abonos', sortable: true, type: 'string', align: 'right' },
          { name: 'falta_cobrar', label: 'Falta cobrar', field: 'falta_cobrar', sortable: true, type: 'string', align: 'right' }
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
    console.log(this.form.fields)
    // this.todos()
  },
  computed: {
    clientesOptions () {
      let clientes = this.$store.getters['ventas/clientes/selectOptions']
      clientes.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return clientes
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return empresas
    }
  },
  methods: {
    ...mapActions({
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh'
    }),
    async loadAll () {
      await this.getClientes()
      await this.getEmpresas()
      await this.cargarDatosReporte()
    },
    async cargarDatosReporte () {
      var fol = this.form.fields.folio
      if (this.form.fields.folio === '') {
        fol = '$$$FOL$$$'
      }
      var ffol = this.form.fields.ffiscal
      if (this.form.fields.ffiscal === '') {
        ffol = '$$$FFIS$$$'
      }
      var rem = this.form.fields.remision
      if (this.form.fields.remision === '') {
        rem = '$$$DEL$$$'
      }
      var finicio = this.form.fields.fecha_inicio
      if (this.form.fields.fecha_inicio === '') {
        finicio = '$$$FINI$$$'
      }
      var ffin = this.form.fields.fecha_fin
      if (this.form.fields.fecha_fin === '') {
        ffin = '$$$FIN$$$'
      }
      await axios.get(`/reportes/get_facturas/${this.form.fields.cliente}/${this.form.fields.empresa}/${fol}/${ffol}/${rem}/${finicio}/${ffin}`).then(({data}) => {
        this.form.data = data.facturas
      }).catch(error => {
        console.error(error)
      })
      // this.form.loading = false
    },
    exportCSV () {
      var ff = this.form.fields.fecha_fin
      var fol = this.form.fields.folio
      if (this.form.fields.folio === '') {
        fol = '$$$FOL$$$'
      }
      var ffol = this.form.fields.ffiscal
      if (this.form.fields.ffiscal === '') {
        ffol = '$$$FFIS$$$'
      }
      var rem = this.form.fields.remision
      if (this.form.fields.remision === '') {
        rem = '$$$DEL$$$'
      }
      var finicio = this.form.fields.fecha_inicio
      if (this.form.fields.fecha_inicio === '') {
        finicio = '$$$FINI$$$'
      }
      var ffin = ff.slice(0, 11) + '23:59:59'
      if (this.form.fields.fecha_fin === '') {
        ffin = '$$$FIN$$$'
      }
      let uri = process.env.API + `reportes/exportCSV_facturas/${this.form.fields.cliente}/${this.form.fields.empresa}/${fol}/${ffol}/${rem}/${finicio}/${ffin}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.cliente = 0
      this.form.fields.empresa = 0
      this.form.fields.folio = ''
      this.form.fields.ffiscal = ''
      this.form.fields.remision = ''
      this.form.fields.fecha_inicio = ''
      this.form.fields.fecha_fin = ''
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
