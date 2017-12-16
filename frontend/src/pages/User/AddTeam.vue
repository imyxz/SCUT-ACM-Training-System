<template>
  <div class="card">
    <div class="card-content  ">
      <p class="big-test">添加小队</p>
      <div class="row">
        <div class="col l4">
          <div class="row">
            <div class="input-field col l12">
              <input id="team_name" type="text" class="validate" v-model="team_name">
              <label for="team_name">小队名称</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col l12">
              <input id="team_vj_account" type="text" class="validate" v-model="team_vj_account">
              <label for="team_vj_account">VJ账号</label>
            </div>
          </div>
          <div class="row right-align">
            <a class="waves-effect waves-light btn " @click="submit()">添加</a>
          </div>
        </div>
        <div class="col l8">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getAllTeam } from '@/helpers/api/common'
import { addTeam } from '@/helpers/api/user'
import {toast} from '@/helpers/common'
export default {
  name: 'AddTeam',
  data () {
    return {
      team_name: '',
      team_vj_account: ''
    }
  },
  created: function () {
  },
  methods:
  {
    submit: function () {
      addTeam(this.team_name, this.team_vj_account)
        .then(r => {
          toast('新增小队成功')
          getAllTeam(false)
        })
    }
  }
}
</script>

<style>

</style>
