<template>
  <div class="row">
    <div class="l12">
      <code-editor ref="editor" :code-type="codeType"></code-editor>
      <run-modal ref="run_modal" :running_status="running_status" :output_code="output_code" :error_code="error_code"></run-modal>
      <save-modal ref="save_modal" :drafts="drafts" :auto_saves="auto_saves"></save-modal>
      <setting-modal ref="setting_modal" :code_type="codeType" @update:code_type="val => codeType = val"></setting-modal>
      <share-modal ref="share_modal"></share-modal>
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
              <i class="material-icons" @click="openSettingModal">settings</i>
            </a>
          </li>
          <li>
            <a class="btn-floating  waves-effect waves-light blue ">
              <i class="material-icons" @click="openSaveModal">save</i>
            </a>
          </li>
          <li>
            <a class="btn-floating waves-effect waves-light yellow darken-4 ">
              <i class="material-icons" @click="formatCode">spellcheck</i>
            </a>
          </li>
          <li>
            <a class="btn-floating  waves-effect waves-light red ">
              <i class="material-icons" @click="shareCode">share</i>
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
import SaveModal from '@/components/VJudge/OnlineIDE/SaveModal'
import SettingModal from '@/components/VJudge/OnlineIDE/SettingModal'
import ShareModal from '@/components/VJudge/OnlineIDE/ShareModal'
import { submitJob, getJobResult, getUserDraft, getDraftCode, formatCode, getCodeTypeDefaultCode, shareCode, getShareCode, saveDraft } from '@/helpers/api/vjudge/onlineide'
import { toast, ramFilter, timeFilter } from '@/helpers/common'
import {getJobSourceCode} from '@/helpers/api/vjudge/problem'
export default {
  name: 'OnlineIDE',
  data () {
    return {
      codeType: 1,
      running_status: '',
      output_code: '',
      error_code: '',
      cur_job_id: 0,
      autoSaveTimerId: 0,
      drafts: [],
      auto_saves: [],
      old_code: ''
    }
  },
  components: {
    'code-editor': CodeEditor,
    'run-modal': RunModal,
    'save-modal': SaveModal,
    'setting-modal': SettingModal,
    'share-modal': ShareModal
  },
  created: function () {
    // this.problem_id = this.$route.params.problem_id
  },
  beforeRouteUpdate: function (to, from, next) {
    // this.problem_id = to.params.problem_id
    next()
  },
  beforeRouteLeave: function (to, from, next) {
    clearTimeout(this.autoSaveTimerId)
    next()
  },
  mounted: function () {
    this.$refs.run_modal.$on('submitJob', inputCode => {
      this.running_status = '正在提交'
      submitJob(this.$refs.editor.getCode(), inputCode, this.codeType)
        .then(r => {
          this.cur_job_id = r.job_id
          this.error_code = ''
          this.output_code = ''
          this.$refs.run_modal.$emit('changeTab', 'output_div')
          this.updateJobStatus()
          toast('提交任务成功')
        })
    })
    this.$refs.save_modal.$on('readCode', draftId => {
      getDraftCode(draftId)
        .then(r => {
          this.$refs.editor.setCode(r.code)
          this.$refs.save_modal.$emit('closeModal')
          toast('已读取代码')
          this.old_code = r.code
        })
    })
    this.$refs.save_modal.$on('saveCode', draftTitle => {
      this.saveDraft(draftTitle, false)
    })
    this.updateUserDraft(false)
    if (this.$route.query.shareCode !== undefined) {
      this.readShareCode(this.$route.query.shareCode)
    } else if (this.$route.query.jobCode !== undefined) {
      this.readJobCode(this.$route.query.jobCode)
    } else {
      this.readDefaultCode(this.codeType)
    }
    this.autoSaveTimerId = setTimeout(this.autoSave, 60000)
  },
  methods: {
    openRunModal: function () {
      this.$refs.run_modal.$emit('openModal')
    },
    openSaveModal: function () {
      this.$refs.save_modal.$emit('openModal')
    },
    openSettingModal: function () {
      this.$refs.setting_modal.$emit('openModal')
    },
    updateJobStatus: function () {
      getJobResult(this.cur_job_id, false)
        .then(r => {
          let result = r.result
          let tips = ''
          let continueUpdate = true
          switch (parseInt(result.job_status)) {
            case 0:
            case 1:
              this.running_status = '正在排队中'
              tips = '正在排队中'
              break
            case 2:
              this.running_status = '正在执行'
              tips = '正在执行'
              break
            case 3:
              this.running_status = '运行完成  时间：' + timeFilter(result.job_info.time_usage) + '内存： ' + ramFilter(result.job_info.mem_usage)
              this.output_code = result.job_info.program_stdout
              this.error_code = result.job_info.compile_error
              tips = '运行完成'
              continueUpdate = false
              if (parseInt(result.job_info.compile_state) !== 0) {
                this.$refs.run_modal.$emit('changeTab', 'error_div')
              }
              break
          }
          toast(tips)
          if (continueUpdate) {
            this.updateJobStatusTimerId = setTimeout(() => {
              this.updateJobStatus()
            }, 1000)
          }
        })
    },
    formatCode: function () {
      formatCode(this.$refs.editor.getCode(), this.codeType)
        .then(r => {
          this.$refs.editor.setCode(r.format_code)
          toast('格式化代码成功')
        })
    },
    readDefaultCode: function (codeType) {
      getCodeTypeDefaultCode(codeType)
        .then(r => {
          this.$refs.editor.setCode(r.code)
        })
    },
    shareCode: function () {
      shareCode(this.$refs.editor.getCode(), this.codeType)
        .then(r => {
          this.$refs.share_modal.$emit('openModal', window.location.origin + this.$route.path + '?shareCode=' + r.share_id)
        })
    },
    readShareCode: function (shareId) {
      getShareCode(shareId)
        .then(r => {
          this.$refs.editor.setCode(r.source_code)
          this.codeType = r.code_type
          toast('读取代码成功')
        })
    },
    readJobCode: function (jobId) {
      getJobSourceCode(jobId)
        .then(r => {
          this.$refs.editor.setCode(r.source_code)
          toast('读取代码成功')
        })
    },
    updateUserDraft: function (cache = false) {
      getUserDraft(cache)
        .then(r => {
          this.drafts = r.drafts
          this.auto_saves = r.autosave
        })
    },
    autoSave: function () {
      if (this.$refs.editor.getCode() !== this.old_code) {
        this.old_code = this.$refs.editor.getCode()
        this.saveDraft(' ', true)
      }
      this.autoSaveTimerId = setTimeout(this.autoSave, 60000)
    },
    saveDraft: function (draftTitle, isAutoSave) {
      saveDraft(draftTitle, this.$refs.editor.getCode(), this.codeType, isAutoSave)
        .then(r => {
          toast('已保存代码')
          this.updateUserDraft(false)
        })
    }
  },
  watch: {
    codeType: function (newVal) {

    }
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
