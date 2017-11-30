<template>
  <div id='app'>
    <common-header :cur-title='curTitle' :is-login='isLogin' :user-info='userInfo' :menus='menus'></common-header>
    <router-view/>
  </div>
</template>

<script>
import CommonHeader from '@/components/CommonHeader'
import { getUserInfo } from '@/helpers/api/common'
import { setBackgroundPic } from '@/helpers/common'
import MENUS from '@/helpers/constants/MenuLink'
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
      },
      menus: MENUS
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
      // eslint-disable-next-line
    MathJax.Hub.Config({
      showProcessingMessages: false, // 关闭js加载过程信息
      messageStyle: 'none', // 不显示信息
      extensions: ['tex2jax.js'],
      jax: ['input/TeX', 'output/HTML-CSS'],
      tex2jax: {
        inlineMath: [['$', '$'], ['\\(', '\\)']], // 行内公式选择符
        displayMath: [['$$', '$$'], ['\\[', '\\]']], // 段内公式选择符
        skipTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code', 'a'], // 避开某些标签
        ignoreClass: '' // 避开含该Class的标签
      },
      'HTML-CSS': {
        availableFonts: ['STIX', 'TeX'], // 可选字体
        showMathMenu: false // 关闭右击菜单显示
      }
    })
  }
}
</script>

<style>

</style>
