import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    vendedores: [],
    gerentes: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.vendedores = payload
    },
    refreshGerentes: (state, payload) => {
      state.gerentes = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/vendedores/getAll')
        .then(response => {
          commit('refresh', response.data.vendedores)
          return response
        })
        .catch(error => error)
    },
    refreshGerentes: ({commit}) => {
      return axios.get('/vendedores/getGerentes')
        .then(response => {
          commit('refreshGerentes', response.data.gerentes)
          return response
        })
        .catch(error => error)
    },
    update: ({dispatch}, payload) => {
      return axios.put('/vendedores/update', qs.stringify(payload))
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    vendedores: state => state.vendedores,
    gerentes: state => state.gerentes,
    selectOptions: state => state.vendedores.reduce((opt, val) => {
      opt.push({label: val.nickname, value: val.id})
      return opt
    }, []),
    selectOptionsGerentes: state => state.gerentes.reduce((opt, val) => {
      opt.push({label: val.nickname, value: val.id})
      return opt
    }, [])
  }
}

export default module
