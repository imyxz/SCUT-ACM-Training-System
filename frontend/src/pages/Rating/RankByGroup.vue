<template>
<div class="container">
  <div class="card">
    <div class="card-content">
      <div class="flex-two-end">
        <span class="card-title">Rank:</span>
        <router-link to="/rating/user/rank/" class="btn">查看个人排名</router-link>
      </div>
      <rating-table :rank="rank" :is-user="false"></rating-table>
    </div>
  </div>
</div>
</template>
<script>
import RatingTable from '@/components/Rating/RatingTable'
import { getGroupRank } from '@/helpers/api/rating'

export default {
  name: 'GroupRank',
  props: [''],
  data () {
    return {
      origin_rank: []
    }
  },
  created: function () {
  },
  mounted: function () {
    getGroupRank()
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
          group_id: r.group_id,
          group_name: r.group_name,
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
