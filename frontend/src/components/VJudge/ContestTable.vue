<template>

  <table class="highlight bordered auto-break">
    <thead>
      <tr>
        <th class="center-align">contest id</th>
        <th class="center-align">contest title</th>
        <th class="center-align">contest type</th>
        <th class="center-align">start time</th>
        <th class="center-align">duration</th>
        <th class="center-align">go</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="contest in contests" :key="contest.contest_id">
        <td class="center-align">{{ contest.contest_id }}</td>
        <td class="center-align">
          <a @click="goContest(contest.contest_id)">{{ contest.contest_title }}</a>
        </td>
        <td class="center-align">{{ contest.contest_type | getContestType }}</td>
        <td class="center-align">{{ contest.contest_start_time }}</td>
        <td class="center-align">{{ contest.contest_last_seconds | secToHour }}</td>
        <td class="center-align">
          <a class="btn-floating" @click="goContest(contest.contest_id)" target="_blank">
            <i class="material-icons">search</i>
          </a>
        </td>

      </tr>
    </tbody>
  </table>
</template>

<script>
import {contestType} from '@/helpers/constants/VJudge'
import {secondToHour} from '@/helpers/common'
export default {
  name: 'ContestTable',
  props: ['contests'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goContest (contestId) {
      this.$router.push({ name: 'contest.viewSummary', params: { contest_id: contestId } })
    }
  },
  filters: {
    getContestType: function (val) {
      return contestType[val]
    },
    secToHour: function (val) {
      return secondToHour(val)
    }
  }
}
</script>

<style>

</style>
