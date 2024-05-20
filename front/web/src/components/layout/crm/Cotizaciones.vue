<template>
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
                  <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                  <q-td key="titulo" :props="props">{{ props.row.titulo }}</q-td>
                  <q-td key="acciones" :props="props">
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
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
             <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
             <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
           </div>
         </div>
       </div>
     </div>
     <!-- <div class="q-pa-md bg-grey-3"> -->
      <div class="row bg-white">
        <div class="col q-pa-md">
          <div class="row q-col-gutter-xs">
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input v-model="form.fields.encabezado" type="textarea" inverted color="dark" float-label="Encabezado"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.titulo.$error" error-label="Escriba el título">
                <q-input upper-case v-model="form.fields.titulo" type="textarea" inverted color="dark" float-label="Título"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="dialpad" icon-color="dark">
                <q-input v-model="form.fields.dirigido" type="textarea" inverted color="dark" float-label="Dirigido a:"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-input v-model="form.fields.lugar" type="text" inverted color="dark" float-label="Lugar"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="business" icon-color="dark">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-input v-model="form.fields.cuerpo" type="textarea" inverted color="dark" float-label="Cuerpo"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input upper-case v-model="form.fields.total_letra" type="text" inverted color="dark" float-label="Total con letra"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input upper-case v-model="form.fields.tiempo_entrega" type="textarea" inverted color="dark" float-label="Tiempo de entrega"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input upper-case v-model="form.fields.condiciones_pago" type="textarea" inverted color="dark" float-label="Condiciones de pago"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input v-model="form.fields.cuerpo_final" type="textarea" inverted color="dark" float-label="Cuerpo final"/>
              </q-field>
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
             <q-btn @click="exportPDF()" color="red" icon-right="fas fa-file-pdf" style="margin-right: 10px;">Generar PDF</q-btn>
             <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
           </div>
         </div>
       </div>
     </div>
      <div class="row bg-white">
        <div class="col q-pa-md">
          <div class="row q-col-gutter-xs">
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input v-model="form.fields.encabezado" type="textarea" inverted color="dark" float-label="Encabezado"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.titulo.$error" error-label="Escriba el título">
                <q-input upper-case v-model="form.fields.titulo" type="textarea" inverted color="dark" float-label="Título"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="dialpad" icon-color="dark">
                <q-input v-model="form.fields.dirigido" type="textarea" inverted color="dark" float-label="Dirigido a:"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-input v-model="form.fields.lugar" type="text" inverted color="dark" float-label="Lugar"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="business" icon-color="dark">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-input v-model="form.fields.cuerpo" type="textarea" inverted color="dark" float-label="Cuerpo"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input upper-case v-model="form.fields.total_letra" type="text" inverted color="dark" float-label="Total con letra"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input upper-case v-model="form.fields.tiempo_entrega" type="textarea" inverted color="dark" float-label="Tiempo de entrega"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input upper-case v-model="form.fields.condiciones_pago" type="textarea" inverted color="dark" float-label="Condiciones de pago"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="description" icon-color="dark">
                <q-input v-model="form.fields.cuerpo_final" type="textarea" inverted color="dark" float-label="Cuerpo final"/>
              </q-field>
            </div>
          </div>

          <!-- <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
            <q-tab name="detalles" slot="title" icon="fas fa-box" label="DETALLES"/>

            <q-tab-pane name="detalles"> -->
              <div class="row q-col-gutter-xs">
                <div class="row q-mt-lg subtitulos">
                  Detalles de la cotización
                </div>
              </div>
              <div class="row q-col-gutter-xs" style="margin-top: 10px;">
                <div class="col-sm-3">
                  <q-field icon="dialpad" icon-color="dark">
                    <q-input upper-case v-model="form_detalles.fields.lote_progresivo" type="text" inverted color="dark" float-label="Lote/Progresivo"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                    <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.precio.$error" error-label="Ingrese el precio unitario">
                    <q-input upper-case v-model="form_detalles.fields.precio" type="text" inverted color="dark" float-label="Precio unitario sin IVA" maxlength="50" @keyup="isNumber($event,'precio')"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="dialpad" icon-color="dark">
                    <q-input upper-case v-model="form_detalles.fields.unidad" type="text" inverted color="dark" float-label="Unidad de medida"/>
                  </q-field>
                </div>
                <div class="col-sm-12">
                  <q-field icon="dialpad" icon-color="dark" :error="$v.form_detalles.fields.articulo.$error" error-label="Ingrese el nombre del artículo/servicio">
                    <q-input upper-case v-model="form_detalles.fields.articulo" type="text" inverted color="dark" float-label="Artículo/Servicio"/>
                  </q-field>
                </div>
                <div class="col-sm-12">
                  <q-field icon="dialpad" icon-color="dark">
                    <q-input upper-case v-model="form_detalles.fields.descripcion" type="text" inverted color="dark" float-label="Descripción"/>
                  </q-field>
                </div>
                <div class="col-sm-12 pull-right">
                  <q-btn @click="saveDetalle()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false">Agregar</q-btn>
                  <q-btn @click="updateDetalle()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true">Actualizar</q-btn>
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
                        <q-td key="lote_progresivo" :props="props">{{ props.row.lote_progresivo }}</q-td>
                        <q-td key="cantidad_formato" :props="props">{{ props.row.cantidad_formato }}</q-td>
                        <q-td key="unidad" :props="props">{{ props.row.unidad }}</q-td>
                        <q-td key="articulo" :props="props">{{ props.row.articulo }}</q-td>
                        <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                        <q-td key="precio_formato" :props="props">{{ props.row.precio_formato }}</q-td>
                        <q-td key="total" :props="props">{{ props.row.total }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat @click="editDetalle(props.row)" color="blue-6" icon="edit">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleDetalle(props.row.id)" color="negative" icon="delete">
                            <q-tooltip>Eliminar</q-tooltip>
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
            <!-- </q-tab-pane>
          </q-tabs> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required, minValue, minLength } from 'vuelidate/lib/validators'
import moment from 'moment'
import axios from 'axios'

export default {
  name: 'Cotizacion',
  props: {
    oportunidad_id: {
      type: Number
    }
  },
  data () {
    return {
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
          fecha: '',
          empresa_id: 0,
          oportunidad_id: 0,
          titulo: '',
          cuerpo: '',
          encabezado: '',
          lugar: '',
          total_letra: '',
          tiempo_entrega: '',
          condiciones_pago: '',
          cuerpo_final: '',
          dirigido: ''
        },
        // data: [],
        columns: [
          { name: 'fecha', label: 'Fecha', field: 'fecha', sortable: true, type: 'string', align: 'center' },
          { name: 'titulo', label: 'Título', field: 'titulo', sortable: true, type: 'string', align: 'center' },
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
          cantidad: 0,
          precio: 0,
          articulo: '',
          lote_progresivo: '',
          cotizacion_id: 0,
          unidad: '',
          descripcion: ''
        },
        // data: [],
        columns: [
          { name: 'lote_progresivo', label: 'Lote/progresivo', field: 'lote_progresivo', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad_formato', label: 'Cantidad', field: 'cantidad_formato', sortable: true, type: 'string', align: 'right' },
          { name: 'unidad', label: 'Unidad de medida', field: 'unidad', sortable: true, type: 'string', align: 'right' },
          { name: 'articulo', label: 'Articulo/Servicio', field: 'articulo', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'precio_formato', label: 'Precio sin IVA', field: 'precio_formato', sortable: true, type: 'string', align: 'right' },
          { name: 'total', label: 'Importe', field: 'total', sortable: true, type: 'string', align: 'right' },
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
    empresasOptions () {
      let empresa = this.$store.getters['vnt/empresa/selectOptions']
      empresa.splice(0, 0, {label: '---Seleccione---', value: 0})
      empresa.sort(function (a, b) {
        if (a.razon_social > b.razon_social) {
          return 1
        }
        if (a.razon_social < b.razon_social) {
          return -1
        }
        return 0
      })
      return empresa
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      saveCotizaciones: 'crm/cotizaciones/save',
      updateCotizaciones: 'crm/cotizaciones/update',
      deleteCotizaciones: 'crm/cotizaciones/delete',
      getEmpresas: 'vnt/empresa/refresh',
      /* getUsers: 'sys/users/refresh',
      getFolioConsecutivo: 'crm/cotizaciones/getFolioConsecutivo', //
      getProductos: 'inv/productos/getByCompra',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getDetalles: 'cmp/recepciones_detalles/getByRecepcion', */
      saveDetalles: 'crm/cotizaciones_detalles/save',
      updateDetalles: 'crm/cotizaciones_detalles/update',
      deleteDetalles: 'crm/cotizaciones_detalles/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEmpresas()
      await this.getCotizaciones()
      this.form.loading = false
    },
    async getCotizaciones () {
      this.form.data = []
      await axios.get(`/cotizaciones/getByOportunidad/${this.oportunidad_id}`).then(({data}) => {
        this.form.data = data.cotizaciones
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
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        this.form.fields.fecha = moment(String(this.form.fields.fecha)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        let params = this.form.fields
        params.oportunidad_id = this.oportunidad_id
        this.saveCotizaciones(params).then(({data}) => {
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
            this.form_detalles.fields.cotizacion_id = data.id
            /* this.limpiarDetalles()
            this.getDetallesByMovimiento() */
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
        this.updateCotizaciones(params).then(({data}) => {
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
      this.form.fields.fecha = row.fecha
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.oportunidad_id = row.oportunidad_id
      this.form.fields.titulo = row.titulo
      this.form.fields.cuerpo = row.cuerpo
      this.form.fields.encabezado = row.encabezado
      this.form.fields.lugar = row.lugar
      this.form.fields.total_letra = row.total_letra
      this.form.fields.tiempo_entrega = row.tiempo_entrega
      this.form.fields.condiciones_pago = row.condiciones_pago
      this.form.fields.cuerpo_final = row.cuerpo_final
      this.form.fields.dirigido = row.dirigido
      this.form_detalles.data = []
      this.form_detalles.fields.cotizacion_id = row.id
      this.limpiarDetalles()
      this.getDetalles()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta cotización?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteCotizaciones(params).then(({data}) => {
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
      this.form.fields.fecha = new Date()
      this.form.fields.empresa_id = 0
      this.form.fields.oportunidad_id = 0
      this.form.fields.titulo = ''
      this.form.fields.cuerpo = ''
      this.form.fields.encabezado = ''
      this.form.fields.lugar = ''
      this.form.fields.total_letra = ''
      this.form.fields.tiempo_entrega = ''
      this.form.fields.condiciones_pago = ''
      this.form.fields.cuerpo_final = ''
      this.form.fields.dirigido = ''
      this.form_detalles.data = []
      this.setView('create')
    },
    atras () {
      this.setView('grid')
      this.getCotizaciones()
    },
    exportPDF () {
      if (this.form.fields.empresa_id > 0) {
        let uri
        uri = process.env.API + `cotizaciones/exportPDF_cotizacion/${this.form.fields.id}`
        window.open(uri, '_blank')
      } else {
        this.$showMessage('Advertencia', 'La cotización debe estar relacionada con alguna empresa')
      }
    },
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_detalles.fields.cantidad = this.form_detalles.fields.cantidad.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.cantidad.$touch()
          break
        case 'precio':
          this.form_detalles.fields.precio = this.form_detalles.fields.precio.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.precio.$touch()
          break
        default:
          break
      }
    },
    limpiarDetalles () {
      this.form_detalles.editar = false
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.lote_progresivo = ''
      this.form_detalles.fields.unidad = ''
      this.form_detalles.fields.articulo = ''
      this.form_detalles.fields.descripcion = ''
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.precio = 0
    },
    async getDetalles () {
      this.form.data = []
      await axios.get(`/cotizaciones_detalles/getByCotizacion/${this.form_detalles.fields.cotizacion_id}`).then(({data}) => {
        this.form_detalles.data = data.detalles
      }).catch(error => {
        console.error(error)
      })
    },
    saveDetalle () {
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
            this.getDetalles()
            this.limpiarDetalles()
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
    updateDetalle () {
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
            this.getDetalles()
            this.limpiarDetalles()
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
    editDetalle (row) {
      this.form_detalles.fields.id = row.id
      this.form_detalles.fields.lote_progresivo = row.lote_progresivo
      this.form_detalles.fields.unidad = row.unidad
      this.form_detalles.fields.articulo = row.articulo
      this.form_detalles.fields.descripcion = row.descripcion
      this.form_detalles.fields.cantidad = row.cantidad
      this.form_detalles.fields.precio = row.precio
      this.form_detalles.editar = true
    },
    deleteSingleDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este detalle?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteDetalle(id)
      }).catch(() => {})
    },
    deleteDetalle (id) {
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
          this.getDetalles()
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
        titulo: { required },
        fecha: { required }
      }
    },
    form_detalles: {
      fields: {
        cantidad: { required, minValue: minValue(1) },
        precio: { required, minValue: minValue(1) },
        articulo: { required, minLength: minLength(1) }
      }
    }
  }
}
</script>

<style scoped>
</style>
