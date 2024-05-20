import Vue from 'vue'
import Vuex from 'vuex'

import auth from './auth'
import user from './user'
import sys from './sys'
import ventas from './ventas'
import vnt from './vnt'
import pmo from './pmo'
import fin from './fin'
import exp from './exp'
import lic from './lic'
import per from './per'
import inv from './inv'
import com from './com'
import pro from './pro'
import crm from './crm'
import cmp from './cmp'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {},
  mutations: {},
  actions: {},
  getters: {},
  modules: {
    auth,
    user,
    sys,
    ventas,
    vnt,
    pmo,
    fin,
    exp,
    lic,
    per,
    inv,
    com,
    pro,
    crm,
    cmp
  }
})
