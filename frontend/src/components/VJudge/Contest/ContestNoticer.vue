<template>
  <div class="card">
    <div class="card-content">
      <h1 class="center-align">{{ contestName }}</h1>
      <div class="progress">
        <div class="determinate" :style="determinatePercentage"></div>
      </div>
      <div style="position: relative;min-height: 22px;font-size: 18px;">
        <span style="position: absolute;left: 0">{{contestStartTime}}</span>
        <span style="position: absolute;right: 0">{{contestEndTime}}</span>
      </div>

    </div>
  </div>
</template>

<script>
import { ramFilter, timeFilter } from '@/helpers/common'
export default {
  name: 'ContestHeader',
  props: ['contestName', 'runningTime', 'contestLong', 'contestStartTime', 'contestEndTime'],
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
  },
  computed: {
    determinatePercentage: function () {
      return 'width: ' + (this.running_time / this.contest_long) * 100 + '%'
    }
  }
}
</script>

<style>

</style>
