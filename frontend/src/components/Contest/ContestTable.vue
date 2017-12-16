<template>

    <table class="highlight bordered auto-break">
      <thead>
        <tr>
          <th class="left-align">比赛名称</th>
          <th class="left-align">描述</th>
          <th class="center-align">操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="one in contestInfo" :key="one.contest_id">
          <td class="red-text left-align long-text">{{ one.name }}</td>
          <td class="light-blue-text left-align long-text">{{ one.desc }}</td>
          <td class="">
            <a class="btn-floating" @click="goContest(one.id)">
              <i class="material-icons">search</i>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
</template>

<script>
export default {
  name: 'ContestTable',
  props: ['contestInfo'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goContest (contestId) {
      this.$router.push({name: 'contest.viewSummary', params: { contest_id: contestId }})
    }
  }
}
</script>

<style>

</style>
