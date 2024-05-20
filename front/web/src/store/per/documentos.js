import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    documentos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.documentos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/documentos_perfiles/getAll')
        .then(response => {
          commit('refresh', response.data.documentos)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/documentos_perfiles/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/documentos_perfiles/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    documentos: state => state.documentos,
    selectOptions: state => state.documentos.reduce((opt, val) => {
      opt.push({label: val.puesto, value: val.id})
      return opt
    }, [])
  }
}

export default module
