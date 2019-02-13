<template>
  <div class="container">
    <div class="card">
      <div class="card-content">
        <span class="card-title">{{contest_name}}:</span>
        <contest-history-table :history="history"></contest-history-table>
      </div>
    </div>
  </div>
</template>

<script>
import ContestHistoryTable from '@/components/Rating/ContestHistoryTable'
import { getContestRatingHistory } from '@/helpers/api/rating'

export default {
  name: 'Contest',
  props: [''],
  data () {
    return {
      origin_history: [],
      rating_id: 0,
      contest_name: ''
    }
  },
  created: function () {
    this.rating_id = this.$route.params.rating_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.rating_id = to.params.rating_id
    next()
  },
  watch: {
    rating_id: function (newVal) {
      getContestRatingHistory(newVal)
        .then(r => {
          this.origin_history = r.history
          this.contest_name = r.contest_name
        })
    }
  },
  methods: {
  },
  filters: {
  },
  components: {
    'contest-history-table': ContestHistoryTable
  },
  computed: {
    history: function () {
      return this.origin_history.map(r => {
        return {
          user_id: r.user_id,
          from_rating: r.from_rating,
          to_rating: r.to_rating,
          nickname: r.user_nickname,
          avatar: r.user_avatar
        }
      })
    }
  }
}
</script>

<style>

</style>
