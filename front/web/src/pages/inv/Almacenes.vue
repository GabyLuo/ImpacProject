<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Almacenes" />
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
                  :data="almacenes"
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
                      <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                      <q-td key="clave" :props="props">{{ props.row.clave }}</q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
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
                <q-breadcrumbs-el label="Almacenes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo almacén"/>
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
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.clave.$error" error-label="Ingrese una clave">
                  <q-input upper-case v-model="form.fields.clave" type="text" inverted color="dark" float-label="Clave" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Seleccione un tipo">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-signs" icon-color="dark" >
                  <q-input upper-case v-model="form.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="room" icon-color="dark">
                  <q-input v-model="form.fields.codigo_postal" type="number" inverted color="dark" float-label="C.P." maxlength="5" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="person" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contacto" type="text" inverted color="dark" float-label="Contacto" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                  <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono"/>
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

      <!-- Modal editar ALMACÉN   -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Almacenes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.clave"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
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
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style"  icon-color="dark" :error="$v.form.fields.clave.$error" error-label="Ingrese una clave">
                  <q-input upper-case v-model="form.fields.clave" type="text" inverted color="dark" float-label="Clave" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Seleccione un tipo">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-signs" icon-color="dark" >
                  <q-input upper-case v-model="form.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="room" icon-color="dark">
                  <q-input v-model="form.fields.codigo_postal" type="number" inverted color="dark" float-label="C.P." maxlength="5" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="person" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contacto" type="text" inverted color="dark" float-label="Contacto" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                  <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono"/>
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase()) {
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
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      tiposOptions: [ { 'label': 'MATERIA PRIMA', 'value': 'MATERIA PRIMA' }, { 'label': 'PRODUCCIÓN', 'value': 'PRODUCCIÓN' }, { 'label': 'PRODUCTO TERMINADO', 'value': 'PRODUCTO TERMINADO' }, { 'label': 'CENTRAL', 'value': 'CENTRAL' }, { 'label': '---Seleccione---', 'value': '0' } ],
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          empresa_id: 0,
          nombre: '',
          estado_id: 0,
          clave: '',
          calle: '',
          codigo_postal: '',
          municipio_id: 0,
          colonia: '',
          contacto: '',
          telefono: '',
          tipo: ''
        },
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Empresa', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'clave', label: 'Clave', field: 'clave', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
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
      almacenes: 'inv/almacenes/almacenes'
    }),
    estadosOptions () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.push({label: '---Seleccione---', value: 0})
      estados.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return estados
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({label: '---Seleccione---', value: 0})
      empresas.sort(function (a, b) {
        if (a.razon_social > b.razon_social) {
          return 1
        }
        if (a.razon_social < b.razon_social) {
          return -1
        }
        return 0
      })
      return empresas
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getAlmacenes: 'inv/almacenes/refresh',
      saveAlmacenes: 'inv/almacenes/save',
      updateAlmacenes: 'inv/almacenes/update',
      deleteAlmacenes: 'inv/almacenes/delete',
      getEstados: 'vnt/estado/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEstados()
      await this.getEmpresas()
      await this.getAlmacenes()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async cargaMunicipios () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    isNumber (evt, input) {
      switch (input) {
        case 'telefono':
          this.form.fields.telefono = this.form.fields.telefono.replace(/[^0-9.]/g, '')
          // this.$v.form.fields.telefono.$touch()
          break
        case 'numero_exterior':
          this.form.fields.numero_exterior = this.form.fields.numero_exterior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_exterior.$touch()
          break
        case 'numero_interior':
          this.form.fields.numero_interior = this.form.fields.numero_interior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_interior.$touch()
          break
        case 'codigo_postal':
          this.form.fields.codigo_postal = this.form.fields.codigo_postal.replace(/[^0-9.]/g, '')
          this.$v.form.fields.codigo_postal.$touch()
          break
        default:
          break
      }
    },
    save () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveAlmacenes(params).then(({data}) => {
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
        this.updateAlmacenes(params).then(({data}) => {
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
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.nombre = row.nombre
      this.form.fields.clave = row.clave
      this.form.fields.calle = row.calle
      this.form.fields.codigo_postal = row.codigo_postal
      this.form.fields.estado_id = row.estado_id
      if (this.form.fields.estado_id > 0) {
        this.cargaMunicipios()
      }
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.colonia = row.colonia
      this.form.fields.contacto = row.contacto
      this.form.fields.telefono = row.telefono
      if (row.tipo === null) {
        this.form.fields.tipo = '0'
      } else {
        this.form.fields.tipo = row.tipo
      }
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este municipio?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteAlmacenes(params).then(({data}) => {
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
      this.form.fields.empresa_id = 0
      this.form.fields.nombre = ''
      this.form.fields.clave = ''
      this.form.fields.calle = ''
      this.form.fields.codigo_postal = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.colonia = ''
      this.form.fields.contacto = ''
      this.form.fields.telefono = ''
      this.form.fields.tipo = '0'
      this.municipiosOptions = []
      this.municipiosOptions.push({label: '---Seleccione---', value: 0})
      this.setView('create')
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        clave: {required},
        telefono: {minLength: minLength(10), maxLength: maxLength(10)},
        codigo_postal: {minLength: minLength(5), maxLength: maxLength(5)},
        tipo: {required, minLength: minLength(2)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
