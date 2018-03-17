<template>
  <div class="container">
    <h4>从下列比赛更新Rating：</h4>
    <p>{{ContestData.contest_info.contest_title}}</p>
    <div class="row">
      <div class="card-panel">
        <h5>Ranking：<button class="btn" @click="display_rank=!display_rank">{{display_rank?'隐藏':'显示'}}</button></h5>
        <div class='card-content not-break' style='overflow-x:auto'>
          <contest-board-table v-show="display_rank" :contest-board-info="ContestData.rank" :problem-count="ContestData.contest_problem.length"></contest-board-table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="card-panel">
        <h5>Preview：<button class="btn" @click="refreshPreview">刷新</button></h5>
        <div class='card-content not-break' style='overflow-x:auto'>
          <contset-history-table :history="history"></contset-history-table>
        </div>
      </div>
    </div>
    <button class="btn" @click="submit">提交</button>
  </div>
</template>

<script>
import ContestBoardTable from '@/components/Contest/ContestBoardTable'
import ContestDataInstance from '@/components/VJudge/Contest/ContestData'
import ContestHistoryTable from '@/components/Rating/ContestHistoryTable'
import { getContestInfo, getContestSubmission } from '@/helpers/api/vjudge/contest'
import {addNewRating} from '@/helpers/api/rating'
import { toast } from '@/helpers/common'

export default {
  name: 'AddRatingFromContest',
  data () {
    return {
      contest_id: 0,
      ContestData: ContestDataInstance(),
      display_rank: false,
      preview: []
    }
  },
  components: {
    'contest-board-table': ContestBoardTable,
    'contset-history-table': ContestHistoryTable
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
    },
    refreshPreview: function () {
      addNewRating(this.contest_id, this.ContestData.contest_info.contest_title, true, true, this.rank)
      .then(r => {
        this.preview = r.preview
      })
    },
    submit: function () {
      addNewRating(this.contest_id, this.ContestData.contest_info.contest_title, true, false, this.rank)
      .then(r => {
        toast('已更新')
      })
    }
  },
  created: function () {
    this.contest_id = this.$route.params.contest_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.contest_id = to.params.contest_id
    next()
  },
  watch: {
    contest_id: function (newVal) {
      this.init(newVal)
    }
  },
  mounted: function () {
  },
  computed: {
    rank: function () {
      return this.ContestData.users_status.filter(r => {
        return r.total_trys > 0
      }).map(r => {
        return [r.user_id, r.rank_index, r.penalty]
      })
    },
    history: function () {
      if (this.ContestData.usersStatusMap === undefined) {
        return []
      }
      return this.preview.map(e => {
        let user = this.ContestData.usersStatusMap[e.user_id]
        return {
          user_id: e.user_id,
          from_rating: e.from_rating,
          to_rating: e.to_rating,
          nickname: user.user_name,
          avatar: user.user_avatar
        }
      })
    }
  }
}
</script>

<style>

</style>
