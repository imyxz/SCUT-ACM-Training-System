<template>
  <div class="row">
    <div class="l12">
      <code-editor ref="editor" :code-type="codeType"></code-editor>
      <run-modal ref="run_modal" :running_status="running_status" :output_code="output_code" :error_code="error_code"></run-modal>
      <div class="button-run">
        <a class="btn-floating btn-large waves-effect waves-light green ">
          <i class="material-icons" @click="openRunModal">play_arrow</i>
        </a>
      </div>
      <div class="fixed-action-btn horizontal button-tools">
        <a class="btn-floating btn-large blue">
          <i class="large material-icons">toc</i>
        </a>
        <ul>
          <li>
            <a class="btn-floating waves-effect waves-light green lighten-1 ">
              <i class="material-icons">settings</i>
            </a>
          </li>
          <li>
            <a class="btn-floating  waves-effect waves-light blue ">
              <i class="material-icons">save</i>
            </a>
          </li>
          <li>
            <a class="btn-floating waves-effect waves-light yellow darken-4 ">
              <i class="material-icons">spellcheck</i>
            </a>
          </li>
          <li>
            <a class="btn-floating  waves-effect waves-light red ">
              <i class="material-icons">share</i>
            </a>
          </li>

        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import CodeEditor from '@/components/VJudge/CodeEditor'
import RunModal from '@/components/VJudge/OnlineIDE/RunModal'
// import { getProblemInfo, getProblemAcRank, submitCode, getJobStatus } from '@/helpers/api/vjudge/problem'
// import { toast } from '@/helpers/common'
export default {
  name: 'OnlineIDE',
  data () {
    return {
      codeType: 'c_cpp',
      running_status: 0,
      output_code: '',
      error_code: ''
    }
  },
  components: {
    'code-editor': CodeEditor,
    'run-modal': RunModal
  },
  created: function () {
    // this.problem_id = this.$route.params.problem_id
  },
  beforeRouteUpdate: function (to, from, next) {
    // this.problem_id = to.params.problem_id
    next()
  },
  beforeRouteLeave: function (to, from, next) {
    // clearTimeout(this.updateJobStatusTimerId)
    next()
  },
  methods: {
    openRunModal: function () {
      this.$refs.run_modal.$emit('openModal')
    }
  },
  watch: {

  }
}
</script>

<style scoped>
.button-tools {
  position: absolute;
  right: 50px;
  bottom: 80px;
}
.button-run {
  position: absolute;
  right: 50px;
  bottom: 150px;
}
</style>
