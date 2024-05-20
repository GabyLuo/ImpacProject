import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    cotizaciones: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.cotizaciones = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/cotizaciones/getAll')
        .then(response => {
          commit('refresh', response.data.cotizaciones)
          return response
        })
        .catch(error => error)
    },
    getByOportunidad: ({commit}, payload) => {
      return axios.get(`/cotizaciones/getByOportunidad/${payload.oportunidad_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/cotizaciones/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/cotizaciones/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/cotizaciones/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    cotizaciones: state => state.cotizaciones,
    selectOptions: state => state.cotizaciones.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
