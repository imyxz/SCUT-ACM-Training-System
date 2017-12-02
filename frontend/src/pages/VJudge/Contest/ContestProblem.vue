<template>
  <div>
    <div class="row">
      <div class="col l3 s12">
        <div class="card-panel ">
          <problem-submit ref="submitor" :compiler-info="problem_info.compiler_info" @submit-code="onSubmitCode($event)"></problem-submit>
        </div>
        <div class="card-panel ">
          <problem-indicator :contest-id="contest_id" :problem-count="contest_problem.length"></problem-indicator>
        </div>
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
import ProblemSubmit from '@/components/VJudge/ProblemSubmit'
import ProblemIndicator from '@/components/VJudge/Contest/ProblemIndicator'
import { getContestProblem, submitContestJob, getContestJobStatus } from '@/helpers/api/vjudge/contest'
import { toast } from '@/helpers/common'
export default {
  name: 'ContestProblem',
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
      problemIndex: 0,
      job_id: 0,
      is_judging: false,
      updateJobStatusTimerId: 0
    }
  },
  props: ['contest_id', 'contest_problem'],
  components: {
    'problem-info': ProblemInfo,
    'problem-submit': ProblemSubmit,
    'problem-indicator': ProblemIndicator
  },
  created: function () {
    this.problemIndex = this.$route.params.problem_index.charCodeAt(0) - 64
  },
  beforeRouteUpdate: function (to, from, next) {
    this.problemIndex = to.params.problem_index.charCodeAt(0) - 64
    next()
  },
  beforeRouteLeave: function (to, from, next) {
    clearTimeout(this.updateJobStatusTimerId)
    next()
  },
  methods: {
    updateProblemInfo: function (problemIndex, update = true, cache = true) {
      this.problem_info.problem_desc = ''
      getContestProblem(this.contest_id, problemIndex, cache)
        .then(r => {
          r.problem_info.compiler_info = JSON.parse(r.problem_info.compiler_info)
          this.problem_info = r.problem_info
          if (update) {
            this.problemIndex = problemIndex
            this.$router.push({ name: 'vjudge.contest.problem', params: { problem_index: String.fromCharCode(problemIndex + 64) } })
          }
        })
    },
    onSubmitCode: function (data) {
      submitContestJob(this.contest_id, this.problemIndex, data.compiler_id, data.source_code)
      .then(r => {
        this.$refs.submitor.$emit('submited')
        this.job_id = r.job_id
        toast('代码已提交')
        this.updateJobStatus()
      })
    },
    updateJobStatus: function () {
      getContestJobStatus(this.job_id, this.problemIndex, false)
      .then(r => {
        this.$refs.submitor.$emit('jobStatusChange', r.status_info)
        toast('状态已更新')
        if (parseInt(r.status_info.running_status) !== 3) {
          this.updateJobStatusTimerId = setTimeout(() => {
            this.updateJobStatus()
          }, 3000)
        }
      })
    },
    goProblem: function (index) {
      this.$router.push({ name: 'vjudge.contest.problem', params: { contest_id: this.contest_id, problem_index: String.fromCharCode(index + 64) } })
    }
  },
  watch: {
    problemIndex: function (newValue) {
      this.updateProblemInfo(newValue)
    }
  }
}
</script>

<style scoped>
</style>
