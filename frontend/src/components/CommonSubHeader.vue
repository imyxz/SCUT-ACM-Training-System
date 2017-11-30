<template>
  <div class="nav-content light-blue">
    <ul class="tabs tabs-transparent">
      <li class="tab" v-for="menu in menus" :key="menu.route" :class="{ 'active': curTitle==menu.title }">
        <a @click="goTo(menu.routeName)">{{ menu.title }}</a>
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
    }
  },
  methods: {
    goTo: function (routeName) {
      this.$router.push({ name: routeName })
    }
  },
  mounted: function () {
    $('.tabs-transparent').each(function () {
      $(this).unbind('click')
    })
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
