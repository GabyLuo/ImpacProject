<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Cuentas bancarias"/>
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
            <div class="col q-pa-md ">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="cuentas"
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
                      <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
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
                <q-inner-loading :visible="form.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--Crear un subprograma-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Cuentas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva cuenta"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre de cuenta" maxlength="100" />
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

      <!-- Modal editar PROGRAMA -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Cuentas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.nombre"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre de cuenta" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="q-pb-md">Saldos por día</div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.saldos.fecha.$error" error-label="Ingrese la fecha inicio">
                  <q-datetime v-model="form.saldos.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.saldos.monto.$error" error-label="Ingrese el monto">
                    <q-input v-model.lazy="form.saldos.monto" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-3 q-pl-sm q-pt-sm">
                <q-btn @click="save_saldo()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="boton_actualiza === false">Guardar</q-btn>
                <q-btn @click="update_saldo()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="boton_actualiza === true">Guardar</q-btn>
              </div>
            </div>
            <div class="col q-pt-md ">
              <div class="col-sm-12 q-pt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="saldos"
                  :columns="form.columnssaldo"
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
                      <q-td key="fecha_formato" :props="props">{{ props.row.fecha_formato }}</q-td>
                      <q-td key="monto" :props="props">{{ props.row.monto }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editSaldo(props.row)" color="blue-6" icon="edit">
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
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minValue } from 'vuelidate/lib/validators'

export default {
  data () {
    return {
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          nombre: '',
          empresa_id: 0
        },
        saldos: {
          id: 0,
          monto: 0,
          fecha: ''
        },
        // data: [],
        columns: [
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Cuenta', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        columnssaldo: [
          { name: 'fecha_formato', label: 'Fecha', field: 'fecha_formato', sortable: true, type: 'string', align: 'center' },
          { name: 'monto', label: 'Monto', field: 'monto', sortable: true, type: 'string', align: 'right' },
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
      },
      saldos: [],
      boton_actualiza: false
    }
  },
  computed: {
    ...mapGetters({
      empresasOptions: 'vnt/empresa/selectOptions',
      cuentas: 'vnt/cuenta/cuentas'
    })
  },
  created () {
    this.loadAll()
  },
  watch: {
    informacion (newValue, old) {
      if (newValue === false) {
        this.objetoInformacion = null
      }
    }
  },
  methods: {
    ...mapActions({
      getCuentas: 'vnt/cuenta/refresh',
      saveCuenta: 'vnt/cuenta/save',
      updateCuenta: 'vnt/cuenta/update',
      deleteCuenta: 'vnt/cuenta/delete',
      getEmpresas: 'vnt/empresa/refresh',
      saveSaldo: 'vnt/saldo/save',
      updateSaldo: 'vnt/saldo/update',
      deleteSaldo: 'vnt/saldo/delete',
      getSaldos: 'vnt/saldo/getByCuenta'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEmpresas()
      await this.getCuentas()
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
        this.saveCuenta(params).then(({data}) => {
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
        this.updateCuenta(params).then(({data}) => {
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
      this.form.fields.empresa_id = row.empresa_id
      this.cargarSaldos()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta cuenta?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteCuenta(params).then(({data}) => {
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
      this.form.fields.nombre = ''
      this.form.fields.empresa_id = 0
      this.setView('create')
    },
    newRowSaldo () {
      this.$v.form.$reset()
      this.form.saldos.id = 0
      this.form.saldos.monto = 0
      this.form.saldos.fecha = ''
      this.boton_actualiza = false
      this.setView('create')
    },
    save_saldo () {
      this.$v.form.saldos.$touch()
      if (!this.$v.form.saldos.$error) {
        this.loadingButton = true
        let params = this.form.saldos
        params.cuenta_id = this.form.fields.id
        this.saveSaldo(params).then(({data}) => {
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
            this.cargarSaldos()
            this.newRowSaldo()
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
    editSaldo (row) {
      this.form.saldos.id = row.id
      this.form.saldos.monto = row.monto
      this.form.saldos.fecha = row.fecha
      this.boton_actualiza = true
    },
    async cargarSaldos () {
      this.saldos = []
      await this.getSaldos({cuenta_id: this.form.fields.id}).then(({data}) => {
        this.saldos = data.saldos
      }).catch(error => {
        console.error(error)
      })
    },
    update_saldo () {
      this.$v.form.saldos.$touch()
      if (!this.$v.form.saldos.$error) {
        this.loadingButton = true
        let params = this.form.saldos
        params.cuenta_id = this.form.fields.id
        this.updateSaldo(params).then(({data}) => {
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
            this.cargarSaldos()
            this.newRowSaldo()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        empresa_id: {required, minValue: minValue(1)}
      },
      saldos: {
        monto: { required, minValue: minValue(1) },
        fecha: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
