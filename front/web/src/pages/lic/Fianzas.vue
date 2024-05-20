<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Fianzas"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
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
                  :data="fianzas"
                  :columns="form.columns"
                  :selected.sync="form.selected"
                  :filter="form.filter"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form.pagination"
                  :loading="form.loading"
                  :rows-per-page-options="form.rowsOptions">
                  <div>
                  </div>
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td key="fianza" :props="props"><q-label @click="saludo()">{{ props.row.fianza }}</q-label></q-td>
                      <q-td key="monto" :props="props">${{ currencyFormat(props.row.monto) }}</q-td>
                      <q-td key="fecha_inicio" :props="props">{{ props.row.fecha_inicio }}</q-td>
                      <q-td key="fecha_fin" :props="props">{{ props.row.fecha_fin }}</q-td>
                      <q-td key="contrato" :props="props">{{ props.row.contrato }}</q-td>
                      <q-td style="text-align: left;" key="nombre_empresa" :props="props">{{ props.row.nombre_empresa }}</q-td>
                      <q-td key="fecha_cancelacion" :props="props">
                        <q-datetime format="DD-MM-YYYY" v-model="props.row.fecha_cancelacion" type="date" @change="cambiarFechaCancelacion(props.row)"></q-datetime>
                      </q-td>
                      <q-td key="documentos" :props="props">
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fianzaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .txt, .TXT" @change="uploadFianzaFile()" />
                        <q-btn small flat @click="selectedFileFianza(props.row)" color="green-9" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir Archivo</q-tooltip>
                        </q-btn>
                        <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                          <q-btn small flat :icon="doc.icon" :color="doc.color">
                            <q-tooltip>{{ doc.name }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verDocumentos(doc.documento_id, doc.doc_type)" v-if="doc.doc_type !== 'docx' && doc.doc_type !== 'xml' && doc.doc_type !== 'xlsx'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarDocumento(doc.documento_id, doc.doc_type)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="fianzaAccion(doc)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-td>
                      <q-td key="documento_final" :props="props">
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="entregableInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadEntregableFile()" />
                        <q-btn small flat @click="selectedRowEntregable(props.row)" color="green-9" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir carta de entera satisfacción</q-tooltip>
                        </q-btn>
                        <q-btn small flat :icon="props.row.icon" :color="props.row.color" v-if="props.row.documento_final !== null">
                          <q-tooltip>{{ props.row.documento_final }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verDocumentoEntregable(props.row.id, props.row.extension)" v-if="props.row.extension !== 'docx' && props.row.extension !== 'xml' && props.row.extension !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarDocumentoEntregable(props.row.id, props.row.extension)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="eliminarDocumentoFinal(props.row.id)">
                                <q-item-main label="Eliminar"/>
                              </q-item>
                            </q-list>
                          </q-popover>
                        </q-btn>
                      </q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <!-- <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn> -->
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

    <!--Crear una fianza-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Fianzas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva fianza"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="label" icon-color="dark" :error="$v.form.fields.fianza.$error" error-label="Ingrese el tipo de gasto">
                  <q-input upper-case v-model="form.fields.fianza" type="text" inverted color="dark" float-label="Fianza" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha de inicio">
                  <q-datetime  format="DD/MM/YYYY" v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha de inicio" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime format="DD/MM/YYYY" v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                    <q-input v-model="form.fields.monto_fianza" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-3">
                <q-field icon="description" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contrato" type="text" inverted color="dark" float-label="Contrato" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar FIANZA -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Fianzas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.fianza"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="label" icon-color="dark" :error="$v.form.fields.fianza.$error" error-label="Ingrese el tipo de gasto">
                  <q-input upper-case v-model="form.fields.fianza" type="text" inverted color="dark" float-label="Fianza" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha de firma">
                  <q-datetime  v-model="form.fields.fecha_inicio" format="DD/MM/YYYY" type="date" inverted color="dark" float-label="Fecha de inicio" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime  v-model="form.fields.fecha_fin" format="DD/MM/YYYY" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)">
                    <q-input v-model.lazy="form.fields.monto_fianza" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-3">
                <q-field icon="description" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contrato" type="text" inverted color="dark" float-label="Contrato" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12  pull-right">
                <q-btn style="margin-top: 1%;" @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- </div> -->
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue, maxValue, maxLength } from 'vuelidate/lib/validators'
import {VMoney} from 'v-money'
import axios from 'axios'

export default {
  // components: {Money},
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase()) {
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
      loadingButton: false,
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          fianza: '',
          monto: 0,
          monto_fianza: 0,
          fecha_inicio: '',
          fecha_fin: '',
          contrato: '',
          empresa: 0,
          fecha_cancelacion: '',
          documento_id: 0,
          name: ''
        },
        // data: [],
        columns: [
          { name: 'fianza', label: 'Fianza', field: 'fianza', sortable: true, type: 'string', align: 'left' },
          { name: 'monto', label: 'Monto', field: 'monto', sortable: true, type: 'string', align: 'right' },
          { name: 'fecha_inicio', label: 'Fecha inicio', field: 'fecha_inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_fin', label: 'Fecha fin', field: 'fecha_fin', sortable: true, type: 'string', align: 'center' },
          { name: 'contrato', label: 'Contrato/Referencia', field: 'contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_empresa', label: 'Empresa', field: 'nombre_empresa', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_cancelacion', label: 'Fecha cancelación', field: 'fecha_cancelacion', sortable: true, type: 'string', align: 'left' },
          { name: 'documentos', label: 'Documento cancelación', field: 'documentos', sortable: true, type: 'string', align: 'left' },
          { name: 'documento_final', label: 'Documento final', field: 'documento_final', sortable: true, type: 'string', align: 'left' },
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
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      fianzas: 'lic/fianzas/fianzas',
      empresasOptions: 'vnt/empresa/selectOptions'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getEmpresas: 'vnt/empresa/refresh',
      getFianzas: 'lic/fianzas/refresh',
      saveFianza: 'lic/fianzas/save',
      updateFianza: 'lic/fianzas/update',
      deleteFianza: 'lic/fianzas/delete',
      eliminarDocumento: 'lic/fianzas/deleteFormato',
      eliminarDocumentoEntregable: 'lic/fianzas/deleteFileFinal'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getFianzas()
      await this.getEmpresas()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    save () {
      this.form.fields.monto = this.evaluaMonto(this.form.fields.monto_fianza)
      this.form.fields.fianza = this.form.fields.fianza.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveFianza(params).then(({data}) => {
          this.loadingButton = false
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    update () {
      this.form.fields.monto = this.evaluaMonto(this.form.fields.monto_fianza)
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateFianza(params).then(({data}) => {
          this.loadingButton = false
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
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
      this.form.fields.fianza = row.fianza
      this.form.fields.monto_fianza = row.monto
      this.form.fields.fecha_inicio = this.stringToDate(row.fecha_inicio, 'dd/MM/yyyy', '/')
      this.form.fields.fecha_fin = this.stringToDate(row.fecha_fin, 'dd/MM/yyyy', '/')
      this.form.fields.fecha_cancelacion = row.fecha_cancelacion
      this.form.fields.contrato = row.contrato
      this.form.fields.empresa = row.empresa
      this.setView('update')
    },
    stringToDate (_date, _format, _delimiter) {
      var formatLowerCase = _format.toLowerCase()
      var formatItems = formatLowerCase.split(_delimiter)
      var dateItems = _date.split(_delimiter)
      var monthIndex = formatItems.indexOf('mm')
      var dayIndex = formatItems.indexOf('dd')
      var yearIndex = formatItems.indexOf('yyyy')
      var month = parseInt(dateItems[monthIndex])
      month -= 1
      var formatedDate = new Date(dateItems[yearIndex], month, dateItems[dayIndex])
      return formatedDate
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta fianza?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteFianza(params).then(({data}) => {
        if (data.result === 'success') {
          this.loadAll()
          this.setView('grid')
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    cambiarFechaCancelacion (row) {
      this.updateFianza({id: row.id, fianza: row.fianza, monto: row.monto, fecha_inicio: row.fecha_inicio, fecha_fin: row.fecha_fin, contrato: row.contrato, empresa: row.empresa, fecha_cancelacion: row.fecha_cancelacion}).then(({data}) => {
        this.$q.notify({
          // only required parameter is the message:
          message: 'Se ha actualizado la fecha de cancelación',
          timeout: 3000,
          type: 'green',
          textColor: 'white', // if default 'white' doesn't fits
          icon: 'done',
          position: 'top-right' // 'top', 'left', 'bottom-left' etc
        })
        if (data.result === 'success') {
          this.loadAll()
          this.setView('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.fianza = ''
      this.form.fields.monto = 0
      this.form.fields.monto_fianza = 0
      this.form.fields.fecha_inicio = ''
      this.form.fields.fecha_fin = ''
      this.form.fields.fecha_cancelacion = ''
      this.form.fields.contrato = ''
      this.form.fields.empresa = 0
      this.setView('create')
    },
    selectedFileFianza (row) {
      this.$refs.fianzaInput.value = ''
      this.registro_fianza = row
      this.$refs.fianzaInput.click()
    },
    uploadFianzaFile () {
      try {
        let file = this.$refs.fianzaInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('fianza_id', this.registro_fianza.id)
        console.log(file.type.split('/')[1].toLowerCase())
        if (this.registro_fianza.documento_id === null) {
          this.registro_fianza.documento_id = 0
        }
        formData.append('formato_requisito_id', this.registro_fianza.documento_id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('fianzas/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: 'Se ha cargado el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.getFianzas()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    fianzaAccion (cotizacion) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarDocumento(cotizacion)
      }).catch(() => {
      })
    },
    borrarDocumento (documento) {
      let params = documento
      this.eliminarDocumento(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.loadAll()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    evaluaMonto (campo) {
      this.cadena2 = '' + campo
      this.cadena = this.cadena2.split(',')
      this.monto_a_pagar = ''
      for (var i = 0; i < this.cadena.length; i++) {
        this.monto_a_pagar = this.monto_a_pagar + this.cadena[i]
      }
      return parseFloat(this.monto_a_pagar)
    },
    descargarDocumento (id, ext) {
      let uri = process.env.API + `fianzas/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    verDocumentos (id, ext) {
      let uri = process.env.API + '/public/assets/licitaciones/fianzas/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    selectedRowEntregable (row) {
      this.$refs.entregableInput.value = ''
      this.registro_entregable = row
      this.$refs.entregableInput.click()
    },
    uploadEntregableFile () {
      try {
        let file = this.$refs.entregableInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('contrato_id', this.registro_entregable.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('fianzas/uploadArchivoFinal', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: 'Se ha cargado el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.loadAll()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    verDocumentoEntregable (id, ext) {
      let uri = process.env.API + '/public/assets/fianzas_documentos_finales/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoEntregable (id, ext) {
      let uri = process.env.API + `fianzas/getFileFinal/${id}/${ext}`
      window.open(uri, '_blank')
    },
    eliminarDocumentoFinal (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarDocumentoEntregable(id)
      }).catch(() => {
      })
    },
    borrarDocumentoEntregable (id) {
      this.eliminarDocumentoEntregable({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.loadAll()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    saludo () {
      console.log('hi')
    }
  },
  validations: {
    form: {
      fields: {
        fianza: { required, maxLength: maxLength(100) },
        monto: {required, minValue: minValue(1), maxValue: maxValue(1000000000)},
        empresa: {required, minValue: minValue(1)},
        fecha_inicio: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
thead {
  margin-bottom: 30px;
}
</style>
