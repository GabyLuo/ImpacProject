<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Menús" />
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
                  :data="menus"
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
                      <q-td key="label" :props="props">{{ props.row.label }}</q-td>
                      <q-td key="ord" :props="props">{{ props.row.ord }}</q-td>
                      <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat color="blue-6" icon="edit" @click="editRow(props.row)">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="negative" icon="delete" @click="deleteSingleRow(props.row.id)">
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

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Menús" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.label.$error" error-label="Ingrese un nombre">
                  <q-input v-model="form.fields.label" inverted color="dark" stack-label="Nombre" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fa-sort-numeric-up" icon-color="dark">
                  <q-input v-model="form.fields.ord" inverted color="dark" stack-label="Orden" maxlength="2" />
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fa-info" icon-color="dark">
                  <q-input v-model="form.fields.descripcion" inverted color="dark" stack-label="Descripción" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn class="btn_guardar" icon-right="save" :loading="loadingButton" @click="save()">Guardar</q-btn>
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
                <q-breadcrumbs-el label="Menús" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.label"/>
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
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.label.$error" error-label="Ingrese un nombre">
                  <q-input v-model="form.fields.label" inverted color="dark" stack-label="Nombre" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fa-sort-numeric-up" icon-color="dark">
                  <q-input v-model="form.fields.ord" inverted color="dark" stack-label="Orden" maxlength="2" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fa-info" icon-color="dark">
                  <q-input v-model="form.fields.descripcion" inverted color="dark" stack-label="Descripción" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-btn style="margin-left:10px;" class="btn_guardar" icon-right="save" :loading="loadingButton" @click="update()">Guardar</q-btn>
              </div>
            </div>

            <div class="row q-col-gutter-sm" style="margin-top: 20px; margin-bottom: 20px;">
              <div class="col-sm-12">
                SUBMENÚS
              </div>
            </div>

            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.menuItem.fields.label.$error" error-label="Ingrese un nombre">
                  <q-input v-model="menuItem.fields.label" inverted color="dark" stack-label="Nombre" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fa-sitemap" icon-color="dark" :error="$v.menuItem.fields.route.$error" error-label="Ingrese una ruta eg: '/usuarios', '/usuarios/editar'">
                  <q-input v-model="menuItem.fields.route" inverted color="dark" stack-label="Ruta" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="content_paste" icon-color="dark" error-label="Ingrese un nombre">
                  <q-input v-model="menuItem.fields.icon" inverted color="dark" stack-label="Ícono" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fa-sort-numeric-down" icon-color="dark" error-label="Ingrese el orden del sub-menú">
                  <q-input v-model="menuItem.fields.ord" inverted color="dark" stack-label="Orden" maxlength="2" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-btn v-if="menuItem.fields.id === 0" style="margin-left:10px;" class="btn_guardar" icon-right="save" @click="saveMenuItem()">Crear</q-btn>
                <q-btn v-else style="margin-left:10px;" class="btn_actualizar" icon-right="save" @click="updateMenuItem()">Actualizar</q-btn>
              </div>
            </div>

            <div class="row q-col-gutter-xs">
              <div class="col q-pa-md">
                  <div class="col-sm-12" style="padding-top: 20px; padding-bottom: 10px;">
                    <q-search color="primary" v-model="menuItem.filter"/>
                  </div>
                  <q-table id="sticky-table-newstyle"
                    :data="menuItem.data"
                    :columns="menuItem.columns"
                    :filter="menuItem.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    :pagination.sync="menuItem.pagination"
                    :loading="menuItem.loading"
                    :rows-per-page-options="menuItem.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                        <q-td key="label" :props="props">{{ props.row.label }}</q-td>
                        <q-td key="route" :props="props">{{ props.row.route }}</q-td>
                        <q-td key="icon" :props="props"><q-icon :name="props.row.icon" size="14px" /></q-td>
                        <q-td key="ord" :props="props">{{ props.row.ord }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat color="blue-6" icon="edit" @click="editRowMenuItem(props.row)">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat color="negative" icon="delete" @click="deleteSingleRowItem(props.row.id)">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <q-inner-loading :visible="menuItem.loading">
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
import { required, maxLength } from 'vuelidate/lib/validators'

export default {
  beforeRouteEnter (to, from, next) {
    next(vm => {
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'ROOT'.toUpperCase()) {
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
      edit_menu: 'Modificar menú',
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          label: '',
          ord: 0,
          descripcion: ''
        },
        columns: [
          { name: 'id', label: '#', field: 'id', sortable: true, type: 'string', align: 'left' },
          { name: 'label', label: 'Padre', field: 'label', sortable: true, type: 'string', align: 'left' },
          { name: 'ord', label: 'Orden', field: 'ord', sortable: true, type: 'string', align: 'right' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      menuItem: {
        fields: {
          id: 0,
          menu_id: '',
          label: '',
          route: '',
          icon: 'content_paste',
          ord: 0
        },
        columns: [
          { name: 'id', label: '#', field: 'id', sortable: true, type: 'string', align: 'left' },
          { name: 'label', label: 'Nombre', field: 'label', sortable: true, type: 'string', align: 'left' },
          { name: 'route', label: 'Ruta', field: 'route', sortable: true, type: 'string', align: 'left' },
          { name: 'icon', label: 'Ícono', field: 'icon', sortable: true, type: 'string', align: 'left' },
          { name: 'ord', label: 'Orden', field: 'ord', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
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
      menus: 'sys/menus/menus'
      // selectRolesOptions: 'sys/roles/selectOptions'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getMenus: 'sys/menus/refresh',
      saveMenu: 'sys/menus/save',
      updateMenu: 'sys/menus/update',
      deleteMenu: 'sys/menus/delete',
      getByMenu: 'sys/menuItems/getByMenu',
      saveSysMenuItem: 'sys/menuItems/save',
      updateSysMenuItem: 'sys/menuItems/update',
      deleteSysMenuItem: 'sys/menuItems/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getMenus()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    save () {
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea crear este menú?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          let params = this.form.fields
          this.saveMenu(params).then(({data}) => {
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
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea actualizar este menú?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          let params = this.form.fields
          this.updateMenu(params).then(({data}) => {
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
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      this.form.fields = { ...row }
      this.cleanMenuItemsFields()
      this.getMenuItems()
      this.setView('update')
    },
    deleteRows () {
      let ids = []
      this.form.selected.forEach(row => {
        ids.push(row.id)
      })
      this.$q.dialog({
        title: 'Confirmar',
        message: (this.form.selected.length > 1 ? '¿Desea borrar estos menús?' : '¿Desea borrar este menú?'),
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(ids)
      }).catch(() => {})
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este menú?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete([id])
      }).catch(() => {})
    },
    delete (ids = []) {
      let params = {ids: ids}
      this.deleteMenu(params).then(({data}) => {
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
      this.form.fields.label = ''
      this.form.fields.ord = 0
      this.form.fields.descripcion = ''
      this.setView('create')
    },
    getMenuItems () {
      this.menuItem.loading = true
      this.getByMenu({menu_id: this.form.fields.id}).then(({data}) => {
        this.menuItem.loading = false
        this.menuItem.data = []
        if (data.menuItems.length > 0) {
          this.menuItem.data = data.menuItems
          this.$store.dispatch('user/refresh')
        }
      }).catch(error => {
        this.menuItem.loading = false
        console.error(error)
      })
    },
    saveMenuItem () {
      this.$v.menuItem.fields.$touch()
      if (!this.$v.menuItem.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea crear este sub-menú?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          let params = this.menuItem.fields
          params.menu_id = this.form.fields.id
          this.saveSysMenuItem(params).then(({data}) => {
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.cleanMenuItemsFields()
              this.getMenuItems()
            }
          }).catch(error => {
            console.error(error)
          })
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    updateMenuItem () {
      this.$v.menuItem.fields.$touch()
      if (!this.$v.menuItem.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea actualizar este sub-menú?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          let params = this.menuItem.fields
          this.updateSysMenuItem(params).then(({data}) => {
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.cleanMenuItemsFields()
              this.getMenuItems()
            }
          }).catch(error => {
            console.error(error)
          })
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    editRowMenuItem (row) {
      this.menuItem.fields = { ...row }
    },
    deleteSingleRowItem (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este sub-menú?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {ids: [id]}
        this.deleteSysMenuItem(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cleanMenuItemsFields()
            this.getMenuItems()
          }
        }).catch(error => {
          console.error(error)
        })
      })
    },
    cleanMenuItemsFields () {
      this.$v.menuItem.fields.$reset()
      this.menuItem.fields.id = 0
      this.menuItem.fields.menu_id = 0
      this.menuItem.fields.label = ''
      this.menuItem.fields.route = ''
      this.menuItem.fields.icon = 'content_paste'
      this.menuItem.fields.ord = 0
    },
    exportCsv () {
      window.open(process.env.API + `roles/export`, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        label: { required, maxLength: maxLength(50) }
      }
    },
    menuItem: {
      fields: {
        label: { required, maxLength: maxLength(50) },
        route: { required, maxLength: maxLength(50) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
