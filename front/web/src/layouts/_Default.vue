<template>
  <q-layout view="hHh Lpr lFf">
    <q-layout-header>
      <q-toolbar color="primary" :glossy="$q.theme === 'mat1'" :inverted="$q.theme === 'ios1'">
        <q-btn flat dense round @click="toggleLeftDrawer()" aria-label="Menu">
          <q-icon class="text-red" name="menu" />
        </q-btn>

        <q-toolbar-title>
          Lubricontrol
        </q-toolbar-title>

        {{ tiempoRestante }}

        <q-btn flat round v-show="hasRoles($roles.SUPER_ADMIN)" :loading="notificationLoading">
          <q-chip floating color="negative" v-show="notifications.filter(n => !n.viewed).length">{{ notifications.filter(n => !n.viewed).length }}</q-chip>
          <q-icon class="text-blue-8" name="fa fa-globe"/>
          <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
            Notificaciones
          </q-tooltip>
          <q-popover>
            <q-list v-if="notifications.length > 0" link style="min-width: 350px">
              <q-item v-for="n in notifications" :key="n.id" :v-close-overlay="false">
                <q-item-side>
                  <q-checkbox v-model="n.viewed" @input="cambiarEstadoNotificacion(n.id, n.active)" readonly disabled />
                </q-item-side>
                <div @click="goToUrl(n)">
                  <q-item-main>
                    <q-item-tile label :style="n.viewed ? '' : 'font-weight:bold;'">{{ n.message }}</q-item-tile>
                    <q-item-tile sublabel class="row">
                      <div class="col-xs-12">
                        &nbsp;&nbsp;{{ n.created | moment('Y/MM/DD') }}
                      </div>
                    </q-item-tile>
                  </q-item-main>
                </div>
              </q-item>
            </q-list>
            <q-list v-else link style="min-width: 350px">
              <q-item v-close-overlay>
                <q-item-side>
                  <!-- <q-checkbox disabled /> -->
                </q-item-side>
                <q-item-main>
                  <q-item-tile label style="font-size: 16px; color: black;">Sin notificaciones</q-item-tile>
                </q-item-main>
              </q-item>
            </q-list>
          </q-popover>
        </q-btn>

        <q-btn flat round>
          <q-icon class="text-red" name="account_circle" />
          <q-tooltip anchor="center left" self="center right" :offset="[10, 10]">
            Cuenta
          </q-tooltip>
          <q-popover v-model="showProfileOptions" @hide="toggleProfileOptions(false)" :show="showProfileOptions">
            <q-list link>
              <q-list-header>{{ user.first_name  || 'firstName' }} {{ user.last_name || 'lastName' }}</q-list-header>
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
              <q-item @click.native="toggleModalPassword(true)">
                <q-item-side style="min-width: 0;">
                  <q-icon name="vpn_key" />
                </q-item-side>
                <q-item-main>
                  <q-item-tile label>Cambiar contraseña</q-item-tile>
                </q-item-main>
              </q-item>
              <q-item @click.native="logout">
                <q-item-side style="min-width: 0;">
                  <q-icon name="power_settings_new"/>
                </q-item-side>
                <q-item-main>
                  <q-item-tile label>Salir</q-item-tile>
                </q-item-main>
              </q-item>
            </q-list>
          </q-popover>
        </q-btn>
      </q-toolbar>
    </q-layout-header>

    <q-layout-drawer v-model="leftDrawerOpen" :content-class="$q.theme === 'mat' ? 'bg-grey-2' : null" :content-style="{width: '255px', overflow: 'hidden'}">
      <main-menu />
    </q-layout-drawer>

    <q-page-container v-if="endLoad">
      <router-view />
    </q-page-container>

    <modal-change-pass :show="showModalPassword" @closeModal="toggleModalPassword($event)"/>

  </q-layout>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { date } from 'quasar'
import MainMenu from '../components/MainMenu'
import ModalChangePass from '../components/account/ModalChangePass'
// import ModalVisualizeMovement from '../components/movements/ModalVisualizeMovement'

const today = new Date()
const { subtractFromDate } = date
const alerts = [
  { color: 'warning', message: 'En 30 minutos expirará la sesión.', icon: 'warning' },
  { color: 'negative', message: 'En 15 minutos expirará la sesión.', icon: 'warning' }
]

export default {
  components: {
    ModalChangePass,
    MainMenu
    // ModalVisualizeMovement
  },
  name: 'LayoutDefault',
  data () {
    return {
      isSelectAll: false,
      notificationLoading: false,
      exp: '',
      tiempoRestante: '',
      roleOpened: '',
      enter: 'bounceInLeft',
      leave: 'bounceOut',
      inactivity: 20,
      leftDrawerOpen: this.$q.platform.is.desktop,
      showModalPassword: false,
      showMovementDetailsModal: false,
      showProdOrderModal: false,
      showProfileOptions: false,
      catalogos: false,
      catalogosProd: false,
      movimientos: false,
      inventario: false,
      reportes: false,
      ventas: false,
      sistema: false,
      movementId: 0,
      existencias: [],
      min: subtractFromDate(today, {days: 5}),
      mov: {
        form: {
          id: '',
          tipo_id: '',
          empresa_id: '',
          almacen_id: '',
          folio: '',
          created: '',
          created_by: '',
          num_transaccion: '',
          status: null,
          // No en modelo
          aux_tipo_id: '',
          traspaso_tipo_id: '',
          traspaso_empresa_id: '',
          traspaso_almacen_id: ''
        },
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'almacen', label: 'Almacén', field: 'almacen', sortable: true, type: 'string', align: 'left' },
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'transaccion_id', label: 'NT', field: 'transaccion_id', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Fecha', field: 'created', sortable: true, type: 'date', align: 'left' },
          { name: 'usuario', label: 'Usuario', field: 'usuario', sortable: true, type: 'string', align: 'left' },
          { name: 'estatus', label: 'Estatus', field: 'estatus', sortable: true, type: 'string', align: 'left' },
          { name: 'documento', label: 'Doc.', field: 'documento', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        details: {
          presentacion_id: '',
          unidad_id: '',
          cantidad: '',
          lote: '',
          lote_id: '',
          observacion: '',
          data: [],
          readOnlyColumns: [
            { name: 'articulo', label: 'Artículo', field: 'articulo', sort: true, type: 'string', align: 'left' },
            { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sort: true, type: 'string', align: 'right' },
            { name: 'unidad', label: 'Unidad', field: 'unidad', sort: true, type: 'string', align: 'right' },
            { name: 'lote', label: 'Lote', field: 'lote', sort: true, type: 'string', align: 'left' },
            { name: 'existencia', label: 'Existencia', field: 'existencia', sort: true, type: 'string', align: 'right' },
            { name: 'observacion', label: 'Observación', field: 'observacion', sort: true, type: 'string', align: 'right' }
          ],
          filter: '',
          auxCantidad: '',
          // No en modelo..
          tipo_articulo_id: '',
          articulo_id: '',
          tipo_presentacion_id: '',
          listToAdd: [],
          listToDelete: [],
          aux_lote_id: ''
        }
      },
      order: {
        empresa_id: null,
        folio: null,
        status: null,
        inicio: null,
        fin: null,
        almacen_id: null,
        check_op: '0',
        lote: null,
        details: []
      },
      orderDetails: {
        form: {
          tipo_suministro: '',
          suministro_id: '',
          cantidad: ''
        }
      },
      opBinnacle: {
        columns: [
          { name: 'usuario', label: 'Usuario', field: 'usuario', align: 'left', sortable: true },
          { name: 'folio', label: 'Folio', field: 'folio', align: 'left', sortable: true },
          { name: 'empresa', label: 'Empresa', field: 'empresa', align: 'left', sortable: true },
          { name: 'almacen', label: 'Almacén', field: 'almacen', align: 'left', sortable: true },
          { name: 'articulo', label: 'Producto', field: 'articulo', align: 'left', sortable: true },
          { name: 'status', label: 'Status', field: 'status', align: 'left', sortable: true },
          { name: 'created', label: 'Creación', field: 'created', align: 'left', sortable: true },
          { name: 'inicio', label: 'Fecha tentativa', field: 'inicio', align: 'left', sortable: true }
        ],
        pagination: {
          rowsPerPage: 5
        },
        details: {
          form: {
            formula_id: null,
            formula: null,
            // presentacion_id: '',
            articulo_id: null,
            presentacion: null,
            cantidad: null
          },
          data: [],
          columns: [
            { name: 'presentacion', label: 'Producto', field: 'presentacion', align: 'left', sortable: true },
            { name: 'formula', label: 'Fórmula', field: 'formula', align: 'left', sortable: true },
            { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right', sortable: true }
          ]
        },
        boom: {
          data: [],
          columns: [
            { name: 'tipo', label: 'Tipo de Suministro', field: 'tipo', align: 'left', sortable: true },
            { name: 'codigo', label: 'Codigo', field: 'codigo', align: 'left', sortable: true },
            { name: 'articulo', label: 'Suministro', field: 'articulo', align: 'left', sortable: true },
            { name: 'cantidad_necesaria', label: 'Cantidad requerida', field: 'cantidad_necesaria', align: 'right', sortable: true },
            // { name: 'cantidad_obtenida', label: 'Cantidad obtenida', field: 'cantidad_obtenida', align: 'right', sortable: true },
            { name: 'existencias', label: 'Existencia', field: 'existencias', align: 'right', sortable: true }
            // { name: 'cantidad_obtenida', label: 'Disponible', field: 'cantidad_obtenida', align: 'right', sortable: true },
            // { name: 'auxAlmacen', label: 'Almacén', field: 'auxAlmacen', align: 'left', sortable: true }
          ],
          didBoom: false
        }
      }
    }
  },
  mounted () {
    if (this.hasRoles(this.$roles.SUPER_ALMACEN, this.$roles.ALMACEN)) {
      this.roleOpened = 'almacen'
    } else if (this.hasRoles(this.$roles.SUPER_PRODUCCION, this.$roles.PRODUCCION)) {
      this.roleOpened = 'produccion'
    } else if (this.hasRoles(this.$roles.SUPER_COMPRAS, this.$roles.COMPRAS)) {
      this.roleOpened = 'compras'
    } else if (this.hasRoles(this.$roles.SUPER_VENTAS, this.$roles.VENTAS)) {
      this.roleOpened = 'ventas'
    }
  },
  created () {
    document.addEventListener('click', this.resetInactivity)
    document.addEventListener('mousemove', this.resetInactivity)
    document.addEventListener('keypress', this.resetInactivity)
    // setTimeout(() => {
    this.loadAll()
    // }, 10000) // Que primero se hagan peticiones para los datos del grid actual
  },
  methods: {
    ...mapActions({
      getSysNotifications: 'sys/notifications/refresh',
      getInvMovementTypes: 'inv/movementTypes/refresh',
      getInvMovements: 'inv/movements/refresh',
      getInvCompanies: 'inv/companies/refresh',
      getInvWarehouses: 'inv/warehouses/refresh',
      getInvMovementDetails: 'inv/movements/getDetails',
      getInvArticles: 'inv/articles/refresh',
      getProdOrders: 'prod/prodOrders/refresh',
      getProdOrderDetails: 'prod/prodOrderDetails/refresh',
      getProdFormulas: 'prod/formulas/refresh',
      getProdFormulations: 'prod/formulations/refresh',
      updateSysNotification: 'sys/notifications/update',
      getKardex: 'inv/movements/getKardex',
      getIatExp: 'sys/usuarios/getIatExp'// obtener exp e iat
    }),
    async loadAll () {
      this.notificationLoading = true
      await this.getSysNotifications()
      this.notificationLoading = false
      await this.getIatExp().then(response => {
        this.exp = response.data.exp
        this.updateExpiration()
        setInterval(this.updateExpiration, 60000)
      })
    },
    logout () {
      this.$store.dispatch('logOut').then(() => {
        window.location.reload()
      })
    },
    showTimeNotification (index, position) {
      const { color, textColor, icon, message, avatar } = alerts[index]
      this.$q.notify({
        color,
        textColor,
        icon,
        message,
        position,
        avatar,
        timeout: Math.random() * 5000 + 3000
      })
    },
    toggleModalPassword (show) {
      this.showModalPassword = show
      this.showProfileOptions = false
    },
    toggleProfileOptions (show) {
      this.showProfileOptions = show
    },
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
      this.cssHelper()
    },
    cssHelper () {
      setTimeout(() => {
        let thead = document.querySelector('#maintable thead tr')
        let tbody = document.querySelector('#maintable tbody tr')
        if (thead !== null && tbody !== null) {
          thead = thead.querySelectorAll('th')
          tbody = tbody.querySelectorAll('td')
          for (let i = 0; i < tbody.length; i++) {
            thead[i].style.width = tbody[i].offsetWidth + 'px'
          }
        }
      }, 250) // Cuando termina de establecer anchos fijos
    },
    updateExpiration () {
      if (this.exp) {
        let auxTiempo = this.exp - (Date.now() / 1000)
        let horas = Math.floor(auxTiempo / 3600)
        let minutos = Math.floor((auxTiempo % 3600) / 60)
        let textoMinutos = minutos < 10 ? '0' + minutos : minutos
        if (horas <= 0 && minutos <= 0) {
          this.logout()
        }
        this.inactivity--
        if (this.inactivity <= 0) {
          this.tiempoRestante = 'La sesión expira en ' + horas + ':' + textoMinutos + ' horas'
        }
      }
    },
    changeRoleOpened (roleName) {
      if (roleName !== this.roleOpened) {
        this.roleOpened = roleName
        this.catalogos = false
        this.catalogosProd = false
        this.movimientos = true
        this.inventario = true
        this.reportes = true
        this.ventas = true
        this.sistema = true
      } else {
        this.roleOpened = ''
        this.catalogos = false
        this.catalogosProd = false
        this.movimientos = false
        this.inventario = false
        this.reportes = false
        this.ventas = false
        this.sistema = false
      }
    },
    resetInactivity () {
      this.inactivity = 20
      this.tiempoRestante = ''
    },
    selectAll () {
      this.isSelectAll = !this.isSelectAll
    },
    cambiarEstadoNotificacion (id, nuevoEstado) {
      let params = {
        id: id,
        visto: nuevoEstado
      }
      this.updateSysNotification(params).then(response => {
        if (response.data.result === 'error') {
          this.$q.dialog({ title: 'Error', message: 'No se ha podido marcar como vista la notificación seleccionada.' })
        }
      })
    },
    goToUrl (n) {
      if (n.viewed) {
        this.$router.push(n.url)
      } else {
        let params = {
          id: n.id,
          viewed: true
        }
        this.updateSysNotification(params).then(response => {
          if (response.data.result === 'success') {
            this.$router.push(n.url)
          }
        })
      }
    },
    hasRoles (...rolesIds) {
      let roles = this.$store.getters['user/rolesIds']
      for (var i = 0; i < rolesIds.length; i++) {
        if (roles.includes(rolesIds[i])) {
          return true
        }
      }
      return false
    },
    hasPrivileges (...privilegesIds) {
      let privileges = this.$store.getters['user/privilegesIds']
      for (var i = 0; i < privilegesIds.length; i++) {
        if (privileges.includes(privilegesIds[i])) {
          return true
        }
      }
      return false
    }
  },
  watch: {
    $route (to, from) {
      this.cssHelper()
    }
  },
  computed: {
    ...mapGetters({
      notifications: 'sys/notifications/notifications'
    }),
    endLoad () { // Terminó de cargar el usuario, ya puede hacer render de lo que sea
      return this.$store.getters['user/endLoad']
    },
    user () {
      return this.$store.getters['user/user']
    },
    userImage () {
      let image = this.$store.getters['user/user'].image || 'default-user.png'
      return `${process.env.API}public/assets/uploads/profile/${image}`
    },
    enterClass () {
      return `animated ${this.enter}`
    },
    leaveClass () {
      return `animated ${this.leave}`
    }
  }
}
</script>

<style scoped>
.left-drawer-list {
  padding: 0px;
}
.nombre {
  font-size: .9em;
}
.q-card-actions {
  padding: 1px;
}
.q-list-header.section {
  background-color: #3b6bbc2e !important;
  cursor: pointer;
}
.role {
  background-color: #3b6bbc !important;
  color: white;
  cursor: pointer;
}

.bg-primary {
/*  background: #3b6bbc !important; */
/*    background: #fff !important; */
}

.bg-positive {
    background: #3b6bbc !important;
    background: #fff !important;
}
</style>
