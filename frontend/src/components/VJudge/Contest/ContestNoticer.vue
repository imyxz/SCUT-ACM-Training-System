<template>
  <div class="col s12" v-if="err_info!=''">
    <div class="card">
      <div class="card-content white lighten-3 red-text">
        <span class="card-title center-align">{{ err_info }}</span>
        <div class="center-align">
          <button class=" btn" v-if="need_participant" @click="startContest()">点此开始比赛</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {joinContest} from '@/helpers/api/vjudge/contest'
import {toast} from '@/helpers/common'
export default {
  name: 'ContestHeader',
  props: ['err_info', 'need_participant', 'contest_id'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    startContest: function () {
      joinContest(this.contest_id)
      .then(r => {
        toast('加入比赛成功！')
        setTimeout(() => {
          window.location.reload()
        },
        500)
      })
    }
  },
  filters: {

  },
  computed: {
  }
}
</script>

<style>

</style>
