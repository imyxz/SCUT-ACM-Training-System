<template>
<div class="container">
  <div class="card">
    <div class="card-content">
      <div class="flex-two-end">
        <span class="card-title">Rank:</span>
        <router-link to="/rating/group/rank/" class="btn">查看队伍排名</router-link>
      </div>

      <rating-table :rank="rank" :is-user="true"></rating-table>
    </div>
  </div>
</div>
</template>

<script>
import RatingTable from '@/components/Rating/RatingTable'
import { getRank } from '@/helpers/api/rating'

export default {
  name: 'Rank',
  props: [''],
  data () {
    return {
      origin_rank: []
    }
  },
  created: function () {
  },
  mounted: function () {
    getRank()
      .then(r => {
        this.origin_rank = r.rank
      })
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

<style scoped>
.flex-two-end{
  display: flex;
  justify-content: space-between;
}
</style>

