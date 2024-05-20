<template>
  <q-page>
  <div class="layout-padding">
    <div class="row" >
      <div class="col-sm-12">
        <div class="row justify-between">
              <div class="col-sm-6">
              <q-btn size="15px" icon="forward" disable class="btn_categoria" label="Autorizaciones" />
            </div>
        </div>
        <div id="item-actividad" :key="idx" v-for="(item, idx) in mis">
        <q-list style="padding: 0px;">
          <q-collapsible :icon-toggle="true" @show="ticketAbierto(item.id)" class="actividades-callapsible">
            <template slot="header">
              <q-item-side v-if="item.tipo_id === 1" icon="mail" color="red-14" />
              <q-item-main :label="item.titulo" />
              <q-item-side right>
                <q-btn-dropdown
                  icon="fas fa-ellipsis-v"
                  inverted
                >
                  <q-list link>
                    <q-item v-close-overlay @click.native="addMenssage(item.id)">
                      <q-item-side icon="fas fa-comment-alt" inverted color="negative" />
                      <q-item-main>
                        <q-item-tile label>Agregar mensaje</q-item-tile>
                      </q-item-main>
                    </q-item>
                    <q-item v-close-overlay @click.native="addArchive(item.id,idx)">
                      <q-item-side icon="fas fa-paperclip" inverted color="negative" />
                      <q-item-main>
                        <input hidden type="file" name="" value="" ref="fileInput" @change="uploadDocFile()" />
                        <q-item-tile label>Adjuntar archivo</q-item-tile>
                      </q-item-main>
                    </q-item>
                  </q-list>
                </q-btn-dropdown>
              </q-item-side>
            </template>
            <div style="padding-left: 0px;">
              <q-tabs class="tkt-tab-show" animated swipeable position="bottom" align="justify">
                <q-tab color="negative" default name="info" slot="title" label="Información" />
                <q-tab color="negative" name="doc" slot="title" label="Documentos" />
                <q-tab color="negative" name="mnj" slot="title" label="Mensajes" />
                <q-tab color="negative" name="logs" slot="title" label="Historial" />

                <q-tab-pane name="info">
                  <div class="row q-mt-lg">
                    <div class="col-sm-2">
                      <p>
                        Contacto:
                      </p>
                    </div>
                    <div class="col-sm-5">
                      <p>
                        {{item.contacto}}
                      </p>
                    </div>
                    <div class="col-sm-5">
                      <p>
                        Fecha limite: {{item.fecha_limite.slice(0, -3)}}
                      </p>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-2">
                      <p>
                        Descripción:
                      </p>
                    </div>
                    <div class="col-sm-10">
                      <p>
                        {{item.descripcion}}
                      </p>
                    </div>
                  </div>
                </q-tab-pane>
                <q-tab-pane name="doc">
                  <q-item v-bind:key="indexDocs" v-for="(doc,indexDocs) in item.docs">
                    <q-item-side icon="assignment" inverted color="grey-6" />
                    <q-item-main>
                      <q-item-tile label>{{doc.filename}}.{{doc.doc_type}}</q-item-tile>
                      <q-item-tile sublabel>
                        <span style="padding-left: 7px;">
                          {{doc.created.slice(0, -3)}}
                        </span>
                      </q-item-tile>
                    </q-item-main>
                    <q-item-side @click.native="downloadDocument()" right icon="fas fa-cloud-download-alt" />
                  </q-item>
                </q-tab-pane>
                <q-tab-pane name="mnj">
                  <q-item multiline v-bind:key="indexMsj" v-for="(msj,indexMsj) in item.mensajes">
                    <q-item-main>
                      <q-item-tile label>{{msj.nickname}}</q-item-tile>
                      <q-item-tile sublabel>
                        <span style="padding-left: 7px;">
                          {{msj.comentario}}
                        </span>
                      </q-item-tile>
                    </q-item-main>
                    <q-item-side right>
                      <q-item-tile stamp>{{msj.fecha_publicacion.slice(0, -3)}}</q-item-tile>
                    </q-item-side>
                  </q-item>
                </q-tab-pane>
                <q-tab-pane name="logs">
                  <q-item style="border-bottom: 1px solid red" multiline v-bind:key="indice" v-for="(log,indice) in item.logs">
                    <q-item-main>
                      <q-item-tile label>
                        {{log.descripcion}}
                      </q-item-tile>
                    </q-item-main>
                    <q-item-side right>
                      <q-item-tile stamp>{{log.creado.slice(0, -3)}}</q-item-tile>
                    </q-item-side>
                  </q-item>
                </q-tab-pane>
              </q-tabs>
            </div>
          </q-collapsible>
        </q-list>
        <br>
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
        if (propiedades[i].toUpperCase() === 'root'.toUpperCase() || propiedades[i].toUpperCase() === 'admin'.toUpperCase()) {
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
      getUsers: 'sys/users/refresh',
      saveUser: 'sys/users/save',
      updateUser: 'sys/users/update',
      deleteUser: 'sys/users/delete',
      getRoles: 'sys/roles/refresh'
    }),
    async loadAll () {
      this.form.loading = true

      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
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
    editRow (row) {
      let user = { ...row }
      this.form.fields.id = row.id
      this.form.fields.email = user.email
      this.form.fields.nickname = user.nickname
      this.form.fields.password = '***'
      this.form.fields.role_id = user.roles[0]
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
