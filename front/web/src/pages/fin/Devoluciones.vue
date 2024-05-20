<template>
  <q-page>
    <div>
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Devolver solicitud" to=""/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="folder" icon-color="dark" :error="$v.form.fields.solicitud_id.$error" error-label="Ingrese el número de solicitud">
                  <q-input upper-case v-model="form.fields.solicitud_id" type="text" inverted color="dark" float-label="Número de solicitud" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-8 pull-right" >
                <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Devolver solicitud</q-btn>
              </div>
              <div class="col-sm-12 q-pt-md q-pl-md">
                NOTA: En caso de devolver gastos ya pagados, éstos serán marcados como no pagados.
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
import { mapActions } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      form: {
        fields: {
          solicitud_id: 0
        }
      },
      modal: {
        x: 0,
        y: 0,
        transition: null
      }
    }
  },
  computed: {},
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      updateSolicitudes: 'fin/solicitudes/updateStatus'
    }),
    async loadAll () {
      this.form.loading = true
      this.form.loading = false
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateSolicitudes(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white',
              icon: 'done',
              position: 'top-right'
            })
            this.form.fields.solicitud_id = 0
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    }
  },
  validations: {
    form: {
      fields: {
        solicitud_id: { required, minValue: minValue(1) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
