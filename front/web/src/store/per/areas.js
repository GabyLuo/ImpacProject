import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    areas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.areas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/areas/getAll')
        .then(response => {
          commit('refresh', response.data.areas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/areas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/areas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/areas/delete', qs.stringify(payload))
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
    areas: state => state.areas,
    selectOptions: state => state.areas.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
