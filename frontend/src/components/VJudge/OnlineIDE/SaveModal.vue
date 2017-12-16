<template>
  <div ref="modal_draft" class="modal bottom-sheet">
    <div class="modal-content">
      <div class="row">
        <div class="col l6">
          <h4>手动保存：</h4>
          <div class="collection">
            <div class="collection-item input-field">
              <span class="badge">
                <a href="#" @click="saveCode(new_draft_title)">
                  <i class="material-icons green-text text-darken-1">save</i>
                </a>
              </span><input type="text" style="width:80%;margin:0;height:22px;" placeholder="请输入保存代码的标题" v-model="new_draft_title" /></div>
            <a href="#" v-for="draft in drafts" @click="readCode(draft.draft_id)" class="collection-item" :key="draft.draft_id">
              <span class="badge">{{draft.save_time}}</span>{{draft.draft_title}}</a>
          </div>
        </div>
        <div class="col l6">
          <h4>自动保存：</h4>
          <div class="collection">
            <a href="#" class="collection-item" v-for="draft in auto_saves" @click="readCode(draft.draft_id)" :key="draft.draft_id">
              <span class="new badge" data-badge-caption="自动保存"></span>{{draft.save_time}}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import $ from 'jquery'
export default {
  name: 'SaveModal',
  props: ['drafts', 'auto_saves'],
  data () {
    return {
      new_draft_title: ''
    }
  },
  created: function () {
  },
  methods: {
    readCode: function (draftId) {
      this.$emit('readCode', draftId)
    },
    saveCode: function (draftTitle) {
      this.$emit('saveCode', draftTitle)
    }
  },
  filters: {
  },
  mounted: function () {
    $(this.$refs.modal_draft).modal()
    this.$on('openModal', event => {
      $(this.$refs.modal_draft).modal('open')
    })
    this.$on('closeModal', event => {
      $(this.$refs.modal_draft).modal('close')
    })
  }
}
</script>

<style scoped>

</style>
