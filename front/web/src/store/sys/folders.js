import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    folders: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.folders = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/carpetas/getAll')
        .then(response => {
          commit('refresh', response.data.folders)
          return response
        })
        .catch(error => error)
    },
    getByPadre: ({commit}, payload) => {
      return axios.get(`/carpetas/getByPadre/${payload.padre}`)
        .then(response => {
          commit('refresh', response.data.folders)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/carpetas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/carpetas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/carpetas/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    deleteFile: ({dispatch}, payload) => {
      return axios.post('/carpetas/deleteFile', qs.stringify(payload))
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
    folders: state => state.folders,
    selectOptions: state => state.folders.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
