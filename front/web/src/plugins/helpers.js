import { Dialog } from 'quasar'
import store from '../store'

export default ({ app, Vue }) => {
  // Mostrar mensajes
  Vue.prototype.$showMessage = (title, message) => {
    Dialog.create({ title: title, message: message })
  }

  // Ccomprobar los roles indicados con los roles del usuario
  Vue.prototype.$hasRoles = (...rolesIds) => {
    for (var i = 0; i < rolesIds.length; i++) {
      if (store.getters['user/rolesIds'].includes(rolesIds[i])) {
        return true
      }
    }
    return false
  }

  // Comprobar los privilegios indicados con los privilegios del usuario
  Vue.prototype.$hasPrivileges = (...privilegesIds) => {
    for (var i = 0; i < privilegesIds.length; i++) {
      if (store.getters['user/privilegesIds'].includes(privilegesIds[i])) {
        return true
      }
    }
    return false
  }

  // Darle formato de número a cualquier cifra eg: (1000, 2) => 1,000.00
  Vue.prototype.$formatNumber = (number, len = 2) => parseFloat(number).toFixed(len).replace(/(\d)(?=(\d{3})+\.)/g, '$1,')

  // Acomodar cabeceras de las tablas
  Vue.prototype.$cssHelper = () => {
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
    }, 250)
  }
}
