<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Remisiones masivas" />
              </q-breadcrumbs>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <q-field  icon="attach_file" icon-color="dark" >
                  <input multiple style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".xml, .XML" @input="checkContrato()"/>
                  <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" :loading="loadingButton" :disable="this.form.proceso_iniciado === true">Adjuntar Factura(s)</q-btn>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-btn @click="iniciarProceso()" class="btn_guardar" :loading="loadingButton" :disable="this.form.proceso_iniciado === true">Iniciar proceso</q-btn>
              </div>
              <div class="col-sm-2">{{ this.form.files_length }} archivos adjuntos
              </div>
              <div class="col-sm-6 pull-right">
                <q-btn @click="newProceso()" class="btn_rechazar" icon="refresh" :loading="loadingButton">Reiniciar proceso</q-btn>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="padding-top: 25px;">
              <div class="col-sm-12" style="margin-bottom: 10px;" v-if="this.form.factura_repetida">
                <q-field icon="fas fa-times" icon-color="red">
                  <q-input upper-case v-model="form.fields.error" type="text" inverted color="red" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.id_empresa.$error" error-label="Elija una empresa">
                  <q-select readonly v-model="form.fields.id_empresa" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.id_cliente.$error" error-label="Elija un cliente">
                  <q-select readonly v-model="form.fields.id_cliente" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_valida.$error" error-label="Seleccione la fecha">
                  <q-datetime readonly v-model="form.fields.fecha_valida" type="date" inverted color="dark" float-label="Fecha de venta" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="description" icon-color="dark" :error="$v.form.fields.metodo_pago.$error" error-label="Por favor seleccione un método de pago">
                  <q-select readonly v-model="form.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="description" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el concepto de la factura">
                  <q-input readonly upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Concepto factura"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-id-card" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.rfc_emisor" type="text" inverted color="dark" float-label="RFC Emisor" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-id-card" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.rfc_receptor" type="text" inverted color="dark" float-label="RFC Receptor" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="https" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.folio_fiscal" type="text" inverted color="dark" float-label="Folio fiscal" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.monto_factura" type="text" inverted color="dark" float-label="Monto factura" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-checkbox readonly v-model="form.fields.amortizacion" color="green-10" label="Amortización"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12 pull-right">
                <q-btn @click="descartar()" class="btn_actualizar" :loading="loadingButton" :disable="this.form.proceso_iniciado === false">Descartar y continuar</q-btn>
                <!-- <q-btn @click="siguiente_cliente()" class="btn_guardar" :loading="loadingButton" :disable="this.form.proceso_iniciado === false || this.form.factura_repetida === true">Guardar para PÚBLICO EN GRAL</q-btn> -->
                <q-btn @click="siguiente()" class="btn_guardar" :loading="loadingButton" :disable="this.form.proceso_iniciado === false || this.form.factura_repetida === true">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase()) {
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
      views: {
        grid: true,
        create: false,
        update: false
      },
      metodoPagoOptions: [ { 'label': 'Pago en una sola exhibición', 'value': 'PUE' }, { 'label': 'Pago en parcialidades', 'value': 'PPD' } ],
      form: {
        fields: {
          id: 0,
          nombre: '',
          id_empresa: 0,
          id_cliente: 0,
          monto_factura: 0,
          metodo_pago: '',
          folio_fiscal: '',
          rfc_emisor: '',
          rfc_receptor: '',
          fecha_valida: '',
          error: '',
          amortizacion: false
        },
        dataFiles: [],
        files_length: 0,
        files_contador: 0,
        proceso_iniciado: false,
        factura_repetida: false,
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
      modal: {
        x: 0,
        y: 0,
        transition: null
      }
    }
  },
  computed: {
    clientesOptions () {
      let clientes = this.$store.getters['ventas/clientes/selectOptions']
      clientes.splice(0, 0, { 'label': '--Seleccione--', 'value': 0 })
      return clientes
    },
    empresasOptions () {
      let empresa = this.$store.getters['vnt/empresa/selectOptions']
      empresa.splice(0, 0, { 'label': '--Seleccione--', 'value': 0 })
      return empresa
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      saveRemision: 'vnt/remisiones/saveWithFactura'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getClientes()
      await this.getEmpresas()
      this.newProceso()
      // await this.getGiros()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    newProceso () {
      this.$v.form.$reset()
      this.form.files_contador = 0
      this.form.fields.id = 0
      this.form.fields.nombre = ''
      this.form.fields.id_empresa = 0
      this.form.fields.id_cliente = 0
      this.form.fields.monto_factura = 0
      this.form.fields.metodo_pago = ''
      this.form.fields.folio_fiscal = ''
      this.form.fields.rfc_emisor = ''
      this.form.fields.rfc_receptor = ''
      this.form.fields.fecha_valida = ''
      this.form.files_contador = 0
      this.form.files_length = 0
      this.form.proceso_iniciado = false
      this.form.factura_repetida = false
      this.form.fields.amortizacion = false
    },
    uploadXML () {
      this.terminado = true
      this.loadingButton = false
      if (this.form.files_length !== 0) {
        let file = this.$refs.fileInputFormato.files[this.form.files_contador]
        let formData = new FormData()
        formData.append('nombre', file.name)
        formData.append('file', file)
        axios.post('remisionesFacturas/uploadArchivoMasivo', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }).then(response => {
          if (response.data.result === 'success') {
            this.terminado = false
            if (response.data.message.title === '¡Error!') {
              this.form.fields.error = response.data.message.content
              this.form.factura_repetida = true
            } else {
              this.$q.notify({
                message: response.data.message.content,
                timeout: 3000,
                type: 'green',
                textColor: 'white', // if default 'white' doesn't fits
                icon: 'done',
                position: 'top-right' // 'top', 'left', 'bottom-left' etc
              })
            }
            this.form.files_contador++
            this.form.fields.id = response.data.id
            this.form.fields.nombre = response.data.nombre
            this.form.fields.id_empresa = response.data.id_empresa
            this.form.fields.id_cliente = response.data.id_cliente
            this.form.fields.monto_factura = this.currencyFormat(response.data.monto_factura)
            this.form.fields.metodo_pago = response.data.metodo_pago
            this.form.fields.folio_fiscal = response.data.uuid
            this.form.fields.rfc_emisor = response.data.rfc_emisor
            this.form.fields.rfc_receptor = response.data.rfc_receptor
            this.form.fields.fecha_valida = response.data.fecha_valida
            this.form.fields.amortizacion = response.data.amortizacion
            this.loadingButton = false
          } else {
            if (response.data.err !== '') {
              this.terminado = false
              this.loadingButton = false
              this.form.fields.error = response.data.message.content
              this.form.factura_repetida = true
            }
          }
        }).catch(error => {
          this.terminado = false
          this.loadingButton = false
          console.error(error)
        })
      } else {
        this.terminado = false
        this.loadingButton = false
        this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .xml')
      }
    },
    checkContrato () {
      this.form.files_length = this.$refs.fileInputFormato.files.length
      this.form.files_contador = 0
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    iniciarProceso () {
      this.form.proceso_iniciado = true
      this.uploadXML()
    },
    descartar () {
      if (this.form.files_length === this.form.files_contador) {
        this.$q.notify({
          message: 'Operación finalizada',
          timeout: 3000,
          type: 'green',
          textColor: 'white', // if default 'white' doesn't fits
          icon: 'done',
          position: 'top-right' // 'top', 'left', 'bottom-left' etc
        })
        this.newProceso()
      } else {
        this.form.factura_repetida = false
        this.uploadXML()
      }
    },
    siguiente () {
      this.save()
    },
    siguiente_cliente () {
      this.form.fields.id_cliente = 52
      this.save()
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.fecha_venta = this.form.fields.fecha_valida
        params.cliente_id = this.form.fields.id_cliente
        params.empresa_id = this.form.fields.id_empresa
        params.tipo = 'HISTÓRICO'
        params.factura_id = this.form.fields.id
        params.amortizacion = this.form.fields.amortizacion
        this.saveRemision(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white',
              icon: 'check',
              position: 'top-right'
            })
            this.descartar()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
        this.loadingButton = false
      }
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required },
        id_empresa: {required, minValue: minValue(1)},
        id_cliente: {required, minValue: minValue(1)},
        fecha_valida: {required},
        metodo_pago: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
