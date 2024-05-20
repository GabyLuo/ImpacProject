<template>
  <div class="row q-mt-sm" style="margin-right: 10px;">
    <div class="row q-col-gutter-xs">
      <div class="row q-mt-lg subtitulos">
        Detalles del movimiento
      </div>
    </div>
    <div class="row q-col-gutter-xs">
      <div class="col-sm-3">
        <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
          <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
        </q-field>
      </div>
      <div class="col-sm-3">
        <q-field icon="fas fa-box" icon-color="dark">
          <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
        </q-field>
      </div>
      <div class="col-sm-2">
        <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
          <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
        </q-field>
      </div>
      <div class="col-sm-2">
        <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_detalles.fields.costo.$error" error-label="Ingrese un costo">
          <q-input upper-case v-model="form_detalles.fields.costo" type="text" inverted color="dark" float-label="Costo" maxlength="50" suffix="MXN" @keyup="isNumber($event,'costo')"/>
        </q-field>
      </div>
      <div class="col-sm-2 pull-right">
        <q-btn @click="saveDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false && status === 'NUEVO'">Agregar</q-btn>
        <q-btn @click="updateDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true && status === 'NUEVO'">Actualizar</q-btn>
        <!-- <q-btn @click="getDetallesByMovimiento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardarrrrrr</q-btn> -->
      </div>
    </div>
    <div class="row q-mt-sm" style="margin-top:10px;">
      <div class="col-sm-12">
        <q-search color="primary" v-model="form_detalles.filter"/>
      </div>
      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
        <q-table id="sticky-table-newstyle"
          :data="form_detalles.data"
          :columns="form_detalles.columns"
          :selected.sync="form_detalles.selected"
          :filter="form_detalles.filter"
          color="positive"
          title=""
          :dense="true"
          :pagination.sync="form_detalles.pagination"
          :loading="form_detalles.loading"
          :rows-per-page-options="form_detalles.rowsOptions">
          <template slot="body" slot-scope="props">
            <q-tr :props="props">
              <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
              <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
              <q-td key="costo" :props="props">${{ props.row.costo }}</q-td>
              <q-td key="importe" :props="props">${{ props.row.importe }}</q-td>
              <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
              <q-td key="acciones" :props="props">
                <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="status === 'NUEVO'">
                  <q-tooltip>Editar</q-tooltip>
                </q-btn>
                <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="status === 'NUEVO'">
                  <q-tooltip>Eliminar</q-tooltip>
                </q-btn>
                <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status !== 'NUEVO'">
                  <q-tooltip>Ejecutado</q-tooltip>
                </q-btn>
              </q-td>
            </q-tr>
          </template>
        </q-table>
        <q-inner-loading :visible="form_detalles.loading">
          <q-spinner-dots size="64px" color="primary" />
        </q-inner-loading>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required } from 'vuelidate/lib/validators'
import axios from 'axios'

export default {
  name: 'Detalles',
  props: {
    show: {
      type: Boolean
    },
    status: {
      type: String
    },
    compra_id: {
      type: Number
    }
  },
  created () {
    this.limpiarCampos()
  },
  watch: {
    show (newValue, old) {
      if (newValue === true) {
        this.limpiarCampos()
        this.getDetalles()
      }
    }
  },
  data () {
    return {
      loadingButton: false,
      form_detalles: {
        fields: {
          id: 0,
          producto_id: 0,
          presentacion_id: 0,
          cantidad: 0,
          costo: 0,
          compra_id: 0
        },
        data: [],
        columns: [
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'costo', label: 'Costo', field: 'costo', sortable: true, type: 'string', align: 'right' },
          { name: 'importe', label: 'Importe', field: 'importe', sortable: true, type: 'string', align: 'right' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ]
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      }
    }
  },
  computed: {},
  methods: {
    ...mapActions({
      getByOportunidad: 'crm/comentarios/getByOportunidad',
      saveComentario: 'crm/comentarios/save',
      updateComentario: 'crm/comentarios/update',
      deleteComentario: 'crm/comentarios/delete',
      deleteArchivo: 'crm/comentarios/deleteFormato'
    }),
    close () {
      this.$emit('closeModal', false)
    },
    getComentarios () {
      this.form.data = []
      this.getByOportunidad({oportunidad_id: this.oportunidad_id}).then(({data}) => {
        this.form.data = data.comentarios
      }).catch(error => {
        console.error(error)
      })
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
            this.getComentarios()
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
                this.getComentarios()
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
          this.getComentarios()
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
