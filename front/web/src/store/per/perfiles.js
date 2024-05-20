import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    perfiles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.perfiles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/perfiles/getAll')
        .then(response => {
          commit('refresh', response.data.perfiles)
          return response
        })
        .catch(error => error)
    },
    filtrar: ({dispatch}, payload) => {
      return axios.get(`/perfiles/filtrar/${payload.estado}/${payload.municipio}/${payload.area}/${payload.aptitudes}/${payload.curso}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/perfiles/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/perfiles/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/perfiles/delete', qs.stringify(payload))
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
    perfiles: state => state.perfiles,
    selectOptions: state => state.perfiles.reduce((opt, val) => {
      opt.push({label: val.nombre + ' ' + val.apellido_paterno + ' ' + val.apellido_materno, value: val.id})
      return opt
    }, [])
  }
}

export default module
