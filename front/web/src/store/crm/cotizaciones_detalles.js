import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cotizaciones_detalles: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cotizaciones_detalles = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cotizaciones_detalles/getAll')
        .then(response => {
          commit('refresh', response.data.cotizaciones_detalles)
          return response
        })
        .catch(error => error)
    },
    getByCotizacion: ({commit}, payload) => {
      return axios.get(`/cotizaciones_detalles/getByCotizacion/${payload.cotizacion_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/cotizaciones_detalles/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/cotizaciones_detalles/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/cotizaciones_detalles/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cotizaciones_detalles: state => state.cotizaciones_detalles,
    selectOptions: state => state.cotizaciones_detalles.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
