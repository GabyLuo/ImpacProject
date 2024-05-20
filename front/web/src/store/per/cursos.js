import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cursos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cursos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cursos/getAll')
        .then(response => {
          commit('refresh', response.data.cursos)
          return response
        })
        .catch(error => error)
    },
    getByPerfil: ({commit}, payload) => {
      return axios.get(`/cursos/getByPerfil/${payload.perfil_id}/${payload.tipo}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/cursos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/cursos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    deleteFormato: ({dispatch}, payload) => {
      return axios.post('/cursos/deleteFormato', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/cursos/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cursos: state => state.cursos,
    selectOptions: state => state.cursos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
