import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    gastos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.gastos = payload
    },
    refreshModerados: (state, payload) => {
      state.gastos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/gastos/getAll')
        .then(response => {
          commit('refresh', response.data.gastos)
          return response
        })
        .catch(error => error)
    },
    getByActividad: ({commit}, payload) => {
      return axios.get(`/gastos/getByActividad/${payload.actividad_id}`)
        .then(response => {
          commit('refresh', response.data.gastos)
          return response
        })
        .catch(error => error)
    },
    getById: ({commit}, payload) => {
      return axios.get(`/gastos/getById/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getBySolicitud: ({commit}, payload) => {
      return axios.get(`/gastos/getBySolicitud/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/gastos/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/gastos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    pagar: ({dispatch}, payload) => {
      return axios.put('/gastos/pagar', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    comprobar: ({dispatch}, payload) => {
      return axios.put('/gastos/comprobar', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateConcepto: ({dispatch}, payload) => {
      return axios.put('/gastos/updateConcepto', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/gastos/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    gastos: state => state.gastos
  }
}

export default module
