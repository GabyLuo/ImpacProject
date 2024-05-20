<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Líneas" />
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
                  :data="lineas"
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
                      <q-td key="codigo" :props="props">{{ props.row.codigo_categoria }} - {{ props.row.codigo }}</q-td>
                      <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td key="categoria" :props="props">{{ props.row.categoria }}</q-td>
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
                  <q-breadcrumbs-el label="Líneas" to="" @click.native="setView('grid')"/>
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
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.categoria_id.$error" error-label="Ingrese una categoría">
                      <q-select v-model="form.fields.categoria_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese un código">
                      <q-input upper-case v-model="form.fields.codigo" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
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
                  <q-breadcrumbs-el label="Líneas" to="" @click.native="setView('grid')"/>
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
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.categoria_id.$error" error-label="Ingrese una categoría">
                      <q-select v-model="form.fields.categoria_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese un código">
                      <q-input upper-case v-model="form.fields.codigo" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                      <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
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
import { required, maxLength, minValue, minLength } from 'vuelidate/lib/validators'

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
          nombre: '',
          categoria_id: 0,
          codigo: ''
        },
        // data: [],
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'categoria', label: 'Categoría', field: 'categoria', sortable: true, type: 'string', align: 'left' },
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
      lineas: 'inv/lineas/lineas'
    }),
    tiposOptions () {
      let tipos = this.$store.getters['inv/tipos_articulos/selectOptions']
      tipos.push({label: '---Seleccione---', value: 0})
      tipos.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return tipos
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getLineas: 'inv/lineas/refresh',
      saveLineas: 'inv/lineas/save',
      updateLineas: 'inv/lineas/update',
      deleteLineas: 'inv/lineas/delete',
      getTiposArticulos: 'inv/tipos_articulos/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getLineas()
      await this.getTiposArticulos()
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
        this.saveLineas(params).then(({data}) => {
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
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateLineas(params).then(({data}) => {
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
      this.form.fields.nombre = row.nombre
      this.form.fields.categoria_id = row.categoria_id
      this.form.fields.codigo = row.codigo
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
      this.deleteLineas(params).then(({data}) => {
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
      this.form.fields.categoria_id = 0
      this.form.fields.codigo = ''
      this.setView('create')
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        categoria_id: { required, minValue: minValue(1) },
        codigo: { required, minLength: minLength(1) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
