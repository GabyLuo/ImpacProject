import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    ordenes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.ordenes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/ordenes/getAll')
        .then(response => {
          commit('refresh', response.data.ordenes)
          return response
        })
        .catch(error => error)
    },
    getFolioConsecutivo: ({commit}, payload) => {
      return axios.get(`/ordenes/getFolioConsecutivo/${payload.tipo}`)
        .then(response => {
          // commit('refresh', response.data.ordenes)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/ordenes/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/ordenes/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/ordenes/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    ordenes: state => state.ordenes,
    selectOptions: state => state.ordenes.reduce((opt, val) => {
      opt.push({label: (val.folio + ' - ' + val.titulo), value: val.id})
      return opt
    }, [])
  }
}

export default module
