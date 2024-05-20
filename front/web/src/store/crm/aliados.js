import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    aliados: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.aliados = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/aliadoscrm/getAll')
        .then(response => {
          commit('refresh', response.data.aliados)
          return response
        })
        .catch(error => error)
    },
    getByOportunidad: ({commit}, payload) => {
      return axios.get(`/aliadoscrm/getByOportunidad/${payload.oportunidad_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/aliadoscrm/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/aliadoscrm/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/aliadoscrm/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    aliados: state => state.aliados,
    selectOptions: state => state.aliados.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
