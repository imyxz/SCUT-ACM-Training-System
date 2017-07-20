<?php include('header.php');?>
    <div class="container" id="bind_player">
        <p class="big-test">绑定小队</p>
        <div class="row">
            <div class="col l4">

                <div class="row">
                    <div class="input-field col l12">
                        <input id="contest_name" type="text" class="validate" v-model.trim="real_name">
                        <label for="contest_name">真实姓名</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <select id="select_team" v-model="team_id">
                            <option value="0" disabled selected>选择小队</option>
                            <option v-for="team in teams" :value="team.team_id">{{ team.team_name }}</option>
                        </select>
                        <label for="select_team">所属小队</label>
                    </div>
                </div>
                <div class="row right-align">
                    <a class="waves-effect waves-light btn " @click="submit()">添加</a>

                </div>


            </div>
            <div class="col l8">
            </div>
        </div>

    </div>
    <script>
        var bind_player=new Vue(
            {
                el: "#bind_player",
                data: {
                    teams:[],
                    real_name:'',
                    team_id:'',
                    basic_url:'<?php echo _Http;?>',
                    loading:true
                },
                created: function(){
                    axios.get(this.basic_url + 'userAPI/getAllTeam/')
                        .then(function(response)
                        {
                            bind_player.teams=response.data.teams;
                            Vue.nextTick(function () {
                                $('select').material_select();
                            });
                            setTimeout(function(){this.loading=false;},2000);

                        });
                },
                methods:
                {
                    submit: function()
                    {
                        var post=new Object();
                        post.real_name=this.real_name;
                        post.team_id=$('#select_team').val();
                        axios.post(this.basic_url+'userAPI/bindPlayer/',JSON.stringify(post))
                            .then(function(response){
                                if(response.data.status==0)
                                {
                                    Materialize.toast('绑定小队成功！', 2000);
                                    delayRefresh(500);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">绑定失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            })


                    }
                }

            }
        );
    </script>

<?php include('footer.php');?>