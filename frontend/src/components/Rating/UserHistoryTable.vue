<template>
  <table class='highlight bordered medium-text'>
    <thead>
      <tr>
        <th class='center-align'>#</th>
        <th class='left-align'>Contest</th>
        <th class='center-align'>From</th>
        <th class='center-align'>To</th>
        <th class='center-align'>Change</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="(one,index) in display_history" :key="index" >
        <td class='center-align'>{{one.rating_id}}</td>
        <td class='left-align' style="font-size:16px;"><a @click="goContest(one.rating_id)">{{one.contest_name}}</a></td>
        <td class='center-align'>{{one.from_rating}}</td>
        <td class='center-align'>{{one.to_rating}}</td>
        <td class='center-align' :class="one.display_class">{{one.change}}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'UserHistoryTable',
  props: ['history'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goContest: function (ratingId) {
      this.$router.push({ name: 'rating.contestHistory', params: { rating_id: ratingId } })
    }
  },
  computed: {
    display_history: function () {
      return this.history.map(e => {
        let displayClass = e.to_rating >= e.from_rating ? 'green-text' : 'red-text'
        let change = (e.to_rating >= e.from_rating ? '+' : '') + (e.to_rating - e.from_rating).toFixed(2)
        let ret = {}
        Object.assign(ret, e, {
          display_class: displayClass,
          change: change
        })
        ret.from_rating = parseFloat(ret.from_rating).toFixed(2)
        ret.to_rating = parseFloat(ret.to_rating).toFixed(2)
        return ret
      })
    }
  }
}
</script>

<style>

</style>
