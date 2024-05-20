import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    proyectos_perfiles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.proyectos_perfiles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/proyectos_perfiles/getAll')
        .then(response => {
          commit('refresh', response.data.proyectos_perfiles)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/proyectos_perfiles/getByPerfil/${payload.perfil_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByProject: ({commit}, payload) => {
      return axios.get(`/proyectos_perfiles/getByProject/${payload.proyecto_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/proyectos_perfiles/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/proyectos_perfiles/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/proyectos_perfiles/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    proyectos_perfiles: state => state.proyectos_perfiles,
    selectOptions: state => state.perfiles.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
