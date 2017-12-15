<template>
  <div ref="share_modal" class="modal">
    <div class="modal-content">
      <h4>代码链接</h4>
      <h5>
        <a :href="shareUrl">{{shareUrl}}</a>
      </h5>
    </div>
    <div class="modal-footer">
      <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">ok</a>
    </div>
  </div>
</template>

<script>
import $ from 'jquery'
export default {
  name: 'ShareModal',
  data () {
    return {
      shareUrl: ''
    }
  },
  created: function () {
  },
  methods: {
  },
  filters: {
  },
  mounted: function () {
    $(this.$refs.share_modal).modal()
    this.$on('openModal', event => {
      this.shareUrl = event
      $(this.$refs.share_modal).modal('open')
    })
    this.$on('closeModal', event => {
      $(this.$refs.share_modal).modal('close')
    })
  },
  watch: {
  }
}
</script>

<style scoped>

</style>
