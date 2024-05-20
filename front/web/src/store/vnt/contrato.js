import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    contratos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.contratos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/contratos/getAll')
        .then(response => {
          commit('refresh', response.data.contratos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/contratos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/contratos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFile: ({dispatch}, payload) => {
      return axios.put('/contratos/deleteFile', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/contratos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    getByID: ({commit}, payload) => {
      return axios.get(`/contratos/getByID/${payload.id}`)
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.contratos)
          }
          return response
        })
        .catch(error => error)
    },
    getOptions: ({commit}, payload) => {
      return axios.get(`/contratos/getOptions/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    contratos: state => state.contratos,
    selectOptions: state => state.contratos.reduce((opt, val) => {
      opt.push({label: val.num_contrato, value: val.id})
      return opt
    }, []),
    selectOtherOptions: state => state.contratos.reduce((opt, val) => {
      opt.push({label: val.num_contrato, value: val.id, recurso_id: val.recurso_id})
      return opt
    }, [])
  }
}

export default module
