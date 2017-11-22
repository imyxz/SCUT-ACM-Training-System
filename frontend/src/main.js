// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import 'materialize-css/dist/css/materialize.min.css'
import '@/assets/materialize-icon.css'
import 'jquery'
import * as ENV from '@/helpers/constants/env'
import 'materialize-css/dist/js/materialize.min.js'
import '@/assets/style.css'
import Materialize from '@/helpers/Materialize'
Vue.use(Materialize)
Vue.config.productionTip = false
router.beforeEach((to, from, next) => {
  window.document.title = to.meta.title + ' - ' + ENV.TITLE
  next()
})
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
