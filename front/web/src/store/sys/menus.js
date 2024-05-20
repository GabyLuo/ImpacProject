import api from '../../commons/api.js'

const module = {
  namespaced: true,
  state: {
    menus: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.menus = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return api.get('menus/getAll').then(response => {
        commit('refresh', response.data.menus)
        return response
      }).catch(error => error)
    },
    get: (context, payload) => api.get(`menus/get/${payload.id}`),
    save: ({dispatch}, payload) => api.post('menus/save', payload),
    update: ({dispatch}, payload) => api.put('menus/update', payload),
    delete: ({dispatch}, payload) => api.post('menus/delete', payload)
  },
  getters: {
    menus: state => state.menus,
    selectOptions: state => state.menus.reduce((opt, val) => {
      opt.push({label: val.label, value: val.id})
      return opt
    }, []),
    selectROptions: state => state.menus.reduce((opt, val) => {
      let text = val.descripcion !== '' && val.descripcion !== null ? `(${val.descripcion})` : ''
      opt.push({label: `${val.label} ${text}`, value: val.id})
      return opt
    }, [])
  }
}

export default module
