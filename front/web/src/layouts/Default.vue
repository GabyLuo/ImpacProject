<template>
  <q-layout view="hHh Lpr lFf">
    <q-layout-header>
      <q-toolbar class="toolbar-1" :glossy="$q.theme === 'mat1'" :inverted="$q.theme === 'ios1'">
        <q-btn flat dense round @click="toggleLeftDrawer()" aria-label="Menu">
          <q-icon class="text-white" name="menu"/>
        </q-btn>
        <q-toolbar-title @click.native="redir()" style="cursor: pointer;">
          IMPACT
        </q-toolbar-title>
        {{ user.nickname }} ({{ role[0] }})
        <q-btn flat round>
          <q-icon class="text-white" name="account_circle" />
          <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
            Cuenta
          </q-tooltip>
          <q-popover v-model="showProfileMenu" @hide="toggleProfileMenu(false)" :show="showProfileMenu">
            <profile />
          </q-popover>
        </q-btn>
      </q-toolbar>
    </q-layout-header>
    <q-layout-drawer v-model="leftDrawerOpen" :content-class="$q.theme === 'mat' ? 'bg-white' : null" :content-style="{width: '255px', overflow: 'hidden'}">
      <main-menu />
    </q-layout-drawer>
    <!-- Render de las vistas si terminó de cargar el perfil -->
    <!-- <q-page-container v-if="endLoad" style="background-color: #f5f5f5;"> -->
    <q-page-container v-if="endLoad" style="background-color: #ffffff;">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import MainMenu from '../components/layout/menus/MainMenu'
import Profile from '../components/layout/menus/Profile'
export default {
  name: 'LayoutDefault',
  components: {
    MainMenu,
    Profile
  },
  data () {
    return {
      leftDrawerOpen: false,
      showProfileMenu: false
    }
  },
  methods: {
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    toggleProfileMenu (show) {
      this.showProfileMenu = show
    },
    redir () {
      this.$router.push('/dashboard')
    }
  },
  computed: {
    endLoad () {
      return this.$store.getters['user/endLoad']
    },
    user () {
      return this.$store.getters['user/user']
    },
    role () {
      return this.$store.getters['user/role']
    }
  },
  watch: {
    leftDrawerOpen () {
      this.$cssHelper()
    },
    $route (to, from) {
      this.$cssHelper()
    }
  }
}

</script>

<style scoped>
.toolbar-1 {
  background-color: rgba(8,85,134,1) !important;
}
</style>
