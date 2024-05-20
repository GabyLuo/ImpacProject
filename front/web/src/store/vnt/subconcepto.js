import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    subconceptos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.subconceptos = payload
    }
  },
  actions: {
    refresh: ({commit}, payload) => {
      return axios.get(`/subconceptos/getAll/${payload.id}`)
        .then(response => {
          commit('refresh', response.data.subconceptos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/subconceptos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    getByConcepto: ({commit}, payload) => {
      return axios.get(`/subconceptos/getByConcepto/${payload.concepto_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/subconceptos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/subconceptos/delete', qs.stringify(payload))
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
    subconceptos: state => state.subconceptos,
    selectOptions: state => state.subconceptos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
