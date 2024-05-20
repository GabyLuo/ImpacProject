import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    prospectos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.prospectos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/prospectos/getAll')
        .then(response => {
          commit('refresh', response.data.prospectos)
          return response
        })
        .catch(error => error)
    },
    getById: ({commit}, payload) => {
      return axios.get(`/prospectos/getById/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getBy: ({commit}, payload) => {
      return axios.get(`/prospectos/getBy/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/prospectos/save', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/prospectos/update', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/prospectos/delete', qs.stringify(payload))
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
    prospectos: state => state.prospectos,
    selectOptions: state => state.prospectos.reduce((opt, val) => {
      opt.push({label: val.nombre_comercial, value: val.id})
      return opt
    }, [])
  }
}

export default module
