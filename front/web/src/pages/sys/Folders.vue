<template>
  <q-page>
      <div  v-if="views.grid">
        <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Repositorio"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 1%;">
        <div class="row q-mt-lg">
          <q-tree
            :nodes="folders"
            node-key="id"
            default-expand-all
            color="black"
          >
            <div slot="header-root" slot-scope="prop" class="row items-center">
              <q-icon :name="prop.node.icon" :color="prop.node.color" size="35px" class="q-mr-sm">
                <q-popover>
                  <q-list link separator class="scroll" style="min-width: 100px">
                    <q-item v-close-overlay @click.native="newFolder(prop.node)">
                      <q-item-main label="Crear nueva carpeta" @click.native="newFolder(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay @click.native="newFolderPrivate(prop.node)" v-if="role === 'Admin' || role === 'Finanzas'">
                      <q-item-main label="Nueva carpeta privada" @click.native="newFolder(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay>
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="repoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRepoFile()" />
                      <q-item-main label="Subir archivo" @click.native="selectedFileRepo(prop.node)"/>
                    </q-item>
                  </q-list>
                </q-popover>
              </q-icon>
              <div>
                {{ prop.node.label }}
              </div>
            </div>

            <div slot="header-generic" slot-scope="prop" class="row items-center">
              <q-icon :name="prop.node.icon" :color="prop.node.color" size="28px" class="q-mr-sm">
                <q-popover v-if="prop.node.tipo==='folder'">
                  <q-list link separator class="scroll" style="min-width: 100px">
                    <q-item v-close-overlay>
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="repoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRepoFile()" />
                      <q-item-main label="Subir archivo" @click.native="selectedFileRepo(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay>
                      <q-item-main label="Crear nueva carpeta" @click.native="newFolder(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay v-if="role === 'Admin' || role === 'Finanzas'">
                      <q-item-main label="Nueva carpeta privada" @click.native="newFolderPrivate(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay>
                      <q-item-main label="Renombrar carpeta" @click.native="updateFolder(prop.node)"/>
                    </q-item>
                    <q-item v-close-overlay>
                      <q-item-main label="Eliminar carpeta" @click.native="eliminarCarpeta(prop.node)"/>
                    </q-item>
                  </q-list>
                </q-popover>
                <q-popover v-if="prop.node.tipo==='file'">
                  <q-list link separator class="scroll" style="min-width: 100px">
                    <q-item v-close-overlay>
                      <q-item-main label="Descargar archivo"  @click.native="descargarArchivo(prop.node.id_file, prop.node.documento_id, prop.node.doc_type)"/>
                    </q-item>
                    <q-item v-close-overlay>
                      <q-item-main label="Eliminar archivo" @click.native="eliminarArchivo(prop.node)"/>
                    </q-item>
                  </q-list>
                </q-popover>
              </q-icon>
              <div>{{ prop.node.label }}
              </div>
            </div>
          </q-tree>
        </div>
      </div>
    </div>
  </div>

        <q-modal no-backdrop-dismiss v-if="form.nuevo_folder" style="background-color: rgba(0,0,0,0.7);" v-model="form.nuevo_folder" :content-css="{width: '30vw', height: '200px'}">
          <q-modal-layout>
            <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
              <div class="col-sm-10">
                <q-toolbar-title>
                  Nueva Carpeta
                </q-toolbar-title>
              </div>
              <div class="col-sm-2 pull-right">
                <q-btn
                  flat
                  round
                  dense
                  color="white"
                  @click="form.nuevo_folder = false"
                  icon="fas fa-times-circle"
                />
              </div>
            </q-toolbar>
          </q-modal-layout>
          <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
            <div class="col-sm-12">
              <q-field icon="folder" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre de la carpeta">
                <q-input v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
              </q-field>
              </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-4 offset-sm-8 pull-right">
              <q-btn @click="save()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Crear</q-btn>
            </div>
          </div>
        </q-modal>

        <q-modal no-backdrop-dismiss v-if="form.modificar_folder" style="background-color: rgba(0,0,0,0.7);" v-model="form.modificar_folder" :content-css="{width: '30vw', height: '200px'}">
          <q-modal-layout>
            <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
              <div class="col-sm-10">
                <q-toolbar-title>
                  Renombrar Carpeta
                </q-toolbar-title>
              </div>
              <div class="col-sm-2 pull-right">
                <q-btn
                  flat
                  round
                  dense
                  color="white"
                  @click="form.modificar_folder = false"
                  icon="fas fa-times-circle"
                />
              </div>
            </q-toolbar>
          </q-modal-layout>
          <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
            <div class="col-sm-12">
              <q-field icon="folder" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre de la carpeta">
                <q-input v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
              </q-field>
              </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-4 offset-sm-8 pull-right">
              <q-btn @click="update()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Actualizar</q-btn>
            </div>
          </div>
        </q-modal>
      </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength } from 'vuelidate/lib/validators'
import axios from 'axios'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      role: '',
      form: {
        fields: {
          id: 0,
          nombre: '',
          nivel: 0,
          padre: 0,
          ruta: '',
          privado: false
        },
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        nuevo_folder: false,
        modificar_folder: false
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
      folders: 'sys/folders/folders'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getCarpetas: 'sys/folders/refresh',
      saveCarpeta: 'sys/folders/save',
      updateCarpeta: 'sys/folders/update',
      deleteCarpeta: 'sys/folders/delete',
      deleteArchivo: 'sys/folders/deleteFile',
      getUser: 'user/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.isAdmin()
      await this.getCarpetas()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async isAdmin () {
      let respuesta = []
      await this.getUser().then(({data}) => {
        respuesta.push(data.user.id)
        respuesta.push(data.role[0])
        respuesta.push(data.user.cliente_id)
        this.role = data.role[0]
        console.log(data.user.account_id)
      }).catch(error => {
        console.log(error)
      })
      return respuesta
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveCarpeta(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            //
            this.loadAll()
            this.form.nuevo_folder = false
            this.setView('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Escriba el nombre de la carpeta.')
      }
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateCarpeta(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            //
            this.loadAll()
            this.form.modificar_folder = false
            this.setView('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este tipo de gasto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    newFolder (prop) {
      this.$v.form.$reset()
      this.form.fields.id = prop.id
      this.form.fields.nivel = prop.nivel
      this.form.fields.nombre = ''
      this.form.fields.padre = prop.padre
      this.form.fields.ruta = prop.ruta
      this.form.fields.privado = false
      this.form.nuevo_folder = true
    }, // aqui comenzará el manejo de archivos, subida, descarga
    newFolderPrivate (prop) {
      this.$v.form.$reset()
      this.form.fields.id = prop.id
      this.form.fields.nivel = prop.nivel
      this.form.fields.nombre = ''
      this.form.fields.padre = prop.padre
      this.form.fields.ruta = prop.ruta
      this.form.fields.privado = true
      this.form.nuevo_folder = true
    },
    updateFolder (prop) {
      this.$v.form.$reset()
      this.form.fields.id = prop.id
      this.form.fields.nivel = prop.nivel
      this.form.fields.nombre = prop.nombre
      this.form.fields.padre = prop.padre
      this.form.modificar_folder = true
    },
    selectedFileRepo (node) {
      this.$refs.repoInput.value = ''
      this.registro_repo = node
      this.$refs.repoInput.click()
    },
    uploadRepoFile () {
      try {
        let file = this.$refs.repoInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('folder_id', this.registro_repo.id)
        formData.append('ruta', this.registro_repo.ruta)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('carpetas/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                // this.$showMessage('Exito', 'Se ha cargado el archivo')
                this.$q.notify({
                // only required parameter is the message:
                  message: 'Se ha cargado el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                //
                this.loadAll()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml .xlsx')
        }
      } catch (error) {
        console.log(error)
      }
    },
    descargarArchivo (idfile, name, ext) {
      console.log(name)
      let id = name
      let uri = process.env.API + `carpetas/getFile/${idfile}/${id}/${ext}`
      window.open(uri, '_blank')
    },
    eliminarArchivo (node) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar el archivo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteF(node)
      }).catch(() => {})
    },
    deleteF (node) {
      this.deleteArchivo({id: node.id_file}).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
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
          //
          this.loadAll()
          this.setView('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    eliminarCarpeta (node) {
      if (node.has_children === 'true') {
        this.$q.dialog({
          title: 'Confirmar',
          message: 'La carpeta no está vacía, si continúa se eliminará todo su contenido',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.deleteC(node)
        }).catch(() => {})
      } else {
        this.deleteC(node)
      }
    },
    deleteC (node) {
      this.deleteCarpeta({id: node.id}).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
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
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
