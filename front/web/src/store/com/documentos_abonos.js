import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    documentos_abonos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.documentos_abonos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/documentos_abonos/getAll')
        .then(response => {
          commit('refresh', response.data.documentos_abonos)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/documentos_abonos/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    documentos_abonos: state => state.documentos_abonos,
    selectOptions: state => state.documentos_abonos.reduce((opt, val) => {
      opt.push({label: val.archivo, value: val.id})
      return opt
    }, [])
  }
}

export default module
