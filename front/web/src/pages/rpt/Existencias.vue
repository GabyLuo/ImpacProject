<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Existencias" to="/" />
            </q-breadcrumbs>
          </div>
        </div>

        <div class="col-sm-6 pull-right">
          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
            <q-btn @click="generarCsvExistencias()" color="green" icon="fas fa-file-excel" style="margin-top:5px;"><q-tooltip>Generar CSV</q-tooltip></q-btn>
            <q-btn @click="generarPdfExistencias()" color="red-14" icon="fas fa-file-pdf" style="margin-top:5px;"><q-tooltip>Generar PDF</q-tooltip></q-btn>
            <q-btn @click="cargarDatosReporte()" color="green" icon="fas fa-search" :loading="loadingButton" style="margin-top:5px;"><q-tooltip>Buscar</q-tooltip></q-btn>
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
                    <q-td key="almacen" :props="props">{{ props.row.almacen }}</q-td>
                    <q-td key="categoria" :props="props">{{ props.row.categoria }}</q-td>
                    <q-td key="linea" :props="props">{{ props.row.linea }}</q-td>
                    <q-td key="presentacion" :props="props">{{ props.row.presentacion }}</q-td>
                    <q-td  key="codigo_categoria" :props="props" style="text-align: left;">{{ props.row.codigo_categoria }} - {{ props.row.codigo_linea }} - {{ props.row.codigo_producto }}</q-td>
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="existencia" :props="props">{{ props.row.existencia }}</q-td>
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
          { name: 'almacen', label: 'Almacen', field: 'almacen', sortable: true, type: 'string', align: 'left' },
          { name: 'categoria', label: 'Categoría', field: 'categoria', sortable: true, type: 'string', align: 'left' },
          { name: 'linea', label: 'Línea', field: 'linea', sortable: true, type: 'string', align: 'left' },
          { name: 'presentacion', label: 'Presentación', field: 'presentacion', sortable: true, type: 'string', align: 'left' },
          { name: 'codigo_categoria', label: 'Código', field: 'codigo_categoria', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Producto', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'existencia', label: 'Existencias', field: 'existencia', sortable: true, type: 'string', align: 'right' } // ,
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
      let almacenes = this.$store.getters['inv/almacenes/selectOptions']
      almacenes.push({ 'label': 'Todos', 'value': 0 })
      return almacenes
    },
    productosOptions () {
      let productos = this.$store.getters['inv/productos/selectOptions']
      productos.push({ 'label': 'Todos', 'value': 0 })
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
      await axios.get(`/reportes/existencias_productos/${this.almacen}/${this.producto}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}`).then(({data}) => {
        this.form.data = data.existencias
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
      // this.form.loading = false
    },
    generarCsvExistencias () {
      if (this.form.fields.almacen === 0) {
        this.almacen = null
      }
      if (this.form.fields.producto === 0) {
        this.producto = null
      }
      const uri = process.env.API + `/reportes/existencias_productos_csv/${this.almacen}/${this.producto}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.almacen = 0
      this.form.fields.producto = 0
      this.form.fields.fecha_inicio = null
      this.form.fields.fecha_fin = null
    },
    generarPdfExistencias () {
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
      const uri = process.env.API + `/reportes/existencias_productos_pdf/${this.almacen}/${this.producto}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}`
      window.open(uri, '_blank')
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
