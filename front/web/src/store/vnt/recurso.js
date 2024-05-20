import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    recursos: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.recursos = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/recursos/getAll')
        .then(response => {
          commit('refresh', response.data.recursos)
          return response
        })
        .catch(error => error)
    },
    getByYear: ({commit}, payload) => {
      return axios.get(`/recursos/getByYear/${payload.year}/${payload.presupuesto}/${payload.sucursal}`)
        .then(response => {
          // commit('refresh', response.data.recursos)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/recursos/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    create_from_licitacion: ({dispatch}, payload) => {
      return axios.post('/recursos/create_from_licitacion', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/recursos/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/recursos/delete', qs.stringify(payload))
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
    recursos: state => state.recursos,
    selectOptions: state => state.recursos.reduce((opt, val) => {
      opt.push({label: val.codigo, value: val.id})
      return opt
    }, []),
    selectOptionsName: state => state.recursos.reduce((opt, val) => {
      opt.push({label: val.codigo + ', ' + val.nombre, value: val.id})
      return opt
    }, []),
    selectOtherOptions: state => state.recursos.reduce((opt, val) => {
      opt.push({label: val.codigo, value: val.id, cliente: val.razon_social})
      return opt
    }, [])
  }
}

export default module
