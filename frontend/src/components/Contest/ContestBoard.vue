<template>
  <div class='card'>
    <div class='card-content not-break' style='overflow-x:auto'>
      <div style='position: absolute;top:10px;left:40px'>
        <input type='checkbox' id='onlyShowInSystem' v-model='onlyShowInSystem' />
        <label for='onlyShowInSystem'>仅显示院队</label>
      </div>
      <contest-board-table :contest-board-info="rank" :problem-count="problemCount"></contest-board-table>
    </div>
  </div>
</template>

<script>
import ContestBoardTable from '@/components/Contest/ContestBoardTable'
export default {
  name: 'ContestBoard',
  props: ['contestBoard', 'problemCount'],
  data () {
    return {
      onlyShowInSystem: false
    }
  },
  created: function () {
  },
  components: {
    'contest-board-table': ContestBoardTable
  },
  computed: {
    rank: function () {
      return this.contestBoard
        .filter(item => {
          if (this.onlyShowInSystem === false) {
            return true
          }
          return item.ac_info.in_system === true
        })
        .map(item => {
          return {
            rank_index: item.rank_index,
            group_name: item.group_name,
            problem_solved: item.problem_solved,
            penalty: item.penalty,
            problems: Object.values(item.ac_info.submission).map(tem => {
              return {
                is_ac: tem.ac,
                is_try: tem.is_try,
                ac_time: tem.ac_time,
                trys: tem.try
              }
            }),
            highlight: item.ac_info.in_system === true
          }
        })
    }
  }
}
</script>

<style>

</style>
