<template>
  <q-page>
    <div class="layout-padding docs-input centrado">
      <div style="width: 500px; max-width: 90vw;">
        <div class="caption text-center">
            <img src="../statics/logo.png" alt="Iniciar Sesión" width="250">
            <h4 style="margin: 10px; font-weight: bold; color:#042950">¡Bienvenido!</h4>
        </div>

        <div class="seccion" style="width: 75%; margin-left: auto; margin-right: auto;">
          <q-field icon="contact_mail" :count="50" helper="Introduzca su email" :error="$v.email.$error" error-label="Por favor ingrese un correo válido">
            <q-input class="text-white" type="email" inverted color="faded" v-model="email" placeholder="Email" />
          </q-field>
        </div>

        <div class="row justify-center seccion">
          <div class="col-xs-12 col-sm-1 col-md-1">
          </div>
          <div class="col-xs-12 col-sm-4 col-md-4 text-center">
            <q-btn @click="$router.push('/')" flat position="pull-right" color="dark">
              Atrás
            </q-btn>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 text-center">
            <q-btn @click="recoverPassword()" :loading="loading" color="green-9">
              Restablecer contraseña
            </q-btn>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { email } from 'vuelidate/lib/validators'
export default {
  created () {},
  methods: {
    recoverPassword () {
      this.$v.$touch()
      if (!this.$v.$error) {
        let params = {
          email: this.email
        }
        this.loading = true
        this.$store.dispatch('user/recoverPassword', params)
          .then(response => {
            if (response.data.result === 'success') {
              this.showMessage('Exito!', 'Se le ha enviado una contraseña temporal a su correo.')
            } else {
              this.showMessage('Error', response.data.message)
            }
            this.loading = false
          })
          .catch(error => {
            this.showMessage('Error', 'Error del servidor.')
            this.loading = false
            console.error(error)
          })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise su correo.')
      }
      /* axios.post('/users/recoverPassword', {
        email: this.email
      }).then(function (response) {
        console.log(response)
      })
        .catch(function (error) {
          console.log('hola')
          console.log(error)
        }) */
      // this.$store.dispatch('logIn', params)
      /* this.$store.dispatch('user/recoverPassword', params)
        .then(({data}) => {
          this.showMessage(data.message.title, data.message.content)
          this.loading = false
          console.log(data)
        }).catch(error => {
          this.showMessage('Error', 'Error del servidor.')
          this.loading = false
          console.error(error)
        }) */
    },
    showMessage (title, message) {
      this.$q.dialog({
        title: title,
        message: message
      })
    }
  },
  validations: {
    email: { email }
  },
  data () {
    return {
      email: null,
      loading: false
    }
  }
}
</script>

<style scoped>
  .centrado {
    height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .seccion {
    margin-top: 1em;
  }
</style>
