<template>
  <q-list link>
    <q-list-header>
      <div class="" style="padding: 10px;">
        {{ user.nickname }}
      </div>
    </q-list-header>
    <q-item @click.native="$router.push('/dashboard')">
      <q-item-side style="min-width: 0;">
        <q-icon name="fa fa-home"/>
      </q-item-side>
      <q-item-main>
        <q-item-tile label>Dashboard</q-item-tile>
      </q-item-main>
    </q-item>
    <q-item @click.native="$router.push('/profile')">
      <q-item-side style="min-width: 0;">
        <q-icon name="account_circle" />
      </q-item-side>
      <q-item-main>
        <q-item-tile label>Perfil</q-item-tile>
      </q-item-main>
    </q-item>
    <!-- <q-item @click.native="$router.push('/changePassword')">
      <q-item-side style="min-width: 0;">
        <q-icon name="vpn_key" />
      </q-item-side>
      <q-item-main>
        <q-item-tile label>Cambiar contraseña</q-item-tile>
      </q-item-main>
    </q-item> -->
    <q-item @click.native="logout">
      <q-item-side style="min-width: 0;">
        <q-icon name="power_settings_new"/>
      </q-item-side>
      <q-item-main>
        <q-item-tile label>Salir</q-item-tile>
      </q-item-main>
    </q-item>
  </q-list>
</template>

<script>
export default {
  name: 'Profile',
  showModalPassword: false,
  data () {
    return {
      firstName: 'Lucio',
      lastName: 'Nunez'
    }
  },
  computed: {
    user () {
      return this.$store.getters['user/user']
    }
  },
  methods: {
    logout () {
      this.$store.dispatch('logOut').then(() => {
        window.location.reload()
      })
    },
    toggleModalPassword (show) {
      console.log(show)
    }
  }
}
</script>

<style scoped>

</style>
