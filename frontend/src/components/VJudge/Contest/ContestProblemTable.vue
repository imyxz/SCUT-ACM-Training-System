<template>
  <table class="highlight bordered" style="font-size: 20px;">
    <thead>
      <tr>
        <th class="center-align">AC状态</th>
        <th class="center-align">State</th>
        <th class="center-align">#</th>
        <th class="left-align">Title</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="problem in problemInfo" :key="problem.info.problem_index">
        <td class="center-align">
          <span v-show="problem.status.is_try" :class="{'yellow-text text-darken-3':!problem.status.is_ac,'green-text text-darken-2':problem.status.is_ac}" style="font-size: 20px;" data-badge-caption="">
            {{problem.status.is_ac?'Accepted':'Try'}}
          </span>
        </td>
        <td class="center-align">{{ problem.status.percentage }}</td>
        <td class="center-align">{{String.fromCharCode(problem.info.problem_index+64)}}</td>
        <td class="left-align blue-text">
          <a @click="goProblem(problem.info.problem_index)">{{ problem.info.problem_title }}</a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import { contestType } from '@/helpers/constants/VJudge'
import { secondToHour } from '@/helpers/common'
export default {
  name: 'ProblemTable',
  props: ['problemList', 'problemSumissions', 'userId', 'contestId'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goProblem: function (index) {
      this.$router.push({ name: 'vjudge.contest.problem', params: { contest_id: this.contestId, problem_index: String.fromCharCode(index + 64) } })
    }
  },
  computed: {
    problemInfo: function () {
      if (this.problemList.length === 0) {
        return []
      }
      let ret = []
      this.problemList.forEach(problem => {
        ret.push({
          info: problem,
          status: {
            'ac': 0,
            'trys': 0,
            'is_ac': false,
            'is_try': false,
            'percentage': ''
          }
        })
      })
      this.problemSumissions.forEach((submit) => {
        // eslint-disable-next-line
        let [userId, problemIndex, submitTime, acStatus, runJobId] = submit
        this.$set(ret[problemIndex - 1].status, 'trys', ret[problemIndex - 1].status.trys + 1)
        if (acStatus === 1) {
          this.$set(ret[problemIndex - 1].status, 'ac', ret[problemIndex - 1].status.ac + 1)
          if (this.userId === userId) {
            this.$set(ret[problemIndex - 1].status, 'is_ac', true)
          }
        }
        if (this.userId === userId) {
          this.$set(ret[problemIndex - 1].status, 'is_try', true)
        }
      })
      ret.forEach(problem => {
        let tmp = ''
        if (problem.status.trys !== 0) {
          tmp = (problem.status.ac + '/' + problem.status.trys) + ' (' + (problem.status.ac / problem.status.trys * 100).toFixed(2) * 100 / 100 + '%)'
        }
        this.$set(problem.status, 'percentage', tmp)
      })
      return ret
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
