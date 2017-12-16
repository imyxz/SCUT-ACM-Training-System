<template>
  <div class="nav-content light-blue">
    <ul class="tabs tabs-transparent" ref="menuer">
      <li class="tab" v-for="(menu,index) in menus" :key="menu.route" >
        <a :class="{ 'active': curSelected==index }" @click="goTo(menu.routeName)">{{ menu.title }}</a>
      </li>
    </ul>
  </div>
</template>
<script>
import $ from 'jquery'
export default {
  name: 'CommonSubHeader',
  props: ['menus', 'curTitle'],
  data () {
    return {
      curSelected: -1
    }
  },
  methods: {
    goTo: function (routeName) {
      this.$router.push({ name: routeName })
    },
    onUpdateRoute: function (newRoute) {
      this.curSelected = -1
      $(this.$refs.menuer).contents('.active').removeClass('active')
      this.menus.forEach((menu, index) => {
        if (menu.finalName === newRoute.name) {
          this.curSelected = index
        }
      })
      this.$nextTick(() => {
        $(this.$refs.menuer).tabs()
      })
    }
  },
  mounted: function () {
    this.onUpdateRoute(this.$route)
    $(this.$refs.menuer).each(function () {
      // $(this).unbind('click')
    })
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

</style>
