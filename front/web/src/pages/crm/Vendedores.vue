<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Vendedores" />
              </q-breadcrumbs>
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
                  :data="vendedores"
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
                      <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <q-td key="meta_anual" :props="props">${{ currencyFormat(props.row.meta_anual) }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
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

    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Vendedores" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.id"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nickname.$error" error-label="Por favor ingrese un nombre de usuario">
                  <q-input upper-case v-model="form.fields.nickname" type="text" inverted color="dark" float-label="Nombre de usuario" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Por favor ingrese un monto">
                  <q-input v-model.lazy="form.fields.monto" v-money="money" inverted color="dark" float-label="Meta anual" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-users" icon-color="dark">
                  <q-select v-model="form.fields.gerente_id" inverted color="dark" float-label="Gerente de ventas" :options="gerentesOptions"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions"></q-select>
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
import {VMoney} from 'v-money'
import { required, maxLength } from 'vuelidate/lib/validators'

export default {
  components: {},
  beforeRouteEnter (to, from, next) {
    next(vm => {
      // if (vm.$store.getters['user/rolesIds'].includes(vm.$roles.SUPER_ADMIN) || vm.$store.getters['user/privileges'].include[vm.$privileges.VER_USUARIO]) {
      //   next()
      // } else {
      //   next(from.path === '/' ? '/dashboard' : from.path)
      // }
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'root'.toUpperCase() || propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      tiposOptions: [ { label: 'GDP', value: 'GDP' }, { label: 'COMERCIAL', value: 'COMERCIAL' }, { label: 'VENDEDORES', value: 'VENDEDORES' } ],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          email: '',
          nickname: '',
          password: '',
          roles: [],
          role_id: 0,
          name: '',
          status: '',
          monto: 0,
          gerente_id: 0,
          tipo: ''
        },
        // data: [],
        columns: [
          { name: 'nickname', label: 'Usuario', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'meta_anual', label: 'Meta anual', field: 'meta_anual', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        pagination: { rowsPerPage: 50 },
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
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
      vendedores: 'crm/vendedores/vendedores'
    }),
    gerentesOptions () {
      let gerentes = this.$store.getters['crm/vendedores/selectOptionsGerentes']
      gerentes.splice(0, 0, {label: '---Seleccione---', value: 0})
      return gerentes
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getVendedores: 'crm/vendedores/refresh',
      updateVendedor: 'crm/vendedores/update',
      getGerentes: 'crm/vendedores/refreshGerentes'
    }),
    evaluaMonto (campo) {
      this.cadena2 = '' + campo
      this.cadena = this.cadena2.split(',')
      this.monto_a_pagar = ''
      for (var i = 0; i < this.cadena.length; i++) {
        this.monto_a_pagar = this.monto_a_pagar + this.cadena[i]
      }
      return parseFloat(this.monto_a_pagar)
    },
    async loadAll () {
      this.form.loading = true
      await this.isAdmin()
      await this.getVendedores()
      await this.getGerentes()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async isAdmin () {
      await this.getUser().then(({data}) => {
        this.role = data.role[0]
      }).catch(error => {
        console.log(error)
      })
    },
    update () {
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea actualizar este vendedor?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          let params = this.form.fields
          params.meta_anual = this.evaluaMonto(this.form.fields.monto)
          this.updateVendedor(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.setView('grid')
              this.loadAll()
            }
          }).catch(error => {
            console.error(error)
          })
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      let user = { ...row }
      this.form.fields.id = row.id
      this.form.fields.nickname = user.nickname
      this.form.fields.monto = user.meta_anual
      this.form.fields.gerente_id = user.gerente_id
      this.form.fields.tipo = user.tipo
      this.setView('update')
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
  },
  validations: {
    form: {
      fields: {
        nickname: { required, maxLength: maxLength(50) },
        monto: { required }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
