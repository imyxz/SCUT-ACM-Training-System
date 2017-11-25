<template>
  <div class="card ">
    <div class="card-content">
      <span class="card-title">Tags:</span>
      <a class="chip" v-for="tag in tags" @click="goTag(tag.tag_id)" :key="tag.tag_id" >{{ tag.tag_name }}</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProblemTags',
  props: ['tags'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goTag: function (tagID) {

    }
  }
}
</script>

<style>

</style>
