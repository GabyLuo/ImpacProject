import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    almacenes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.almacenes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/almacenes/getAll')
        .then(response => {
          commit('refresh', response.data.almacenes)
          return response
        })
        .catch(error => error)
    },
    getByEmpresa: ({commit}, payload) => {
      return axios.get(`/almacenes/getByEmpresa/${payload.empresa_id}`)
        .then(response => {
          commit('refresh', response.data.almacenes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/almacenes/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/almacenes/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/almacenes/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.almacenes)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    almacenes: state => state.almacenes,
    selectOptions: state => state.almacenes.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, []),
    selectOtherOptions: state => state.almacenes.reduce((opt, val) => {
      opt.push({label: val.nombreopt, value: val.id})
      return opt
    }, [])
  }
}
export default module
