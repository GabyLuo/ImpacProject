<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Destinos" />
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
                  :data="destinos"
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
                      <q-td key="codigo" :props="props">{{ props.row.code }}</q-td>
                      <q-td key="nombre_destino" :props="props">{{ props.row.nombre_destino }}</q-td>
                      <q-td key="recibe_persona" :props="props">{{ props.row.recibe_persona }}</q-td>
                      <q-td key="domicilio" :props="props">{{ props.row.domicilio }}</q-td>
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

    <!--Crear un municipio-->

      <div v-if="views.create">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Destinos" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el label="Nuevo"/>
                </q-breadcrumbs>
              </div>
            </div>
            <div class="col-sm-6 pull-right">
                <div class="q-pa-sm q-gutter-sm">
               <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
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
                    <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.code.$error" error-label="Ingrese un código">
                      <q-input upper-case v-model="form.fields.code" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.domicilio.$error" error-label="Ingrese una categoría">
                     <q-input upper-case v-model="form.fields.domicilio" type="text" inverted color="dark" float-label="Domicilio" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.nombre_destino.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.nombre_destino" type="text" inverted color="dark" float-label="Nombre de destino" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.recibe_persona.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.recibe_persona" type="text" inverted color="dark" float-label="Persona que recibe " maxlength="100" />
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

      <!-- Modal editar TIPO DE PRESENTACIÓN   -->
      <div v-if="views.update">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Destinos" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el :label="form.fields.codigo"/>
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
                  <div class="col-sm-4">
                    <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.code.$error" error-label="Ingrese un código">
                      <q-input upper-case v-model="form.fields.code" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.domicilio.$error" error-label="Ingrese una categoría">
                     <q-input upper-case v-model="form.fields.domicilio" type="text" inverted color="dark" float-label="Domicilio" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.nombre_destino.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.nombre_destino" type="text" inverted color="dark" float-label="Nombre de destino" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.recibe_persona.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.recibe_persona" type="text" inverted color="dark" float-label="Persona que recibe " maxlength="100" />
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
import { required, maxLength, minLength } from 'vuelidate/lib/validators'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase()) {
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
          nombre_destino: '',
          recibe_persona: '',
          domicilio: '',
          code: ''
        },
        // data: [],
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_destino', label: 'Destino', field: 'Destino', sortable: true, type: 'string', align: 'left' },
          { name: 'recibe_persona', label: 'Persona que recibe', field: 'categoria', sortable: true, type: 'string', align: 'left' },
          { name: 'domicilio', label: 'Domicilio', field: 'domicilio', sortable: true, type: 'string', align: 'left' },
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
    ...mapGetters({
      destinos: 'crm/destinos/destinos'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getDestinos: 'crm/destinos/refresh',
      saveDestinos: 'crm/destinos/save',
      updateDestinos: 'crm/destinos/update',
      deleteDestinos: 'crm/destinos/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getDestinos()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    save () {
      // this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveDestinos(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
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
      // this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateDestinos(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
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
      this.form.fields.nombre_destino = row.nombre_destino
      this.form.fields.domicilio = row.domicilio
      this.form.fields.recibe_persona = row.recibe_persona
      this.form.fields.code = row.code
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar la línea?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteDestinos(params).then(({data}) => {
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
      this.form.fields.code = ''
      this.form.fields.recibe_persona = ''
      this.form.fields.domicilio = ''
      this.form.fields.nombre_destino = ''
      this.setView('create')
    }
  },
  validations: {
    form: {
      fields: {
        nombre_destino: { required, maxLength: maxLength(100) },
        recibe_persona: { required, maxLength: maxLength(100) },
        domicilio: { required, maxLength: maxLength(100) },
        code: { required, minLength: minLength(1) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
