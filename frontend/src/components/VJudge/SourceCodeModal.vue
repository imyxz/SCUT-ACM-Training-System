<template>
  <div ref="code_modal" class="modal" style="width:90%;overflow: visible;height:100%">
    <div class="modal-content">
      <div ref="editor" class="editor"></div>
    </div>
  </div>
</template>

<script>
import $ from 'jquery'
export default {
  name: 'SourceCodeModal',
  props: [],
  data () {
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
