<template>
  <div id="q-app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
  mounted () {
    this.$store.dispatch('tokenExists').then(response => {
      if (response.result === true) {
        this.$store.dispatch('user/refresh')
      } else {
        this.$router.push('/')
      }
    })
  }
}
</script>

<style>
</style>
