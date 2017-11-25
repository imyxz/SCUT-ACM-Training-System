<template>
  <div class="card ">
    <div class="card-content">
      <span class="card-title">Rank:</span>
      <ul class="collection">
        <li class="collection-item avatar" v-for="(one,rank) in ranks" :key="rank">
          <img :src="one.user_avatar" alt="" class="circle">
          <span class="title">
            <span class="red-text">{{one.user_nickname}}</span> Rank #{{ rank+1 }}</span>
          <p>Time: {{ one.time_usage | time_filter}}<br />Ram: {{ one.ram_usage | ram_filter}}</p>
          <a href="#" class="secondary-content">
            <i class="material-icons">grade</i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ramFilter, timeFilter } from '@/helpers/common'
export default {
  name: 'ProblemAcRank',
  props: ['ranks'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
  },
  filters: {
    ram_filter: (val) => ramFilter(val),
    time_filter: (val) => timeFilter(val)
  }
}
</script>

<style>

</style>
