<template>
  <q-page>
    <div class="layout-padding">
      <div class="row" v-if="views.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="attach_money" disable class="btn_categoria" label="GASTOS PENDIENTES" />
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter" />
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="gastos"
                :columns="form.columns"
                :selected.sync="form.selected"
                :filter="form.filter"
                color="positive"
                title=""
                :pagination.sync="form.pagination"
                :loading="form.loading"
                :rows-per-page-options="form.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                    <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
                    <q-td key="actividad_principal" :props="props">{{ props.row.actividad_principal }}</q-td>
                    <q-td key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}</q-td>
                    <q-td key="fecha_solicitado" :props="props">{{ props.row.fecha_solicitado }}</q-td>
                    <q-td key="monto" :props="props">{{ props.row.monto }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="search">
                        <q-tooltip>Ver detalles</q-tooltip>
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

      <!--Editar status gasto-->

      <div class="row" v-if="views.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">AUTORIZAR GASTO - # {{ form.fields.id }}</h5>
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
            <div class="col-sm-6">
              <q-field icon="work" icon-color="dark">
                <q-input upper-case v-model="form.fields.nombre_proyecto" readonly disable inverted color="dark" float-label="Nombre del presupuesto"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="folder" icon-color="dark">
                <q-input upper-case v-model="form.fields.actividad_principal" readonly disable inverted color="dark" float-label="Actividad principal"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-6">
              <q-field icon="folder_open" icon-color="dark">
                <q-input upper-case v-model="form.fields.nombre_actividad" readonly disable inverted color="dark" float-label="Actividad"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="label" icon-color="dark">
                <q-input upper-case v-model="form.fields.descripcion" readonly disable inverted color="dark" float-label="Descripción"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-input upper-case v-model="form.fields.fecha_solicitado" readonly disable inverted color="dark" float-label="Fecha solicitud"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input upper-case v-model="form.fields.monto" readonly disable inverted color="dark" float-label="Monto"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="person" icon-color="dark">
                <q-input upper-case v-model="form.fields.nickname" readonly disable inverted color="dark" float-label="Responsable actividad"/>
              </q-field>
            </div>
            <!-- <div class="col-sm-4">
              <q-field icon="fas fa-id-badge" icon-color="dark">
                <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"/>
              </q-field>
            </div> -->
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark" helper="Presupuesto actividad">
                <money v-model="form.fields.costo" readonly disable v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fields.costo.$error), moneyError: $v.form.fields.costo.$error }"></money>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark" helper="Cantidad disponible">
                <money v-model="form.fields.cantidad_disponible" readonly disable v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fields.cantidad_disponible.$error), moneyError: $v.form.fields.cantidad_disponible.$error }"></money>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="actualizaPresupuesto()" class="btn_guardar" icon-right="done" :loading="loadingButton">Autorizar gasto</q-btn>
            </div>
          </div>
        </div>
      </div>

    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxValue, minValue } from 'vuelidate/lib/validators'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase()) {
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
      money: {
        decimal: '.',
        thousands: ',',
        suffix: ' MXN',
        precision: 2,
        masked: false
      },
      form: {
        fields: {
          id: 0,
          actividad_id: 0,
          descripcion: '',
          monto: 0,
          fecha_ejercido: '',
          status: '',
          fecha_solicitado: '',
          actividad_principal: '',
          responsable_id: 0,
          nickname: '',
          nombre_actividad: '',
          nombre_proyecto: '',
          proveedor_id: 0,
          costo: 0,
          cantidad_disponible: 0
        },
        // data: [],
        columns: [
          {name: 'id', label: 'Solicitud', field: 'id', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre_proyecto', label: 'Nombre del presupuesto', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left'},
          { name: 'actividad_principal', label: 'Actividad principal', field: 'actividad_principal', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_solicitado', label: 'Fecha de solicitud', field: 'fecha_solicitado', sortable: true, type: 'string', align: 'left' },
          { name: 'monto', label: 'Monto', field: 'monto', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        selectStatus: [ { 'label': 'SOLICITDADO', 'value': 'SOLICITADO' }, { 'label': 'APROBADO', 'value': 'APROBADO' } ],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_editar_costo: {
        fields: {
          actividad_id_costo: 0,
          padre_id_costo: 0,
          proyecto_id_costo: 0,
          padre_principal_costo: 0
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
      gastos: 'pmo/gastos/gastos'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getGastos: 'pmo/gastos/getGastosEspeciales',
      actualizarGasto: 'pmo/gastos/update',
      getActividad: 'pmo/carga/getById',
      actualizaCosto: 'pmo/carga/update_costo'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getGastos()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    editRow (row) {
      let gasto = { ...row }
      this.form.fields.id = gasto.id
      this.form.fields.actividad_principal = gasto.actividad_principal
      this.form.fields.nombre_actividad = gasto.nombre_actividad
      this.form.fields.fecha_solicitado = gasto.fecha_solicitado
      this.form.fields.descripcion = gasto.descripcion
      this.form.fields.monto = gasto.monto
      this.form.fields.status = 'APROBADO'
      this.form.fields.actividad_id = gasto.actividad_id
      this.form.fields.fecha_ejercido = gasto.fecha_ejercido
      this.form.fields.nickname = gasto.nickname
      this.form.fields.nombre_proyecto = gasto.nombre_proyecto
      this.form.fields.costo = gasto.costo
      this.form.fields.cantidad_disponible = gasto.cantidad_disponible
      this.form.fields.num_solicitud = gasto.num_solicitud
      this.setView('update')
    },
    update () {
      this.$v.form.$touch()
      if (parseFloat(this.form.fields.monto) < parseFloat(this.form.fields.cantidad_disponible)) {
        this.loadingButton = true
        let params = this.form.fields
        this.actualizarGasto(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$q.dialog({
          title: 'Error',
          message: 'El gasto se autorizó ajustando el presupuesto en la actividad',
          ok: 'Aceptar',
          preventClose: true
        })
      }
    },
    actualizaPresupuesto () {
      this.getActividad({id: this.form.fields.actividad_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_editar_costo.fields.actividad_id_costo = data.actividades[0].id
          this.form_editar_costo.fields.padre_id_costo = data.actividades[0].padre_id
          this.form_editar_costo.fields.proyecto_id_costo = data.actividades[0].proyecto_id
          //
          this.actualizaCosto({id: this.form_editar_costo.fields.actividad_id_costo, costo: this.form.fields.monto}).then(({data}) => {
            if (data.result === 'success') {
              this.actualizaCosto({id: this.form_editar_costo.fields.padre_id_costo, costo: this.form.fields.monto}).then(({data}) => {
                if (data.result === 'success') {
                  this.getActividad({id: this.form_editar_costo.fields.padre_id_costo}).then(({data}) => {
                    if (data.result === 'success') {
                      this.form_editar_costo.fields.padre_principal_costo = data.actividades[0].padre_id
                      this.actualizaCosto({id: this.form_editar_costo.fields.padre_principal_costo, costo: this.form.fields.monto}).then(({data}) => {
                        if (data.result === 'success') {
                          // this.form_editar_costo.fields.padre_principal_costo = data.actividades[0].padre_id
                          console.log(data)
                          // this.update()
                          this.$v.form.$touch()
                          if (parseFloat(this.form.fields.monto) < parseFloat(this.form.fields.cantidad_disponible)) {
                            this.loadingButton = true
                            let params = this.form.fields
                            this.actualizarGasto(params).then(({data}) => {
                              this.loadingButton = false
                              this.$showMessage(data.message.title, data.message.content)
                              if (data.result === 'success') {
                                this.loadAll()
                                this.setView('grid')
                              }
                            }).catch(error => {
                              console.error(error)
                            })
                          } else {
                            this.$q.dialog({
                              title: 'Advertencia',
                              message: 'El gasto se autorizó ajustando el presupuesto en la actividad',
                              ok: 'Aceptar',
                              preventClose: true
                            })
                          }
                        }
                      }).catch(error => { console.error(error) })
                    }
                  }).catch(error => { console.error(error) })
                }
              }).catch(error => { console.error(error) })
            }
          }).catch(error => { console.error(error) })
        }
      }).catch(error => { console.error(error) })
    }
  },
  validations: {
    form: {
      fields: {
        cantidad_disponible: {required, minValue: minValue(0), maxValue: maxValue(1000000000)},
        costo: {required, minValue: minValue(0), maxValue: maxValue(1000000000)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>

</style>
