<template>
  <div class="router-container">
    <div class="row">
      <div class="col l3 s12">
        <div class="card">
          <div class="card-content  ">
            <span class="card-title">比赛标题</span>
            <div class="input-field">
              <input id="contest_title" type="text" v-model="contest_title">
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-content ">
            <span class="card-title">比赛描述</span>
            <div class="input-field">
              <textarea id="contest_desc" class="materialize-textarea" rows="6" v-model="contest_desc"></textarea>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-content ">
            <div class="row">
              <div class=" col l12">
                <label>比赛开始时间:</label>
                <input id="contest_start_time" type="datetime-local" class="validate no-margin" placeholder="">

              </div>
            </div>
            <div class="row">
              <div class=" col l12">
                <label>持续时间(小时):</label>
                <input id="contest_last_time" type="number" class="validate no-margin" placeholder="">
              </div>
            </div>
            <form action="#">
              <p>
                <label>比赛类型</label>
              </p>
              <input name="contest_type" type="radio" id="radio-1" value="NormalContest" checked/>
              <label for="radio-1">统一开始</label>
              <input name="contest_type" type="radio" id="radio-2" value="FlexibleContest" />
              <label for="radio-2">自由开始</label>
            </form>

          </div>
        </div>
      </div>
      <div class="col l9 s12">
        <div class="card-panel hoverable">
          <problem-selector ref="problem_selector"></problem-selector>
          <div class="row" style="margin-top:16px;">
            <div class="right-align">
              <button class="btn" @click="addContest">提交保存</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProblemSelector from '@/components/VJudge/ProblemSelector'
import { newContest } from '@/helpers/api/vjudge/contest'
import { toast } from '@/helpers/common'
import $ from 'jquery'
export default {
  name: 'AddContest',
  data () {
    return {
      contest_title: '',
      contest_desc: ''
    }
  },
  components: {
    'problem-selector': ProblemSelector
  },
  created: function () {
  },
  beforeRouteUpdate: function (to, from, next) {
  },
  methods: {
    addContest: function () {
      let startTime = (Date.parse($('#contest_start_time').val()) / 1000).toFixed(0)
      let lastTime = $('#contest_last_time').val() * 60 * 60
      let type = $("input[name='contest_type']:checked").val()
      newContest(this.contest_title, this.contest_desc, this.$refs.problem_selector.getProblemList(), startTime, lastTime, type)
        .then(r => {
          toast('新建比赛成功')
          setTimeout(() => {
            this.$router.push({ name: 'vjudge.contest.info', params: { contest_id: r.contest_id } }, 1000)
          })
        })
    }
  },
  watch: {
  },
  mounted: function () {
  }
}
</script>

<style>

</style>
