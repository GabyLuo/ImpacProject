import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    subprogramas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.subprogramas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/subprogramas/getAll')
        .then(response => {
          commit('refresh', response.data.subprogramas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/subprogramas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/subprogramas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/subprogramas/delete', qs.stringify(payload))
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
    subprogramas: state => state.subprogramas,
    selectOptions: state => state.subprogramas.reduce((opt, val) => {
      opt.push({label: val.programa_nombre + ', ' + val.subprograma_nombre, value: val.id})
      return opt
    }, []),
    selectOtherOptions: state => state.subprogramas.reduce((opt, val) => {
      opt.push({label: val.subprograma_nombre, value: val.id, programa_id: val.programa_id})
      return opt
    }, [])
  }
}

export default module
