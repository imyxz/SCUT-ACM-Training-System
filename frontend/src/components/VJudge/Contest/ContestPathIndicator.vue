<template>
  <ul class="tabs row" style="overflow-x: hidden" ref="menuer">
    <li class="tab col s3" v-for="menu in menus" :key="menu.title">
      <a class="active" @click="goTo(menu.routeName)">{{menu.title}}</a>
    </li>
  </ul>
</template>
<script>
import $ from 'jquery'
let MENUS = [
  {
    title: '比赛详情',
    routeName: 'vjudge.contest.info'
  },
  {
    title: '题目列表',
    routeName: 'vjudge.contest.problem.index'
  },
  {
    title: '提交状态',
    routeName: 'user.addTeam'
  },
  {
    title: '排行榜',
    routeName: 'user.bindTeam'
  }
]
export default {
  name: 'ContestPathIndicator',
  props: ['contestId'],
  data () {
    return {
      menus: MENUS
    }
  },
  methods: {
    goTo: function (routeName) {
      this.$router.push({ name: routeName, params: { contest_id: this.contestId } })
    }
  },
  mounted: function () {
    $(this.$refs.menuer).tabs({ onShow: function (tab) { } })
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
