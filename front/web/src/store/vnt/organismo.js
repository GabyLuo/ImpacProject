import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    organismos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.organismos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/organismos/getAll')
        .then(response => {
          commit('refresh', response.data.organismos)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/organismos/getByProyecto/${payload.id}`)
        .then(response => {
          commit('refresh', response.data.organismos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/organismos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/organismos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/organismos/delete', qs.stringify(payload))
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
    organismos: state => state.organismos,
    selectOptions: state => state.organismos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
