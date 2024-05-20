import api from '../../commons/api.js'

const module = {
  namespaced: true,
  state: {
    grants: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.grants = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return api.get('grants/getAll')
        .then(response => {
          commit('refresh', response.data.grants)
          return response
        })
        .catch(error => error)
    },
    get: (context, payload) => {
      return api.post('grants/get', payload)
    },
    save: ({dispatch}, payload) => {
      return api.post('grants/save', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return api.put('grants/update', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return api.post('grants/delete', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    grants: state => state.grants
  }
}

export default module
