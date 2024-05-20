<template>
  <q-modal @hide="close()" style="background-color: rgba(0,0,0,0.7);" v-model="show" :content-css="{width: '60vw', height: '350px'}">
    <q-modal-layout>
      <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
        <div class="col-sm-6">
          <q-toolbar-title v-if="create">
            Nueva tarea
          </q-toolbar-title>
          <q-toolbar-title v-else>
            Editar tarea
          </q-toolbar-title>
        </div>
        <div class="col-sm-6 pull-right">
          <q-btn
            flat
            round
            dense
            color="white"
            @click="close()"
            icon="fas fa-times-circle"
          />
        </div>
      </q-toolbar>
      <div class="row q-mt-sm" style="margin-right: 10px;">
        <div class="row q-col-qutter-xs">
          <div class="col-sm-4">
            <q-field icon="label" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Seleccione un tipo">
              <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="alarm" icon-color="dark" :error="$v.form.fields.fecha_limite.$error" error-label="Ingrese la fecha límite para completar la tarea">
              <q-datetime v-model="form.fields.fecha_limite" type="date" inverted color="dark" float-label="Fecha límite" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.asignado.$error" error-label="Seleccione el usuario al que será asignada la tarea">
              <q-select v-model="form.fields.asignado" inverted color="dark" float-label="Asignado" :options="usersOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="star" icon-color="dark">
              <q-select v-model="form.fields.oportunidad_id" inverted color="dark" float-label="Oportunidad" :options="oportunidadesOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-4" v-if="form.fields.tipo === 'EMAIL'">
            <q-field icon="email" icon-color="dark">
              <q-input v-model="form.fields.correo" type="text" inverted color="dark" float-label="Correo"/>
            </q-field>
          </div>
          <div class="col-sm-4" v-if="form.fields.tipo === 'LLAMADA'">
            <q-field icon="call" icon-color="dark">
              <q-input v-model="form.fields.telefono" type="text" inverted color="dark" float-label="Teléfono"/>
            </q-field>
          </div>
          <div class="col-sm-4" v-if="form.fields.tipo === 'CITA'">
            <q-field icon="fas fa-calendar" icon-color="dark">
              <q-datetime v-model="form.fields.fecha_cita" type="date" inverted color="dark" float-label="Fecha cita" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-4" v-if="form.fields.tipo === 'CITA'">
            <q-field icon="store" icon-color="dark">
              <q-input v-model="form.fields.lugar" type="text" inverted color="dark" float-label="Lugar"/>
            </q-field>
          </div>
          <div class="col-sm-12">
            <q-field icon="label" icon-color="dark">
              <q-input v-model="form.fields.descripcion" type="textarea" inverted color="dark" float-label="Descripción"/>
            </q-field>
          </div>
          <div class="col-sm-12 pull-right" >
            <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="this.form.fields.id === 0">Guardar</q-btn>
            <q-btn @click="update()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-else>Actualizar</q-btn>
          </div>
        </div>
        <!-- <div class="col-sm-12 q-mt-sm" style="padding-left: 10px;">
          <div class="col-sm-12 q-mt-sm">
            <q-list bordered separator v-for="com in form.data" :key="com.id">
              <q-item multiline>
                <q-item-side>
                  <q-item-tile avatar>
                    <img src="statics/usuario_avatar.png">
                  </q-item-tile>
                </q-item-side>
                <q-item-main>
                  <div class="col-sm-6">
                  <q-item-tile label class="text-green-10">Autor: {{ com.nickname }}</q-item-tile></div>
                  <div class="col-sm-6">
                  <q-item-tile sublabel>Fecha: {{ com.created }}</q-item-tile></div>
                  <q-item-tile sublabel v-for="comment in com.comentario" :key="comment.id">{{ comment.comment }}</q-item-tile>
                  <q-btn small flat @click="selectedComentario(com)" color="green-9">
                    <q-icon name="cloud_upload" />&nbsp;
                    <q-tooltip>Subir archivo</q-tooltip>
                  </q-btn>
                  <div style="display: inline;" v-for="file in com.archivos" :key="file.id">
                    <q-btn small flat :icon="file.icon" :color="file.color">
                      <q-tooltip>{{ file.nombre }}</q-tooltip>
                      <q-popover>
                        <q-list link separator class="scroll" style="min-width: 100px">
                          <q-item v-close-overlay @click.native="verDocumento(file)" v-if="file.extension !== 'docx' && file.extension !== 'xml' && file.extension !== 'xlsx' && file.extension !== 'XLSX' && file.extension !== 'DOCX'">
                            <q-item-main label="Ver"/>
                          </q-item>
                          <q-item v-close-overlay @click.native="descargarDocumento(file.id, file.extension)">
                            <q-item-main label="Descargar"/>
                          </q-item>
                          <q-item v-close-overlay @click.native="comentarioAccion(file)">
                            <q-item-main label="Eliminar"/>
                          </q-item>
                        </q-list>
                      </q-popover>
                    </q-btn>
                  </div>
                </q-item-main>
                <q-item-side right>
                  <q-btn flat round dense icon="more_vert">
                    <q-popover>
                      <q-list link>
                        <q-item v-close-overlay>
                          <q-item-main label="Eliminar" @click.native="eliminarComment(com)"/>
                        </q-item>
                      </q-list>
                    </q-popover>
                  </q-btn>
                </q-item-side>
              </q-item>
            </q-list>
          </div>
        </div>
        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tareaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" @change="uploadComentarioFile()" /> -->
      </div>
    </q-modal-layout>
  </q-modal>
</template>

<script>
import { mapActions } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'
import axios from 'axios'

export default {
  name: 'Tareas',
  props: {
    show: {
      type: Boolean
    },
    create: {
      type: Boolean
    },
    tarea: {
      type: Number
    }
  },
  created () {},
  watch: {
    async show (newValue, old) {
      if (newValue === true) {
        this.$q.loading.show({message: 'Cargando...'})
        await this.getUsers()
        await this.getOportunidades()
        if (this.create === true) {
          this.limpiarCampos()
        } else {
          this.getTareaById()
        }
        this.$q.loading.hide()
      }
    }
  },
  data () {
    return {
      loadingButton: false,
      tiposOptions: [ { 'label': 'EMAIL', 'value': 'EMAIL' }, { 'label': 'LLAMADA', 'value': 'LLAMADA' }, { 'label': 'CITA', 'value': 'CITA' }, { 'label': 'TAREA', 'value': 'TAREA' } ],
      form: {
        registro_oportunidad: [],
        fields: {
          id: 0,
          comentario: '',
          tipo: '',
          status: '',
          fecha_limite: '',
          asignado: 0,
          correo: '',
          asunto: '',
          descripcion: '',
          telefono: '',
          fecha_cita: '',
          lugar: '',
          oportunidad_id: 0
        },
        data: []
      }
    }
  },
  computed: {
    usersOptions () {
      let users = this.$store.getters['sys/users/selectOptionsName']
      // users.push({ 'label': 'Todos', 'value': 0 })
      users.splice(0, 0, { 'label': '---Seleccione---', 'value': 0 })
      return users
    },
    oportunidadesOptions () {
      let oportunidades = this.$store.getters['crm/oportunidades/selectOptions']
      // oportunidades.push({ 'label': 'Todos', 'value': 0 })
      oportunidades.splice(0, 0, { 'label': '---Seleccione---', 'value': 0 })
      return oportunidades
    }
  },
  methods: {
    ...mapActions({
      getUsers: 'sys/users/refresh',
      getOportunidades: 'crm/oportunidades/refresh',
      saveTarea: 'crm/tareas/save',
      updateTarea: 'crm/tareas/update',
      getTarea: 'crm/tareas/getBy'
    }),
    close () {
      this.$emit('closeModal', false)
    },
    getTareaById () {
      this.form.data = []
      this.getTarea({id: this.tarea}).then(({data}) => {
        this.form.fields = data.tareas[0]
      }).catch(error => {
        console.error(error)
      })
    },
    getFiles () {
      this.form.data = []
      this.getByOportunidad({oportunidad_id: this.oportunidad_id}).then(({data}) => {
        this.form.data = data.comentarios
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarCampos () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.comentario = ''
      this.form.fields.tipo = ''
      this.form.fields.status = ''
      this.form.fields.fecha_limite = ''
      this.form.fields.asignado = 0
      this.form.fields.correo = ''
      this.form.fields.asunto = ''
      this.form.fields.descripcion = ''
      this.form.fields.telefono = ''
      this.form.fields.fecha_cita = ''
      this.form.fields.lugar = ''
      this.form.fields.oportunidad_id = 0
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        let params = this.form.fields
        this.saveTarea(params).then(({data}) => {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.form.fields.id = data.id
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        let params = this.form.fields
        this.updateTarea(params).then(({data}) => {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.close()
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    uploadComentarioFile () {
      try {
        let file = this.$refs.tareaInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('comentario_id', this.form.registro_oportunidad.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('comentarios/uploadDocumento', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.getFiles()
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
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    verDocumento (comment) {
      var uri = process.env.API + '/public/assets/archivos_comentarios/' + comment.id + '.' + comment.extension
      window.open(uri, '_blank')
    },
    descargarDocumento (id, ext) {
      let uri = process.env.API + `comentarios/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    comentarioAccion (archivo) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará el archivo',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarArchivo(archivo)
      }).catch(() => {})
    },
    borrarArchivo (archivo) {
      let params = archivo
      this.deleteArchivo(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getFiles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    eliminarComment (comment) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará el comentario y todos sus archivos',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarComentario(comment)
      }).catch(() => {})
    },
    borrarComentario (comment) {
      let params = comment
      this.deleteComentario(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getFiles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        tipo: { required },
        fecha_limite: { required },
        asignado: { required, minValue: minValue(1) }
      }
    }
  }
}
</script>
