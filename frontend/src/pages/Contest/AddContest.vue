<template>
  <div class="container">
    <div class="card">
      <div class="card-content  ">
        <p class="big-text">添加比赛</p>
        <div class="row">
          <div class="col l4">
            <div class="row">
              <div class="input-field col l12">
                <input id="contest_name" type="text" class="validate" v-model="contest_name">
                <label for="contest_name">比赛名称</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col l12">
                <input id="problem_count" type="text" class="validate" v-model.number="problem_count">
                <label for="problem_count">比赛题数
                  <span v-if="problem_count>0"> A-{{ String.fromCharCode(problem_count+64) }}</span>
                </label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col l12">
                <input id="contest_start_date" type="date" class="datepicker" onchange="add_contest.contest_start_date=$(this).val()">
                <label for="contest_start_date">开始日期</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col l12">
                <input id="contest_start_time" type="text" class="timepicker" onchange="add_contest.contest_start_time=$(this).val()">
                <label for="contest_start_time">开始时间</label>
              </div>
            </div>

            <div class="row">
              <div class="input-field col l12">
                <input id="contest_end_date" type="date" class="datepicker" onchange="add_contest.contest_end_date=$(this).val()">
                <label for="contest_end_date">结束日期</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col l12">
                <input id="contest_end_time" type="text" class="timepicker" onchange="add_contest.contest_end_time=$(this).val()">
                <label for="contest_end_time">结束时间</label>
              </div>
            </div>
            <div class="row right-align">
              <a class="waves-effect waves-light btn " @click="submitContest()">保存</a>

            </div>

          </div>
          <div class="col l8">
            <div class="row">
              <div class="input-field col l12">
                <textarea id="contest_desc" class="materialize-textarea" rows="6" v-model="contest_desc"></textarea>
                <label for="contest_desc">比赛简述</label>
              </div>
            </div>
            <div class="row">
              <div class="input-field col l12">
                <textarea id="csv_data" class="materialize-textarea" rows="12" v-model="csv_data"></textarea>
                <label for="csv_data">比赛board</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from '@/helpers/common'
import { addContest } from '@/helpers/api/contest'
import $ from 'jquery'
export default {
  name: 'AddContest',
  data () {
    return {
      page: 0,
      contest_name: '',
      problem_count: '',
      contest_start_date: '',
      contest_start_time: '',
      contest_end_date: '',
      contest_end_time: '',
      csv_data: '',
      contest_desc: ''
    }
  },
  created: function () {
    this.$nextTick(() => {
      $('.datepicker').pickadate({
        selectMonths: true,
        selectYears: 15,
        format: 'yyyy/mm/dd'
      })
      $('.timepicker').pickatime({
        default: 'now',
        fromnow: 0,
        twelvehour: false,
        donetext: 'OK',
        cleartext: '清空',
        canceltext: '关闭',
        autoclose: false,
        ampmclickable: true,
        aftershow: function () { }
      })
    })
  },
  methods: {
    submitContest: function () {
      let startDate = new Date()
      let tmp = this.contest_start_date.split('/')
      startDate.setFullYear(tmp[0], tmp[1], tmp[2])
      tmp = this.contest_start_time.split(':')
      startDate.setHours(tmp[0], tmp[1], 0)
      let endDate = new Date()
      tmp = this.contest_end_date.split('/')
      endDate.setFullYear(tmp[0], tmp[1], tmp[2])
      tmp = this.contest_end_time.split(':')
      endDate.setHours(tmp[0], tmp[1], 0)
      let contestStarttime = (startDate.getTime() / 1000).toFixed(0)
      let contestEndtime = (endDate.getTime() / 1000).toFixed(0)
      toast('正在提交数据', 5000)
      addContest(this.contest_name, this.contest_desc, this.problem_count, contestStarttime, contestEndtime, this.csv_data)
        .then(r => {
          toast('提交成功!')
          setTimeout(() => {
            this.$router.push({ name: 'contest.viewSummary', params: { contest_id: r.contest_id } })
          }, 1000)
        })
    }
  }
}
</script>

<style>

</style>
