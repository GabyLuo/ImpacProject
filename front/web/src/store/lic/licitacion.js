import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    licitaciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.licitaciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/licitaciones/getAll')
        .then(response => {
          commit('refresh', response.data.licitaciones)
          return response
        })
        .catch(error => error)
    },
    getByRecurso: ({commit}, payload) => {
      return axios.get(`/licitaciones/getByRecurso/${payload.recurso_id}`)
        .then(response => {
          commit('refresh', response.data.licitaciones)
          return response
        })
        .catch(error => error)
    },
    getEmpresaConcursanteByIdLicitacion: ({commit}, payload) => {
      return axios.get(`/licitaciones/getEmpresaConcursanteByIdLicitacion/${payload.id}`)
        .then(response => {
          // commit('refresh', response.data.licitaciones)
          return response
        })
        .catch(error => error)
    },
    filtrar: ({dispatch}, payload) => {
      return axios.post('/licitaciones/filtrar', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/licitaciones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/licitaciones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFile: ({dispatch}, payload) => {
      return axios.put('/licitaciones/deleteFile', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.put('/licitaciones/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    licitaciones: state => state.licitaciones,
    selectOptions: state => state.licitaciones.reduce((opt, val) => {
      opt.push({label: (val.folio + ' - ' + val.titulo), value: val.id})
      return opt
    }, [])
  }
}

export default module
