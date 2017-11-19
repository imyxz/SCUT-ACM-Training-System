<?php include('header.php');?>
    <div class="" id="summary_container">
        <div style="position: fixed;top:80px;width:100%;left:0;z-index:10">
            <div v-show="loading" class="center-align" id="loading" style="position: relative">
                <div class="preloader-wrapper small active">
                    <div class="spinner-layer spinner-green-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col l3  s12">
                <div class="card">
                    <div class="card-content red lighten-3 white-text">
                        <span class="card-title">{{ contest_name }}</span>
                        <p>{{ contest_desc }}</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content blue-grey white-text">
                        <span class="card-title">参赛人员</span>
                        <div v-for="team in team_info">
                            <span class="red-text text-lighten-4 medium-text">{{ team.name }}</span>:<p class="yellow-text text-lighten-5 right-align medium-text">{{ team.player | array2Person }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col l9  s12">
                <div class="card">
                    <div class="card-content" style="overflow-x:auto">
                        <table class="highlight bordered medium-text ">
                            <thead>
                            <tr>
                                <th class="left-align">Team</th>
                                <th class="center-align" v-for="x in problem_count">{{ String.fromCharCode(x+64) }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(problems,name) in contest_summary">
                                <td class="red-text">{{ name }}</td>
                                <td v-for="players in problems">
                                    <span v-for="person in players" v-if="person.ac_status!=4"
                                        :class="{
                                            'green-text': person.ac_status==1,
                                            'yellow-text text-darken-3': person.ac_status==2,
                                            'blue-text': person.ac_status==3,
                                            }"
                                          class="center-align"
                                        style="display: block">
                                        {{ person.player_name }}
                                    </span>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                        <a class="btn-floating btn waves-effect waves-light red" style="position:absolute;left:80px;top:30px" @click="$('#modal_ac').modal('open');"><i class="material-icons">mode_edit</i></a>

                        <p class="center-align">
                            <span class=" badge yellow darken-3 white-text">TRY</span>
                            <span class=" badge blue lighten-2 white-text">补题</span>
                            <span class=" badge green lighten-2 white-text">AC</span>

                        </p>
                    </div>
                </div>

            </div>
        </div>
        <div class="card">
            <div class="card-content" style="overflow-x:auto">
                <div style="position: absolute;top:10px;left:40px">
                    <input type="checkbox" id="only_show_in_system" v-model="only_show_in_system" />
                    <label for="only_show_in_system">仅显示院队</label>
                </div>
                <table class="highlight bordered medium-text">
                    <thead>
                    <tr>
                        <th class="center-align">Rank</th>
                        <th class="left-align">Team</th>
                        <th class="center-align">Solve</th>
                        <th class="center-align">Penalty</th>
                        <th class="center-align" v-for="x in problem_count">{{ String.fromCharCode(x+64) }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="team in contest_board" :class="{'blue lighten-5':team.ac_info.in_system}" v-show="!only_show_in_system || team.ac_info.in_system">
                        <td class="center-align">{{ team.rank_index }}</td>
                        <td class="left-align">{{ team.group_name }}</td>
                        <td class="center-align">{{ team.problem_solved }}</td>
                        <td class="center-align">{{ team.penalty }}</td>
                        <td v-for="status in team.ac_info.submission"
                            :class="{
                            'green':status.ac,
                            'red':!status.ac && status.is_try}"
                            class="center-align lighten-4">
                            <span v-if="status.ac" style="display: block">
                                {{ getAcTime(status.ac_time)}}
                            </span>
                            <span v-if="status.try!=0" style="display: block">
                                (-{{ status.try }})
                            </span>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modal_ac" class="modal bottom-sheet">
            <div class="modal-content">
                <div class="container">
                    <h4>更新做题状况 </h4>
                    <div v-for="x in problem_count" class="row">
                        <div class="col l1 right-align">
                        <span class="medium-text " >{{ String.fromCharCode(x+64) }}:</span>
                        </div>
                        <div class="col l11">
                            <input type="radio" :name="'problem_'+x" value="1" :id="'problem_'+x+'_1'" v-model="player_summary[x]"/><label :for="'problem_'+x+'_1'" class="green-text text-lighten-2 medium-text">赛内参与做题并AC</label>
                            <input type="radio" :name="'problem_'+x" value="2" :id="'problem_'+x+'_2'" v-model="player_summary[x]"/><label :for="'problem_'+x+'_2'" class="yellow-text text-darken-3 medium-text">赛内参与做题但WA</label>
                            <input type="radio" :name="'problem_'+x" value="3" :id="'problem_'+x+'_3'" v-model="player_summary[x]"/><label :for="'problem_'+x+'_3'" class="blue-text text-lighten-2 medium-text">赛后补题</label>
                            <input type="radio" :name="'problem_'+x" value="4" :id="'problem_'+x+'_4'" v-model="player_summary[x]"/><label :for="'problem_'+x+'_4'" class="red-text text-lighten-2 medium-text">未动</label>
                        </div>

                    </div>
                    <a class="waves-effect waves-light btn" @click="updateAcStatus()">保存</a>
                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>


    <script>
        var summary_container=new Vue(
            {
                el: "#summary_container",
                data: {
                    team_info:[],
                    problem_count:0,
                    contest_summary:[],
                    contest_name:"",
                    contest_desc:"",
                    contest_board:[],
                    player_summary:[],
                    contest_id:<?php echo $contest_id?>,
                    basic_url:'<?php echo _Http;?>',
                    loading:true,
                    only_show_in_system:false
                },
                filters: {
                    array2Person:function(arr)
                    {
                        var ret="";
                        for(var x in arr)
                        {
                            ret=ret+arr[x]+" ";
                        }
                        return ret;
                    }
                },
                created: function(){
                    axios.get(this.basic_url + 'contestAPI/getContestSummary/id/'+this.contest_id)
                        .then(function(response)
                        {
                            summary_container.team_info=response.data.team_info;
                            summary_container.problem_count=response.data.problem_count;
                            summary_container.player_summary=response.data.player_summary;
                            summary_container.contest_summary=response.data.contest_summary;
                            summary_container.contest_name=response.data.contest_name;
                            summary_container.contest_desc=response.data.contest_desc;
                            summary_container.contest_board=response.data.contest_board;
                            $('.modal').modal();
                            setTimeout(function(){summary_container.loading=false;},500);

                        });
                },
                methods: {
                    getAcTime:function(sec)
                    {
                        var second,minute,hour;
                        second=sec%60;
                        sec/=60;
                        sec=Math.floor(sec);
                        minute=sec%60;
                        sec/=60;
                        sec=Math.floor(sec);
                        hour=sec;
                        return this.addZero(hour) + ":" + this.addZero(minute) + ":" + this.addZero(second);

                    },
                    addZero:function(val)
                    {
                        if(val==0)
                            return "00";
                        else if(val<10)
                            return "0" + val.toString();
                        else return val.toString();
                    },
                    updateAcStatus:function()
                    {
                        Materialize.toast('正在提交中.....', 2000);
                        axios.post(this.basic_url+'userAPI/updateAcStatus/id/'+this.contest_id,JSON.stringify(this.player_summary))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('做题状态更新成功！', 2000);
                                    delayRefresh(500);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">更新失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            })
                            .catch(function(error)
                            {
                                Materialize.toast('<span class="">更新失败：'+'网络通信错误'+'</span>' , 2000);
                            })
                    }
                }
            }
        );

    </script>
<?php include('footer.php');?>