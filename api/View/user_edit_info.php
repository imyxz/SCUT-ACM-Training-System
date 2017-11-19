<?php include('header.php');?>
    <div class="container" id="container">
    <div class="card">
    <div class="card-content  ">
        <p class="big-test">修改用户信息</p>
        <div class="row">
            <div class="col l4 s11">

                <div class="row">
                    <div class="input-field col l12 s12">
                        <input id="nick_name" type="text" class="validate" v-model.trim="nick_name" placeholder="   ">
                        <label for="nick_name">昵称</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col l12 s12">
                        <input id="bg_url" type="text" class="validate" v-model.trim="bg_url" placeholder="   ">
                        <label for="bg_url">背景图片(填入url，留空则使用系统自带)</label>
                    </div>
                </div>
                <div class="row right-align">
                    <a class="waves-effect waves-light btn " @click="submit()">保存</a>
                </div>
            </div>
            <div class="col l8 s1">
            </div>
        </div>
    </div>
        </div>
    </div>
    <script>
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    nick_name:'',
                    basic_url:'<?php echo _Http;?>',
                    loading:true,
                    bg_url:''
                },
                created: function(){
                    axios.get(this.basic_url + 'userAPI/getUserInfo/')
                        .then(function(response)
                        {
                            vm.nick_name=response.data.user_info.user_nickname;
                            vm.bg_url=response.data.user_info.user_bgpic;
                        });
                },
                methods:
                {
                    submit: function()
                    {
                        var post=new Object();
                        post.nick_name=this.nick_name;
                        post.bg_url=this.bg_url;
                        axios.post(this.basic_url+'userAPI/updateUserInfo/',JSON.stringify(post))
                            .then(function(response){
                                if(response.data.status==0)
                                {
                                    Materialize.toast('更新用户信息成功！', 2000);
                                    delayRefresh(500);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">更新用户信息失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            })


                    }
                }

            }
        );
    </script>

<?php include('footer.php');?>