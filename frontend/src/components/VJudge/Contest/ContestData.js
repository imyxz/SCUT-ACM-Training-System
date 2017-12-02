import Vue from 'vue'
import {
  isAcStatusCalPenalty,
  getAcTime
} from '@/helpers/common'
const PENALTY = 20 * 60
export default function () {
  return new Vue({
    name: 'ContestData',
    data () {
      return {
        contest_info: {
          contest_title: '',
          contest_start_time_ts: '',
          contest_start_time: '',
          contest_last_seconds: '',
          contest_id: '',
          contest_desc: '',
          contest_type: ''
        },
        contest_submissions: [],
        contest_problem: [],
        participants: [],
        running_time: 0,
        contest_id: 0,
        need_participant: false,
        user_id: 0,
        err_info: ''
      }
    },
    computed: {
      problemInfo: function () {
        if (this.contest_problem.length === 0) {
          return []
        }
        let ret = []
        this.contest_problem.forEach(problem => {
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
        this.contest_submissions.forEach((submit) => {
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
      },
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
        userStatus = Object.values(userStatus)
        userStatus.forEach(user => {
          user.problems.forEach(problem => {
            if (problem.is_ac === true) {
              user.penalty += PENALTY * problem.trys
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
  })
}
