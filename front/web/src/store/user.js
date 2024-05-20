import api from '../commons/api.js'
import jwt from '../commons/jwt.js'

const module = {
  namespaced: true,
  state: {
    user: [],
    role: [],
    endLoad: false,
    menuData: []
  },
  mutations: {
    refresh: (state, payload) => {
      state.user = payload
      var d = new Date()
      state.user.image += '?v=' + d.getTime()
    },
    setRole: (state, payload) => {
      state.role = payload
    },
    setEndLoad: (state, payload) => {
      state.endLoad = payload
    },
    setMenuData: (state, payload) => {
      state.menuData = payload
    }
  },
  actions: {
    refresh: ({commit, dispatch}) => {
      return api.get('users/getProfile')
        .then(response => {
          if (response.data.result === 'success') {
            commit('refresh', response.data.user)
            commit('setMenuData', response.data.menuData)
            commit('setRole', response.data.role)
          }
          commit('setEndLoad', true)
          return response
        }).catch(error => {
          jwt.destroyToken()
          window.location.reload()
          return error
        })
    },
    update: ({dispatch}, payload) => {
      return api.put('users/update', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateProfile: ({dispatch}, payload) => {
      return api.put('users/updateProfile', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updatePassword: ({dispatch, commit}, payload) => {
      return api.put('users/updatePassword', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    updateStatus: ({dispatch, commit}, payload) => {
      return api.put('users/updateStatus', payload)
        .then(response => {
          if (response.data.result === 'success') {
            dispatch('refresh')
          }
          return response
        })
        .catch(error => error)
    },
    uploadImage: ({dispatch, commit}, payload) => {
      return api.post('users/uploadImage', payload, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    },
    /* recoverPassword: ({dispatch, commit}, payload) => {
      return api.post('users/recoverPassword', payload)
    } */
    recoverPassword: ({dispatch, commit}, payload) => api.post('users/recoverPassword', payload)
  },
  getters: {
    user: state => state.user,
    role: state => state.role,
    endLoad: state => state.endLoad,
    menuData: state => state.menuData
  }
}

export default module
