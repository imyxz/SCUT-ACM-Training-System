<template>
  <div>
    <ul class="pagination" style="display: inline-block">
      <li :class="{'disabled':curPage<=1,'waves-effect':curPage>1}">
        <a @click="changePage(curPage-1)">
          <i class="material-icons">chevron_left</i>
        </a>
      </li>
      <li v-for="pg in pagePagination" :class="{'active':pg==curPage,'waves-effect':pg!=curPage}" :key="pg">
        <a @click="changePage(pg)">{{pg}}</a>
      </li>
      <li :class="{'disabled':curPage==maxPage,'waves-effect':curPage!=maxPage}">
        <a @click="changePage(curPage+1)">
          <i class="material-icons">chevron_right</i>
        </a>
      </li>
    </ul>
    <div class="input-field" style="display: inline-block">
      <input type="number" @keyup.enter="onInputPage(parseInt(inputPage))" v-model="inputPage">
      <label><i class="material-icons">search</i>前往: </label>
    </div>
  </div>

</template>

<script>
export default {
  name: 'PageIndicator',
  props: ['curPage', 'maxPage'],
  data () {
    return {
      maxLength: 11,
      inputPage: ''
    }
  },
  methods: {
    changePage (newpage) {
      if (newpage >= 1 && newpage <= this.maxPage) {
        this.$emit('page-change', newpage)
      }
    },
    onInputPage (newpage) {
      this.changePage(newpage)
      this.inputPage = ''
    }
  },
  computed: {
    pagePagination: function () {
      let maxLength = parseInt(this.maxLength) - 1
      let front = maxLength / 2 + maxLength % 2
      let back = maxLength / 2
      let start = parseInt(this.curPage) - front
      let end = parseInt(this.curPage) + back
      if (start < 1 && end > this.maxPage) {
        start = 1
        end = this.maxPage
      } else if (start < 1) {
        end = end + 1 - start
        start = 1
      } else if (end > this.maxPage) {
        start = start - end + this.maxPage
        end = this.maxPage
      }
      if (start < 1) {
        start = 1
      }
      if (end > this.maxPage) {
        end = this.maxPage
      }
      let tmp = []
      for (let i = start; i <= end; i++) {
        tmp.push(i)
      }
      return tmp
    }
  }
}
</script>

<style>

</style>
