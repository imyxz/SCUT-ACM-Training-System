<template>
  <div class="card">
    <div class="card-content">
      <h1 class="center-align">{{ contestName }}</h1>
      <div class="progress">
        <div class="determinate" :style="determinatePercentage"></div>
      </div>
      <div style="position: relative;min-height: 22px;font-size: 18px;">
        <span style="position: absolute;left: 0">{{startTime}}</span>
        <span style="position: absolute;right: 0">{{endTime}}</span>
      </div>

    </div>
  </div>
</template>

<script>
import { ramFilter, timeFilter, fromUnixTime } from '@/helpers/common'
export default {
  name: 'ContestHeader',
  props: ['contestName', 'runningTime', 'contestLong', 'contestStartTime'],
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
      let percentage = 0
      if (this.contestLong > 0) {
        percentage = (parseInt(this.runningTime) / parseInt(this.contestLong)) * 100
      }

      return 'width: ' + percentage + '%'
    },
    startTime: function () {
      return fromUnixTime(this.contestStartTime)
    },
    endTime: function () {
      return fromUnixTime(this.contestStartTime + this.contestLong)
    }
  }
}
</script>

<style>

</style>
