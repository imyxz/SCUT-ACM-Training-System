<template>
  <div class="">
    <a class="chip" v-for="(tag,index) in tagList" :key="index" @click="changeTag(tag.tag_name)" :class="{'green lighten-2':tag.tag_name==curTagName}">
      {{ tag.tag_name }}
      <i class="green-text">{{tag.tag_count}}</i>
    </a>
  </div>
</template>

<script>
export default {
  name: 'TagTable',
  props: ['tagList', 'curTagName'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    changeTag (tagName) {
      this.$emit('changeTag', tagName)
    }
  },
  watch: {
    tagList: function (newList) {
      if (newList.length > 0 && this.curTagName === '') {
        this.changeTag(newList[0].tag_name)
      }
    }
  }
}
</script>

<style>

</style>
