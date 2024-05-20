<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Aliados" />
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
                    :data="aliados"
                    :columns="form.columns"
                    :selected.sync="form.selected"
                    :filter="form.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    class="bg-white"
                    :pagination.sync="form.pagination"
                    :loading="form.loading"
                    :rows-per-page-options="form.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                        <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                        <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                        <q-td key="municipio" :props="props">{{ props.row.municipio }}</q-td>
                        <q-td key="area" :props="props">{{ props.row.area }}</q-td>
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
                  <q-breadcrumbs-el label="Aliados" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el label="Nuevo"/>
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
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                    <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre comercial" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Escriba la razón social">
                    <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un rfc válido">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="grade" icon-color="dark">
                    <q-input upper-case v-model="form.fields.area" type="text" inverted color="dark" float-label="Área" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark" >
                    <q-input upper-case v-model="form.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="100" />
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

      <div v-if="views.update">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Aliados" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el :label="form.fields.nombre"/>
                </q-breadcrumbs>
              </div>
            </div>
            <div class="col-sm-6 pull-right">
              <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
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
                <div class="col-sm-3">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                    <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre comercial" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Escriba la razón social">
                    <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un rfc válido">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="grade" icon-color="dark">
                    <q-input upper-case v-model="form.fields.area" type="text" inverted color="dark" float-label="Área" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark" >
                    <q-input upper-case v-model="form.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="100" />
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
import { required, maxLength, minLength, helpers } from 'vuelidate/lib/validators'
const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          razon_social: '',
          nombre: '',
          estado_id: 0,
          rfc: '',
          area: '',
          municipio_id: 0,
          descripcion: ''
        },
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Razón social', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'area', label: 'Área', field: 'area', sortable: true, type: 'string', align: 'left' },
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
      aliados: 'com/aliados/aliados'
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
      getAliados: 'com/aliados/refresh',
      saveAliados: 'com/aliados/save',
      updateAliados: 'com/aliados/update',
      deleteAliados: 'com/aliados/delete',
      getEstados: 'vnt/estado/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEstados()
      // await this.getEmpresas()
      await this.getAliados()
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
        this.saveAliados(params).then(({data}) => {
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
        this.updateAliados(params).then(({data}) => {
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
      this.form.fields.razon_social = row.razon_social
      this.form.fields.nombre = row.nombre
      this.form.fields.rfc = row.rfc
      this.form.fields.area = row.area
      this.form.fields.estado_id = row.estado_id
      if (this.form.fields.estado_id > 0) {
        this.cargaMunicipios()
      }
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.descripcion = row.descripcion
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
      this.deleteAliados(params).then(({data}) => {
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
      this.form.fields.razon_social = ''
      this.form.fields.nombre = ''
      this.form.fields.rfc = ''
      this.form.fields.area = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.descripcion = ''
      this.municipiosOptions = []
      this.municipiosOptions.push({label: '---Seleccione---', value: 0})
      this.setView('create')
    }
  },
  validations: {
    form: {
      fields: {
        razon_social: { required, maxLength: maxLength(100) },
        nombre: { required, maxLength: maxLength(100) },
        rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc}
      }
    }
  }
}
</script>

<style scoped>
</style>
