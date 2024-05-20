import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    etapas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.etapas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/etapas/getAll')
        .then(response => {
          commit('refresh', response.data.etapas)
          return response
        })
        .catch(error => error)
    },
    getByCarril: ({commit}, payload) => {
      return axios.get(`/etapas/getByCarril/${payload.carril_id}`)
        .then(response => {
          // commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    getWithData: ({commit}, payload) => {
      return axios.get(`/etapas/getWithData/${payload.carril_id}/${payload.year}`)
        .then(response => {
          // commit('refresh', response.data.movimientos_detalles)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/etapas/save', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/etapas/update', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    },
    updateCierre: ({dispatch}, payload) => {
      return axios.put('/etapas/updateCierre', qs.stringify(payload))
        .then(response => {
          // if (response.data.result === 'success') {
          // dispatch('refresh')
          // }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/etapas/delete', qs.stringify(payload))
        .then(response => {
          /* if (response.data.result === 'success') {
            dispatch('refresh')
          } */
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    etapas: state => state.etapas,
    selectOptions: state => state.etapas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
