<template>
  <div>
    <div class="title">
      <p>{{ problem_title }}
        <a class="problem-soruce" :href="problem_url" target="_blank" v-if="problem_identity !== undefined">{{oj_name}}-{{problem_identity}}</a>
      </p>
    </div>
    <div class="limits center-align">
      <div class="memory-limit">Memory: {{ memory_limit | ram_filter }}</div>
      <div class="time-limit">Time: {{ time_limit | time_filter}}</div>
    </div>
    <div class="content" data-disable-for-latex-v-html="problem_desc" id="problem_desc">
    </div>
  </div>
</template>

<script>
import { ramFilter, timeFilter } from '@/helpers/common'
import $ from 'jquery'
export default {
  name: 'ProblemInfo',
  props: ['problem_title', 'problem_desc', 'memory_limit', 'time_limit', 'problem_url', 'problem_id', 'problem_identity', 'oj_id', 'oj_name'],
  data () {
    return {
    }
  },
  created: function () {
    /* 已在 App.vue中定义
    // eslint-disable-next-line
    MathJax.Hub.Config({
      showProcessingMessages: false, // 关闭js加载过程信息
      messageStyle: 'none', // 不显示信息
      extensions: ['tex2jax.js'],
      jax: ['input/TeX', 'output/HTML-CSS'],
      tex2jax: {
        inlineMath: [['$', '$'], ['\\(', '\\)']], // 行内公式选择符
        displayMath: [['$$', '$$'], ['\\[', '\\]']], // 段内公式选择符
        skipTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code', 'a'], // 避开某些标签
        ignoreClass: '' // 避开含该Class的标签
      },
      'HTML-CSS': {
        availableFonts: ['STIX', 'TeX'], // 可选字体
        showMathMenu: false // 关闭右击菜单显示
      }
    })
    */
  },
  methods: {
  },
  filters: {
    ram_filter: (val) => ramFilter(val),
    time_filter: (val) => timeFilter(val)
  },
  watch: {
    problem_desc: function (newDesc) {
      this.$nextTick(() => {
        document.getElementById('problem_desc').innerHTML = this.problem_desc
        // eslint-disable-next-line
        MathJax.Hub.Queue(['Typeset', MathJax.Hub, document.getElementById('problem_desc')])
        if ($('#pdf-div').length > 0) {
          let target = $('#pdf-div').data('pdf-url')
          target = target.substr(0, 4) === 'http' ? target : '/' + target
          // eslint-disable-next-line
          PDFObject.embed(target, '#pdf-div', { width: '100%', height: $(window).height() + 'px' })
        }
      })
    }
  }
}
</script>

<style>
.problem-header {
  display: none;
}
.title {
  text-align: center;
  font-weight: bold;

  font-size: 48px;
}
.title p {
  margin: 6px;
}
.content span {
  font-weight: bold;
}

.content p {
  font-size: 18px;
  text-indent: 2em;
}
.problem-input-div > :first-child,
.problem-output-div > :first-child,
.problem-examples > :first-child,
.problem-hint > :first-child {
  font-weight: bold;
  font-size: 32px;
}
.problem-example {
  font-size: 16px;
}
.problem-input,
.problem-output {
  background-color: lightgray;
  border-width: 1px;
  border-style: solid;
  border-color: black;
}
.problem-output {
  margin-bottom: 16px;
}
.problem-input :first-child,
.problem-output :first-child {
  border-bottom-width: 1px;
  border-bottom-style: solid;
  border-bottom-color: black;
}
.problem-input :last-child,
.problem-output :last-child {
  line-height: 16px;
  padding: 4px;
  margin: 0;
}
.problem-soruce {
  margin-left: 32px;
  font-size: 20px;
  text-decoration: underline;
  position: absolute;
}
.memory-limit,
.time-limit {
  display: inline-block;
  margin: 0 12px;
  color: red;
  font-size: 16px;
}
</style>
