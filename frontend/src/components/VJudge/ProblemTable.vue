<template>
  <table class="highlight bordered" style="font-size: 20px;">
    <thead>
      <tr>
        <th class="center-align">problem id</th>
        <th class="center-align">oj</th>
        <th class="center-align">problem</th>
        <th class="center-align">Title</th>
        <th class="center-align">add time</th>
        <th class="center-align">go</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="problem in problemList" :key="problem.problem_id">
        <td class="center-align">{{ problem.problem_id }}</td>
        <td class="center-align yellow-text text-darken-3">{{ problem.oj_name }}</td>
        <td class="center-align">
          <a @click="goProblem(problem.problem_id)" target="_blank">{{ problem.problem_identity }}</a>
        </td>
        <td class="center-align green-text">{{ problem.problem_title }}</td>
        <td class="center-align">{{ problem.add_time }}</td>
        <td class="center-align">
          <a class="btn-floating" @click="goProblem(problem.problem_id)">
            <i class="material-icons">search</i>
          </a>
        </td>

      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'ProblemTable',
  props: ['problemList'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goProblem (problemId) {
      this.$router.push({ name: 'vjudge.viewProblem', params: { problem_id: problemId } })
    }
  }
}
</script>

<style>

</style>
