<template>
<div class="router-container">
  <div class="container">
    <div class="card-panel hoverable">

      <page-indicator :cur-page="page" :max-page="100" @page-change="page = $event"></page-indicator>
      <contest-table :contests="contest_info"></contest-table>

    </div>
  </div>
</div>
</template>

<script>
import ContestTable from '@/components/VJudge/ContestTable'
import PageIndicator from '@/components/PageIndicator'
import { getContestList } from '@/helpers/api/vjudge/contest'
export default {
  name: 'AllContest',
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
          this.contest_info = r.contests
          if (updatePage) {
            this.page = page
            this.$router.push({ name: 'vjudge.allContest', params: { page: page } })
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
