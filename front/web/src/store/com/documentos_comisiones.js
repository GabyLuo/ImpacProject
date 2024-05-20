import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    documentos_comisiones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.documentos_comisiones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/documentos_comisiones/getAll')
        .then(response => {
          commit('refresh', response.data.documentos_comisiones)
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/documentos_comisiones/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    documentos_comisiones: state => state.documentos_comisiones,
    selectOptions: state => state.documentos_comisiones.reduce((opt, val) => {
      opt.push({label: val.archivo, value: val.id})
      return opt
    }, [])
  }
}

export default module
