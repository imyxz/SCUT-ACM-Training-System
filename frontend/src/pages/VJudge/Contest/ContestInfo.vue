<template>
  <div class="row">
    <div class="col l3 s12">
      <div class="card">
        <div class="card-content red lighten-3 white-text auto-break">
          <span class="card-title">{{ contest_info.contest_title }}</span>
          <p>{{ contest_info.contest_desc }}</p>
        </div>
      </div>
    </div>
    <div class="col l9 s12">
      <div class="card-panel hoverable over-flow-auto" style="position: relative">
        <contest-problem-table :problem-list="contest_problem" :problem-sumissions="contest_submissions" :user-id="user_id" :contest-id="contest_id"></contest-problem-table>
      </div>

    </div>
  </div>
</template>

<script>
import ContestProblemTable from '@/components/VJudge/Contest/ContestProblemTable'

export default {
  name: 'ContestInfo',
  props: ['contest_info', 'contest_problem', 'contest_submissions', 'user_id', 'contest_id'],
  data () {
    return {
    }
  },
  components: {
    'contest-problem-table': ContestProblemTable
  },
  created: function () {
    console.log(this.$attrs)
  },
  methods: {
  },
  watch: {
  }
}
</script>

<style>

</style>
