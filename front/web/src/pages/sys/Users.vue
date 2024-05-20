<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Usuarios" />
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
                  :data="users"
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
                      <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                      <q-td key="email" :props="props">{{ props.row.email }}</q-td>
                      <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                      <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="updateStatusUser(props.row)" color="teal" icon="done" v-if="props.row.status === 'INACTIVO'">
                        <q-tooltip>Activar usuario</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="updateStatusUser(props.row)" color="red-6" icon="highlight_off" v-if="props.row.status === 'ACTIVO'">
                        <q-tooltip>Desactivar usuario</q-tooltip>
                      </q-btn>
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit" v-if="parseInt(props.row.id) !== 1">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn v-else small flat color="faded" icon="edit" disabled></q-btn>
                        <q-btn v-if="parseInt(props.row.id) !== 1" small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                        <q-btn v-else small flat color="faded" icon="delete" disabled></q-btn>
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
                <q-breadcrumbs-el label="Usuarios" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo usuario"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left"/>
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                  <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nickname.$error" error-label="Por favor ingrese un nombre de usuario">
                  <q-input upper-case  v-model="form.fields.nickname" type="text" inverted color="dark" float-label="Nombre de usuario" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-unlock" icon-color="dark" :error="$v.form.fields.password.$error" error-label="Por favor ingrese una contraseña">
                  <q-input v-model="form.fields.password" type="password" inverted color="dark" float-label="Contraseña" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.role_id.$error" error-label="Elija un rol para este usuario">
                  <q-select v-model="form.fields.role_id" inverted color="dark" float-label="Roles" :options="selectRolesOptions"></q-select>
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
      <!-- Modal editar usuario -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Usuarios" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.nickname"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
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
                <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                  <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nickname.$error" error-label="Por favor ingrese un nombre de usuario">
                  <q-input upper-case v-model="form.fields.nickname" type="text" inverted color="dark" float-label="Nombre de usuario" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3" v-if="role === 'Planeación' || role.toUpperCase() === 'root'.toUpperCase()">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.password.$error" error-label="Por favor ingrese una contraseña">
                  <q-input v-model="form.fields.password" type="password" inverted color="dark" float-label="Contraseña" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3" v-else>
                <q-field icon="fas fa-unlock" icon-color="dark" :error="$v.form.fields.password.$error" error-label="Por favor ingrese una contraseña">
                  <q-input v-model="form.fields.password" readonly disable type="password" inverted color="dark" float-label="Contraseña" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.role_id" inverted color="dark" float-label="Roles" :options="selectRolesOptions"></q-select>
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
import { required, maxLength, email, minValue } from 'vuelidate/lib/validators'

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
        if (propiedades[i].toUpperCase() === 'root'.toUpperCase() || propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i] === 'Planeación' || propiedades[i].toUpperCase() === 'Finanzas/Comisiones'.toUpperCase()) {
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
          status: ''
        },
        // data: [],
        columns: [
          { name: 'id', label: '#', field: 'id', sortable: true, type: 'string', align: 'left' },
          { name: 'email', label: 'e-mail', field: 'email', sortable: true, type: 'string', align: 'left' },
          { name: 'nickname', label: 'Usuario', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Rol', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
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
  computed: {
    ...mapGetters({
      users: 'sys/users/users',
      selectRolesOptions: 'sys/roles/selectOtherOptions'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getUsers: 'sys/users/refresh',
      saveUser: 'sys/users/save',
      updateUser: 'sys/users/update',
      updateStatus: 'sys/users/updateStatus',
      deleteUser: 'sys/users/delete',
      getRoles: 'sys/roles/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.isAdmin()
      await this.getRoles()
      await this.getUsers()
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
    eliminarEspacios () {
      this.form.fields.email = this.form.fields.email.trim()
      this.form.fields.nickname = this.form.fields.nickname.trim()
      this.form.fields.password = this.form.fields.password.trim()
    },
    save () {
      this.eliminarEspacios()
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea crear este usuario?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          this.form.fields.roles = []
          this.form.fields.roles = [this.form.fields.role_id]
          this.form.fields.nickname = this.form.fields.nickname.toUpperCase()
          let params = this.form.fields
          this.saveUser(params).then(({data}) => {
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
        this.$showMessage('Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.eliminarEspacios()
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea actualizar este usuario?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          this.form.fields.roles = []
          this.form.fields.roles = [this.form.fields.role_id]
          this.form.fields.nickname = this.form.fields.nickname.toUpperCase()
          let params = this.form.fields
          this.updateUser(params).then(({data}) => {
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
    updateStatusUser (row) {
      if (row.status === 'ACTIVO') {
        this.form.fields.status = 'INACTIVO'
      } else {
        this.form.fields.status = 'ACTIVO'
      }
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea actualizar este usuario?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        this.updateStatus({id: row.id, status: this.form.fields.status}).then(({data}) => {
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
    },
    editRow (row) {
      let user = { ...row }
      this.form.fields.id = row.id
      this.form.fields.email = user.email
      this.form.fields.nickname = user.nickname
      this.form.fields.password = '***'
      this.form.fields.role_id = user.roles
      this.form.fields.name = user.name
      this.form.fields.status = user.status
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este usuario?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete([id])
      }).catch(() => {})
    },
    delete (ids = []) {
      let params = {ids: ids}
      this.deleteUser(params).then(({data}) => {
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
      this.form.fields.nickname = ''
      this.form.fields.email = ''
      this.form.fields.password = ''
      this.form.fields.roles = []
      this.form.fields.role_id = 0
      this.form.fields.name = ''
      this.setView('create')
    }
  },
  validations: {
    form: {
      fields: {
        nickname: { required, maxLength: maxLength(50) },
        email: { required, maxLength: maxLength(100), email },
        password: { required, maxLength: maxLength(100) },
        role_id: {required, minValue: minValue(1)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
