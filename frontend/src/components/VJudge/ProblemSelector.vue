<template>
  <table class="highlight bordered" style="font-size: 20px">
    <thead>
      <tr>
        <th class="center-align">problem id</th>
        <th class="center-align">Title</th>
        <th class="center-align">Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(problem,index) in problem_list" :key="index">
        <td class="center-align">
          <a :href="problem.problem_id | problemUrl" target="_blank">{{ problem.problem_id }}</a>
        </td>
        <td class="center-align green-text">{{ problem.problem_title }}</td>
        <td class="center-align">
          <a @click="swapPosition(index,'up')">
            <i class="material-icons">arrow_upward</i>
          </a>
          <a @click="swapPosition(index,'down')">
            <i class="material-icons">arrow_downward</i>
          </a>
          <a @click="deletePosition(index)">
            <i class="material-icons red-text">delete</i>
          </a>
        </td>
      </tr>
      <tr>
        <td class="center-align" colspan="1">
          <div class="input-field">
            <input id="problem_selector_problem_id" type="text" v-model="new_problem_id" @blur="getProblemIDByInfo(new_problem_id)" @keyup.enter="focusTitle()">
            <label for="problem_selector_problem_id">Problem ID 或 oj/id</label>
          </div>
        </td>
        <td class="center-align">
          <div class="input-field">
            <input id="problem_selector_problem_title" type="text" v-model="new_problem_title">
            <label for="problem_selector_problem_title">Problem Title</label>
          </div>
        </td>
        <td class="center-align">
          <a @click="addProblem()">
            <i class="material-icons" style="font-size: 40px">add_circle</i>
          </a>
        </td>

      </tr>
    </tbody>
  </table>
</template>

<script>
import {getProblemByInfo, getProblemInfo} from '@/helpers/api/vjudge/problem'
import {toast} from '@/helpers/common'
import $ from 'jquery'
export default {
  name: 'ProblemSelector',
  data () {
    return {
      problem_list: [],
      new_problem_id: '',
      new_problem_title: ''
    }
  },
  created: function () {
  },
  methods: {
    swapPosition: function (first, where) {
      let second = 0
      where === 'up' ? (second = first - 1) : (second = first + 1)
      if (first < 0 || second < 0 || first >= this.problem_list.length || second >= this.problem_list.length) {
        return
      }
      let target = this.$data.problem_list
      let tmp = target[first]
      this.$set(this.problem_list, first, target[second])
      this.$set(this.problem_list, second, tmp)
    },
    deletePosition: function (pos) {
      this.$delete(this.problem_list, pos)
    },
    updateProblemTitle: function () {
      getProblemInfo(this.new_problem_id)
      .then(r => {
        this.new_problem_title = r.problem_info.problem_title
      })
    },
    getProblemIDByInfo: function (id) {
      if (id === '') {
        return
      }
      let pos1 = id.indexOf('/', -1)
      if (pos1 >= 0) {
        let ojName = id.substr(0, pos1)
        let problemName = id.substr(pos1 + 1, id.length - pos1 - 1)
        getProblemByInfo(ojName, problemName)
        .then(r => {
          this.new_problem_id = r.problem_id
          this.updateProblemTitle()
        })
      } else {
        this.updateProblemTitle()
      }
    },
    focusTitle: function () {
      $('#problem_selector_problem_title').focus()
    },
    addProblem: function () {
      if (this.new_problem_id === '' || this.new_problem_title === '') {
        toast('请填写完全相关信息')
        return
      }
      this.$set(this.problem_list, this.problem_list.length, {
        problem_id: this.new_problem_id,
        problem_title: this.new_problem_title
      })
      this.new_problem_id = ''
      this.new_problem_title = ''
    },
    getProblemList: function () {
      return this.problem_list
    },
    setProblemList: function (list) {
      this.problem_list = list
    }
  },
  mounted: function () {
  },
  filters: {
    problemUrl: function (problemId) {
      return window.location.origin + '/vJudge/problem/' + problemId
    }
  }
}
</script>

<style>

</style>
