<template>
  <div ref="modal_setting" class="modal bottom-sheet">
    <div class="modal-content">
      <div class="row">
        <div class="col l11">
          <p>选择编程语言：</p>
          <template v-for="(type, index) in codeTypes">
            <input type="radio" :value="index" :id="'code-type-' + index" v-model="code_real_type" :key="'input' + index" />
            <label :for="'code-type-' + index" class="green-text text-lighten-2 medium-text" :key="'label' + index">{{type.name}}</label>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import $ from 'jquery'
import {codeType} from '@/helpers/constants/VJudge'
export default {
  name: 'SettingModal',
  props: ['code_type'],
  data () {
    return {
      codeTypes: codeType,
      code_real_type: 1
    }
  },
  created: function () {
  },
  methods: {
  },
  filters: {
  },
  mounted: function () {
    $(this.$refs.modal_setting).modal()
    this.$on('openModal', event => {
      $(this.$refs.modal_setting).modal('open')
    })
    this.$on('closeModal', event => {
      $(this.$refs.modal_setting).modal('close')
    })
  },
  watch: {
    code_real_type: function (newVal) {
      this.$emit('update:code_type', newVal)
    },
    code_type: function (newVal) {
      this.code_real_type = newVal
    }
  }
}
</script>

<style scoped>

</style>
