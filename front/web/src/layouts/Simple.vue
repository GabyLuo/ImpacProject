<template>
  <q-layout style="background-image: url('../statics/fondo3.jpg');background-size: cover;">
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { openURL } from 'quasar'

export default {
  name: 'LayoutSimple',
  beforeCreate () {
    this.$store.dispatch('tokenExists').then(response => {
      if (response.result === true) {
        this.$router.push('/dashboard')
      } else {
        console.log('No existe el token!')
      }
    })
  },
  data () {
    return {
      // leftDrawerOpen: this.$q.platform.is.desktop
    }
  },
  methods: {
    openURL
  },
  computed: {
    token () {
      return this.$store.getters.token
    }
  }
}
</script>

<style>
</style>
