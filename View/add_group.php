<?php include('header.php');?>
    <div class="container" id="add_team">
        <p class="big-test">添加小队</p>
        <div class="row">
            <div class="col l4">

                <div class="row">
                    <div class="input-field col l12">
                        <input id="contest_name" type="text" class="validate" v-model="team_name">
                        <label for="contest_name">小队名称</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col l12">
                        <input id="problem_count" type="text" class="validate" v-model="team_vj_account">
                        <label for="problem_count">VJ账号</label>
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
        var add_team=new Vue(
            {
                el: "#add_team",
                data: {
                    team_name:'',
                    team_vj_account:'',
                    basic_url:'<?php echo _Http;?>'
                },
                created: function(){

                },
                methods:
                {
                    submit: function()
                    {
                        var post=new Object();
                        post.team_name=this.team_name;
                        post.team_vj_account=this.team_vj_account;

                        axios.post(this.basic_url+'userAPI/addTeam/',JSON.stringify(post))
                            .then(function(response){
                                if(response.data.status==0)
                                {
                                    Materialize.toast('添加小队成功！', 2000);
                                    delayRefresh(500);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">添加失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            })


                    }
                }

            }
        );
    </script>

<?php include('footer.php');?>