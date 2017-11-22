import Vue from 'vue'
import Router from 'vue-router'
import Common404 from '@/components/Common404'
import UserRoute from './UserRoute'
import ContestRoute from './ContestRoute'
Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Index',
      meta: {
        title: '首页'
      },
      redirect: '/contest/viewList/1'
    },
    UserRoute,
    ContestRoute,
    {
      path: '*',
      name: 'Notice404',
      component: Common404,
      meta: {
        title: '404'
      }
    }
  ]
})
