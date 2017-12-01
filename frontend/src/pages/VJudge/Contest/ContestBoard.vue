<template>
  <div class='card'>
    <div class='card-content not-break' style='overflow-x:auto'>
      <contest-board-table :contest-board-info="rank" :problem-count="contest_problem.length"></contest-board-table>
    </div>
  </div>
</template>

<script>
import ContestBoardTable from '@/components/Contest/ContestBoardTable'
import { isAcStatusCalPenalty, getAcTime } from '@/helpers/common'
export default {
  name: 'ContestBoard',
  props: ['contest_problem', 'contest_submissions', 'user_id', 'contest_id', 'participants'],
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
    users_status: function () {
      let userStatus = {}
      if (this.participants.length === 0 || this.contest_problem.length === 0) {
        return {}
      }
      this.participants.forEach(person => {
        let newProblem = []
        for (let i = 0; i < this.contest_problem.length; i++) {
          newProblem[i] = {
            is_ac: false,
            ac_time: 0,
            trys: 0,
            is_try: false
          }
        }
        userStatus[person.user_id] = {
          rank_index: 0,
          user_name: person.user_nickname,
          user_id: person.user_id,
          problem_solved: 0,
          penalty: 0,
          problems: newProblem
        }
      })
      this.contest_submissions.forEach(submit => {
        // eslint-disable-next-line
        let [userId, problemIndex, submitTime, acStatus, runJobId] = submit
        let user = userStatus[userId]
        let problem = user.problems[problemIndex - 1]
        if (problem.is_ac === false) {
          if (acStatus === 1) {
            problem.is_ac = true
            problem.ac_time = submitTime
            user.problem_solved++
            user.penalty += submitTime
            problem.is_try = true
          } else if (isAcStatusCalPenalty(acStatus)) {
            problem.trys++
            problem.is_try = true
          }
        }
      })
      console.log(userStatus)
      userStatus = Object.values(userStatus)
      userStatus.forEach(user => {
        user.problems.forEach(problem => {
          if (problem.is_ac === true) {
            user.penalty += this.penalty * problem.trys
          }
        })
      })
      userStatus.sort((a, b) => {
        if (a.problem_solved === b.problem_solved) {
          return a.penalty - b.penalty
        } else {
          return b.problem_solved - a.problem_solved
        }
      })
      let index = 1
      userStatus.forEach(user => {
        user.rank_index = index++
      })
      return userStatus
    },
    rank: function () {
      return Object.values(this.users_status).map(item => {
        return {
          rank_index: item.rank_index,
          group_name: item.user_name,
          problem_solved: item.problem_solved,
          penalty: getAcTime(item.penalty),
          problems: Object.values(item.problems).map(tem => {
            return {
              is_ac: tem.is_ac,
              is_try: tem.is_try,
              ac_time: tem.ac_time,
              trys: tem.trys
            }
          }),
          highlight: item.user_id === this.user_id
        }
      })
    }
  }
}
</script>

<style>

</style>
