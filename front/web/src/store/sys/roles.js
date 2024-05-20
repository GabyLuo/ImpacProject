import api from '../../commons/api.js'

const module = {
  namespaced: true,
  state: {
    roles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.roles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return api.get('roles/getAll').then(response => {
        commit('refresh', response.data.roles)
        return response
      }).catch(error => error)
    },
    get: (context, payload) => {
      return api.get(`roles/get/${payload.id}`)
    },
    save: ({dispatch}, payload) => api.post('roles/save', payload),
    update: ({dispatch}, payload) => api.put('roles/update', payload),
    delete: ({dispatch}, payload) => api.post('roles/delete', payload)
  },
  getters: {
    roles: state => state.roles,
    selectOptions: state => state.roles.reduce((opt, val) => {
      opt.push({label: val.name, value: val.id})
      return opt
    }, [{label: '-- Sin padre --', value: 0}]),
    selectOtherOptions: state => state.roles.reduce((opt, val) => {
      opt.push({label: val.name, value: val.id})
      return opt
    }, [])
  }
}

export default module
