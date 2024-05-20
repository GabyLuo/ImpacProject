import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    comisiones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.comisiones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/comisiones/getAll')
        .then(response => {
          // commit('refresh', response.data.comisiones)
          return response
        })
        .catch(error => error)
    },
    getByProyecto: ({commit}, payload) => {
      return axios.get(`/comisiones/getByProyecto/${payload.proyecto_id}`)
        .then(response => {
          // commit('refresh', response.data.comisiones)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/comisiones/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/comisiones/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/comisiones/delete', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    comisiones: state => state.comisiones,
    selectOptions: state => state.comisiones.reduce((opt, val) => {
      opt.push({label: val.id, value: val.id})
      return opt
    }, [])
  }
}

export default module
