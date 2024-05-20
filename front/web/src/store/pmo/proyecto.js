import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    proyectos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.proyectos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/proyectos/getAll')
        .then(response => {
          commit('refresh', response.data.proyectos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/proyectos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/proyectos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateMontos: ({dispatch}, payload) => {
      return axios.put('/proyectos/updateMontos', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateStatus: ({dispatch}, payload) => {
      return axios.put('/proyectos/updateStatus', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateFinalizado: ({dispatch}, payload) => {
      return axios.put('/proyectos/updateFinalizado', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/proyectos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    getProyectosPerfiles: ({commit}, payload) => {
      return axios.get(`/proyectos/getProyectosPerfiles/${payload.year}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getProyectosPerfilesByRecurso: ({commit}, payload) => {
      return axios.get(`/proyectos/getProyectosPerfilesByRecurso/${payload.recurso}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getProyectosColaborador: ({commit}, payload) => {
      return axios.get(`/proyectos/getProyectosColaborador/${payload.year}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getUsuariosByPresupuesto: ({commit}, payload) => {
      return axios.get(`/proyectos/getUsuariosByPresupuesto/${payload.proyecto_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByRecurso: ({commit}, payload) => {
      return axios.get(`/proyectos/getByRecurso/${payload.recurso_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByID: ({commit}, payload) => {
      return axios.get(`/proyectos/getByID/${payload.id}/${payload.year}`)
        .then(response => {
          if (response.data.result === 'success') {
            // commit('refresh', response.data.proyectos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    proyectos: state => state.proyectos,
    selectOptions: state => state.proyectos.reduce((opt, val) => {
      opt.push({label: val.nombre_proyecto, value: val.id})
      return opt
    }, [])
  }
}

export default module
