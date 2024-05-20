import api from '../../commons/api.js'

const module = {
  namespaced: true,
  state: {
    users: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.users = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return api.get('users/getAll').then(response => {
        commit('refresh', response.data.users)
        return response
      }).catch(error => error)
    },
    getClientes: ({commit}) => {
      return api.get('users/getClientes').then(response => {
        commit('refresh', response.data.users)
        return response
      }).catch(error => error)
    },
    get: (context, payload) => {
      return api.get(`users/get/${payload.id}`)
    },
    save: ({dispatch}, payload) => api.post('users/save', payload),
    update: ({dispatch}, payload) => api.put('users/update', payload),
    updateStatus: ({dispatch}, payload) => api.put('users/updateStatus', payload),
    delete: ({dispatch}, payload) => api.post('users/delete', payload)
  },
  getters: {
    users: state => state.users,
    selectOptions: state => state.users.reduce((opt, val) => {
      opt.push({label: val.email, value: val.id})
      return opt
    }, []),
    selectOptionsName: state => state.users.reduce((opt, val) => {
      opt.push({label: val.nickname, value: val.id})
      return opt
    }, [])
  }
}

export default module
