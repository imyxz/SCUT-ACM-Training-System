<template>
<div class="router-container">
<div class="container">
<div class="card-panel hoverable over-flow-auto">
    <tag-table ref="tag_table" :tag-list="tag_list" :cur-tag-name="tag_name"></tag-table>
    <page-indicator ref="page_indicator" :cur-page="page" :max-page="100" ></page-indicator>
    <problem-table :problem-list="problem_list"></problem-table>
  </div>
</div>
</div>
</template>

<script>
import TagTable from '@/components/VJudge/TagTable'
import ProblemTable from '@/components/VJudge/ProblemTable'
import PageIndicator from '@/components/PageIndicator'
import { getTagList, getAllTags } from '@/helpers/api/vjudge/tag'
export default {
  name: 'AllTag',
  data () {
    return {
      page: 0,
      problem_list: [],
      tag_name: '',
      tag_list: []
    }
  },
  components: {
    'problem-table': ProblemTable,
    'page-indicator': PageIndicator,
    'tag-table': TagTable
  },
  created: function () {
  },
  beforeRouteUpdate: function (to, from, next) {
    /* if (to.params.tag_name !== undefined) {
      this.tag_name = to.params.tag_name
      if (to.params.page !== undefined) {
        this.page = to.params.page
      } else {
        this.page = 1
      }
      this.updateProblems(this.tag_name, this.page)
    } */
    next()
  },
  methods: {
    updateProblems: function (tagName, page, update = true) {
      getTagList(tagName, page)
        .then(r => {
          this.problem_list = r.problem_list
          if (update) {
            this.page = page
            this.tag_name = tagName
            this.$router.push({ name: 'vjudge.viewTag', params: { tag_name: tagName, page: page } })
          }
        })
    }
  },
  watch: {
  },
  mounted: function () {
    if (this.$route.params.tag_name !== undefined) {
      this.tag_name = this.$route.params.tag_name
      if (this.$route.params.page !== undefined) {
        this.page = this.$route.params.page
      } else {
        this.page = 1
      }
      this.updateProblems(this.tag_name, this.page)
    }
    getAllTags()
    .then(r => {
      this.tag_list = r.tags
    })
    this.$refs.tag_table.$on('changeTag', event => {
      this.updateProblems(event, 1)
    })
    this.$refs.page_indicator.$on('page-change', event => {
      this.updateProblems(this.tag_name, event)
    })
  }
}
</script>

<style>

</style>
