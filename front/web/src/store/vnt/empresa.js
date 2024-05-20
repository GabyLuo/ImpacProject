import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    empresas: [],
    subempresas: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.empresas = payload
    },
    refreshSubempresas: (state, payload) => {
      state.subempresas = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/empresas/getAll')
        .then(response => {
          commit('refresh', response.data.empresas)
          return response
        })
        .catch(error => error)
    },
    getByPadre: ({commit}, payload) => {
      return axios.get(`/empresas/getByPadre/${payload.padre}`)
        .then(response => {
          commit('refreshSubempresas', response.data.subempresas)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/empresas/save', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/empresas/update', qs.stringify(payload))
        .then(response => {
          if (response.data.result === 'success') {
            // dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    delete: ({dispatch}, payload) => {
      return axios.post('/empresas/delete', qs.stringify(payload))
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
    empresas: state => state.empresas,
    subempresas: state => state.subempresas,
    selectOptions: state => state.empresas.reduce((opt, val) => {
      opt.push({label: val.razon_social, value: val.id})
      return opt
    }, []),
    selectOptionsSub: state => state.subempresas.reduce((opt, val) => {
      opt.push({label: val.razon_social, value: val.id})
      return opt
    }, [])
  }
}

export default module
