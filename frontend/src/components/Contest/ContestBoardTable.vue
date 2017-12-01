<template>
  <table class='highlight bordered medium-text'>
    <thead>
      <tr>
        <th class='center-align'>Rank</th>
        <th class='left-align'>Team</th>
        <th class='center-align'>Solve</th>
        <th class='center-align'>Penalty</th>
        <th class='center-align' v-for='x in problemCount' :key='x'>{{ String.fromCharCode(x+64) }}</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for='team in contestBoardInfo' :class="{'blue lighten-5': team.highlight}" :key='team.name'>
        <td class='center-align'>{{ team.rank_index }}</td>
        <td class='left-align'>{{ team.group_name }}</td>
        <td class='center-align'>{{ team.problem_solved }}</td>
        <td class='center-align'>{{ team.penalty }}</td>
        <td v-for='(problem,index) in team.problems' :class="{
                            'green':problem.ac,
                            'red':!problem.ac && problem.is_try}" class='center-align lighten-4' :key='index'>
          <span v-if='problem.ac' style='display: block'>
            {{ getAcTime(problem.ac_time)}}
          </span>
          <span v-if='problem.try!=0' style='display: block'>
            (-{{ problem.try }})
          </span>
          <span style='display: block;height: 0;min-width: 60px;'>
          </span>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'ContestBoardTable',
  props: ['contestBoardInfo', 'problemCount'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    getAcTime: function (sec) {
      let second, minute, hour
      second = sec % 60
      sec /= 60
      sec = Math.floor(sec)
      minute = sec % 60
      sec /= 60
      sec = Math.floor(sec)
      hour = sec
      return this.addZero(hour) + ':' + this.addZero(minute) + ':' + this.addZero(second)
    },
    addZero: function (val) {
      if (val === 0) {
        return '00'
      } else if (val < 10) {
        return '0' + val.toString()
      } else {
        return val.toString()
      }
    }
  }
}
</script>

<style>

</style>
