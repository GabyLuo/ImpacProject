<template>
  <q-page>
    <div class="layout-padding">
      <div class="row" v-if="views.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="forward" disable class="btn_categoria" label="SOLICITUDES" />
            </div>
            <div class="col-sm-6">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="newSolicitud()" class="btn_nuevo" icon="add">
                    Nueva Solicitud
                  </q-btn>
                </div>
              </div>
            </div>
          </div>

          <div class="row q-mt-lg" style="margin-top: 50px;">
            <div class="col-sm-4">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosOptions2" filter @input="selectActividades()"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="folder" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesByProyectoOptions2" filter @input="presupuestoActividad()"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="forward" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.status" inverted color="dark" float-label="Status" :options="form_solicitudes.selectStatus" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.responsable_id" inverted color="dark" float-label="Asignado a:" :options="usuariosOptions2" filter/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fa-check" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.comprobado" inverted color="dark" float-label="Comprobado" :options="form_solicitudes.selectComprobada" filter/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="search" icon-color="dark">
                <!-- <q-search hide-underline color="secondary" v-model="form_solicitudes.filter" /> -->
                <q-input v-model="form_solicitudes.filter" type="text" inverted color="dark" float-label="Buscar" maxlenght=1000></q-input>
              </q-field>
            </div>
          </div>

          <div class="row justify-end" style="text-align: right">
          <div class="col-sm-6">
            <br>
            <q-btn @click="filtrar()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_solicitudes.data"
                :columns="form_solicitudes.columns"
                :selected.sync="form_solicitudes.selected"
                :filter="form_solicitudes.filter"
                color="positive"
                title=""
                :pagination.sync="form_solicitudes.pagination"
                :loading="form_solicitudes.loading"
                :rows-per-page-options="form_solicitudes.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="acciones" :props="props">
                    <q-btn small flat @click="verInformacion(props.row)" color="green" icon="search">
                        <q-tooltip>Ver detalles</q-tooltip>
                    </q-btn>
                    </q-td>
                    <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                    <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
                    <q-td key="creado" :props="props">{{ props.row.creado }}</q-td>
                    <q-td key="fecha_solicitado" :props="props">{{ props.row.fecha_solicitado }}</q-td>
                    <q-td key="responsable_solicitud" :props="props">{{ props.row.responsable_solicitud }}</q-td>
                    <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_solicitudes.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>

          <q-modal no-backdrop-dismiss v-if="form_gastos.modal_informacion" style="background-color: rgba(0,0,0,0.7);" v-model="form_gastos.modal_informacion" :content-css="{minWidth: '70vw', minHeight: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-9">
              <q-toolbar-title>
                Solicitud #{{ form_solicitudes.fields.id}}
              </q-toolbar-title>
            </div>
            <div class="col-sm-3 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_gastos.modal_informacion = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-chart-line" icon-color="dark">
                <q-input readonly upper-case v-model="form_solicitudes.fields.nombre_proyecto" type="text" inverted color="dark" float-label="Nombre del proyecto"></q-input>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="business" icon-color="dark">
                <q-input readonly upper-case v-model="form_solicitudes.fields.nombre_proveedor" type="text" inverted color="dark" float-label="Nombre del proveedor"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-3">
              <q-field icon="forward" icon-color="dark">
                <q-input readonly v-model="form_solicitudes.fields.status" type="text" inverted color="dark" float-label="Status"/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form_solicitudes.fields.creado" type="date" inverted color="dark" float-label="Fecha creación" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form_solicitudes.fields.fecha_solicitado" type="date" inverted color="dark" float-label="Fecha de solicitud" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form_solicitudes.fields.fecha_ejercido" type="date" inverted color="dark" float-label="Fecha ejercido" clearable align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-12" style="margin-top:5px;">
              <q-field icon="label" icon-color="dark">
                <q-input readonly upper-case v-model="form_solicitudes.fields.descripcion" type="text" inverted color="dark" float-label="Descripción"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:40px;">
            GASTOS DE SOLICTUD
          </div>
          <div class="row q-mt-sm" style="margin-top:50px; margin-left:50px; margin-right:30px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_gastos.data"
                :columns="form_gastos.columns_detalles"
                :selected.sync="form_gastos.selected"
                :filter="form_gastos.filter"
                color="positive"
                title=""
                :pagination.sync="form_gastos.pagination"
                :loading="form_gastos.loading"
                :rows-per-page-options="form_gastos.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="num" :props="props">{{ props.row.num }}</q-td>
                    <q-td key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}</q-td>
                    <q-td key="montocopia" :props="props">${{ props.row.montocopia }}</q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_gastos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>

        </div>
      </div>

      <!-- VISTA NUEVA SOLICITUD -->
      <div class="row" v-if="views.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">NUEVA SOLICITUD  #{{form_gastos.fields.solicitud_id}}</h5>
            </div>
            <div class="col-sm-3">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
                </div>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-3">
              <q-field icon="work" icon-color="dark" :error="$v.form_gastos.fields.proyecto_id.$error" error-label="Elija un proyecto">
                <q-select v-model="form_gastos.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosOptions" filter @input="selectActividades()"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="business" icon-color="dark" :error="$v.form_gastos.fields.proveedor_id.$error" error-label="Elija un proveedor">
                <q-select v-model="form_gastos.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" id="contieneDiv">
            <div class="col-sm-6">
              <q-field icon="folder" icon-color="dark" :error="$v.form_gastos.fields.actividad_id.$error" error-label="Elija una actividad">
                <q-select v-model="form_gastos.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesByProyectoOptions" filter @input="presupuestoActividad()"/>
              </q-field>
            </div>
            <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" helper="Presupuesto real">
                  <money readonly disable v-model="form_gastos.fields.presupuesto_real" v-bind="money" style="height:55px;width:100%;" v-bind:class="{ moneyBien: true, moneyError: false }"></money>
                </q-field>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" helper="Presupuesto disponible">
                  <money readonly v-model="form_gastos.fields.presupuesto_disponible" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: true, moneyError: false }"></money>
                </q-field>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_gastos.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" helper="Cantidad a solicitar">
                  <money v-model="form_gastos.fields.monto" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form_gastos.fields.monto.$error), moneyError: $v.form_gastos.fields.monto.$error }" @input="$v.form_gastos.fields.monto.$touch"></money>
                </q-field>
              </div>
            </div>
            <div class="col-sm-1 offset-sm-0 pull-left" style="margin-left: 40px;">
              <q-btn @click="validarGastos()" class="btn_guardar" icon="add" :loading="loadingButton" v-if="form_gastos.fields.id===0"></q-btn>
              <q-btn @click="actualizarGastos()" icon="done_all" :loading="loadingButton" style="background-color: orange;" v-if="form_gastos.fields.id!==0"></q-btn>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-6">
              <q-field icon="insert_comment" icon-color="dark" :error="$v.form_gastos.fields.descripcion.$error" error-label="Escriba la descripción">
                <q-input upper-case v-model="form_gastos.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlenght=1000></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="saveSolicitud()" class="btn_guardar" icon-right="save" :loading="loadingButton">Crear Solicitud</q-btn>
            </div>
          </div>

          <div class="row q-mt-sm" style="margin:auto;width:85vw;margin-top:10px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_gastos.gastosNuevos"
                :columns="form_gastos.columns"
                :selected.sync="form_gastos.selected"
                :filter="form_gastos.filter"
                color="positive"
                title=""
                :pagination.sync="form_gastos.pagination"
                :loading="form_gastos.loading"
                :rows-per-page-options="form_gastos.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                    <q-td key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}</q-td>
                    <q-td key="monto" :props="props">{{ props.row.monto }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="deleteSingleGasto(props.row)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="editSingleGasto(props.row)" color="blue" icon="edit">
                        <q-tooltip>Editar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_gastos.loading">
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
import { required, maxValue, minValue, maxLength } from 'vuelidate/lib/validators'
import {Money} from 'v-money'

export default {
  components: {Money},
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase()) {
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
      isExpanded: true,
      loadingButton: false,
      alterar_presupuesto: false,

      money: {
        decimal: '.',
        thousands: ',',
        suffix: ' MXN',
        precision: 2,
        masked: false
      },

      views: {
        grid: true,
        update: false
      },
      form_solicitudes: {
        fields: {
          id: 0,
          status: 'all',
          fecha_solicitado: 0,
          proveedor_id: 0,
          descripcion: '',
          creado: '',
          nombre_proyecto: '',
          fecha_ejercido: 0,
          proyecto_id: 0,
          responsable: '',
          responsable_solicitud: '',
          presupuesto_real: 0,
          presupuesto_disponible: 0,
          responsable_id: 0,
          actividad_id: 0,
          comprobado: 'all',
          total: 0,
          nombre_proveedor: ''
        },
        // data: [],
        selectComprobada: [ { 'label': 'SI', 'value': 'SI' }, { 'label': 'NO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ],
        selectStatus: [ { 'label': 'CREADO', 'value': 'CREADO' }, { 'label': 'SOLICITADO', 'value': 'SOLICITADO' }, { 'label': 'Todos', 'value': 'all' } ],
        columns: [
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' },
          {name: 'id', label: '# Solicitud', field: 'id', sortable: true, type: 'string', align: 'right'},
          { name: 'nombre_proyecto', label: 'Presupuesto', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'creado', label: 'Fecha creación', field: 'creado', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_solicitado', label: 'Fecha de solicitud', field: 'fecha_solicitado', sortable: true, type: 'string', align: 'center' },
          { name: 'responsable_solicitud', label: 'Asignado', field: 'responsable_solicitud', sortable: true, type: 'string', align: 'center' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        solicitudes_proyecto: false,
        loading: false
      },
      form_actividades: {
        loading: false,
        modal_editar: false,
        fields: {
          id: 0,
          proyecto_id: 0,
          nombre: '',
          avance: 0,
          inicio: '',
          fin: '',
          costo: 0,
          presupuesto_inicial_copia: '',
          fin_real: '',
          indice: '',
          nivel: 0,
          resumen: false,
          duracion: 0,
          duracion_restante: 0,
          padre_id: 0
        }
      },
      form_gastos: {
        gastosNuevos: [],
        selectStatus: [ { 'label': 'SOLICITADO', 'value': 'SOLICITADO' }, { 'label': 'APROBADO', 'value': 'APROBADO' } ],
        columns: [
          {name: 'id', label: '# Gasto', field: 'id', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left'},
          {name: 'monto', label: 'Cantidad solicitada', field: 'monto', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center'}
        ],
        columns_detalles: [
          {name: 'num', label: '#', field: 'num', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left'},
          {name: 'montocopia', label: 'Cantidad solicitada', field: 'montocopia', sortable: true, type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        solicitudes_proyecto: false,
        modal_informacion: false,
        fields: {
          id: 0,
          contador: 0,
          num: 0,
          actividad_id: 0,
          responsale_id: 0,
          monto: 0,
          montocopia: 0,
          fecha_ejercido: '',
          fecha_solicitado: '',
          status: '',
          proveedor_id: 0,
          categoria: 'MODERADO',
          proyecto_id: 0,
          solicitud_id: 0,
          presupuesto_real: 0,
          presupuesto_disponible: 0,
          nombre_actividad: '',
          descripcion: '',
          total: 0
        }
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
      recursosOptions: 'vnt/recurso/selectOptions',
      actividadesOptions: 'pmo/actividades/selectOtherOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions',
      usuariosOptions: 'sys/users/selectOptionsName',
      proyectosOptions: 'pmo/proyecto/selectOptions',
      proyectos: 'pmo/proyecto/proyectos',
      solicitudes: 'fin/solicitudes/solicitudes'
    }),
    actividadesByProyectoOptions () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form_gastos.fields.proyecto_id)
      return actividades
    },
    actividadesByProyectoOptions2 () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form_solicitudes.fields.proyecto_id)
      actividades.push({ 'label': 'Todos', 'value': 0 })
      return actividades
    },
    usuariosOptions2 () {
      let usuarios = this.$store.getters['sys/users/selectOptionsName']
      usuarios.push({ 'label': 'Todos', 'value': 0 })
      return usuarios
    },
    proyectosOptions2 () {
      let proyectos2 = this.$store.getters['pmo/proyecto/selectOptions']
      proyectos2.push({ 'label': 'Todos', 'value': 0 })
      return proyectos2
    },
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
    alterar_presupuesto (newValue, old) {
      if (newValue === true) {
        this.form.fields.alterar_presupuesto = ''
      }
    }
  },
  methods: {
    ...mapActions({
      getProyectos: 'pmo/proyecto/refresh',
      getSolicitudes: 'fin/solicitudes/refresh',
      getRecursos: 'vnt/recurso/refresh',
      getProveedores: 'pmo/proveedor/refresh',
      getUsuarios: 'sys/users/refresh',
      getActividades: 'pmo/actividades/refresh',
      getActividadesByProyecto: 'pmo/carga/getByProyecto',
      getGastosByActividad: 'fin/gastos/getByActividad',
      getGastosBySolicitud: 'fin/gastos/getBySolicitud',
      getPresupuestoActividad: 'pmo/actividades/getPresupuestoActividad',
      getActividad: 'pmo/actividades/getByActividad',
      saveGasto: 'fin/gastos/save',
      deleteGasto: 'fin/gastos/delete',
      savenewSolicitud: 'fin/solicitudes/save',
      getSolicitudById: 'fin/solicitudes/getById',
      filtrarSolicitudes: 'fin/solicitudes/filtrar',
      sigSolicitud: 'fin/solicitudes/obtenerSigSolicitud'
    }),

    recursive (obj, newObj, level, itemId, isExpend) {
      let vm = this

      obj.forEach(function (o) {
        if (o.children && o.children.length !== 0) {
          o.level = level
          newObj.push(o)
          if (o.id === itemId) {
            o.expend = isExpend
          }
          if (o.expend === true) {
            vm.recursive(o.children, newObj, o.level + 1, itemId, isExpend)
          }
        } else {
          o.level = level
          newObj.push(o)
          return false
        }
      })
    },
    iconName (item) {
      if (item.expend === true) {
        return 'fas fa-minus-circle'
      }

      if (item.children && item.children.length > 0) {
        return 'fas fa-plus-circle'
      }

      return 'fas fa-check'
    },
    toggle (item, index) {
      let vm = this
      vm.itemId = item.id
      // show  sub items after click on + (more)
      if (item.expend === undefined && item.children !== undefined) {
        if (item.children.length !== 0) {
          vm.recursive(item.children, [], item.level + 1, item.id, true)
        }
      }

      if (item.expend === true && item.children !== undefined) {
        item.children.forEach(function (o) {
          o.expend = undefined
        })

        // this.item.expend = undefined
        vm.$set(item, 'expend', undefined)
        vm.itemId = null
      }
    },
    setPadding (item) {
      return `padding-left: ${item.level * 15}px;`
    },

    async loadAll () {
      // await this.getRecursos()
      await this.getSolicitudes()
      await this.getProyectos()
      await this.getProveedores()
      await this.getUsuarios()
      await this.getActividades()
    },
    setView (view) {
      this.views.grid = false
      this.views.update = false
      this.views[view] = true
    },
    limpiar_gasto () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.monto = 0
      this.form_gastos.fields.status = 'CREADO'
      this.form_gastos.fields.categoria = 'MODERADO'
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.presupuesto_disponible = 0
    },
    async cargarGastos () {
      this.form_gastos.loading = true
      this.form_gastos.data = []
      this.form_gastos.gastosActividad = []
      await this.getGastosByActividad({actividad_id: this.form_gastos.fields.actividad_id}).then(({data}) => {
        this.form_gastos.gastosActividad = data.gastos
        if (data.gastos.length > 0) {
          data.gastos.forEach(function (element) {
            if (element.status === 'SOLICITADO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'APROBADO') {
              element.color = 'green'
              element.icon = 'fas fa-check-circle'
            }
          })
          this.form_gastos.data = data.gastos
        }
        this.form_gastos.loading = false
      }).catch(error => {
        console.error(error)
        this.form_gastos.loading = false
      })
    },
    validarGastos () {
      this.$v.form_gastos.$touch()
      if (!this.$v.form_gastos.$error) {
        if (parseFloat(this.form_gastos.fields.presupuesto_disponible) < parseFloat(this.form_gastos.fields.monto)) {
          this.form_gastos.fields.categoria = 'EXCESIVO'
          this.$q.dialog({
            title: 'Confirmar',
            message: '¿Estas seguro que deseas agregar este gasto? Al agregar este gasto sobrepasas el monto asignado para esta actividad',
            ok: 'Aceptar',
            cancel: 'Cancelar'
          }).then(() => {
            this.agregarGasto()
          }).catch(() => {
            this.$q.notify('Cancelado')
          })
        } else {
          this.agregarGasto()
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteSingleGasto (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este gasto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.form_gastos.gastosNuevos.splice(row.id - 1, 1)
      }).catch(() => {})
    },
    editSingleGasto (row) {
      this.form_gastos.fields.actividad_id = this.form_gastos.gastosNuevos[row.id - 1].actividad_id
      this.form_gastos.fields.monto = this.form_gastos.gastosNuevos[row.id - 1].monto
      this.form_gastos.fields.id = this.form_gastos.gastosNuevos[row.id - 1].id
      this.presupuestoActividad()
    },
    actualizarGastos () {
      this.form_gastos.gastosNuevos[this.form_gastos.fields.id - 1].actividad_id = this.form_gastos.fields.actividad_id
      this.form_gastos.gastosNuevos[this.form_gastos.fields.id - 1].monto = this.form_gastos.fields.monto
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.monto = 0
      this.form_gastos.fields.id = 0
    },
    selectActividades () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.monto = 0
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.gastosNuevos = []
    },
    presupuestoActividad () {
      this.getPresupuestoActividad({id: this.form_gastos.fields.actividad_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_gastos.fields.presupuesto_disponible = data.actividades[0].cantidad_disponible
          this.form_gastos.fields.presupuesto_real = data.actividades[0].costo
          this.form_gastos.fields.nombre_actividad = data.actividades[0].nombre
        }
      }).catch(error => {
        console.error(error)
      })
    },
    saveSolicitud () {
      if (this.form_gastos.fields.descripcion !== '') {
        if ((this.form_gastos.gastosNuevos.length > 0) || ((this.form_gastos.fields.proyecto_id > 0) && (this.form_gastos.fields.actividad_id > 0))) {
          if ((this.form_gastos.fields.proyecto_id > 0) && (this.form_gastos.fields.actividad_id > 0)) {
            this.agregarGasto()
          }
          this.savenewSolicitud({proyecto_id: this.form_gastos.fields.proyecto_id, gastos: this.form_gastos.gastosNuevos, descripcion: this.form_gastos.fields.descripcion, proveedor_id: this.form_gastos.fields.proveedor_id, total: this.form_gastos.fields.total}).then(({data}) => {
            if (data.result === 'success') {
              this.$showMessage(data.message.title, data.message.content)
              this.getSolicitudes()
              this.setView('grid')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.$showMessage('¡Advertencia!', 'Para crear una solicitud debe agregar al menos un gasto.')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Debe agregar una descripción')
      }
    },
    newSolicitud () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.descripcion = ''
      this.form_gastos.fields.proyecto_id = 0
      this.form_gastos.solicitudes_proyecto = true
      this.form_gastos.fields.monto = 0
      this.form_gastos.fields.status = 'CREADO'
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.fields.categoria = 'MODERADO'
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.total = 0
      this.form_gastos.gastosNuevos = []

      this.sigSolicitud().then(({data}) => {
        console.log(data)
        if (data.solicitudes.length > 0) {
          this.form_gastos.fields.solicitud_id = data.solicitudes[0].max + 1
        } else {
          this.form_gastos.fields.solicitud_id = 1
        }
        this.setView('update')
      }).catch(error => {
        console.error(error)
      })
    },
    filtrar () {
      this.loadingButton = true
      this.form_solicitudes.loading = true
      this.form_solicitudes.data = []
      let params = this.form_solicitudes.fields
      this.filtrarSolicitudes(params).then(({data}) => {
        this.loadingButton = false
        if (data.solicitudes.length > 0) {
          data.solicitudes.forEach(function (element) {
            if (element.status === 'CREADO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'SOLICITADO') {
              element.color = 'green'
              element.icon = 'fas fa-check-circle'
            }
          })
          this.form_solicitudes.data = data.solicitudes
        }
        this.form_solicitudes.loading = false
      }).catch(error => {
        console.error(error)
        this.form_solicitudes.loading = false
      })
    },
    verInformacion (row) {
      this.form_gastos.modal_informacion = true
      this.form_solicitudes.fields.id = row.id
      this.form_solicitudes.fields.status = row.status
      this.form_solicitudes.fields.fecha_solicitado = row.fecha_solicitado
      this.form_solicitudes.fields.autorizacion_id = row.autorizacion_id
      this.form_solicitudes.fields.fecha_ejercido = row.fecha_ejercido
      this.form_solicitudes.fields.proyecto_id = row.proyecto_id
      this.form_solicitudes.fields.responsable = row.responsable
      this.form_solicitudes.fields.creado = row.creado
      this.form_solicitudes.fields.descripcion = row.descripcion
      this.form_solicitudes.fields.nombre_proyecto = row.nombre_proyecto
      this.form_solicitudes.fields.responsable_solicitud = row.responsable_solicitud
      this.form_solicitudes.fields.nombre_proveedor = row.nombre_proveedor
      this.form_gastos.loading = true
      this.form_gastos.data = []

      this.getGastosBySolicitud({id: row.id}).then(({data}) => {
        if (data.gastos.length > 0) {
          this.form_gastos.data = data.gastos
        }
        this.form_gastos.loading = false
      }).catch(error => {
        console.log(error)
        this.form_gastos.loading = false
      })
    },
    agregarGasto () {
      this.form_gastos.fields.contador = this.form_gastos.fields.contador + 1
      var otroGasto = {id: this.form_gastos.fields.contador, actividad_id: this.form_gastos.fields.actividad_id, monto: this.form_gastos.fields.monto, cantidad_disponible: this.form_gastos.fields.presupuesto_disponible, cantidad_real: this.form_gastos.fields.presupuesto_real, nombre_actividad: this.form_gastos.fields.nombre_actividad}
      this.form_gastos.gastosNuevos.push(otroGasto)
      this.limpiar_gasto()
    }
  },
  validations: {
    form_gastos: {
      fields: {
        monto: {required, minValue: minValue(0), maxValue: maxValue(1000000000)},
        actividad_id: {required, minValue: minValue(1)},
        proyecto_id: {required, minValue: minValue(1)},
        descripcion: {required, maxLenght: maxLength(1000)},
        proveedor_id: {required, minValue: minValue(1)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>

</style>
