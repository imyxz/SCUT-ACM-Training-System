<template>
  <div ref="result-modal" class="modal modal-fixed-footer black white-text" style="overflow: visible;">
    <div class="modal-content " style="padding:0;overflow: visible;">
      <ul class="tabs black white-text tabs-fixed-width">
        <li class="tab col s4">
          <a class="active" href="#input_div">输入数据</a>
        </li>
        <li class="tab col s4">
          <a href="#output_div">输出结果</a>
        </li>
        <li class="tab col s4">
          <a href="#error_div">编译器输出</a>
        </li>
      </ul>

      <div ref="input_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="input_code" rows="100" style="height: 100%" v-model="input_code"></textarea>
        <label for="input_code"></label>
      </div>
      <div ref="output_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="output_code" rows="100" style="height: 100%" v-model="output_code"></textarea>
        <label for="output_code"></label>
      </div>
      <div ref="error_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="error_code" rows="100" style="height: 100%" v-model="error_code"></textarea>
        <label for="error_code"></label>
      </div>

    </div>
    <div class="modal-footer black white-text" ref="modal_footer">
      <div class="row" key="div_2" style="font-size: 20px; ">
        <div class="col l8 left-align" style="padding:8px;">
          <span>状态：{{ running_status }}</span>
        </div>

        <div class="col l4 right-align">
          <a href="#" class="btn waves-effect waves-blue" @click="submitJob">提交运行</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import $ from 'jquery'
export default {
  name: 'RunModal',
  props: [],
  data() {
    return {
      editor: {}
    }
  },
  created: function () {
  },
  methods: {
  },
  filters: {
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
    this.$on('openModal', event => {
      this.editor.getSession().setMode('ace/mode/' + event.code_type)
      this.editor.setValue(event.source_code, -1)
      $(this.$refs.code_modal).modal('open')
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
