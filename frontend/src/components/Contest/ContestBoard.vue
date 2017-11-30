<template>
  <div class='card'>
    <div class='card-content not-break' style='overflow-x:auto'>
      <div style='position: absolute;top:10px;left:40px'>
        <input type='checkbox' id='onlyShowInSystem' v-model='onlyShowInSystem' />
        <label for='onlyShowInSystem'>仅显示院队</label>
      </div>
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
          <tr v-for='team in contestBoard' :class="{'blue lighten-5': team.ac_info.in_system}" v-show='!onlyShowInSystem || team.ac_info.in_system' :key='team.name'>
            <td class='center-align'>{{ team.rank_index }}</td>
            <td class='left-align'>{{ team.group_name }}</td>
            <td class='center-align'>{{ team.problem_solved }}</td>
            <td class='center-align'>{{ team.penalty }}</td>
            <td v-for='(status,index) in team.ac_info.submission' :class="{
                            'green':status.ac,
                            'red':!status.ac && status.is_try}" class='center-align lighten-4' :key='index'>
              <span v-if='status.ac' style='display: block'>
                {{ getAcTime(status.ac_time)}}
              </span>
              <span v-if='status.try!=0' style='display: block'>
                (-{{ status.try }})
              </span>
              <span style='display: block;height: 0;min-width: 60px;'>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
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
