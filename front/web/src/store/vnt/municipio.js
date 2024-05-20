import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    municipios: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.municipios = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/municipios/getAll')
        .then(response => {
          commit('refresh', response.data.municipios)
          return response
        })
        .catch(error => error)
    },
    getByEstado: ({commit}, payload) => {
      return axios.get(`/municipios/getByEstado/${payload.estado_id}`)
        .then(response => {
          commit('refresh', response.data.municipios)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/municipios/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/municipios/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/municipios/delete', qs.stringify(payload))
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
    municipios: state => state.municipios,
    selectOptions: state => state.municipios.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
