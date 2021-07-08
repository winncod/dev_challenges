import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    loading: false
  },
  mutations: {
    showLoading (state) {
      state.loading = true
    },
    hideLoading (state) {
        state.loading = false
    }
  }
})

export default store