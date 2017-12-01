<template>
  <div>
    <common-sub-header :menus="menus"></common-sub-header>
    <div class="card-panel teal">
      <span class="white-text">网站前端正在重构中，部分功能暂不可用，请见谅
      </span>
    </div>
    <router-view/>
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

</style>
