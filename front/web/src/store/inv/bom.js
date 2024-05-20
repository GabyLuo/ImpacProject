import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    bom: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.bom = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/bom/getAll')
        .then(response => {
          commit('refresh', response.data.bom)
          return response
        })
        .catch(error => error)
    },
    getByProducto: ({commit}, payload) => {
      return axios.get(`/bom/getByProducto/${payload.producto_id}`)
        .then(response => {
          // commit('refresh', response.data.bom)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/bom/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/bom/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/bom/delete', qs.stringify(payload))
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
    bom: state => state.bom,
    selectOptions: state => state.bom.reduce((opt, val) => {
      opt.push({label: (val.folio + ' - ' + val.titulo), value: val.id})
      return opt
    }, [])
  }
}

export default module
