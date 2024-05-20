import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    actividades: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.actividades = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/actividades/getAll')
        .then(response => {
          commit('refresh', response.data.actividades)
          return response
        })
        .catch(error => error)
    },
    getByEtapa: ({commit}, payload) => {
      return axios.get(`/actividades/getByEtapa/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/actividades/save', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/actividades/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateObligatorio: ({dispatch}, payload) => {
      return axios.put('/actividades/updateObligatorio', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateLicitacion: ({dispatch}, payload) => {
      return axios.put('/actividades/updateLicitacion', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    updateProyecto: ({dispatch}, payload) => {
      return axios.put('/actividades/updateProyecto', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/actividades/delete', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    actividades: state => state.actividades,
    selectOptions: state => state.actividades.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id})
      return opt
    }, [])
  }
}

export default module
