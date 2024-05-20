<template>
  <!-- <q-page> -->
  <div>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
            </div>
          </div>
        </div>
      </div>
          <div class="col">
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
                    class="bg-white"
                    :pagination.sync="form.pagination"
                    :loading="form.loading"
                    :rows-per-page-options="form.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                        <q-td key="fecha_recepcion" :props="props">{{ props.row.fecha_recepcion }}</q-td>
                        <q-td key="name_user" :props="props">{{ props.row.name_user }}</q-td>
                        <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete" v-if="props.row.status === 'NUEVO'">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
              </div>
            </div>
          </div>
    </div>

    <!--Crear un municipio-->

      <div v-if="views.create">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6 pull-right">
              <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
               <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
             </div>
           </div>
          </div>
        </div>
        <!-- <div class="q-pa-md bg-grey-3"> -->
          <div class="row bg-white">
            <div class="col q-pa-md">
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Escriba el folio">
                    <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                    <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="person" icon-color="dark">
                    <q-select v-model="form.fields.user_id" inverted color="dark" float-label="Recibido por" :options="usersOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="fas fa-info" icon-color="dark">
                    <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-2 offset-sm-10 pull-right">
                  <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
            </div>
          </div>
        <!-- </div> -->
      </div>

      <div v-if="views.update">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6 pull-right">
              <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
               <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
             </div>
           </div>
          </div>
        </div>
        <!-- <div class="q-pa-md bg-grey-3"> -->
          <div class="row bg-white">
            <div class="col q-pa-md">
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Escriba el folio">
                    <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                    <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="person" icon-color="dark">
                    <q-select v-model="form.fields.user_id" inverted color="dark" float-label="Recibido por" :options="usersOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="fas fa-info" icon-color="dark">
                    <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-12 pull-right">
                  <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn>
                  <q-btn @click="ejecutar()" class="btn_solicitar" icon-right="check" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0">Ejecutar</q-btn>
                  <q-btn @click="cancelar()" color="orange-8" icon-right="clear" :loading="loadingButton" v-if="form.fields.status === 'NUEVO'">Cancelar</q-btn>
                </div>
              </div>

              <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
              <q-tab name="detalles" slot="title" icon="fas fa-box" label="DETALLES"/>

              <q-tab-pane name="detalles">
                <div class="row q-col-gutter-xs">
                  <div class="row q-mt-lg subtitulos" v-if="form.fields.status === 'NUEVO'">
                    Detalles de la recepción
                  </div>
                </div>
                <div class="row q-col-gutter-xs" style="margin-top: 10px;" v-if="form.fields.status === 'NUEVO'">
                  <div class="col-sm-3">
                    <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                      <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-box" icon-color="dark">
                      <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                      <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                    </q-field>
                  </div>
                  <div class="col-sm-2 pull-right">
                    <q-btn @click="saveMovimientosDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false && this.form.fields.status === 'NUEVO'">Agregar</q-btn>
                    <q-btn @click="updateMovimientosDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true && this.form.fields.status === 'NUEVO'">Actualizar</q-btn>
                    <!-- <q-btn @click="getDetallesByMovimiento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardarrrrrr</q-btn> -->
                  </div>
                </div>
                <div class="row q-mt-sm" style="margin-top:10px;">
                  <div class="col-sm-12">
                    <q-search color="primary" v-model="form_detalles.filter"/>
                  </div>
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                    <q-table id="sticky-table-newstyle"
                      :data="form_detalles.data"
                      :columns="form_detalles.columns"
                      :selected.sync="form_detalles.selected"
                      :filter="form_detalles.filter"
                      color="positive"
                      title=""
                      :dense="true"
                      :pagination.sync="form_detalles.pagination"
                      :loading="form_detalles.loading"
                      :rows-per-page-options="form_detalles.rowsOptions">
                      <template slot="body" slot-scope="props">
                        <q-tr :props="props">
                          <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                          <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                          <q-td key="acciones" :props="props">
                            <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status === 'NUEVO'">
                              <q-tooltip>Editar</q-tooltip>
                            </q-btn>
                            <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                              <q-tooltip>Eliminar</q-tooltip>
                            </q-btn>
                            <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status === 'EJECUTADO'">
                              <q-tooltip>{{ form.fields.status }}</q-tooltip>
                            </q-btn>
                            <q-btn small flat color="orange-8" icon="clear" v-if="form.fields.status === 'CANCELADO'">
                              <q-tooltip>{{ form.fields.status }}</q-tooltip>
                            </q-btn>
                          </q-td>
                        </q-tr>
                      </template>
                    </q-table>
                    <q-inner-loading :visible="form_detalles.loading">
                      <q-spinner-dots size="64px" color="primary" />
                    </q-inner-loading>
                  </div>
                </div>
              </q-tab-pane>
            </q-tabs>
            </div>
          </div>
        <!-- </div> -->
      </div>
  </div>
  <!-- </q-page> -->
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'
import moment from 'moment'
import axios from 'axios'

export default {
  name: 'Recepciones',
  props: {
    compra_id: {
      type: Number
    }
  },
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase()) {
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
      productosOptions: [{label: '---Seleccione---', value: 0}],
      tab: 'detalles',
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          folio: '',
          fecha: '',
          user_id: '',
          status: 0
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_recepcion', label: 'Fecha', field: 'fecha_recepcion', sortable: true, type: 'string', align: 'center' },
          { name: 'name_user', label: 'Usuario', field: 'name_user', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        data: [],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_detalles: {
        data: [],
        editar: false,
        fields: {
          id: 0,
          producto_id: 0,
          presentacion_id: 0,
          cantidad: 0,
          costo: 0,
          recepcion_id: 0
        },
        // data: [],
        columns: [
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
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
      // recepciones: 'cmp/recepciones/recepciones'
    }),
    usersOptions () {
      let users = this.$store.getters['sys/users/selectOptionsName']
      users.push({label: '---Seleccione---', value: 0})
      users.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return users
    },
    presentacionesOptions () {
      let presentaciones = this.$store.getters['inv/tipos_presentaciones/selectOptions']
      presentaciones.push({label: '---Seleccione---', value: 0})
      presentaciones.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return presentaciones
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getRecepciones: 'cmp/recepciones/getByCompra',
      saveRecepciones: 'cmp/recepciones/save',
      updateRecepciones: 'cmp/recepciones/update',
      deleteRecepciones: 'cmp/recepciones/delete',
      getUsers: 'sys/users/refresh',
      getFolioConsecutivo: 'cmp/recepciones/getFolioConsecutivo', //
      getProductos: 'inv/productos/getByCompra',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getDetalles: 'cmp/recepciones_detalles/getByRecepcion',
      saveDetalles: 'cmp/recepciones_detalles/save',
      updateDetalles: 'cmp/recepciones_detalles/update',
      deleteDetalles: 'cmp/recepciones_detalles/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getRecepcionesByCompra()
      await this.getUsers()
      await this.cargaProductos()
      await this.getPresentaciones()
      this.form.loading = false
    },
    async getRecepcionesByCompra () {
      this.form.data = []
      await axios.get(`/recepciones/getByCompra/${this.compra_id}`).then(({data}) => {
        this.form.data = data.recepciones
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async getFolioNuevo () {
      this.form.fields.folio = ''
      await this.getFolioConsecutivo().then(({data}) => {
        if (data.result === 'success') {
          this.form.fields.folio = data.folio
        }
      }).catch(error => {
        console.error(error)
      })
    },
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_detalles.fields.cantidad = this.form_detalles.fields.cantidad.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.cantidad.$touch()
          break
        case 'costo':
          this.form_detalles.fields.costo = this.form_detalles.fields.costo.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.costo.$touch()
          break
        default:
          break
      }
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        this.form.fields.fecha = moment(String(this.form.fields.fecha)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        let params = this.form.fields
        params.compra_id = this.compra_id
        this.saveRecepciones(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.form.fields.id = data.id
            this.form_detalles.fields.recepcion_id = data.id
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
            this.setView('update')
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
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateRecepciones(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.form.fields.status = data.status
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      this.form.fields.id = row.id
      this.form.fields.folio = row.folio
      this.form.fields.fecha = row.fecha
      this.form.fields.user_id = row.user_id
      this.form.fields.status = row.status
      this.form_detalles.fields.recepcion_id = row.id
      this.limpiarDetalles()
      this.getDetallesByMovimiento()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta recepción?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteRecepciones(params).then(({data}) => {
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
      this.form.fields.folio = ''
      this.form.fields.fecha = new Date()
      this.form.fields.status = 'NUEVO'
      this.form.fields.user_id = ''
      this.form_detalles.data = []
      this.getFolioNuevo()
      this.setView('create')
    },
    atras () {
      this.setView('grid')
      this.loadAll()
    },
    async getPresentacion () {
      await axios.get(`/productos/getPresentacionByProducto/${this.form_detalles.fields.producto_id}`).then(({data}) => {
        this.form_detalles.fields.presentacion_id = data.productos[0].presentacion_id
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    async saveMovimientosDetalles () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        this.loadingButton = true
        let params = this.form_detalles.fields
        this.saveDetalles(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
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
    async updateMovimientosDetalles () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        this.loadingButton = true
        let params = this.form_detalles.fields
        this.updateDetalles(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.form_detalles.editar = false
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
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
    async getDetallesByMovimiento () {
      this.form_detalles.data = []
      await this.getDetalles({recepcion_id: this.form_detalles.fields.recepcion_id}).then(({data}) => {
        if (data.recepciones.length > 0) {
          this.form_detalles.data = data.recepciones
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async editRowDetalle (row) {
      //
      this.form_detalles.fields.id = row.id
      this.form_detalles.fields.producto_id = row.producto_id
      // this.form_detalles.fields.presentacion_id = row.presentacion_id
      this.form_detalles.fields.cantidad = row.cantidad
      this.form_detalles.fields.costo = row.costo
      this.form_detalles.fields.recepcion_id = this.form.fields.id
      this.getPresentacion()
      this.form_detalles.editar = true
      //
      this.$v.form_detalles.$reset()
    },
    async limpiarDetalles () {
      //
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.producto_id = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.costo = 0
      this.form_detalles.fields.recepcion_id = this.form.fields.id
      this.form_detalles.editar = false
      //
      this.$v.form_detalles.$reset()
    },
    deleteSingleRowDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este detalle de recepción?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteMovimientoDetalle(id)
      }).catch(() => {})
    },
    deleteMovimientoDetalle (id) {
      let params = {id: id}
      this.deleteDetalles(params).then(({data}) => {
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
          this.getDetallesByMovimiento()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    ejecutar () {
      this.form.fields.status = 'EJECUTADO'
      this.update()
    },
    cancelar () {
      this.form.fields.status = 'CANCELADO'
      this.update()
    },
    async cargaProductos () {
      this.productosOptions = []
      await this.getProductos({compra_id: this.compra_id}).then(({data}) => {
        this.productosOptions.push({label: '---Seleccione---', value: 0})
        if (data.productos.length > 0) {
          data.productos.forEach(producto => {
            this.productosOptions.push({label: producto.nombre, value: producto.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        folio: { required },
        fecha: { required }
      }
    },
    form_detalles: {
      fields: {
        producto_id: { required, minValue: minValue(1) },
        cantidad: { required, minValue: minValue(1) }
      }
    }
  }
}
</script>

<style scoped>
</style>
