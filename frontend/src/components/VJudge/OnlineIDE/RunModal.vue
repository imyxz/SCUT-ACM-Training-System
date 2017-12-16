<template>
  <div ref="result_modal" class="modal modal-fixed-footer black white-text" style="overflow: visible;">
    <div class="modal-content " style="padding:0;overflow: visible;">
      <ul class="tabs black white-text tabs-fixed-width" ref="tabs">
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

      <div id="input_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="input_code"style="height: 100%" v-model="input_code"></textarea>
        <label for="input_code"></label>
      </div>
      <div id="output_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="output_code" style="height: 100%" v-model="output_code" readonly="readonly"></textarea>
        <label for="output_code"></label>
      </div>
      <div id="error_div" class="col s12 input-field" style="height: 90%">
        <textarea ref="error_code" style="height: 100%" v-model="error_code" readonly="readonly"></textarea>
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
  props: ['running_status', 'output_code', 'error_code'],
  data () {
    return {
      input_code: ''
    }
  },
  created: function () {
  },
  methods: {
    submitJob: function () {
      this.$emit('submitJob', this.input_code)
    }
  },
  filters: {
  },
  mounted: function () {
    $(this.$refs.result_modal).modal()
    $(this.$refs.result_modal).contents('.indicator').css('background-color', 'white')
    this.$on('openModal', event => {
      $(this.$refs.result_modal).modal('open')
      $(this.$refs.tabs).tabs('select_tab', 'input_div')
    })
    this.$on('changeTab', event => {
      $(this.$refs.tabs).tabs('select_tab', event)
    })
    $(this.$refs.tabs).tabs()
  }
}
</script>

<style scoped>
.tabs .tab a {
  color: white;
}
.tabs .tab a:hover,
.tabs .tab a.active {
  background-color: transparent;
  color: white;
}
.tabs .tab.disabled a,
.tabs .tab.disabled a:hover {
  color: rgba(255, 255, 255, 0.7);
}
.tabs .indicator {
  background-color: white;
}
</style>
