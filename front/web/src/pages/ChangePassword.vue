<template>
  <div class="layout-padding docs-input row justify-center">
    <div style="width: 100vw; max-width: 100vw;">
      <div class="row" style="width: 100%;">
        <div class="col-sm-6" style="padding: 5px">
            <p class="caption" style="font-weight:bold; font-size:1.2em;">Cambio de contraseña</p>

            <q-field icon="vpn_key" :count="50">
              <q-input v-model="form.password" :error="$v.form.password.$error" type="password" inverted color="dark" float-label="Contraseña actual"/>
            </q-field>

            <q-field icon="vpn_key" :count="100" >
              <q-input v-model="form.newPassword" :error="$v.form.newPassword.$error" type="password" inverted color="dark" float-label="Nueva contraseña"/>
            </q-field>

            <q-field icon="vpn_key" :count="100" >
              <q-input v-model="form.newPasswordVerified" :error="$v.form.newPasswordVerified.$error" type="password" inverted color="dark" float-label="Nueva contraseña"/>
            </q-field>
            <div class="col-sm-2 offset-sm-4 pull-right">
                <q-btn @click="changePassword()" class="btn_guardar" icon-right="save" :loading="loadingButton">
                    Guardar
                </q-btn>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex'
import { required } from 'vuelidate/lib/validators'

export default {
  components: {},
  created () {
    this.limpiarFields()
  },
  methods: {
    ...mapActions({
      updatePassword: 'user/updatePassword'
    }),
    changePassword () {
      this.form.newPassword = this.form.newPassword.trim()
      this.form.newPasswordVerified = this.form.newPasswordVerified.trim()
      this.form.password = this.form.password.trim()

      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (this.form.newPassword === this.form.newPasswordVerified) {
          this.loadingButton = true
          let params = this.form
          console.log(params)
          this.updatePassword(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.limpiarFields()
              this.$router.push('/dashboard')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.$showMessage('¡Advertencia!', 'La contraseña nueva no coincide')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    limpiarFields () {
      this.form.newPassword = ''
      this.form.newPasswordVerified = ''
      this.form.password = ''
    }
  },
  data () {
    return {
      loadingButton: false,
      form: {
        password: '',
        newPassword: '',
        newPasswordVerified: ''
      }
    }
  },
  validations: {
    form: {
      password: { required },
      newPassword: { required },
      newPasswordVerified: { required }
    }
  }
}
</script>

<style>
</style>
