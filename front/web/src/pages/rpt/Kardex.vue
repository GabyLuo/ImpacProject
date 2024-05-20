<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Kárdex" to="/" />
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
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="store" icon-color="dark">
                <q-select v-model="form.fields.almacen" inverted color="dark" float-label="Almacen" :options="almacenesOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-box" icon-color="dark">
                <q-select v-model="form.fields.producto" inverted color="dark" float-label="Producto" :options="productosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
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
                    <q-td  key="folio" :props="props" style="text-align: left;">{{ props.row.folio }}</q-td>
                    <q-td key="movimiento" :props="props"><q-chip style="border-radius: 0;" dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.movimiento }}</q-chip></q-td>
                    <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                    <q-td key="created" :props="props">{{ props.row.created }}</q-td>
                    <q-td key="almacen" :props="props">{{ props.row.almacen }}</q-td>
                    <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                    <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                    <q-td key="existencia" :props="props">{{ props.row.existencia }}</q-td>
                    <!-- <q-td key="monto_total" :props="props">${{ props.row.monto_total }}</q-td>
                    <q-td key="contratos_impact" :props="props">${{ props.row.contratos_impact }}</q-td>
                    <q-td key="facturado_impact" :props="props">${{ props.row.facturado_impact }}</q-td>
                    <q-td key="porcentaje_facturado" :props="props">{{ props.row.porcentaje_facturado }}%</q-td>
                    <q-td key="falta_cobrar" :props="props">${{ props.row.falta_cobrar }}</q-td> -->
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase()) {
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
      views: {
        grid: true,
        create: false,
        update: false,
        grid_direcciones: false,
        create_direccion: false,
        update_direccion: false
      },
      form: {
        data: [],
        fields: {
          almacen: 0,
          producto: 0,
          fecha_inicio: null,
          fecha_fin: null
        },
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'movimiento', label: 'Movimiento', field: 'movimiento', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Fecha', field: 'created', sortable: true, type: 'string', align: 'left' },
          { name: 'almacen', label: 'Almacén', field: 'almacen', sortable: true, type: 'string', align: 'left' },
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'existencia', label: 'Saldo', field: 'existencia', sortable: true, type: 'string', align: 'right' } // ,
          /* { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_total', label: 'Monto total', field: 'monto_total', sortable: true, type: 'string', align: 'right' },
          { name: 'contratos_impact', label: 'Contratos IMPACT', field: 'contratos_impact', sortable: true, type: 'string', align: 'right' },
          { name: 'facturado_impact', label: 'Facturado IMPACT', field: 'facturado_impact', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_facturado', label: '% Facturado', field: 'porcentaje_facturado', sortable: true, type: 'string', align: 'right' },
          { name: 'falta_cobrar', label: 'Falta cobrar', field: 'falta_cobrar', sortable: true, type: 'string', align: 'right' } */
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
    almacenesOptions () {
      let almacenes = this.$store.getters['inv/almacenes/selectOtherOptions']
      // almacenes.push({ 'label': 'Todos', 'value': 0 })
      almacenes.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return almacenes
    },
    productosOptions () {
      let productos = this.$store.getters['inv/productos/selectOptions']
      // productos.push({ 'label': 'Todos', 'value': 0 })
      productos.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return productos
    }
  },
  methods: {
    ...mapActions({
      getAlmacenes: 'inv/almacenes/refresh',
      getProductos: 'inv/productos/refresh'// ,
      // getEmpresas: 'vnt/empresa/refresh'
    }),
    async loadAll () {
      await this.getAlmacenes()
      // this.proyectosOptions2 = this.getProyectos()
      await this.getProductos()
      await this.cargarDatosReporte()
    },
    async cargarDatosReporte () {
      // this.form.loading = true
      this.form.data = []
      if (this.form.fields.almacen === 0) {
        this.almacen = null
      } else {
        this.almacen = this.form.fields.almacen
      }
      if (this.form.fields.producto === 0) {
        this.producto = null
      } else {
        this.producto = this.form.fields.producto
      }
      await axios.get(`/reportes/getKardex/${this.almacen}/${this.producto}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}`).then(({data}) => {
        this.form.data = data.kardex
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
      // this.form.loading = false
    },
    exportCSV () {
      if (this.form.fields.almacen === 0) {
        this.almacen = null
      }
      if (this.form.fields.producto === 0) {
        this.producto = null
      }
      let uri = process.env.API + `reportes/exportCSV_kardex/${this.almacen}/${this.producto}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.almacen = 0
      this.form.fields.producto = 0
      this.form.fields.fecha_inicio = null
      this.form.fields.fecha_fin = null
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
