import axios from 'axios'

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
      return axios.get('/carga_csv/getAllActividades')
        .then(response => {
          commit('refresh', response.data.actividades)
          return response
        })
        .catch(error => error)
    },
    getAllActividadesProject: ({commit}) => {
      return axios.get('/carga_csv/getAllActividadesProject')
        .then(response => {
          commit('refresh', response.data.actividades)
          return response
        })
        .catch(error => error)
    },
    getPresupuestoActividad: ({commit}, payload) => {
      return axios.get(`/carga_csv/getPresupuestoActividad/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getPresupuestoActividadReal: ({commit}, payload) => {
      return axios.get(`/carga_csv/getPresupuestoActividadReal/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getActividadesOpt: ({commit}, payload) => {
      return axios.get(`/carga_csv/getActividadesOpt/${payload.proyecto_id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    },
    getByActividad: ({commit}, payload) => {
      return axios.get(`/carga_csv/getByActividad/${payload.id}`)
        .then(response => {
          return response
        })
        .catch(error => error)
    }
  },
  getters: {
    actividades: state => state.actividades,
    selectOtherOptions: state => state.actividades.reduce((opt, val) => {
      opt.push({label: val.nombre, value: val.id, proyecto_id: val.proyecto_id})
      return opt
    }, [])
  }
}
export default module
