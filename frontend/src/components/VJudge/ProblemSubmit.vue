<template>
  <div>
    <button class="btn blue btn-float" style="width: 90%" @click="openCodeModal">提交</button>
    <div ref="code_modal" class="modal modal-fixed-footer" style="width:90%;overflow: visible;">
      <div class="modal-content">
        <div ref="editor" class="editor"></div>
      </div>
      <div class="modal-footer">
        <template v-if="!submited">
          <div class="row" key="div_1">
            <div class="col l3">
              <select ref="select_compiler">
                <option value="0" disabled selected>选择编译器</option>
                <option v-for="(name,id) in compilerInfo" :value="id" :key="id">{{ name }}</option>
              </select>
            </div>
            <div class="col l9">
              <button class="waves-effect waves-green btn blue" :class="{'disabled':compilerId==-1}" @click="submitCode">提交</button>
            </div>
          </div>
        </template>
        <template v-if="submited">
          <div class="row" key="div_2" style="font-size: 20px; padding:8px;">
            <div class="col l4 left-align">
              <span class="" style="margin:auto">时间：{{ jobStatus.time_usage | time_filter }}</span>
            </div>
            <div class="col l4 left-align">
              <span class="" style="margin:auto" :class="{'green-text':jobStatus.ac_status==1,'red-text':jobStatus.ac_status!=1}">状态：{{ jobStatus.wrong_info }}</span>

            </div>
            <div class="col l4 right-align">
              <span class="" style="margin:auto">内存：{{ jobStatus.ram_usage | ram_filter }}</span>

            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { ramFilter, timeFilter } from '@/helpers/common'
import $ from 'jquery'
export default {
  name: 'ProblemSubimit',
  props: ['compilerInfo'],
  data () {
    return {
      submited: false,
      editor: {},
      compilerId: -1,
      jobStatus: {
        ac_status: 0,
        wrong_info: ''
      }
    }
  },
  created: function () {
  },
  methods: {
    openCodeModal: function () {
      this.submited = false
      this.compilerId = -1
      this.$nextTick(function () {
        $('select').material_select()
        $(this.$refs.code_modal).modal('open')
        let that = this
        this.$refs.select_compiler.onchange = function (e) {
          that.compilerId = this.value
        }
      })
    },
    submitCode: function () {
      this.$emit('submit-code', {
        source_code: this.editor.getValue(),
        compiler_id: this.compilerId
      })
    }
  },
  filters: {
    ram_filter: (val) => ramFilter(val),
    time_filter: (val) => timeFilter(val)
  },
  mounted: function () {
    $('.modal').modal()
    // eslint-disable-next-line
    ace.require('ace/ext/language_tools')
    // eslint-disable-next-line
    this.editor = ace.edit(this.$refs.editor)
    this.editor.getSession().setMode('ace/mode/c_cpp')
    this.editor.setTheme('ace/theme/twilight')
    this.editor.setFontSize(16)
    this.editor.setOptions({
      enableBasicAutocompletion: true,
      enableSnippets: true,
      enableLiveAutocompletion: true
    })
    this.$on('submited', event => {
      this.submited = true
      $(this.$refs.select_compiler).hide()
    })
    this.$on('jobStatusChange', event => {
      this.jobStatus = event
    })
  }
}
</script>

<style scoped>
.editor {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>
