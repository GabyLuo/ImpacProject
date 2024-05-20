import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    posgrados: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.posgrados = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/posgrados/getAll')
        .then(response => {
          commit('refresh', response.data.posgrados)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/posgrados/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/posgrados/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/posgrados/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/posgrados/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/posgrados/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    posgrados: state => state.posgrados,
    selectOptions: state => state.posgrados.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
