<template>
  <div class="layout-padding docs-input row justify-center">
    <div style="width: 100vw; max-width: 100vw;">
      <div class="row" style="width: 100%;">

        <div class="col-sm-6" style="padding: 5px">
            <p class="caption">Perfil del usuario - {{ form.nickName }}</p>

            <q-field icon="fas fa-user" :count="50" >
              <q-input upper-case v-model="form.nickName" :error="$v.form.nickName.$error" type="text" inverted color="dark"  stack-label="Nombre de usuario" @keyup="checkLength('nickName', 50)" @keydown="checkLength('nickName', 50)" />
            </q-field>

            <q-field icon="fas fa-envelope" :count="100" >
              <q-input v-model="form.email" :error="$v.form.email.$error" type="email" inverted color="dark"  stack-label="Correo electrónico" @keyup="checkLength('email', 100)" @keydown="checkLength('email', 100)" />
            </q-field>

            <q-btn @click="saveProfile" class="btn_guardar" icon-right="save">
              Guardar
            </q-btn>
        </div>

        <!--<div class="col-sm-6" style="padding: 5px">
          <p class="caption">Cambiar imagen</p>
          <q-field icon="cloud_upload"  helper="Imagen de perfil">
            <q-uploader ref="uploader" :headers="headers" :url="uploadUrl" :extensions="'.gif,.jpg,.jpeg,.png'" @uploaded="afterUpload()" color="green-3"/>
          </q-field>
        </div>-->

      </div>
    </div>
  </div>
</template>

<script>
import { required } from 'vuelidate/lib/validators'

export default {
  components: {},
  created () {
    this.form.nickName = this.user.nickname
    this.form.email = this.user.email
    this.getProfile()
  },
  methods: {
    getProfile () {
      this.$store.dispatch('user/refresh').then(response => {
        if (response.data.result === 'success') {
          this.form.nickName = response.data.user.nickname
          this.form.email = response.data.user.email
        }
      })
    },
    upload () {},
    saveProfile (event, done) {
      this.form.nickName = this.form.nickName.trim()
      this.form.email = this.form.email.trim()

      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        let params = {
          email: this.form.email,
          nickname: this.form.nickName
        }
        this.$store.dispatch('user/updateProfile', params).then(response => {
          this.$showMessage(response.data.message.title, response.data.message.content)
          if (response.data.result === 'success') {
            this.$router.push('/dashboard')
          // let uploader = this.$refs.uploader
          // uploader.files = uploader.files.concat(this.$refs.uploader.files)
          // uploader.upload()
          // this.$q.dialog({ title: 'Perfil actualizado', message: 'Los datos del perfil han sido actualizados.' })
          }
        })
      } else {
        this.$q.dialog({ title: 'Error', message: 'Por favor no deje campos vacios.' })
      }
    },
    afterUpload (file, xhr) {
      let resp = JSON.parse(xhr.response)
      if (resp.result === 'success') {
        this.$q.dialog({ title: 'Exito!', message: 'Se ha guardado la imagen.' })
        this.$store.dispatch('user/refresh')
      } else {
        this.$q.dialog({ title: 'Error!', message: resp.message || 'Error' })
      }
    },
    checkLength (mod, len) {
      while (this.form[mod].length > len) {
        this.form[mod] = this.form[mod].slice(0, len)
      }
    }
  },
  data () {
    return {
      form: {
        nickName: '',
        email: ''
      }
    }
  },
  computed: {
    uploadUrl () {
      return process.env.API + 'users/uploadImage'
    },
    headers () {
      return {'Authorization': `Bearer ${this.$store.getters.token}`}
    },
    user () {
      return this.$store.getters['user/user']
    }
  },
  validations: {
    form: {
      nickName: { required },
      email: { required }
    }
  }
}
</script>

<style>
</style>
