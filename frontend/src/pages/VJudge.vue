<template>
  <div>
    <common-sub-header :menus="menus"></common-sub-header>
    <transition enter-active-class="animated fadeIn delay-3" leave-active-class="animated fadeOut delay-3">
      <router-view />
    </transition>
  </div>
</template>

<script>
import CommonSubHeader from '@/components/CommonSubHeader'
import VJudgeMenu from '@/helpers/constants/VJudgeSubMenu'
export default {
  name: 'VJudge',
  data () {
    return {
      menus: VJudgeMenu,
      curTitle: ''
    }
  },
  created: function () {
    this.curTitle = this.$router.currentRoute.meta.title
    this.$router.beforeEach((to, from, next) => {
      this.curTitle = to.meta.title
      next()
    })
  },
  components: {
    'common-sub-header': CommonSubHeader
  }
}
</script>

<style>
.delay-1 {
animation-duration: .25s;
}
.delay-2 {
animation-duration: .5s;
}
.delay-3 {
animation-duration: .75s;
}
.delay-4 {
animation-duration: 1s;
}
</style>
