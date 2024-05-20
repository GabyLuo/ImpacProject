import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    tipos_movimientos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.tipos_movimientos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/tipos_movimientos/getAll')
        .then(response => {
          commit('refresh', response.data.tipos_movimientos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/tipos_movimientos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/tipos_movimientos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/tipos_movimientos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.tipos_movimientos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    tipos_movimientos: state => state.tipos_movimientos,
    selectOptions: state => state.tipos_movimientos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}
export default module
