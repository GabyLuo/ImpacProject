<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Roles" />
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
                  :data="roles"
                  :columns="form.columns"
                  :selected.sync="form.selected"
                  :filter="form.filter"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form.pagination"
                  :loading="form.loading"
                  :rows-per-page-options="form.rowsOptions">
                  <template slot="top-selection" slot-scope="props">
                    <div class="col" />
                    <q-btn color="negative" icon="delete" @click="deleteRows(props)">
                      <i>Eliminar</i>
                    </q-btn>
                  </template>
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                      <!-- <q-td key="parent" :props="props">{{ props.row.parent }}</q-td> -->
                      <q-td key="name" :props="props">{{ props.row.name }}</q-td>
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
      <!--Crear rol-->
    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Menús" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo rol"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.name.$error" error-label="Por favor ingrese un nombre">
                  <q-input v-model="form.fields.name" inverted color="dark" stack-label="Nombre" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-8">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.menuItems" multiple inverted color="dark" float-label="Acceso" :options="selectSubMenusOptions"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar rol -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Roles" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.name"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.name.$error" error-label="Por favor ingrese un nombre">
                  <q-input v-model="form.fields.name" inverted color="dark" stack-label="Nombre" maxlength="50" disable/>
                </q-field>
              </div>
              <div class="col-sm-8">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.menuItems" multiple inverted color="dark" float-label="Accesos" :options="selectSubMenusOptions"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
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
import { required, maxLength } from 'vuelidate/lib/validators'

export default {
  beforeRouteEnter (to, from, next) {
    next(vm => {
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'root'.toUpperCase() || propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase()) {
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
      form: {
        fields: {
          id: 0,
          parent_id: 0,
          name: '',
          menuItems: []
        },
        columns: [
          { name: 'id', label: '#', field: 'id', sortable: true, type: 'string', align: 'left' },
          // { name: 'parent', label: 'Padre', field: 'parent', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Rol', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      }
    }
  },
  computed: {
    ...mapGetters({
      roles: 'sys/roles/roles',
      selectRolesOptions: 'sys/roles/selectOptions',
      selectSubMenusOptions: 'sys/menuItems/selectOptions'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getRoles: 'sys/roles/refresh',
      getSubmenuItems: 'sys/menuItems/refresh',
      saveRole: 'sys/roles/save',
      updateRole: 'sys/roles/update',
      deleteRole: 'sys/roles/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getRoles()
      await this.getSubmenuItems()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    save () {
      this.form.fields.name = this.form.fields.name.trim()
      if (this.form.fields.menuItems.length > 0) {
        this.$v.form.fields.$touch()
        if (!this.$v.form.fields.$error) {
          this.$q.dialog({
            title: 'Confirmar',
            message: '¿Desea crear este rol?',
            ok: 'Aceptar',
            cancel: 'Cancelar'
          }).then(() => {
            this.loadingButton = true
            let params = this.form.fields
            this.saveRole(params).then(({data}) => {
              this.loadingButton = false
              this.$showMessage(data.message.title, data.message.content)
              if (data.result === 'success') {
                this.setView('grid')
                this.loadAll()
              }
            }).catch(error => {
              console.error(error)
            })
          }).catch(() => {})
        } else {
          this.$showMessage('Advertencia!', 'Por favor revise los campos.')
        }
      } else {
        this.$q.dialog({
          title: 'Error',
          message: 'Aún no ha agregado accesos a este rol'
        })
      }
    },
    update () {
      this.form.fields.name = this.form.fields.name.trim()
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea actualizar este rol?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          let params = this.form.fields
          this.updateRole(params).then(({data}) => {
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
      this.form.fields = { ...row }
      this.setView('update')
    },
    deleteRows () {
      let ids = []
      this.form.selected.forEach(row => {
        ids.push(row.id)
      })
      this.$q.dialog({
        title: 'Confirmar',
        message: (this.form.selected.length > 1 ? '¿Desea borrar estos roles?' : '¿Desea borrar este rol?'),
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(ids)
      })
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este rol?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete([id])
      }).catch(() => {})
    },
    delete (ids = []) {
      let params = {ids: ids}
      this.deleteRole(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.setView('grid')
          this.loadAll()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.parent_id = 0
      this.form.fields.name = ''
      this.form.fields.menuItems = []
      this.setView('create')
    },
    exportCsv () {
      window.open(process.env.API + `roles/export`, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        name: { required, maxLength: maxLength(50) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
