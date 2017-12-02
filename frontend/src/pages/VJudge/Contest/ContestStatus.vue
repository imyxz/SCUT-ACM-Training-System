<template>
<div class="container">
    <div class="card-panel hoverable">
      <page-indicator :cur-page="page" :max-page="100" @page-change="page = $event"></page-indicator>
        <status-table :in-contest="true" :status-info="status_info"></status-table>
    </div>
  </div>
</template>

<script>
import StatusTable from '@/components/VJudge/StatusTable'
import PageIndicator from '@/components/PageIndicator'
import {getContestStatus} from '@/helpers/api/vjudge/contest'
export default {
  name: 'ContestStatus',
  props: ['ContestData'],
  data () {
    return {
      page: 0,
      submit_status: []
    }
  },
  components: {
    'status-table': StatusTable,
    'page-indicator': PageIndicator

  },
  created: function () {
    this.page = this.$route.params.page
  },
  beforeRouteUpdate: function (to, from, next) {
    this.page = to.params.page
    next()
  },
  methods: {
    updateContestStatus: function (page, updatePage = true) {
      getContestStatus(this.ContestData.contest_id, page, false)
        .then(r => {
          this.submit_status = r.submit_status
          if (updatePage) {
            this.page = page
            this.$router.push({ name: 'vjudge.contest.status', params: { contest_id: this.ContestData.contest_id, page: page } })
          }
        })
    }
  },
  computed: {
    status_info: function () {
      return this.submit_status.map(item => {
        return {
          job_id: item.run_job_id,
          user_nickname: item.user_nickname,
          problem_identity: String.fromCharCode(item.problem_index + 64),
          problem_route: { name: 'vjudge.contest.problem', params: { contest_id: this.ContestData.contest_id, problem_index: String.fromCharCode(item.problem_index + 64) } },
          ac_status: item.ac_status,
          wrong_info: item.wrong_info,
          time_usage: item.time_usage,
          ram_usage: item.ram_usage,
          submit_time: item.submit_time
        }
      })
    }
  },
  watch: {
    page: function (newPage) {
      this.updateContestStatus(newPage)
    }
  }
}
</script>

<style>

</style>
