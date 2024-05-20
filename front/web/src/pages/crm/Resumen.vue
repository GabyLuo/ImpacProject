<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-8">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Resumen prospección" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 q-pr-md text-right">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="cargarResumen()"/>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-item-tile class="text-black">META ANUAL</q-item-tile>
              </div>
              <div class="col-sm-2">
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">${{ this.resumen[0].meta_anual }}</q-item-tile>
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <q-item-tile class="text-black">OPORTUNIDADES REGISTRADAS</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">{{ this.resumen[0].oportunidades_registradas }}</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">${{ this.resumen[0].o_monto_registradas }}</q-item-tile>
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <q-item-tile class="text-black">OPORTUNIDADES EN CIERRE</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">{{ this.resumen[0].oportunidades_cierre }}</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">${{ this.resumen[0].o_monto_cierre }}</q-item-tile>
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <q-item-tile class="text-black">OPORTUNIDADES LOGRADAS</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">{{ this.resumen[0].oportunidades_logradas }}</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">${{ this.resumen[0].o_monto_logradas }}</q-item-tile>
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <q-item-tile class="text-black">OPORTUNIDADES NO LOGRADAS</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">{{ this.resumen[0].oportunidades_no_logradas }}</q-item-tile>
              </div>
              <div class="col-sm-2">
                <q-item-tile class="text-black text-right">${{ this.resumen[0].o_monto_no_logradas }}</q-item-tile>
              </div>
              <div class="col-sm-4">
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="margin-top: 30px;">
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
                      <q-td key="meta_anual" :props="props">${{ currencyFormat(props.row.meta_anual) }}</q-td>
                      <q-td key="o_registradas" :props="props">{{ props.row.o_registradas }}</q-td>
                      <q-td key="o_monto_registradas" :props="props">${{ currencyFormat(props.row.o_monto_registradas) }}</q-td>
                      <q-td key="o_cierre" :props="props">{{ props.row.o_cierre }}</q-td>
                      <q-td key="o_monto_cierre" :props="props">${{ currencyFormat(props.row.o_monto_cierre) }}</q-td>
                      <q-td key="acciones" :props="props">
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
import {VMoney} from 'v-money'
import { mapActions, mapGetters } from 'vuex'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) } ],
      year: '' + (new Date().getFullYear()),
      resumen: [],
      vendedores: [],
      today: new Date(),
      tabPrincipal: false,
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      tiposOptions: [ { label: 'GDP', value: 'GDP' }, { label: 'RETAIL', value: 'RETAIL' } ],
      tipo_prospecto: [ { label: 'NUEVO', value: 'NUEVO' }, { label: 'EXISTENTE', value: 'EXISTENTE' } ],
      ordenOptions: [ { label: 'FEDERAL', value: 'FEDERAL' }, { label: 'ESTATAL', value: 'ESTATAL' }, { label: 'MUNICIPAL', value: 'MUNICIPAL' }, { label: '--Seleccione--', value: 'all' } ],
      enteOptions: [ { label: 'EJECUTIVO', value: 'EJECUTIVO' }, { label: 'DEPENDENCIA', value: 'DEPENDENCIA' }, { label: 'OPD', value: 'OPD' }, { label: '--Seleccione--', value: 'all' } ],
      adjudicacionOptions: [ { label: 'LICITACIÓN', value: 'LICITACIÓN' }, { label: 'ADJUDICACIÓN DIRECTA', value: 'ADJUDICACIÓN DIRECTA' }, { label: 'INVITACIÓN A 3', value: 'INVITACIÓN A 3' }, { label: '--Seleccione--', value: 'all' } ],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      carril_id: 1,
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
          sector_id: 0,
          subsector_id: 0,
          email: '',
          telefono: '',
          nombre_contacto: '',
          prospecto: '',
          prospecto_id: 0,
          giro_comercial: 0,
          valor: 0,
          valor_final: 0,
          ejecutivo_id: 0,
          tipo: 'GDP',
          tipo_prospecto: 'EXISTENTE',
          empresa_id: 0,
          orden: 'all',
          ente: 'all',
          adjudicacion: 'all',
          estado_id: 0,
          municipio_id: 0,
          folio: '',
          procedimiento: '',
          valor_licitacion: 0
        },
        // data: [],
        columns: [
          { name: 'nickname', label: 'Vendedor', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'meta_anual', label: 'Meta anual', field: 'meta_anual', sortable: true, type: 'string', align: 'right' },
          { name: 'o_registradas', label: 'Oportunidades registradas', field: 'o_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_registradas', label: 'Monto regsitradas', field: 'o_monto_registradas', sortable: true, type: 'string', align: 'right' },
          { name: 'o_cierre', label: 'Oportunidades en cierre', field: 'o_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'o_monto_cierre', label: 'Monto cierre', field: 'o_monto_cierre', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Avance', field: 'acciones', sortable: false, type: 'string', align: 'right' }
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
      carrilesOptions: 'crm/crm/selectOptions'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getResumen: 'crm/oportunidades/getResumen'
    }),
    cargarResumen () {
      this.getResumen({year: this.year}).then(({data}) => {
        this.resumen = data.resumen
        this.vendedores = data.vendedores
      }).catch(error => {
        console.error(error)
      })
    },
    async loadAll () {
      this.form.loading = true
      await this.cargarResumen()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
  }
}
</script>

<style scoped>
</style>
