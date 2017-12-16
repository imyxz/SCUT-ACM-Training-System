<template>
  <div ref="editor" class="editor"></div>
</template>

<script>
import {getCodeTypeMode} from '@/helpers/constants/VJudge'
export default {
  name: 'CodeEditor',
  props: ['codeType'],
  data () {
    return {
      editor: {}
    }
  },
  created: function () {
  },
  methods: {
    setCode (newCode) {
      this.editor.setValue(newCode, -1)
    },
    getCode () {
      return this.editor.getValue()
    }
  },
  filters: {
  },
  mounted: function () {
    // eslint-disable-next-line
    ace.require('ace/ext/language_tools')
    // eslint-disable-next-line
    this.editor = ace.edit(this.$refs.editor)
    if (this.codeType >= 0) {
      this.editor.getSession().setMode('ace/mode/' + getCodeTypeMode(this.codeType))
    }
    this.editor.setTheme('ace/theme/twilight')
    this.editor.setFontSize(16)
    this.editor.setOptions({
      enableBasicAutocompletion: true,
      enableSnippets: true,
      enableLiveAutocompletion: true
    })
  },
  watch: {
    codeType: function (newType) {
      if (newType >= 0) {
        this.editor.getSession().setMode('ace/mode/' + getCodeTypeMode(newType))
      }
    }
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
