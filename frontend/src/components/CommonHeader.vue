<template>
  <nav class="nav-extended">
    <div class="nav-wrapper light-blue">
      <a class="brand-logo" style="margin-left: 20px;">SCUT-ACM</a>
      <a href="#" data-activates="mobile-nav" class="button-collapse">
        <i class="material-icons">menu</i>
      </a>
      <ul class="right hide-on-med-and-down ">
        <li v-for="menu in menus" :key="menu.route" :class="{ 'active': curTitle==menu.title }">
          <a @click="goTo(menu.routeName)" class="waves-effect waves-teal">{{ menu.title }}</a>
        </li>
        <template v-if="isLogin">
          <li>
            <a class="btn-floating green dropdown-button" data-activates='user_dropdown'>
              <i class="material-icons">perm_identity</i>
            </a>
          </li>
          <ul id='user_dropdown' class='dropdown-content'>
            <li>
              <a @click="goTo('user.editInfo')">更改信息</a>
            </li>
            <li class="divider"></li>
            <li>
              <a @click="goLogOut">退出登录</a>
            </li>
          </ul>
        </template>
        <template v-else>
          <li>
            <a class="btn-floating red" @click="goLogin">
              <i class="material-icons">perm_identity</i>
            </a>
          </li>
        </template>

      </ul>
      <ul class="side-nav" id="mobile-nav">
        <li v-for="menu in menus" :key="menu.route" :class="{ 'active': curTitle==menu.title }">
          <a @click="goTo(menu.routeName)" class="waves-effect waves-teal">{{ menu.title }}</a>
        </li>
      </ul>
    </div>
  </nav>
</template>
<script>
import { BASIC_URL } from '@/helpers/constants/env'
import { logOut } from '@/helpers/api/user'
import $ from 'jquery'
export default {
  name: 'CommonHeader',
  props: ['curTitle', 'isLogin', 'userInfo', 'menus'],
  data () {
    return {
    }
  },
  methods: {
    goTo: function (routeName) {
      this.$router.push({ name: routeName })
    },
    goLogin: function () {
      window.location = 'https://encuss.yxz.me/userAPI/loginFromQQ/site_id/3/viewing/' + encodeURIComponent(BASIC_URL + 'user/loginFromEncuss/viewing/' + encodeURIComponent(window.location) + '/')
    },
    goLogOut: function () {
      logOut().finally(r => {
        window.location.reload()
      })
    }
  },
  mounted: function () {
    $('.button-collapse').sideNav({
      menuWidth: 240, // Default is 240
      edge: 'left', // Choose the horizontal origin
      closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
      draggable: true // Choose whether you can drag to open on touch screens
    }
    )
    $('.dropdown-button').dropdown({
      inDuration: 300,
      outDuration: 225,
      constrain_width: false,
      hover: true,
      gutter: 0,
      belowOrigin: true,
      alignment: 'right'
    }
    )
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
