<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-6">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Egresos" to="/" />
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
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions2" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="folder" icon-color="dark">
                <q-select v-model="form.fields.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions2" filter @input="getProyectosBy()"/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Project" :options="proyectosOptions" filter v-if="this.role !== 'Auxiliar'"/>
                <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Project" :options="proyectosAuxiliarOpt" filter v-else/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions2" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.lider_id" inverted color="dark" float-label="Líder" :options="usuariosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="folder" icon-color="dark">
                <q-select v-model="form.fields.concepto_id" inverted color="dark" float-label="Concepto" :options="conceptosOptions" filter @input="getSubconceptosByconcepto()"/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="folder_open" icon-color="dark">
                <q-select v-model="form.fields.subconcepto_id" inverted color="dark" float-label="Subconcepto" :options="subconceptosOpt" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="forward" icon-color="dark">
                <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="selectStatus" readobnly filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="forward" icon-color="dark">
                <q-select v-model="form.fields.factura" inverted color="dark" float-label="Factura" :options="selectFactura" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="forward" icon-color="dark">
                <q-select v-model="form.fields.comprobado" inverted color="dark" float-label="Comprobado" :options="selectComprobado" filter/>
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
                    <q-td  key="lider" :props="props" style="text-align: left;">{{ props.row.lider }}</q-td>
                    <q-td  key="nombre_project" :props="props" style="text-align: left;">{{ props.row.nombre_project }}</q-td>
                    <q-td  key="cliente" :props="props" style="text-align: left;">{{ props.row.cliente }}</q-td>
                    <q-td  key="sucursal" :props="props" style="text-align: left;">{{ props.row.sucursal }}</q-td>
                    <q-td  key="proyecto_comision" :props="props" style="text-align: left;">{{ props.row.proyecto_comision }}</q-td>
                    <q-td  key="status" :props="props" style="text-align: left;">{{ props.row.status }}</q-td>
                    <q-td  key="colaborador" :props="props" style="text-align: left;">{{ props.row.colaborador }}</q-td>
                    <q-td key="codigo" :props="props">{{ props.row.codigo}}</q-td>
                    <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto}}</q-td>
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                    <q-td key="fecha_creado" :props="props">{{ props.row.fecha_creado }}</q-td>
                    <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                    <q-td key="concepto" :props="props">{{ props.row.concepto }}</q-td>
                    <q-td key="subconcepto" :props="props">{{ props.row.subconcepto }}</q-td>
                    <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                    <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                    <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                    <q-td key="subempresa" :props="props">{{ props.row.subempresa }}</q-td>
                    <q-td key="banco" :props="props">{{ props.row.banco }}</q-td>
                    <q-td key="clabe" :props="props">{{ props.row.clabe }}</q-td>
                    <!-- <q-td key="presupuesto_actual" :props="props">${{ props.row.presupuesto_actual }}</q-td> -->
                    <q-td key="subtotal" :props="props">${{ props.row.subtotal }}</q-td>
                    <q-td key="iva" :props="props">${{ props.row.iva }}</q-td>
                    <q-td key="total" :props="props">${{ props.row.total }}</q-td>
                    <q-td key="cantidad_comision" :props="props">${{ props.row.cantidad_comision }}</q-td>
                    <q-td key="total_comision" :props="props">${{ props.row.total_comision }}</q-td>
                    <q-td key="egresos" :props="props">{{ props.row.egresos }}</q-td>
                    <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                    <q-td key="factura" :props="props">{{ props.row.factura }}</q-td>
                    <q-td key="monto_cobrado" :props="props">${{ props.row.monto_cobrado }}</q-td>
                    <q-td key="porcentaje_cobrado" :props="props">{{ props.row.porcentaje_cobrado }}%</q-td>
                    <q-td key="monto_comprobado" :props="props">{{ props.row.monto_comprobado }}</q-td>
                    <q-td key="solicitud_facturada" :props="props"><q-field :icon="props.row.icon_factura" :icon-color="props.row.color_factura"></q-field></q-td>
                    <q-td key="fecha_pago2" :props="props">{{ props.row.fecha_pago }}</q-td>
                    <q-td key="mes" :props="props">{{ props.row.mes }}</q-td>
                    <q-td key="rubro" :props="props">{{ props.row.rubro }}</q-td>
                    <!-- <q-td key="cantidad_disponible" :props="props">${{ props.row.cantidad_disponible }}</q-td> -->
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
      // if (vm.$store.getters['user/rolesIds'].includes(vm.$roles.SUPER_ADMIN) || vm.$store.getters['user/privileges'].include[vm.$privileges.VER_patron]) {
      //   next()
      // } else {
      //   next(from.path === '/' ? '/dashboard' : from.path)
      // }
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase()) {
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
      role: '',
      user_id: 0,
      proyectosAuxiliarOpt: [],
      proyectosOptions: [],
      reportes: [],
      subconceptosOpt: [{'label': 'Todos', 'value': 0}],
      selectStatus: [ { 'label': 'CREADO', 'value': 'CREADO' }, { 'label': 'CANCELADO', 'value': 'CANCELADO' }, { 'label': 'POR AUTORIZAR', 'value': 'POR AUTORIZAR' }, { 'label': 'RECHAZADO', 'value': 'RECHAZADO' }, { 'label': 'POR PAGAR', 'value': 'POR PAGAR' }, { 'label': 'PAGADO', 'value': 'PAGADO' }, { 'label': 'PAGO PARCIAL', 'value': 'PAGO PARCIAL' }, { 'label': 'Todos', 'value': 'all' } ],
      selectFactura: [ { 'label': 'SI', 'value': 'SI' }, { 'label': 'NO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ],
      selectComprobado: [ { 'label': 'COMPROBADO', 'value': 'SI' }, { 'label': 'NO COMPROBADO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ],
      // proyectosOptions2: [],
      // proveedoresOptions2: [],
      // empresasOptions2: [],
      loadingButton: false,
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
          id: 0,
          razon_social: '',
          rfc: '',
          rep_legal: '',
          curp_representante: '',
          regimen: '',
          nombre_corto: '',
          rfc_representante: '',
          recurso_id: 0,
          proyecto_id: 0,
          proveedor_id: 0,
          empresa_id: 0,
          status: 'PAGADO',
          factura: 'all',
          comprobado: 'all',
          fecha_inicio: null,
          fecha_fin: null,
          lider_id: 0,
          concepto_id: 0,
          subconcepto_id: 0
        },
        columns: [
          { name: 'lider', label: 'Líder', field: 'lider', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_project', label: 'Project', field: 'nombre_project', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'sucursal', label: 'Sucursal', field: 'sucursal', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto_comision', label: 'Presupuesto comisión', field: 'proyecto_comision', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'colaborador', label: 'Colaborador', field: 'colaborador', sortable: true, type: 'string', align: 'left' },
          { name: 'codigo', label: 'Clave/Proyecto', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_proyecto', label: 'Proyecto', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Tarea', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'id', label: '# Solicitud', field: 'id', sortable: true, type: 'string', align: 'right' },
          { name: 'fecha_creado', label: 'Fecha creado', field: 'fecha_creado', sortable: true, type: 'string', align: 'right' },
          { name: 'fecha_pago', label: 'Fecha pago', field: 'fecha_pago', sortable: true, type: 'string', align: 'right' },
          { name: 'concepto', label: 'Concepto general', field: 'concepto', sortable: true, type: 'string', align: 'left' },
          { name: 'subconcepto', label: 'Suboncepto', field: 'subconcepto', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Beneficiario', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Concepto específico', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'subempresa', label: 'Subempresa', field: 'subempresa', sortable: true, type: 'string', align: 'left' },
          { name: 'banco', label: 'Banco beneficiario', field: 'banco', sortable: true, type: 'string', align: 'left' },
          { name: 'clabe', label: 'Cuenta beneficiaria', field: 'clabe', sortable: true, type: 'string', align: 'right' },
          // { name: 'presupuesto_actual', label: 'Presupuesto project', field: 'presupuesto_actual', sortable: true, type: 'string', align: 'right' },
          { name: 'subtotal', label: 'Subtotal', field: 'subtotal', sortable: true, type: 'string', align: 'right' },
          { name: 'iva', label: 'Monto IVA', field: 'iva', sortable: true, type: 'string', align: 'right' },
          { name: 'total', label: 'Monto Total', field: 'total', sortable: true, type: 'string', align: 'right' },
          { name: 'cantidad_comision', label: 'Monto comisión', field: 'cantidad_comision', sortable: true, type: 'string', align: 'right' },
          { name: 'total_comision', label: 'Total + comisión', field: 'total_comision', sortable: true, type: 'string', align: 'right' },
          { name: 'egresos', label: 'Tipo', field: 'egresos', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Concluido', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'factura', label: 'No. Factura', field: 'factura', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_cobrado', label: 'Monto cobrado', field: 'monto_cobrado', sortable: true, type: 'string', align: 'right' },
          { name: 'porcentaje_cobrado', label: '% cobrado', field: 'porcentaje_cobrado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_comprobado', label: 'Monto comprobado', field: 'monto_comprobado', sortable: true, type: 'string', align: 'left' },
          { name: 'solicitud_facturada', label: 'Factura', field: 'solicitud_facturada', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_pago2', label: 'Posible fecha de pago', field: 'fecha_pago2', sortable: true, type: 'string', align: 'right' },
          { name: 'mes', label: 'Mes', field: 'mes', sortable: true, type: 'string', align: 'left' },
          { name: 'rubro', label: 'Rubro', field: 'rubro', sortable: true, type: 'string', align: 'left' }
          // { name: 'cantidad_disponible', label: 'Disponible project', field: 'cantidad_disponible', sortable: true, type: 'string', align: 'right' }
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
    ...mapGetters({
      empresasOptions: 'vnt/empresa/selectOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions',
      recursosOptions: 'vnt/recurso/selectOptionsName'
    }),
    proveedoresOptions2 () {
      let proveedores = this.$store.getters['pmo/proveedor/selectOptions']
      proveedores.push({ 'label': 'Todos', 'value': 0 })
      return proveedores
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
    },
    conceptosOptions () {
      let conceptos = this.$store.getters['vnt/concepto/selectOptions']
      conceptos.push({ 'label': 'Todos', 'value': 0 })
      return conceptos
    },
    recursosOptions2 () {
      let recursos = this.$store.getters['vnt/recurso/selectOptionsName']
      recursos.push({ 'label': 'Todos', 'value': 0 })
      return recursos
    }
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getProyectosByRecurso: 'pmo/proyecto/getByRecurso',
      getRecursos: 'vnt/recurso/refresh',
      getProyectosPerfilesByRecurso: 'pmo/proyecto/getProyectosPerfilesByRecurso',
      getUsers: 'sys/users/refresh',
      getConceptos: 'vnt/concepto/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getSubconceptos: 'vnt/subconcepto/getByConcepto',
      getProveedores: 'pmo/proveedor/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getRole()
      await this.getRecursos()
      // this.proyectosOptions2 = this.getProyectos()
      await this.getEmpresas()
      await this.getProveedores()
      await this.getUsers()
      await this.getConceptos()
      await this.getProyectosAuxiliar()
      await this.getProyectosBy()
      // await this.cargarDatosReporte()
      this.form.loading = false
    },
    async getRole () {
      await this.getUser().then(({data}) => {
        this.role = data.role[0]
        this.user_id = data.user.id
        console.log(data.user.id)
      }).catch(error => {
        console.log(error)
      })
    },
    async cargarDatosReporte () {
      this.form.loading = true
      this.form.data = []
      await axios.get(`/reportes/getReporteGastos/${this.form.fields.recurso_id}/${this.form.fields.proyecto_id}/${this.form.fields.proveedor_id}/${this.form.fields.empresa_id}/${this.form.fields.lider_id}/${this.form.fields.concepto_id}/${this.form.fields.subconcepto_id}/${this.form.fields.factura}/${this.form.fields.status}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}/${this.form.fields.comprobado}`).then(({data}) => {
        this.form.data = data.reportes_gastos
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    exportCSV () {
      if (this.role === 'Finanzas/Comisiones') {
        let uri = process.env.API + `reportes/exportCSV_gastos/${this.form.fields.recurso_id}/${this.form.fields.proyecto_id}/${this.form.fields.proveedor_id}/${this.form.fields.empresa_id}/${this.form.fields.lider_id}/${this.form.fields.concepto_id}/${this.form.fields.subconcepto_id}/${this.form.fields.factura}/${this.form.fields.status}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}/Finanzas/${this.user_id}/${this.form.fields.comprobado}`
        window.open(uri, '_blank')
      } else {
        let uri = process.env.API + `reportes/exportCSV_gastos/${this.form.fields.recurso_id}/${this.form.fields.proyecto_id}/${this.form.fields.proveedor_id}/${this.form.fields.empresa_id}/${this.form.fields.lider_id}/${this.form.fields.concepto_id}/${this.form.fields.subconcepto_id}/${this.form.fields.factura}/${this.form.fields.status}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}/${this.role}/${this.user_id}/${this.form.fields.comprobado}`
        window.open(uri, '_blank')
      }
    },
    borrar () {
      this.form.fields.proyecto_id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.proveedor_id = 0
      this.form.fields.lider_id = 0
      this.form.fields.concepto_id = 0
      this.form.fields.subconcepto_id = 0
      this.form.fields.status = 'PAGADO'
      this.form.fields.factura = 'all'
      this.form.fields.comprobado = 'all'
      this.form.fields.fecha_inicio = null
      this.form.fields.fecha_fin = null
    },
    async getProyectosBy () {
      this.proyectosOptions = []
      this.getProyectosByRecurso({recurso_id: this.form.fields.recurso_id}).then(({data}) => {
        if (data.result === 'success') {
          this.proyectosOptions = data.proyectos
          this.proyectosOptions.push({ 'label': 'Todos', 'value': 0 })
          this.form.fields.proyecto_id = 0
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getSubconceptosByconcepto () {
      this.subconceptosOpt = []
      this.getSubconceptos({concepto_id: this.form.fields.concepto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.subconceptosOpt = data.subconceptos
          this.subconceptosOpt.push({ 'label': 'Todos', 'value': 0 })
          this.form.fields.subconcepto_id = 0
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getProyectosAuxiliar () {
      this.proyectosAuxiliarOpt = []
      await this.getProyectosPerfilesByRecurso({recurso: 0}).then(({data}) => {
        if (data.result === 'success') {
          this.proyectosAuxiliarOpt = data.proyectos
          this.proyectosAuxiliarOpt.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
