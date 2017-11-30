<template>
  <div class="card">
    <div class="card-content not-break" style="overflow-x:auto">
      <table class="highlight bordered medium-text ">
        <thead>
          <tr>
            <th class="left-align">Team</th>
            <th class="center-align" v-for="x in problemCount" :key="x">{{ String.fromCharCode(parseInt(x)+64) }}</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(team,name) in contestSummary" :key="name">
            <td class="red-text">{{ name }}</td>
            <td v-for="(problem,problem_index) in team" :key="problem_index">
              <span v-for="person in problem" v-if="person.ac_status!=4" :class="{
                                            'green-text': person.ac_status==1,
                                            'yellow-text text-darken-3': person.ac_status==2,
                                            'blue-text': person.ac_status==3,
                                            }" class="center-align" style="display: block" :key="person.player_name">
                {{ person.player_name }}
              </span>
            </td>

          </tr>
        </tbody>
      </table>
      <a class="btn-floating btn waves-effect waves-light red" style="position:absolute;left:80px;top:30px" @click="openModal()">
        <i class="material-icons">mode_edit</i>
      </a>

      <p class="center-align">
        <span class=" badge yellow darken-3 white-text">TRY</span>
        <span class=" badge blue lighten-2 white-text">补题</span>
        <span class=" badge green lighten-2 white-text">AC</span>

      </p>
    </div>
    <div id="modal_ac" class="modal bottom-sheet">
            <div class="modal-content">
                <div class="container">
                    <h4>更新做题状况 </h4>
                    <div v-for="x in problemCount" class="row" :key="x">
                        <div class="col l1 right-align">
                        <span class="medium-text " >{{ String.fromCharCode(x+64) }}:</span>
                        </div>
                        <div class="col l11">
                            <input type="radio" :name="'problem_'+x" value="1" :id="'problem_'+x+'_1'" v-model="playerSummary[x]"/><label :for="'problem_'+x+'_1'" class="green-text text-lighten-2 medium-text">赛内参与做题并AC</label>
                            <input type="radio" :name="'problem_'+x" value="2" :id="'problem_'+x+'_2'" v-model="playerSummary[x]"/><label :for="'problem_'+x+'_2'" class="yellow-text text-darken-3 medium-text">赛内参与做题但WA</label>
                            <input type="radio" :name="'problem_'+x" value="3" :id="'problem_'+x+'_3'" v-model="playerSummary[x]"/><label :for="'problem_'+x+'_3'" class="blue-text text-lighten-2 medium-text">赛后补题</label>
                            <input type="radio" :name="'problem_'+x" value="4" :id="'problem_'+x+'_4'" v-model="playerSummary[x]"/><label :for="'problem_'+x+'_4'" class="red-text text-lighten-2 medium-text">未动</label>
                        </div>
                    </div>
                    <a class="waves-effect waves-light btn" @click="updateAcStatus()">保存</a>
                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
  </div>
</template>

<script>
import $ from 'jquery'
export default {
  name: 'AcStatus',
  props: ['contestSummary', 'problemCount', 'playerSummary'],
  data () {
    return {
    }
  },
  created: function () {
  },
  methods: {
    updateAcStatus: function () {
      this.$emit('player-summary-change', this.playerSummary)
    },
    openModal: function () {
      $('.modal').modal()
      $('#modal_ac').modal('open')
    }
  }
}
</script>

<style>
.form-inline .radio {
    vertical-align: -3px;
    margin-left: 0px;
    padding-right:14px;
    padding-left:25px;
}
.ac_label {
    padding-left:5px;
}
.input-group{
    padding-top: 5px;
    padding-bottom: 5px;
}
.long-text{
    word-break: break-all;
    word-wrap: break-word;
}
</style>
