<template>
  <div class="container">
    <div class="card-panel hoverable">

      <page-indicator :cur-page="page" :max-page="100" @page-change="page = $event"></page-indicator>
      <contest-table :contest-info="contest_info"></contest-table>

    </div>
  </div>
</template>

<script>
import ContestTable from '@/components/Contest/ContestTable'
import PageIndicator from '@/components/PageIndicator'
import { getContestList } from '@/helpers/api/contest'
export default {
  name: 'ContestList',
  data () {
    return {
      page: 0,
      contest_info: []
    }
  },
  components: {
    'contest-table': ContestTable,
    'page-indicator': PageIndicator
  },
  created: function () {
    this.page = this.$route.params.page
  },
  beforeRouteUpdate: function (to, from, next) {
    this.page = to.params.page
    next()
  },
  methods: {
    updateContestList: function (page, updatePage = true) {
      getContestList(page)
        .then(r => {
          this.contest_info = r.contest
          if (updatePage) {
            this.page = page
            this.$router.push({ name: 'contest.viewList', params: { page: page } })
          }
        })
    }
  },
  watch: {
    page: function (newPage) {
      this.updateContestList(newPage)
    }
  }
}
</script>

<style>

</style>
