<template>
<div class="container">
  <share-modal ref="share_modal"></share-modal>
    <div class="card-panel hoverable">
      <page-indicator :cur-page="page" :max-page="100" @page-change="page = $event"></page-indicator>
        <status-table :in-contest="false" :status-info="status_info" @share-code="onShareCode($event)" @unshare-code="onUnShareCode($event)"></status-table>
    </div>
  </div>
</template>

<script>
import StatusTable from '@/components/VJudge/StatusTable'
import PageIndicator from '@/components/PageIndicator'
import {getMyStatus} from '@/helpers/api/vjudge/user'
import ShareModal from '@/components/VJudge/OnlineIDE/ShareModal'

export default {
  name: 'MyStatus',
  data () {
    return {
      page: 0,
      submit_status: [],
      isLoading: false
    }
  },
  components: {
    'status-table': StatusTable,
    'page-indicator': PageIndicator,
    'share-modal': ShareModal
  },
  created: function () {
    this.page = this.$route.params.page
  },
  beforeRouteUpdate: function (to, from, next) {
    // this.page = to.params.page
    next()
  },
  methods: {
    updateStatus: function (page, updatePage = true) {
      getMyStatus(page, false)
        .then(r => {
          this.submit_status = r.status_info
          if (updatePage) {
            this.$router.push({ name: 'vjudge.myStatus', params: { page: page } })
          }
        })
    },
    onShareCode: function (jobId) {
      this.$refs.share_modal.$emit('openModal', window.location.origin + '/vJudge/onlineIDE?jobCode=' + jobId)
      this.updateStatus(this.page, false)
    },
    onUnShareCode: function (jobId) {
      this.updateStatus(this.page, false)
    }
  },
  computed: {
    status_info: function () {
      return this.submit_status.map(item => {
        return {
          job_id: item.job_id,
          user_nickname: item.user_nickname,
          problem_identity: item.problem_identity,
          problem_route: { name: 'vjudge.viewProblem', params: { problem_id: item.problem_id } },
          ac_status: item.ac_status,
          wrong_info: item.wrong_info,
          time_usage: item.time_usage,
          ram_usage: item.ram_usage,
          submit_time: item.submit_time,
          oj_name: item.oj_name,
          is_shared: item.is_shared
        }
      })
    }
  },
  watch: {
    page: function (newPage) {
      this.updateStatus(newPage)
    }
  }
}
</script>

<style>

</style>
