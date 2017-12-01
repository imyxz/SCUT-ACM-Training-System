<template>
  <div style="margin: 10px">
    <contest-header :contest-name="contest_info.contest_title" :running-time="running_time" :contest-long="contest_info.contest_last_seconds" :contest-start-time="contest_info.contest_start_time" :contest-end-time="contest_end_time"></contest-header>
    <contest-noticer :err_info="err_info" :need_participant="need_participant" :contest_id="contest_id"></contest-noticer>
    <contest-path-indicator :contest-id="contest_id"></contest-path-indicator>
    <router-view v-bind="$data"></router-view>
  </div>
</template>

<script>
import ContestHeader from '@/components/VJudge/Contest/ContestHeader'
import ContestNoticer from '@/components/VJudge/Contest/ContestNoticer'
import ContestPathIndicator from '@/components/VJudge/Contest/ContestPathIndicator'
import { getContestInfo, getContestSubmission } from '@/helpers/api/vjudge/contest'
import {addZero} from '@/helpers/common'
export default {
  name: 'ViewContest',
  data () {
    return {
      contest_info: {
        contest_title: '',
        contest_start_time_ts: '',
        contest_start_time: '',
        contest_last_seconds: '',
        contest_id: '',
        contest_desc: '',
        contest_type: ''
      },
      contest_submissions: [],
      contest_problem: [],
      participants: [],
      running_time: 0,
      contest_id: 0,
      need_participant: false,
      user_id: 0,
      err_info: ''
    }
  },
  components: {
    'contest-header': ContestHeader,
    'contest-noticer': ContestNoticer,
    'contest-path-indicator': ContestPathIndicator
  },
  created: function () {
    this.contest_id = this.$route.params.contest_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.contest_id = to.params.contest_id
    next()
  },
  methods: {
    init (contestId) {
      getContestInfo(contestId)
        .then(r => {
          this.contest_info = r.contest_info
          this.contest_problem = r.contest_problem
          this.need_participant = r.need_participant
          this.running_time = r.running_time
          this.user_id = r.user_id
          if (r.need_participant) {
            this.err_info = '请先参与比赛'
          }
          this.need_participant = r.need_participant
        })
        .catch(r => {
          this.contest_info = r.contest_info
          this.contest_problem = r.contest_problem
          this.need_participant = r.need_participant
          this.running_time = r.running_time
          this.user_id = r.user_id
          this.err_info = r.err_msg
          if (r.need_participant) {
            this.err_info = '请先参与比赛'
          }
          this.need_participant = r.need_participant
        })
      getContestSubmission(contestId, -1, false)
        .then(r => {
          this.contest_submissions = r.submissions
          this.participants = r.participants
          this.running_time = r.running_time
        })
    }
  },
  watch: {
    contest_id: function (newVal) {
      this.init(newVal)
    }
  },
  computed: {
    contest_end_time: function () {
      var time = new Date(this.contest_info.contest_start_time_ts * 1000)
      var val = new Date(time.getTime() + this.contest_info.contest_last_seconds * 1000)
      return val.getFullYear() + '/' + (val.getMonth() + 1) + '/' + (val.getDate() + 1) + ' ' + addZero(val.getHours()) + ':' + addZero(val.getMinutes())
    }
  }
}
</script>

<style>

</style>
