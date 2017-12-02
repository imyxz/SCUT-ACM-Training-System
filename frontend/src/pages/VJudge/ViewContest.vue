<template>
  <div style="margin: 10px">
    <contest-header :contest-name="ContestData.contest_info.contest_title" :running-time="ContestData.running_time" :contest-long="ContestData.contest_info.contest_last_seconds" :contest-start-time="ContestData.contest_info.contest_start_time_ts"></contest-header>
    <contest-noticer :err_info="ContestData.err_info" :need_participant="ContestData.need_participant" :contest_id="ContestData.contest_id"></contest-noticer>
    <contest-path-indicator :contest-id="ContestData.contest_id"></contest-path-indicator>
    <router-view :contest-data="ContestData"></router-view>
  </div>
</template>

<script>
import ContestHeader from '@/components/VJudge/Contest/ContestHeader'
import ContestNoticer from '@/components/VJudge/Contest/ContestNoticer'
import ContestPathIndicator from '@/components/VJudge/Contest/ContestPathIndicator'
import ContestDataInstance from '@/components/VJudge/Contest/ContestData'
import { getContestInfo, getContestSubmission } from '@/helpers/api/vjudge/contest'
export default {
  name: 'ViewContest',
  data () {
    return {
      ContestData: ContestDataInstance(),
      contest_id: 0
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
          this.ContestData.contest_info = r.contest_info
          this.ContestData.contest_problem = r.contest_problem
          this.ContestData.need_participant = r.need_participant
          this.ContestData.running_time = r.running_time
          this.ContestData.user_id = r.user_id
          if (r.need_participant) {
            this.ContestData.err_info = '请先参与比赛'
          }
          this.ContestData.need_participant = r.need_participant
        })
        .catch(r => {
          this.ContestData.contest_info = r.contest_info
          this.ContestData.contest_problem = r.contest_problem
          this.ContestData.need_participant = r.need_participant
          this.ContestData.running_time = r.running_time
          this.ContestData.user_id = r.user_id
          this.ContestData.err_info = r.err_msg
          if (r.need_participant) {
            this.ContestData.err_info = '请先参与比赛'
          }
          this.ContestData.need_participant = r.need_participant
        })
      getContestSubmission(contestId, -1, false)
        .then(r => {
          this.ContestData.contest_submissions = r.submissions
          this.ContestData.participants = r.participants
          this.ContestData.contest_submissions = r.submissions
          this.ContestData.participants = r.participants
          this.ContestData.running_time = r.running_time
        })
    }
  },
  watch: {
    contest_id: function (newVal) {
      this.ContestData.contest_id = newVal
      this.init(newVal)
    }
  },
  computed: {
  }
}
</script>

<style>

</style>
