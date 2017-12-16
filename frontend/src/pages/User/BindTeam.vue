<template>
  <div class="card">
    <div class="card-content  ">
      <p class="big-test">绑定小队</p>
      <div class="row">
        <div class="col l4">
          <div class="row">
            <div class="input-field col l12">
              <input id="contest_name" type="text" class="validate" v-model.trim="real_name">
              <label for="contest_name">真实姓名</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <select id="select_team" v-model="team_id">
                <option value="0" disabled selected>选择小队</option>
                <option v-for="team in teams" :value="team.team_id" :key="team.team_id">{{ team.team_name }}</option>
              </select>
              <label for="select_team">所属小队</label>
            </div>
          </div>
          <div class="row right-align">
            <a class="waves-effect waves-light btn " @click="submit()">绑定</a>
          </div>
        </div>
        <div class="col l8">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getAllTeam, getUserInfo } from '@/helpers/api/common'
import { bindPlayer } from '@/helpers/api/user'
import {toast} from '@/helpers/common'
import $ from 'jquery'
export default {
  name: 'BindTeam',
  data () {
    return {
      teams: [],
      real_name: '',
      team_id: ''
    }
  },
  created: function () {
    getAllTeam()
      .then(r => {
        this.teams = r.teams
        this.$nextTick(() => $('select').material_select())
      })
  },
  methods:
  {
    submit: function () {
      bindPlayer(this.real_name, $('#select_team').val())
        .then(r => {
          toast('绑定小队成功')
          getUserInfo(false)
        })
    }
  }
}
</script>

<style>

</style>
