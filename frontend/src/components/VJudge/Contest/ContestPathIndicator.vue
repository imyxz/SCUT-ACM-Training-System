<template>
  <ul class="tabs row" style="overflow-x: hidden" ref="menuer">
    <li class="tab col s3" v-for="(menu,index) in menus" :key="menu.title">
      <a @click="goTo(menu.routeName)" :class="{'active': curSelected==index}">{{menu.title}}</a>
    </li>
  </ul>
</template>
<script>
import $ from 'jquery'
let MENUS = [
  {
    title: '比赛详情',
    routeName: 'vjudge.contest.info',
    finalName: 'vjudge.contest.info'
  },
  {
    title: '题目列表',
    routeName: 'vjudge.contest.problem.index',
    finalName: 'vjudge.contest.problem'
  },
  {
    title: '提交状态',
    routeName: 'vjudge.contest.status.index',
    finalName: 'vjudge.contest.status'
  },
  {
    title: '排行榜',
    routeName: 'vjudge.contest.rank',
    finalName: 'vjudge.contest.rank'
  }
]
export default {
  name: 'ContestPathIndicator',
  props: ['contestId'],
  data () {
    return {
      menus: MENUS,
      curSelected: 0
    }
  },
  methods: {
    goTo: function (routeName) {
      this.$router.push({ name: routeName, params: { contest_id: this.contestId } })
    },
    onUpdateRoute: function (newRoute) {
      $(this.$refs.menuer).contents('.active').removeClass('active')
      this.menus.forEach((menu, index) => {
        if (menu.finalName === newRoute.name) {
          this.curSelected = index
        }
      })
    }
  },
  mounted: function () {
    $(this.$refs.menuer).tabs()
    $(this.$refs.menuer).contents('.indicator').css('background-color', 'deepskyblue')
    this.onUpdateRoute(this.$route)
  },
  watch: {
    $route: function (newRoute) {
      this.onUpdateRoute(newRoute)
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.tabs .tab a {
  color: deepskyblue !important;
  font-size: 18px;
}
.tabs .tab a:hover,
.tabs .tab a.active {
  background-color: transparent;
  color: deepskyblue;
}
.tabs .tab.disabled a,
.tabs .tab.disabled a:hover {
  color: rgba(255, 255, 255, 0.7);
}
.tabs .indicator {
  background-color: deepskyblue;
}
</style>
