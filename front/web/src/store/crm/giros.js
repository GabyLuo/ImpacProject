import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    giros: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.giros = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/giros/getAll')
        .then(response => {
          commit('refresh', response.data.giros)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/giros/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/giros/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/giros/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    giros: state => state.giros,
    selectOptions: state => state.giros.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
