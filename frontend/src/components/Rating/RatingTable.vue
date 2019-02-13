<template>
  <table class='highlight bordered medium-text'>
    <thead>
      <tr>
        <th class='center-align'>#</th>
        <th v-if="isUser"></th>
        <th class='left-align'>Who</th>
        <th class='center-align' v-if="isUser">Participations</th>
        <th class='center-align'>Rating</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="(one,index) in rank" :key="one.user_id" >
        <td class='center-align'>{{index + 1}}</td>
        <td class='center-align' v-if="isUser" style="width:48px;padding:0"><img  :src="one.avatar" class="circle valign-wrapper" style="height:48px;width:48px;opacity:1"></td>
        <td class='left-align' style="font-size:16px;" v-if="isUser"><a  @click="goUser(one.user_id)">{{one.nickname}}</a></td>
        <td class='left-align' style="font-size:16px;" v-if="!isUser"><a  @click="goGroup(one.group_id)">{{one.group_name}}</a></td>
        <td class='center-align' v-if="isUser">{{one.contest_count}}</td>
        <td class='center-align'>{{one.rating}}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'RatingTable',
  props: ['rank', 'isUser'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goUser: function (userId) {
      this.$router.push({ name: 'rating.userHistory', params: { user_id: userId } })
    },
    goGroup: function (groupId) {
      this.$router.push({ name: 'rating.groupInfo', params: { group_id: groupId } })
    }
  }
}
</script>

<style>

</style>
