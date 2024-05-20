import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    licitaciones_perfiles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.licitaciones_perfiles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/licitaciones_perfiles/getAll')
        .then(response => {
          commit('refresh', response.data.licitaciones_perfiles)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/licitaciones_perfiles/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByLicitacion: ({commit}, payload) => {
      return axios.get(`/licitaciones_perfiles/getByLicitacion/${payload.licitacion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/licitaciones_perfiles/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/licitaciones_perfiles/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/licitaciones_perfiles/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    licitaciones_perfiles: state => state.licitaciones_perfiles,
    selectOptions: state => state.perfiles.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
