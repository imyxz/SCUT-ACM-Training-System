<template>
<div>
  <ul class="collapsible popout" data-collapsible="accordion" ref="problem_list">
    <li v-for="(problem,index) in problemInfo" :key="index" :data-problem-index="index + 1">
      <div class="collapsible-header" :class="getColor(problem) + (index + 1==curProblemIndex?' active':'')">
        <i class="material-icons" style="font-size:2em">{{problem | getIcon}}</i>
        <span style="font-size:1.3em">{{String.fromCharCode(problem.info.problem_index + 64) + '.' + problem.info.problem_title }}</span>
      </div>
      <div class="collapsible-body white">
        <span style="font-size:1.5em">Status: {{problem.status.percentage}}</span>
        <ul class="collection">
          <li class="collection-item" style="font-size:1.3em" v-for="submit in problem.userSubmissions" :key="submit.jobId"><div><span :class="submit.acStatus | getColorByStatus">{{ submit.acStatus | getAcStatus }}</span> - {{submit.submitTime | getAcTime}}<a class="secondary-content" @click="displaySourceCode(submit.jobId)" style="cursor: pointer"><i class="material-icons">search</i></a></div></li>
        </ul>
      </div>
    </li>
  </ul>
</div>
</template>
<script>
import $ from 'jquery'
import {getAcTime} from '@/helpers/common'
import { getAcStatus } from '@/helpers/constants/VJudge'
export default {
  name: 'ProblemIndicator',
  props: ['contestId', 'problemCount', 'problemInfo'],
  data () {
    return {
      curProblemIndex: 0
    }
  },
  methods: {
    goProblem: function (index) {
      this.$router.push({ name: 'vjudge.contest.problem', params: { contest_id: this.contestId, problem_index: String.fromCharCode(index + 64) } })
    },
    getColor: function (problem) {
      let isAc = problem.status.is_ac
      let isTry = problem.status.is_try
      let addon = ''
      if (isAc) {
        return 'green-text text-lighten-2' + addon
      } else if (isTry) {
        return 'yellow-text text-darken-3' + addon
      } else {
        return 'grey-text text-darken-1' + addon
      }
    },
    displaySourceCode (jobId) {
      this.$emit('viewSourceCode', jobId)
    }
  },
  created: function () {
    this.curProblemIndex = this.$route.params.problem_index.charCodeAt(0) - 64
    this.$nextTick(() => {
      $(this.$refs.problem_list).collapsible({
        accordion: false, // A setting that changes the collapsible behavior to expandable instead of the default accordion style
        onOpen: el => {
          this.curProblemIndex = parseInt(el[0].dataset.problemIndex)
          this.goProblem(parseInt(el[0].dataset.problemIndex))
        } // 回调当开启开启时
      })
    })
  },
  watch: {
    $route: function (newVal) {
      this.curProblemIndex = newVal.params.problem_index.charCodeAt(0) - 64
    },
    problemInfo: function (newVal) {
      this.$nextTick(() => {
        $(this.$refs.problem_list).collapsible({
          accordion: false, // A setting that changes the collapsible behavior to expandable instead of the default accordion style
          onOpen: el => {
            this.curProblemIndex = parseInt(el[0].dataset.problemIndex)
            this.goProblem(parseInt(el[0].dataset.problemIndex))
          } // 回调当开启开启时
        })
      })
    }
  },
  filters: {
    getIcon: function (problem) {
      let isAc = problem.status.is_ac
      let isTry = problem.status.is_try
      if (isAc) {
        return 'done'
      } else if (isTry) {
        return 'info_outline'
      } else {
        return 'query_builder'
      }
    },
    getAcTime: val => getAcTime(val),
    getColorByStatus: function (status) {
      switch (parseInt(status)) {
        case 15: return 'grey-text text-darken-1'
        case 1: return 'green-text text-lighten-2'
        default: return 'yellow-text text-darken-3'
      }
    },
    getAcStatus: status => getAcStatus(status)
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
