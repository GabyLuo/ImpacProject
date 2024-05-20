import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    tipos_articulos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.tipos_articulos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/tipos_articulos/getAll')
        .then(response => {
          commit('refresh', response.data.tipos_articulos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/tipos_articulos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/tipos_articulos/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({commit}, payload) => {
      return axios.post('/tipos_articulos/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.tipos_articulos)
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    tipos_articulos: state => state.tipos_articulos,
    selectOptions: state => state.tipos_articulos.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}
export default module
