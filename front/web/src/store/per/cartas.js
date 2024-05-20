import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cartas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cartas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cartas/getAll')
        .then(response => {
          commit('refresh', response.data.cartas)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/cartas/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/cartas/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cartas: state => state.cartas,
    selectOptions: state => state.cartas.reduce((opt, val) => {
      opt.push({label: val.archivo, value: val.id})
      return opt
    }, [])
  }
}

export default module
