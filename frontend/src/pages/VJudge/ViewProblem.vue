<template>
  <div>
    <div class="row">
      <div class="col l3 s12">
        <div class="card-panel ">

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
import { getProblemInfo, getProblemAcRank } from '@/helpers/api/vjudge/problem'
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
        oj_name: ''
      },
      problem_id: 0,
      problem_rank: [],
      problem_tags: []
    }
  },
  components: {
    'problem-info': ProblemInfo,
    'problem-ac-rank': ProblemAcRank,
    'problem-tags': ProblemTags
  },
  created: function () {
    this.problem_id = this.$route.params.problem_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.problem_id = to.params.problem_id
    next()
  },
  methods: {
    updateProblemInfo: function (problemID, update = true, cache = true) {
      getProblemInfo(problemID, cache)
        .then(r => {
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
