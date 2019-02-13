<template>
  <div class="router-container">
    <div class="row">
      <div class="col l3 s12">
        <div class="card-panel ">
          <problem-submit ref="submitor" :compiler-info="problem_info.compiler_info" @submit-code="onSubmitCode($event)"></problem-submit>
        </div>
        <problem-tags :tags="problem_tags"></problem-tags>
        <problem-ac-rank :ranks="problem_rank"></problem-ac-rank>

      </div>
      <div class="col l8 s12">
        <div class="card-panel hoverable">
          <problem-info v-bind="problem_info"></problem-info>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import ProblemInfo from '@/components/VJudge/ProblemInfo'
import ProblemAcRank from '@/components/VJudge/ProblemAcRank'
import ProblemTags from '@/components/VJudge/ProblemTags'
import ProblemSubmit from '@/components/VJudge/ProblemSubmit'
import { getProblemInfo, getProblemAcRank, submitCode, getJobStatus } from '@/helpers/api/vjudge/problem'
import { toast } from '@/helpers/common'
export default {
  name: 'ViewProblem',
  data () {
    return {
      problem_info: {
        problem_title: '',
        problem_desc: '',
        memory_limit: '',
        time_limit: '',
        problem_url: '',
        problem_id: '',
        problem_identity: '',
        oj_id: '',
        oj_name: '',
        compiler_info: []
      },
      problem_id: 0,
      problem_rank: [],
      problem_tags: [],
      job_id: 0,
      is_judging: false,
      updateJobStatusTimerId: 0
    }
  },
  components: {
    'problem-info': ProblemInfo,
    'problem-ac-rank': ProblemAcRank,
    'problem-tags': ProblemTags,
    'problem-submit': ProblemSubmit
  },
  created: function () {
    this.problem_id = this.$route.params.problem_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.problem_id = to.params.problem_id
    next()
  },
  beforeRouteLeave: function (to, from, next) {
    clearTimeout(this.updateJobStatusTimerId)
    next()
  },
  methods: {
    updateProblemInfo: function (problemID, update = true, cache = true) {
      getProblemInfo(problemID, cache)
        .then(r => {
          r.problem_info.compiler_info = JSON.parse(r.problem_info.compiler_info)
          this.problem_info = r.problem_info
          this.problem_tags = r.problem_tags
          if (update) {
            this.problem_id = problemID
            this.$router.push({ name: 'vjudge.viewProblem', params: { problem_id: problemID } })
          }
        })
      getProblemAcRank(problemID, cache)
        .then(r => {
          this.problem_rank = r.rank
        })
    },
    onSubmitCode: function (data) {
      submitCode(this.problem_id, data.compiler_id, data.source_code)
      .then(r => {
        this.$refs.submitor.$emit('submited')
        this.job_id = r.job_id
        toast('代码已提交')
        this.updateJobStatus()
      })
    },
    updateJobStatus: function () {
      getJobStatus(this.job_id, false)
      .then(r => {
        this.$refs.submitor.$emit('jobStatusChange', r.status_info)
        toast('状态已更新')
        if (parseInt(r.status_info.running_status) !== 3) {
          this.updateJobStatusTimerId = setTimeout(() => {
            this.updateJobStatus()
          }, 3000)
        }
      })
    }
  },
  watch: {
    problem_id: function (newValue) {
      this.updateProblemInfo(newValue)
    }
  }
}
</script>

<style scoped>
</style>
