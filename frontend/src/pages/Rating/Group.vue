<template>
<div class="container">
  <div class="card">
    <div class="card-content">
      <span class="card-title">{{group_name}}:</span>
      <rating-table :rank="rank" :is-user="true"></rating-table>
    </div>
  </div>
</div>
</template>

<script>
import RatingTable from '@/components/Rating/RatingTable'
import { getGroupPlayers } from '@/helpers/api/rating'

export default {
  name: 'Group',
  props: [''],
  data () {
    return {
      origin_rank: [],
      group_name: '',
      group_id: 0
    }
  },
  created: function () {
    this.group_id = this.$route.params.group_id
  },
  beforeRouteUpdate: function (to, from, next) {
    this.group_id = to.params.group_id
    next()
  },
  watch: {
    group_id: function (newVal) {
      getGroupPlayers(newVal)
      .then(r => {
        this.origin_rank = r.players
        this.group_name = r.group_name
      })
    }
  },
  methods: {
  },
  filters: {
  },
  components: {
    'rating-table': RatingTable
  },
  computed: {
    rank: function () {
      return this.origin_rank.map(r => {
        return {
          user_id: r.user_id,
          nickname: r.user_nickname,
          avatar: r.user_avatar,
          contest_count: r.contest_cnt,
          rating: parseFloat(r.rating).toFixed(2)
        }
      })
    }
  }
}
</script>

<style>

</style>
