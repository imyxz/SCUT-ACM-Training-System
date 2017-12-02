<template>
  <div class='card'>
    <div class='card-content not-break' style='overflow-x:auto'>
      <contest-board-table :contest-board-info="ContestData.rank" :problem-count="ContestData.contest_problem.length"></contest-board-table>
    </div>
  </div>
</template>

<script>
import ContestBoardTable from '@/components/Contest/ContestBoardTable'
export default {
  name: 'ContestBoard',
  props: ['ContestData'],
  data () {
    return {
      penalty: 20 * 60
    }
  },
  created: function () {
  },
  components: {
    'contest-board-table': ContestBoardTable
  },
  computed: {
  }
}
</script>

<style>

</style>
