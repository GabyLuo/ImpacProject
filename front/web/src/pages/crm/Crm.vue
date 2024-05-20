<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Etapas"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 col-md-6 col-xs-12 pull-right">
             <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
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
                  :data="crm"
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
                      <q-td key="nombre" style="text-align: left;" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td key="acciones" style="text-align: right;" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                      </q-td>
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
    </div>

    <!--Crear un estado-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Etapas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva etapa"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
             <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6" >
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre del carril">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
            </div>
            <!-- <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar ESTADO -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Etapas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Editar etapa"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6  col-xs-12 col-md-6 pull-right">
             <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12" style="padding-left: 0;padding-right: 0;">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6" style="padding-left: 1%;">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre del carril">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
            </div>
            <q-tabs class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 30px;">
              <q-tab default name="etapas" slot="title" icon="format_list_numbered" label="Etapas"/>
              <q-tab name="tareas" slot="title" icon="style" label="Tareas"/>

              <q-tab-pane name="etapas" class="bg-white">
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-4">
                    <q-field icon="format_list_numbered" icon-color="dark" :error="$v.form_etapas.fields.nombre.$error" error-label="Ingrese el nombre de la etapa">
                      <q-input upper-case v-model="form_etapas.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="dialpad" icon-color="dark">
                      <q-input @keyup="isNumber($event,'porcentaje')" v-model="form_etapas.fields.porcentaje" type="text" inverted color="dark" float-label="Porcentaje" suffix="%" maxlength="3"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="fas fa-calendar" icon-color="dark" :error="$v.form_etapas.fields.dias.$error" error-label="Ingrese la duración de la etapa">
                      <q-input @keyup="isNumber($event,'dias')" v-model="form_etapas.fields.dias" type="text" inverted color="dark" float-label="Duración(días)" maxlength="3"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="color_lens" icon-color="dark">
                      <q-item-side left style="padding-left: .5rem;">
                        <q-btn size="lg" label="Color" inverted :color="color">
                          <q-popover>
                            <q-list link>
                              <q-item v-bind:key="ci" v-for="(color, ci) in colorsEs" :class="color.class" v-close-overlay @click.native="selectColor(color.color, color.color)">
                                <q-item-main>
                                  <q-item-tile label>{{color.name}}</q-item-tile>
                                </q-item-main>
                              </q-item>
                            </q-list>
                          </q-popover>
                        </q-btn>
                      </q-item-side>
                    </q-field>
                  </div>
                  <!-- <div class="col-sm-2">
                    <q-checkbox v-model="form_etapas.fields.cierre" color="teal" label="Etapa de cierre" @click.native="etapa_cierre()"/>
                  </div> -->
                  <div class="col-sm-2 pull-right" style="padding-top: 1%;">
                    <q-btn @click="guardarEtapa()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="form_etapas.editar === false">Guardar</q-btn>
                    <q-btn @click="actualizarEtapa()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-else>Actualizar</q-btn>
                  </div>
                </div>
                <div class="col q-pa-md" style="padding: 0;">
                  <div class="col q-pa-md ">
                    <div class="col-sm-12" style="padding-bottom: 10px;">
                      <q-search color="primary" v-model="form_etapas.filter"/>
                    </div>
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                      <q-table id="sticky-table-newstyle"
                        :data="form_etapas.data"
                        :columns="form_etapas.columns"
                        :selected.sync="form_etapas.selected"
                        :filter="form_etapas.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_etapas.pagination"
                        :loading="form_etapas.loading"
                        :rows-per-page-options="form_etapas.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nivel" :props="props">{{ props.row.nivel }}</q-td>
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="porcentaje" :props="props">{{ props.row.porcentaje }} %</q-td>
                            <q-td key="dias" :props="props">{{ props.row.dias }}</q-td>
                            <q-td key="cierre" :props="props">
                              <q-checkbox v-model="props.row.cierre" @click.native="actualizarCierre(props.row)" color="teal"/>
                            </q-td>
                            <q-td key="acciones" style="text-align: left;" :props="props">
                              <q-btn small flat @click="editEtapa(props.row)" color="blue-6" icon="edit">
                                <q-tooltip>Editar</q-tooltip>
                              </q-btn>
                              <q-btn small flat @click="deleteSingleEtapa(props.row.id)" color="negative" icon="delete">
                                <q-tooltip>Eliminar</q-tooltip>
                              </q-btn>
                            </q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_etapas.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="tareas" class="bg-white">
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-3">
                    <q-field icon="format_list_numbered" icon-color="dark" :error="$v.form_actividades.fields.etapa_id.$error" error-label="Elija una etapa">
                      <q-select v-model="form_actividades.fields.etapa_id" inverted color="dark" float-label="Etapa" :options="etapasOptions" filter @input="getActividades()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="style" icon-color="dark" :error="$v.form_actividades.fields.nombre.$error" error-label="Ingrese el nombre de la actividad">
                      <q-input upper-case v-model="form_actividades.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="search" icon-color="dark" :error="$v.form_actividades.fields.tipo.$error" error-label="Seleccione un tipo">
                      <q-select v-model="form_actividades.fields.tipo" inverted color="dark" float-label="Tipo" :options="form_actividades.tiposOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="alarm" icon-color="dark" :error="$v.form_actividades.fields.tiempo_respuesta.$error" error-label="Ingrese el tiempo">
                      <q-input upper-case v-model="form_actividades.fields.tiempo_respuesta" type="text" inverted color="dark" float-label="Tiempo respuesta(días)" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="alarm" icon-color="dark" :error="$v.form_actividades.fields.tiempo_solucion.$error" error-label="Ingrese el tiempo">
                      <q-input upper-case v-model="form_actividades.fields.tiempo_solucion" type="text" inverted color="dark" float-label="Tiempo solución(días)" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-12 pull-right" style="padding-top: 1.5%;">
                    <q-btn v-if="form_actividades.editar === false" @click="guardarActividad()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                    <q-btn v-else @click="actualizarActividad()" class="btn_actualizar" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
                  </div>
                </div>
                <div class="row q-col-gutter-xs" style="padding-top: 20px;">
                  <div class="col q-pa-md">
                    <div class="col-sm-12" style="padding-bottom: 10px;">
                      <q-search color="primary" v-model="form_actividades.filter"/>
                    </div>
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                      <q-table id="sticky-table-newstyle"
                        :data="form_actividades.data"
                        :columns="form_actividades.columns"
                        :selected.sync="form_actividades.selected"
                        :filter="form_actividades.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_actividades.pagination"
                        :loading="form_actividades.loading"
                        :rows-per-page-options="form_actividades.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                            <q-td key="tiempo_respuesta" :props="props">{{ props.row.tiempo_respuesta }}</q-td>
                            <q-td key="tiempo_solucion" :props="props">{{ props.row.tiempo_solucion }}</q-td>
                            <q-td key="obligatorio" :props="props">
                              <q-checkbox v-model="props.row.obligatorio" @click.native="actualizarObligatorio(props.row)" color="teal"/>
                            </q-td>
                            <q-td key="licitacion" :props="props">
                              <q-checkbox v-model="props.row.licitacion" @click.native="actualizarLicitacion(props.row)" color="teal"/>
                            </q-td>
                            <q-td key="proyecto" :props="props">
                              <q-checkbox v-model="props.row.proyecto" @click.native="actualizarProyecto(props.row)" color="teal"/>
                            </q-td>
                            <q-td key="acciones" :props="props">
                              <q-btn small flat @click="editActividad(props.row)" color="blue-6" icon="edit">
                                <q-tooltip>Editar</q-tooltip>
                              </q-btn>
                              <q-btn small flat @click="deleteSingleActividad(props.row.id)" color="negative" icon="delete">
                                <q-tooltip>Eliminar</q-tooltip>
                              </q-btn>
                            </q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_actividades.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-tab-pane>
            </q-tabs>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minValue } from 'vuelidate/lib/validators'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      color: 'black',
      loadingButton: false,
      etapasOptions: [{label: '---Sin etapas---', value: 0}],
      views: {
        grid: true,
        create: false,
        update: false
      },
      colorsEs: [
        {name: 'Azul', class: 'text-white bg-blue', color: 'blue'},
        {name: 'Verde azulado', class: 'text-white bg-teal', color: 'teal'},
        {name: 'Morado', class: 'text-white bg-purple', color: 'purple'},
        {name: 'Negro', class: 'text-white bg-black', color: 'black'},
        {name: 'Verde', class: 'text-white bg-green', color: 'green'},
        {name: 'Rojo', class: 'text-white bg-red', color: 'red'},
        {name: 'Cian', class: 'text-white bg-cyan', color: 'cyan'},
        {name: 'Ambar', class: 'text-white bg-amber', color: 'amber'}
      ],
      form: {
        fields: {
          id: 0,
          nombre: ''
        },
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_etapas: {
        fields: {
          carril_id: 0,
          id: 0,
          nombre: '',
          porcentaje: 0,
          color: '',
          nivel: 0,
          cierre: false,
          dias: 0
        },
        data: [],
        columns: [
          { name: 'nivel', label: 'Nivel', field: 'nivel', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'porcentaje', label: 'Porcentaje', field: 'porcentaje', sortable: true, type: 'string', align: 'right' },
          { name: 'dias', label: 'Duración (días)', field: 'dias', sortable: true, type: 'string', align: 'right' },
          { name: 'cierre', label: 'Cierre', field: 'cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        editar: false
      },
      form_actividades: {
        fields: {
          etapa_id: 0,
          id: 0,
          nombre: '',
          tipo: '',
          tiempo_respuesta: '',
          tiempo_solucion: ''
        },
        tiposOptions: [ { 'label': 'EMAIL', 'value': 'EMAIL' }, { 'label': 'LLAMADA', 'value': 'LLAMADA' },
          { 'label': 'CITA', 'value': 'CITA' }, { 'label': 'TAREA', 'value': 'TAREA' } ],
        data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'tiempo_respuesta', label: 'Respuesta (días)', field: 'tiempo_respuesta', sortable: true, type: 'string', align: 'right' },
          { name: 'tiempo_solucion', label: 'Solución (días)', field: 'tiempo_solucion', sortable: true, type: 'string', align: 'right' },
          { name: 'obligatorio', label: 'Obligatorio', field: 'obligatorio', sortable: true, type: 'string', align: 'right' },
          { name: 'licitacion', label: 'Licitación', field: 'licitacion', sortable: true, type: 'string', align: 'right' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        editar: false
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
      crm: 'crm/crm/crm'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getCarriles: 'crm/crm/refresh',
      saveCarriles: 'crm/crm/save',
      updateCarriles: 'crm/crm/update',
      deleteCarriles: 'crm/crm/delete',
      saveEtapa: 'crm/etapas/save',
      updateEtapa: 'crm/etapas/update',
      updateCierre: 'crm/etapas/updateCierre',
      deleteEtapa: 'crm/etapas/delete',
      getEtapasByCarril: 'crm/etapas/getByCarril',
      getActividadesByEtapa: 'crm/actividades/getByEtapa',
      saveActividad: 'crm/actividades/save',
      updateActividad: 'crm/actividades/update',
      updateObligatorio: 'crm/actividades/updateObligatorio',
      updateLicitacion: 'crm/actividades/updateLicitacion',
      updateProyecto: 'crm/actividades/updateProyecto',
      deleteActividad: 'crm/actividades/delete'
    }),
    isNumber (evt, input) {
      switch (input) {
        case 'porcentaje':
          this.form_etapas.fields.porcentaje = this.form_etapas.fields.porcentaje.replace(/[^0-9.]/g, '')
          break
        case 'dias':
          this.form_etapas.fields.dias = this.form_etapas.fields.dias.replace(/[^0-9.]/g, '')
          break
        default:
          break
      }
    },
    async loadAll () {
      this.form.loading = true
      await this.getCarriles()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    save () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveCarriles(params).then(({data}) => {
          this.loadingButton = false
          // modificaciones 10/11/2019 *
          // this.$showMessage(data.message.title, data.message.content) *
          if (data.result === 'success') {
            //
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            // *
            this.loadAll()
            this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateCarriles(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            // ***
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            // ***
            this.loadAll()
            this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      this.form.fields.id = row.id
      this.form.fields.nombre = row.nombre
      this.form_etapas.fields.carril_id = row.id
      this.limpiarEtapas()
      this.limpiarActividades()
      this.form_actividades.fields.etapa_id = 0
      this.form_actividades.data = []
      this.getEtapas()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este carril?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteCarriles(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // ***
          this.$q.notify({
          // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          // ***
          this.loadAll()
          this.setView('grid')
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.nombre = ''
      this.setView('create')
    },
    selectColor (color, text) {
      this.color = color
      this.color_text = text
      this.form_etapas.fields.color = color
    },
    limpiarEtapas () {
      this.$v.form_etapas.$reset()
      this.form_etapas.fields.nombre = ''
      this.form_etapas.fields.porcentaje = 0
      this.form_etapas.fields.color = 'black'
      this.form_etapas.fields.cierre = false
      this.form_etapas.fields.dias = 0
      this.color = 'black'
      this.form_etapas.editar = false
    },
    etapa_cierre () {
      console.log(this.form_etapas.fields.cierre)
    },
    getEtapas () {
      this.form_etapas.data = []
      this.etapasOptions = []
      this.getEtapasByCarril({carril_id: this.form_etapas.fields.carril_id}).then(({data}) => {
        if (data.etapas.length > 0) {
          this.form_etapas.data = data.etapas
          this.etapasOptions = []
          this.etapasOptions.push({label: '---Seleccione---', value: 0})
          data.etapas.forEach(etapa => {
            this.etapasOptions.push({label: etapa.nombre, value: etapa.id})
          })
        } else {
          this.etapasOptions.push({label: '---Sin etapas---', value: 0})
        }
      }).catch(error => {
        console.error(error)
      })
    },
    guardarEtapa () {
      this.$v.form_etapas.$touch()
      if (!this.$v.form_etapas.$error) {
        this.loadingButton = true
        let params = this.form_etapas.fields
        this.saveEtapa(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white',
              icon: 'done',
              position: 'top-right'
            })
            this.limpiarEtapas()
            this.getEtapas()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    editEtapa (row) {
      this.form_etapas.fields.id = row.id
      this.form_etapas.fields.nombre = row.nombre
      this.form_etapas.fields.porcentaje = row.porcentaje
      this.form_etapas.fields.carril_id = this.form.fields.id
      this.form_etapas.fields.color = row.color
      this.form_etapas.fields.dias = row.dias
      this.color = row.color
      this.form_etapas.editar = true
    },
    actualizarEtapa () {
      this.$v.form_etapas.$touch()
      if (!this.$v.form_etapas.$error) {
        this.loadingButton = true
        let params = this.form_etapas.fields
        this.updateEtapa(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white',
              icon: 'done',
              position: 'top-right'
            })
            this.form_etapas.editar = false
            this.limpiarEtapas()
            this.getEtapas()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    actualizarCierre (row) {
      this.loadingButton = true
      let params = row
      this.updateCierre(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white',
            icon: 'done',
            position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        this.getEtapas()
      }).catch(error => {
        console.error(error)
      })
    },
    deleteSingleEtapa (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta etapa?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteEtapas(id)
      }).catch(() => {})
    },
    deleteEtapas (id) {
      let params = {id: id}
      this.deleteEtapa(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // ***
          this.$q.notify({
          // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          // ***
          this.getEtapas()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarActividades () {
      this.$v.form_actividades.$reset()
      // this.form_actividades.fields.etapa_id = 0
      this.form_actividades.fields.nombre = ''
      this.form_actividades.fields.tiempo_respuesta = ''
      this.form_actividades.fields.tiempo_solucion = ''
      this.form_actividades.fields.tipo = ''
      this.form_actividades.editar = false
    },
    getActividades () {
      if (this.form_actividades.fields.etapa_id > 0) {
        this.form_actividades.data = []
        this.getActividadesByEtapa({id: this.form_actividades.fields.etapa_id}).then(({data}) => {
          if (data.actividades.length > 0) {
            this.form_actividades.data = data.actividades
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.form_actividades.data = []
      }
    },
    guardarActividad () {
      this.$v.form_actividades.$touch()
      if (!this.$v.form_actividades.$error) {
        this.loadingButton = true
        let params = this.form_actividades.fields
        this.saveActividad(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.limpiarActividades()
            this.getActividades()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    actualizarActividad () {
      this.$v.form_actividades.$touch()
      if (!this.$v.form_actividades.$error) {
        this.loadingButton = true
        let params = this.form_actividades.fields
        this.updateActividad(params).then(({data}) => {
          this.loadingButton = false
          this.form_actividades.editar = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.limpiarActividades()
            this.getActividades()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    actualizarObligatorio (row) {
      this.loadingButton = true
      let params = row
      this.updateObligatorio(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white',
            icon: 'done',
            position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        this.getActividades()
      }).catch(error => {
        console.error(error)
      })
    },
    actualizarLicitacion (row) {
      this.loadingButton = true
      let params = row
      this.updateLicitacion(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white',
            icon: 'done',
            position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        this.getActividades()
      }).catch(error => {
        console.error(error)
      })
    },
    actualizarProyecto (row) {
      this.loadingButton = true
      let params = row
      this.updateProyecto(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white',
            icon: 'done',
            position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        this.getActividades()
      }).catch(error => {
        console.error(error)
      })
    },
    editActividad (row) {
      this.form_actividades.fields.etapa_id = row.etapa_id
      this.form_actividades.fields.id = row.id
      this.form_actividades.fields.nombre = row.nombre
      this.form_actividades.fields.tipo = row.tipo
      this.form_actividades.fields.tiempo_respuesta = row.tiempo_respuesta
      this.form_actividades.fields.tiempo_solucion = row.tiempo_solucion
      this.form_actividades.editar = true
    },
    deleteSingleActividad (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta tarea?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteTarea(id)
      }).catch(() => {})
    },
    deleteTarea (id) {
      let params = {id: id}
      this.deleteActividad(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // ***
          this.$q.notify({
          // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          // ***
          this.getActividades()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) }
      }
    },
    form_etapas: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        porcentaje: { required, minValue: minValue(0) },
        dias: { required, minValue: minValue(1) }
      }
    },
    form_actividades: {
      fields: {
        etapa_id: { required, minValue: minValue(1) },
        nombre: { required, maxLength: maxLength(100) },
        tipo: { required },
        tiempo_respuesta: { required, minValue: minValue(1) },
        tiempo_solucion: { required, minValue: minValue(1) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
