<template>
  <div class="row">
    <div class="col l3 s12">
      <div class="card">
        <div class="card-content red lighten-3 white-text auto-break">
          <span class="card-title">{{ ContestData.contest_info.contest_title }}</span>
          <p>{{ ContestData.contest_info.contest_desc }}</p>
        </div>
      </div>
    </div>
    <div class="col l9 s12">
      <div class="card-panel hoverable over-flow-auto" style="position: relative">
        <contest-problem-table :problem-list="ContestData.contest_problem" :problem-sumissions="ContestData.contest_submissions" :user-id="ContestData.user_id" :contest-id="ContestData.contest_id"></contest-problem-table>
      </div>

    </div>
  </div>
</template>

<script>
import ContestProblemTable from '@/components/VJudge/Contest/ContestProblemTable'

export default {
  name: 'ContestInfo',
  props: ['ContestData'],
  data () {
    return {
    }
  },
  components: {
    'contest-problem-table': ContestProblemTable
  },
  created: function () {
  },
  methods: {
  },
  watch: {
  }
}
</script>

<style>

</style>
