<template>
  <div class="container">
    <div class="card">
      <div class="card-content">
        <span class="card-title">{{nickname}}:</span>
        <user-history-table :history="history"></user-history-table>
      </div>
    </div>
  </div>
</template>

<script>
import UserHistoryTable from '@/components/Rating/UserHistoryTable'
import { getUserRatingHistory } from '@/helpers/api/rating'

export default {
  name: 'User',
  props: [''],
  data () {
    return {
      history: [],
      user_id: 0,
      nickname: ''
    }
  },
  created: function () {
    this.user_id = this.$route.params.user_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.user_id = to.params.user_id
    next()
  },
  watch: {
    user_id: function (newVal) {
      getUserRatingHistory(newVal)
        .then(r => {
          this.history = r.history
          this.nickname = r.nickname
        })
    }
  },
  methods: {
  },
  filters: {
  },
  components: {
    'user-history-table': UserHistoryTable
  },
  computed: {
  }
}
</script>

<style>

</style>
