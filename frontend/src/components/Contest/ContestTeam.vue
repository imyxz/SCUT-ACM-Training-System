<template>

  <div class="card">
    <div class="card-content blue-grey white-text">
      <span class="card-title">参赛人员</span>
      <div v-for="team in teamInfo" :key="team.name">
        <span class="red-text text-lighten-4 medium-text">{{ team.name }}</span>:
        <p class="yellow-text text-lighten-5 right-align medium-text">{{ team.player.join(' ') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContestTeam',
  props: ['teamInfo'],
  data () {
    return {
    }
  },
  methods: {
  }
}
</script>

<style>

</style>
