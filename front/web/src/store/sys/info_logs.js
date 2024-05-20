import axios from 'axios'
import qs from 'qs'

const module = {
  namespaced: true,
  state: {
    info_logs: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.info_logs = payload
    }
  },
  actions: {
    refresh: ({commit}) => {
      return axios.get('/info_logs/getAll')
        .then(response => {
          commit('refresh', response.data.info_logs)
          return response
        })
        .catch(error => error)
    },
    save: ({dispatch}, payload) => {
      return axios.post('/info_logs/save', qs.stringify(payload))
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
    info_logs: state => state.info_logs,
    selectOptions: state => state.info_logs.reduce((opt, val) => {
      opt.push({label: val.name, value: val.id})
      return opt
    }, [])
  }
}

export default module
