<template>
  <div>
    <h1 class="center-align">404</h1>
    <h2 class="center-align">{{ noticeContent }}</h2>
  </div>
  </template>

<script>
export default {
  name: 'Common404',
  data () {
    return {
      noticeContent: 'If you reached here, you have been fucked!'
    }
  }
}
</script>

<style>

</style>
