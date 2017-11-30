<template>
  <div>
    <div class="row">
      <div class="col l3  s12">
        <div class="card">
          <div class="card-content red lighten-3 white-text auto-break">
            <span class="card-title">{{ contest_info.contest_name }}</span>
            <p>{{ contest_info.contest_desc }}</p>
          </div>
        </div>
        <contest-team :team-info="contest_info.team_info"></contest-team>
      </div>
      <div class="col l9  s12">
        <ac-status :contest-summary="contest_info.contest_summary" :problem-count="contest_info.problem_count" :player-summary="contest_info.player_summary" @player-summary-change="OnUpdatePlayerAcStatus($event)">
        </ac-status>

      </div>
    </div>
    <div class="row">
      <contest-board :contest-board="contest_info.contest_board" :problem-count="contest_info.problem_count"></contest-board>
    </div>
  </div>
</template>

<script>
import AcStatus from '@/components/Contest/AcStatus'
import ContestTeam from '@/components/Contest/ContestTeam'
import ContestBoard from '@/components/Contest/ContestBoard'
import { getContestSummary, updatePlayerAcStatus } from '@/helpers/api/contest'
export default {
  name: 'ContestSummary',
  data () {
    return {
      contest_info: {
        contest_board: [],
        contset_desc: '',
        contest_name: '',
        contest_summary: {},
        player_summary: {},
        problem_count: 0,
        team_info: []
      },
      contest_id: 0
    }
  },
  components: {
    'ac-status': AcStatus,
    'contest-team': ContestTeam,
    'contest-board': ContestBoard
  },
  created: function () {
    this.contest_id = this.$route.params.contest_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.contest_id = to.params.contest_id
    next()
  },
  methods: {
    updateContestSummary: function (contestId, update = true, cache = true) {
      getContestSummary(contestId, cache)
        .then(r => {
          this.contest_info = r
          if (update) {
            this.contest_id = contestId
            this.$router.push({ name: 'contest.viewSummary', params: { contest_id: contestId } })
          }
        })
    },
    OnUpdatePlayerAcStatus: function (newSummary) {
      updatePlayerAcStatus(this.contest_id, newSummary).then(
        r => {
          this.updateContestSummary(this.contest_id, false, false)
        }
      )
    }
  },
  watch: {
    contest_id: function (newValue) {
      this.updateContestSummary(newValue)
    }
  }
}
</script>

<style>

</style>
