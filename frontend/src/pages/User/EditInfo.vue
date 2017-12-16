<template>
  <div class="card">
    <div class="card-content  ">
      <p class="big-test">修改用户信息</p>
      <div class="row">
        <div class="col l4 s11">

          <div class="row">
            <div class="input-field col l12 s12">
              <input id="nick_name" type="text" class="validate" v-model.trim="nick_name" placeholder="   ">
              <label for="nick_name">昵称</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col l12 s12">
              <input id="bg_url" type="text" class="validate" v-model.trim="bg_url" placeholder="   ">
              <label for="bg_url">背景图片(填入url，留空则使用系统自带)</label>
            </div>
          </div>
          <div class="row right-align">
            <a class="waves-effect waves-light btn " @click="submit()">保存</a>
          </div>
        </div>
        <div class="col l8 s1">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast, setBackgroundPic } from '@/helpers/common'
import { updateUserInfo } from '@/helpers/api/user'
import { getUserInfo } from '@/helpers/api/common'
export default {
  name: 'BindTeam',
  data () {
    return {
      nick_name: '',
      bg_url: ''
    }
  },
  created: function () {
    getUserInfo()
      .then(r => {
        this.nick_name = r.user_info.user_nickname
        this.bg_url = r.user_info.user_bgpic
      })
  },
  methods:
  {
    submit: function () {
      updateUserInfo(this.nick_name.trim(), this.bg_url.trim())
        .then(r => {
          toast('更新信息成功')
          getUserInfo(false)
            .then(r => {
              setBackgroundPic(r.pic_url)
            })
        })
    }
  }
}
</script>

<style>

</style>
