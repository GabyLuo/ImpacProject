<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Logs" />
              </q-breadcrumbs>
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
                  :data="info_logs"
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
                      <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                      <q-td key="log" :props="props">{{ props.row.log }}</q-td>
                      <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                      <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="search">
                          <q-tooltip>Ver reporte</q-tooltip>
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
import { required } from 'vuelidate/lib/validators'

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
          name: ''
        },
        // data: [],
        columns: [
          {name: 'nickname', label: 'Usuario', field: 'nickname', sortable: true, type: 'string', align: 'left'},
          {name: 'log', label: 'Acción', field: 'log', sortable: true, type: 'string', align: 'left'},
          {name: 'name', label: 'Nivel log', field: 'name', sortable: true, type: 'string', align: 'left'},
          {name: 'fecha', label: 'Fecha', field: 'fecha', sortable: true, type: 'string', align: 'left'}
        ],
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
      info_logs: 'sys/info_logs/info_logs'
    })
  },
  created () {
    this.loadAll()
    // this.cargarGastos()
  },
  methods: {
    ...mapActions({
      getLogs: 'sys/info_logs/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getLogs()
      this.form.loading = false
    }
  },
  validations: {
    form: {
      fields: {
        name: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>

</style>
