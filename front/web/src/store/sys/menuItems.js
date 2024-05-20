import api from '../../commons/api.js'

const module = {
  namespaced: true,
  state: {
    menuItems: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.menuItems = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return api.get('menuItems/getAll').then(response => {
        commit('refresh', response.data.menuItems)
        return response
      }).catch(error => error)
    },
    get: (context, payload) => api.get(`menuItems/get/${payload.id}`),
    getByMenu: (context, payload) => api.get(`menuItems/getByMenu/${payload.menu_id}`),
    save: ({dispatch}, payload) => api.post('menuItems/save', payload),
    update: ({dispatch}, payload) => api.put('menuItems/update', payload),
    delete: ({dispatch}, payload) => api.post('menuItems/delete', payload)
  },
  getters: {
    menuItems: state => state.menuItems,
    selectOptions: state => state.menuItems.reduce((opt, val) => {
      opt.push({label: val.label, value: val.id})
      return opt
    }, []),
    selectROptions: state => state.menuItems.reduce((opt, val) => {
      let text = val.descripcion !== '' && val.descripcion !== null ? `(${val.descripcion})` : ''
      opt.push({label: `${val.label} ${text}`, value: val.id})
      return opt
    }, [])
  }
}

export default module
