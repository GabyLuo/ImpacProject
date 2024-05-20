<template>
  <q-page>
    <div v-if="views.grid_direcciones">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-8">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Sucursales" to=""/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right q-pr-md">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
              <q-btn @click="newSucursalRow()" class="btn_nuevo" icon="add">Nuevo</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="col q-pa-md border-panel">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form_sucursales.filter"/>
              </div>
              <q-table id="sticky-table-newstyle"
                :data="form_sucursales.data"
                :columns="form_sucursales.columns"
                :selected.sync="form_sucursales.selected"
                :filter="form_sucursales.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_sucursales.pagination"
                :loading="form_sucursales.loading"
                :rows-per-page-options="form_sucursales.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="encargado" :props="props">{{ props.row.encargado }}</q-td>
                    <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                    <q-td key="poblacion" :props="props">{{ props.row.poblacion }}</q-td>
                    <q-td key="cp" :props="props">{{ props.row.cp }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editSucursalRow(props.row)" color="blue-6" icon="edit">
                        <q-tooltip>Editar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteSucursalSingleRow(props.row.id)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_sucursales.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!--Crear una direccion-->

    <div v-if="views.create_direccion">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Sucursal" to="" @click.native="setView('grid_direcciones')"/>
                <q-breadcrumbs-el label="Nueva sucursal"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right q-pr-md">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
              <q-btn @click="setView('grid_direcciones')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="save_sucursal()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            Datos generales
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="location_city" icon-color="dark" :error="$v.form_sucursales.fields.nombre.$error" error-label="Escriba el nombre">
                  <q-input upper-case v-model="form_sucursales.fields.nombre" type="text" inverted color="dark" float-label="Nombre sucursal"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.encargado" type="text" inverted color="dark" float-label="Encargado"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select readonly v-model="form_sucursales.fields.tipo" inverted color="dark" float-label="Tipo" :options="form_sucursales.selectTipos"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.tipo_oficina" inverted color="dark" float-label="Tipo de oficina" :options="form_sucursales.selectTipoOficina"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.tipo_propiedad" inverted color="dark" float-label="Tipo de propiedad" :options="form_sucursales.selectTipoPropiedad"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="call" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.telefono" type="text" inverted color="dark" float-label="Teléfono" maxlength="10" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.num_ext" type="text" inverted color="dark" float-label="Núm. Ext." maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.num_int" type="text" inverted color="dark" float-label="Núm. Int." maxlength="20" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-map-signs" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_sucursales.municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="location_on" icon-color="dark" :error="$v.form_sucursales.fields.cp.$error" error-label="Ingrese un código postal válido">
                  <q-input upper-case v-model="form_sucursales.fields.cp" type="text" inverted color="dark" float-label="Código postal" maxlength="5" @keydown="numberOnly"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-fax" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.fax" type="text" inverted color="dark" float-label="Fax" @keydown="numberOnly"/>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!--Editar una direccion-->

    <div v-if="views.update_direccion">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid_direcciones')"/>
                <q-breadcrumbs-el label="Editar sucursal"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right q-pr-md">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
              <q-btn @click="setView('grid_direcciones')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update_sucursal()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            Datos generales
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="location_city" icon-color="dark" :error="$v.form_sucursales.fields.nombre.$error" error-label="Escriba el nombre">
                  <q-input upper-case v-model="form_sucursales.fields.nombre" type="text" inverted color="dark" float-label="Nombre sucursal"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.encargado" type="text" inverted color="dark" float-label="Encargado"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select readonly v-model="form_sucursales.fields.tipo" inverted color="dark" float-label="Tipo" :options="form_sucursales.selectTipos"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.tipo_oficina" inverted color="dark" float-label="Tipo de oficina" :options="form_sucursales.selectTipoOficina"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-home" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.tipo_propiedad" inverted color="dark" float-label="Tipo de propiedad" :options="form_sucursales.selectTipoPropiedad"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="call" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.telefono" type="text" inverted color="dark" float-label="Teléfono" maxlength="10" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.num_ext" type="text" inverted color="dark" float-label="Núm. Ext." maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.num_int" type="text" inverted color="dark" float-label="Núm. Int." maxlength="20" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-map-signs" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form_sucursales.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_sucursales.municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="location_on" icon-color="dark" :error="$v.form_sucursales.fields.cp.$error" error-label="Ingrese un código postal válido">
                  <q-input upper-case v-model="form_sucursales.fields.cp" type="text" inverted color="dark" float-label="Código postal" maxlength="5" @keydown="numberOnly"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-fax" icon-color="dark">
                  <q-input upper-case v-model="form_sucursales.fields.fax" type="text" inverted color="dark" float-label="Fax" @keydown="numberOnly"/>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, numeric } from 'vuelidate/lib/validators'

export default {
  name: 'Sucursales',
  data () {
    return {
      loadingButton: false,
      informacion: false,
      reporte_clientes: false,
      objetoInformacion: null,
      props: [],
      propsExpanded: ['Clientes'],
      views: {
        grid_direcciones: true,
        create_direccion: false,
        update_direccion: false
      },
      form_sucursales: {
        informacion_direccion: false,
        objetoInformacion_direccion: null,
        fields: {
          id: 0,
          empresa_id: 0,
          empresa: '',
          tipo: '',
          calle: '',
          num_ext: '',
          num_int: '',
          colonia: '',
          poblacion: '',
          municipio_id: 0,
          estado_id: 0,
          cp: '',
          nombre: '',
          encargado: '',
          telefono: '',
          fax: '',
          tipo_oficina: '',
          tipo_propiedad: ''
        },
        selectTipos: [ { 'label': 'DOMICILIO FISCAL', 'value': 'DOMICILIO FISCAL' }, { 'label': 'SUCURSAL FISCAL', 'value': 'SUCURSAL FISCAL' } ],
        selectTipoOficina: [ { 'label': 'FÍSICA', 'value': 'FÍSICA' }, { 'label': 'VIRTUAL', 'value': 'VIRTUAL' } ],
        selectTipoPropiedad: [ { 'label': 'PROPIA', 'value': 'PROPIA' }, { 'label': 'RENTADA', 'value': 'RENTADA' } ],
        municipiosOptions: [],
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'encargado', label: 'Encargado', field: 'encargado', sortable: true, type: 'string', align: 'left' },
          { name: 'poblacion', label: 'Población', field: 'poblacion', sortable: true, type: 'string', align: 'left' },
          { name: 'cp', label: 'C. P.', field: 'cp', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        data: [],
        filter: '',
        loading: false
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
      estadosOptions: 'vnt/estado/selectOptions'
    })
  },
  created () {
    this.loadAll()
  },
  watch: {},
  methods: {
    ...mapActions({
      getSucursales: 'pmo/pmosucursales/refresh',
      saveSucursal: 'pmo/pmosucursales/save',
      updateSucursal: 'pmo/pmosucursales/update',
      deleteSucursal: 'pmo/pmosucursales/delete',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form_sucursales.loading = true
      await this.getEstados()
      await this.cargarDirecciones()
      this.form_sucursales.loading = false
    },
    setView (view) {
      this.views.grid_direcciones = false
      this.views.create_direccion = false
      this.views.update_direccion = false
      this.views[view] = true
    },
    async cargarDirecciones () {
      this.form_sucursales.data = []
      this.getSucursales().then(({data}) => {
        if (data.sucursales.length > 0) {
          this.form_sucursales.data = data.sucursales
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newSucursalRow () {
      this.$v.form_sucursales.$reset()
      this.form_sucursales.fields.empresa_id = this.empresa_id
      this.form_sucursales.fields.id = 0
      this.form_sucursales.fields.tipo = 'SUCURSAL FISCAL'
      this.form_sucursales.fields.calle = ''
      this.form_sucursales.fields.num_ext = ''
      this.form_sucursales.fields.num_int = ''
      this.form_sucursales.fields.colonia = ''
      this.form_sucursales.fields.poblacion = ''
      this.form_sucursales.fields.municipio_id = 0
      this.form_sucursales.fields.estado_id = 0
      this.form_sucursales.fields.cp = ''
      this.form_sucursales.fields.nombre = ''
      this.form_sucursales.fields.encargado = ''
      this.form_sucursales.fields.telefono = ''
      this.form_sucursales.fields.fax = ''
      this.form_sucursales.fields.tipo_oficina = ''
      this.form_sucursales.fields.tipo_propiedad = ''
      this.form_sucursales.municipiosOptions = []
      this.setView('create_direccion')
    },
    async editSucursalRow (row) {
      this.form_sucursales.fields.empresa_id = this.empresa_id
      this.form_sucursales.municipiosOptions = []
      let sucursal = { ...row }
      this.form_sucursales.fields.id = row.id
      this.form_sucursales.fields.tipo = sucursal.tipo
      this.form_sucursales.fields.calle = sucursal.calle
      this.form_sucursales.fields.num_ext = sucursal.num_ext
      this.form_sucursales.fields.num_int = sucursal.num_int
      this.form_sucursales.fields.colonia = sucursal.colonia
      this.form_sucursales.fields.poblacion = sucursal.poblacion
      this.form_sucursales.fields.nombre = sucursal.nombre
      this.form_sucursales.fields.encargado = sucursal.encargado
      this.form_sucursales.fields.telefono = sucursal.telefono
      this.form_sucursales.fields.fax = sucursal.fax
      if (sucursal.estado_id === null) {
        this.form_sucursales.fields.estado_id = 0
        this.form_sucursales.fields.municipio_id = 0
      } else {
        this.form_sucursales.fields.estado_id = sucursal.estado_id
        await this.cargaMunicipios()
        if (sucursal.municipio_id === null) {
          this.form_sucursales.fields.municipio_id = 0
        } else {
          this.form_sucursales.fields.municipio_id = sucursal.municipio_id
        }
      }
      this.form_sucursales.fields.cp = sucursal.cp
      this.form_sucursales.fields.tipo_oficina = sucursal.tipo_oficina
      this.form_sucursales.fields.tipo_propiedad = sucursal.tipo_propiedad
      this.setView('update_direccion')
    },
    deleteSucursalSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta sucursal?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteSucursal({id: id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'delete',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDirecciones()
            this.setView('grid_direcciones')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    save_sucursal () {
      this.$v.form_sucursales.$touch()
      if (!this.$v.form_sucursales.$error) {
        this.loadingButton = true
        let params = this.form_sucursales.fields
        this.saveSucursal(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDirecciones()
            this.setView('grid_direcciones')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update_sucursal () {
      this.$v.form_sucursales.$touch()
      if (!this.$v.form_sucursales.$error) {
        this.loadingButton = true
        let params = this.form_sucursales.fields
        this.updateSucursal(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDirecciones()
            this.setView('grid_direcciones')
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
    cargaMunicipios () {
      this.form_sucursales.municipiosOptions = []
      this.form_sucursales.fields.municipio_id = 0
      this.getMunicipiosByEstado({estado_id: this.form_sucursales.fields.estado_id}).then(({data}) => {
        this.form_sucursales.municipiosOptions.push({label: '---Ninguno---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.form_sucursales.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    numberOnly (event) {
      let allowedKeys = [8]
      let numeros = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105]
      let key = event.keyCode
      if (!numeros.includes(key) && !allowedKeys.includes(key)) {
        event.preventDefault()
      }
    }
  },
  validations: {
    form_sucursales: {
      fields: {
        nombre: {required},
        cp: {numeric, minLength: minLength(5), maxLength: maxLength(5)}
      }
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
