<template>
  <table class='highlight bordered medium-text'>
    <thead>
      <tr>
        <th class='center-align'></th>
        <th></th>
        <th class='left-align'>Who</th>
        <th class='center-align'>Before</th>
        <th class='center-align'>After</th>
        <th class='center-align'>Change</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="(one,index) in display_history" :key="one.user_id" >
        <td class='center-align'>{{index + 1}}</td>
        <td class='center-align' style="width:48px;padding:0"><img  :src="one.avatar" class="circle valign-wrapper" style="height:48px;width:48px;opacity:1"></td>
        <td class='left-align'><a @click="goUser(one.user_id)">{{one.nickname}}</a></td>
        <td class='center-align'>{{one.from_rating}}</td>
        <td class='center-align'>{{one.to_rating}}</td>
        <td class='center-align' :class="one.display_class">{{one.change}}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'ContestHistoryTable',
  props: ['history'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goUser: function (userId) {
      this.$router.push({ name: 'rating.userHistory', params: { user_id: userId } })
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
