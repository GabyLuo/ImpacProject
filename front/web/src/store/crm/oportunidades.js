import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    oportunidades: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.oportunidades = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/oportunidades/getAll')
        .then(response => {
          commit('refresh', response.data.oportunidades)
          return response
        })
        .catch(error => error)
    },
    getResumen: ({commit}, payload) => {
      return axios.get(`/oportunidades/getResumen/${payload.year}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getHistorial: ({commit}, payload) => {
      return axios.get(`/oportunidades/getHistorial/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByEjecutivo: ({commit}, payload) => {
      return axios.get(`/oportunidades/getByEjecutivo/${payload.oportunidade_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getComentariosByOportunidad: ({commit}, payload) => {
      return axios.get(`/oportunidades/getComentariosByOportunidad/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getGanadas: ({commit}, payload) => {
      return axios.get(`/oportunidades/getGanadas/${payload.carril_id}/${payload.year}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getNoLogradas: ({commit}, payload) => {
      return axios.get(`/oportunidades/getNoLogradas/${payload.carril_id}/${payload.year}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/oportunidades/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/oportunidades/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateEjecutivo: ({dispatch}, payload) => {
      return axios.put('/oportunidades/updateEjecutivo', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateStep: ({dispatch}, payload) => {
      return axios.put('/oportunidades/nextStep', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    previousStep: ({dispatch}, payload) => {
      return axios.put('/oportunidades/previousStep', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    noLograda: ({dispatch}, payload) => {
      return axios.put('/oportunidades/noLograda', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/oportunidades/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    oportunidades: state => state.oportunidades,
    selectOptions: state => state.oportunidades.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
