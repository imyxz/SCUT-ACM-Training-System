<template>
  <div id='app'>
    <common-header :cur-title='curTitle' :is-login='isLogin' :user-info='userInfo'></common-header>
    <router-view/>
  </div>
</template>

<script>
import CommonHeader from '@/components/CommonHeader'
import { getUserInfo } from '@/helpers/api/common'
import { setBackgroundPic } from '@/helpers/common'

export default {
  name: 'app',
  components: {
    'common-header': CommonHeader
  },
  data () {
    return {
      curTitle: '首页',
      isLogin: true,
      userInfo: {
        user_nickname: '',
        user_avatar: '',
        user_bgpic: ''
      }
    }
  },
  created: function () {
    this.curTitle = this.$router.currentRoute.meta.title
    this.$router.beforeEach((to, from, next) => {
      this.curTitle = to.meta.title
      next()
    })
    getUserInfo()
      .then(r => {
        this.isLogin = r.is_login
        this.userInfo = r.user_info
        setBackgroundPic(r.pic_url)
      })
  }
}
</script>

<style>

</style>
