<template>
  <q-modal @hide="close()" style="background-color: rgba(0,0,0,0.7);" v-model="show" :content-css="{width: '70vw', height: '700px'}">
    <q-modal-layout>
      <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
        <div class="col-sm-6">
          <q-toolbar-title>
            Historial
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
        <div class="col-sm-12 q-mt-sm" style="padding-left: 10px;">
          <div class="col-sm-12 q-mt-sm">
            <div class="col-sm-6">
              <q-field icon="folder" icon-color="dark" :error="$v.form.fields.comentario.$error" error-label="Por favor ingrese el comentario">
                <q-input v-model="form.fields.comentario" type="textarea" inverted color="dark" float-label="Comentario"/>
              </q-field>
            </div>
            <div class="col-sm-6 pull-right" >
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
          <div class="col-sm-12 q-mt-sm">
            <q-timeline responsive>
              <q-timeline-entry v-for="historial in form.data" :key="historial.id"
                :subtitle="historial.created"
                :side="historial.side"
                :icon="historial.icon"
                :color="historial.color"
              >
                <div :class="'text-' + historial.color + '-10'">
                  {{ historial.descripcion }}
                </div>
                <div v-if="historial.hist.length > 0">
                  <q-list bordered separator v-for="com in historial.hist" :key="com.id">
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
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-item-main>
                    </q-item>
                  </q-list>
                </div>
                <div v-else>
                  No hay comentarios
                </div>
              </q-timeline-entry>
            </q-timeline>
          </div>
        </div>
        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tareaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" @change="uploadComentarioFile()" />
      </div>
    </q-modal-layout>
  </q-modal>
</template>

<script>
import { mapActions } from 'vuex'
import { required } from 'vuelidate/lib/validators'
import axios from 'axios'

export default {
  name: 'Historial',
  props: {
    show: {
      type: Boolean
    },
    oportunidad_id: {
      type: Number
    }
  },
  created () {
    this.limpiarComentario()
  },
  watch: {
    show (newValue, old) {
      if (newValue === true) {
        this.limpiarComentario()
        this.getHistorial()
      }
    }
  },
  data () {
    return {
      loadingButton: false,
      form: {
        registro_oportunidad: 0,
        fields: {
          comentario: ''
        },
        data: []
      }
    }
  },
  computed: {},
  methods: {
    ...mapActions({
      getByOportunidad: 'crm/oportunidades/getHistorial',
      deleteArchivo: 'crm/comentarios/deleteFormato',
      saveComentario: 'crm/comentarios/save'
    }),
    close () {
      this.$emit('closeModal', false)
    },
    limpiarComentario () {
      this.$v.form.$reset()
      this.form.fields.comentario = ''
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        let params = this.form.fields
        params.oportunidad_id = this.oportunidad_id
        params.etapa_id = this.etapa_id
        this.saveComentario(params).then(({data}) => {
          if (data.result === 'success') {
            this.limpiarComentario()
            this.getHistorial()
          }
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    getHistorial () {
      this.form.data = []
      this.getByOportunidad({id: this.oportunidad_id}).then(({data}) => {
        this.form.data = data.history
      }).catch(error => {
        console.error(error)
      })
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
          this.getComentarios()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    isNumber (evt, input) {
      if (evt.key === '@') {
        console.log('arroba')
      }
    },
    selectedComentario (row) {
      this.$refs.tareaInput.value = ''
      this.form.registro_oportunidad = row
      this.$refs.tareaInput.click()
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
                this.getHistorial()
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
    }
  },
  validations: {
    form: {
      fields: {
        comentario: { required }
      }
    }
  }
}
</script>
