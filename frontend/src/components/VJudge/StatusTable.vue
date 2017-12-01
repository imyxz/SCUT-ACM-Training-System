<template>
<div>
<table class="highlight bordered">
    <thead>
      <tr>
        <th class="center-align">job id</th>
        <th class="center-align">user name</th>
        <th class="center-align" v-if="!inContest">oj</th>
        <th class="center-align">problem</th>
        <th class="center-align">Result</th>
        <th class="center-align">Time</th>
        <th class="center-align">Mem</th>
        <th class="center-align">submit time</th>
        <th class="center-align">view source</th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="status in statusInfo" :key="status.job_id">
        <td class="center-align">{{ status.job_id }}</td>
        <td class="center-align">{{ status.user_nickname }}</td>
        <td class="center-align" v-if="!inContest">{{ status.oj_name }}</td>
        <td class="center-align">
          <a @click="goRoute(status.problem_route)">{{ status.problem_identity }}</a>
        </td>
        <td class="center-align" :class="{'green-text':status.ac_status==1,'red-text':status.ac_status!=1}">{{ status.wrong_info }}</td>
        <td class="center-align">{{ status.time_usage | time_filter }}</td>
        <td class="center-align">{{ status.ram_usage | ram_filter }}</td>
        <td class="center-align">{{ status.submit_time }}</td>
        <td class="center-align">
          <a class="btn-floating" @click="displaySourceCode(status.job_id)">
            <i class="material-icons">search</i>
          </a>
          <a class="btn-floating" :class="{'red':status.is_shared,'blue':!status.is_shared }" @click="setJobShare(status.job_id,!status.is_shared, status)" v-if="!inContest">
            <i class="material-icons">share</i>
          </a>
        </td>
      </tr>
    </tbody>
  </table>
  <source-code-modal ref="code_modal"></source-code-modal>
</div>
</template>

<script>
import { ramFilter, timeFilter, toast } from '@/helpers/common'
import SourceCodeModal from '@/components/VJudge/SourceCodeModal'
import {getJobSourceCode, setJobShare} from '@/helpers/api/vjudge/problem'
export default {
  name: 'StatusTable',
  props: ['statusInfo', 'inContest'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    goRoute (route) {
      this.$router.push(route)
    },
    displaySourceCode (jobId) {
      getJobSourceCode(jobId, true)
      .then(r => {
        this.$refs.code_modal.$emit('openModal', {
          source_code: r.source_code,
          code_type: 'c_cpp'
        })
      })
    },
    setJobShare (jobId, isShare, obj) {
      setJobShare(jobId, isShare)
      .then(r => {
        if (isShare) {
          toast('代码已设置为分享')
        } else {
          toast('代码已取消分享')
        }
        obj.is_share = isShare
      })
    }
  },
  filters: {
    ram_filter: (val) => ramFilter(val),
    time_filter: (val) => timeFilter(val)
  },
  components: {
    'source-code-modal': SourceCodeModal
  }
}
</script>

<style>

</style>
