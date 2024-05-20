import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    destinos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.destinos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/destinos/getAll')
        .then(response => {
          commit('refresh', response.data.destinos)
          return response
        })
        .catch(error => error)
    },
    getByCategoria: ({commit}, payload) => {
      return axios.get(`/destinos/getById/${payload.id}`)
        .then(response => {
          commit('refresh', response.data.lineas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/destinos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/destinos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/destinos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.destinos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    destinos: state => state.destinos,
    selectOptions: state => state.destinos.reduce((opt, val) => {
      opt.push({label: val.nombre_destino, value: val.id})
      return opt
    }, [])
  }
}
export default module
