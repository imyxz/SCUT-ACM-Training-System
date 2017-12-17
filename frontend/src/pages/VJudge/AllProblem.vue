<template>
<div class="router-container">
  <div class="container">
    <div class="card-panel hoverable">
      <page-indicator :cur-page="page" :max-page="100" @page-change="page = $event"></page-indicator>
      <problem-table :problem-list="problem_list"></problem-table>
    </div>
  </div>
</div>
</template>

<script>
import ProblemTable from '@/components/VJudge/ProblemTable'
import PageIndicator from '@/components/PageIndicator'
import { getAllProblem } from '@/helpers/api/vjudge/problem'
export default {
  name: 'AllProblem',
  data () {
    return {
      page: 0,
      problem_list: []
    }
  },
  components: {
    'problem-table': ProblemTable,
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
    updateProblems: function (page, update = true) {
      getAllProblem(page)
        .then(r => {
          this.problem_list = r.problem_list
          if (update) {
            this.page = page
            this.$router.push({ name: 'vjudge.allProblem', params: { page: page } })
          }
        })
    }
  },
  watch: {
    page: function (newPage) {
      this.updateProblems(newPage)
    }
  }
}
</script>

<style>

</style>
