<template>
  <ul class="pagination">
    <li v-for="index in problemCount" :class="{'active':index==curProblemIndex,'waves-effect':index!=curProblemIndex}" :key="index">
      <a @click="goProblem(index)">{{String.fromCharCode(index+64)}}</a>
    </li>
  </ul>
</template>
<script>
export default {
  name: 'ProblemIndicator',
  props: ['contestId', 'problemCount'],
  data () {
    return {
      curProblemIndex: 0
    }
  },
  methods: {
    goProblem: function (index) {
      this.$router.push({ name: 'vjudge.contest.problem', params: { contest_id: this.contestId, problem_index: String.fromCharCode(index + 64) } })
    }
  },
  created: function () {
    this.curProblemIndex = this.$route.params.problem_index.charCodeAt(0) - 64
  },
  watch: {
    $route: function (newVal) {
      this.curProblemIndex = newVal.params.problem_index.charCodeAt(0) - 64
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
