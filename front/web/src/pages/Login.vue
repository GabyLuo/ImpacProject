<template>
  <q-page>
    <div class="layout-padding docs-input centrado" @keyup.enter="login()">
      <div class="hijo" style="width: 500px; max-width: 90vw; margin">
        <div class="caption text-center">
            <img src="../statics/logo.png" alt="Iniciar Sesión" width="250" @dblclick="magic" @keyup.enter="login()">
            <h4 style="margin: 10px; color:#042950">¡Bienvenido!</h4>
        </div>

        <div class="seccion" style="width: 75%; margin-left: auto; margin-right: auto;">
          <q-field icon="fa-user" :count="50" helper="Ingrese su correo electrónico">
            <q-input class="text-white" type="text" inverted color="faded" v-model="username" placeholder="Correo electrónico" ref="nickname"/>
          </q-field>

          <q-field icon="lock" :count="10" helper="Por favor escriba su contraseña">
            <q-input class="text-white" type="password" inverted color="faded" v-model="password" placeholder="Contraseña"/>
          </q-field>
        </div>

        <div class="row justify-center seccion">
          <div class="col-xs-12 col-sm-1 col-md-1">
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 text-center" v-if="5===5">
            <q-btn @click="$router.push('/recover')" flat position="pull-right" color="dark">
              ¿Olvidaste tu contraseña?
            </q-btn>
          </div>
          <div class="col-xs-12 col-sm-4 col-md-4 text-center">
            <q-btn @click="login" :loading="loading" color="green-9">
              Iniciar Sesión
            </q-btn>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>

export default {
  name: 'PageLogin',
  created () {},
  methods: {
    login () {
      let params = {
        username: this.username,
        password: this.password
      }
      this.loading = true
      this.$store.dispatch('logIn', params)
        .then(response => {
          if (response.data.result === 'success') {
            this.$store.dispatch('user/refresh').then(() => {
              this.$router.push('/dashboard')
            })
          } else {
            this.showMessage('Error', response.data.message + ' ' + response.data.result)
          }
          this.loading = false
        })
        .catch(error => {
          this.showMessage('Error', 'Revise su usuario y contraseña')
          this.loading = false
          console.error(error)
        })
    },
    showMessage (title, message) {
      this.$q.dialog({
        title: title,
        message: message
      })
    },
    magic () {
      // this.username = 'sa@impact.mx'
      // this.password = 'ANT123'
    }
  },
  data () {
    return {
      username: null,
      password: '',
      loading: false
    }
  }
}
</script>

<style scoped>
  .centrado {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .seccion {
    margin-top: 1em;
  }
  .hijo {
    height: 70vh;
  }

.text-positive {
    color: #21ba45 !important;
    color: #3b6bbc !important;
}
</style>
