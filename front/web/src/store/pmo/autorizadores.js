import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    autorizadores: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.autorizadores = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/autorizadores/getAll')
        .then(response => {
          commit('refresh', response.data.autorizadores)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/autorizadores/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          commit('refresh', response.data.autorizadores)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/autorizadores/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/autorizadores/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/autorizadores/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.autorizadores)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    autorizadores: state => state.autorizadores,
    selectOtherOptions: state => state.actividades.reduce((opt, val) => {
      opt.push({label: val.orden, value: val.orden, proyecto_id: val.proyecto_id, orden: val.orden})
      return opt
    }, [])
  }
}
export default module
