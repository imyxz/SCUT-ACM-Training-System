<?php include('header.php');?>
    <div class="container" id="problem_list_table">
        <div class="row">
            <div class="col l3 s12">
                <div class="card">
                    <div class="card-content  ">
                        <span class="card-title">列表标题</span>
                        <div class="input-field">
                            <input id="list_title" type="text" v-model="list_title">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content ">
                        <span class="card-title">列表描述</span>
                        <div class="input-field">
                            <textarea id="list_desc" class="materialize-textarea" rows="6" v-model="list_desc"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col l9 s12">
                <div class="card-panel hoverable" >
                    <table class="highlight bordered" style="font-size: 20px;">
                        <thead>
                        <tr>
                            <th class="center-align">problem id</th>
                            <th class="center-align">Title</th>
                            <th class="center-align">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="problem,index in problem_list">
                            <td class="center-align"><a :href="problem.problem_id | generate_url" target="_blank">{{ problem.problem_id }}</a></td>
                            <td class="center-align green-text">{{ problem.problem_title }}</td>
                            <td class="center-align" >
                                <a href="javascript:void(0);" @click="swapPosition(index,'up')"><i class="material-icons">arrow_upward</i></a>
                                <a href="javascript:void(0);" @click="swapPosition(index,'down')"><i class="material-icons">arrow_downward</i></a>
                                <a href="javascript:void(0);" @click="deletePosition(index)"><i class="material-icons red-text">delete</i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="center-align" colspan="1">
                                <div class="input-field">
                                    <input id="problem_id" type="text" v-model="new_problem_id" @blur="getProblemIDByInfo(new_problem_id)" @keyup.enter="$('#problem_title').focus()">
                                    <label for="problem_id">Problem ID 或 oj/id</label>
                                </div>
                            </td>
                            <td class="center-align">
                                <div class="input-field">
                                    <input id="problem_title" type="text" v-model="new_problem_title">
                                    <label  for="problem_title">Problem Title</label>
                                </div>
                            </td>
                            <td class="center-align">
                                <a href="javascript:void(0);" @click="addProblem()"><i class="material-icons" style="font-size: 40px">add_circle</i></a>
                            </td>

                        </tr>
                        </tbody>
                    </table>

                    <div class="row" style="margin-top:16px;">
                        <div class="right-align">
                            <button class="btn" @click="saveList">提交保存</button>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <script>
        var editor;
        var problem_list_table=new Vue(
            {
                el: "#problem_list_table",
                data: {
                    status_info:[],
                    loading: true,
                    waiting:0,
                    basic_url:'<?php echo _Http;?>',
                    page:0,
                    problem_list:[],
                    list_id:<?php echo $list_id;?>,
                    list_title:'',
                    list_desc:'',
                    new_problem_id:'',
                    new_problem_title:''
                },
                filters:{
                    generate_url:function(val)
                    {
                        return problem_list_table.basic_url+"vJudge/viewProblem/id/"+val;
                    }
                },
                created: function(){
                    if(this.list_id>0)
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getListProblems/id/'+this.list_id)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    problem_list_table.problem_list=response.data.problem_list;
                                    problem_list_table.is_end=response.data.is_end;
                                    problem_list_table.loading=false;
                                    problem_list_table.list_title=response.data.list_title;
                                    problem_list_table.list_desc=response.data.list_desc;
                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    }
                },
                methods:{
                    swapPosition:function(first,where)
                    {
                        var second=0;
                        if(where=='up')
                            second=first-1;
                        else
                            second=first+1;
                        if(first<0 || second<0 || first>=problem_list_table.problem_list.length || second>=problem_list_table.problem_list.length)
                            return;
                        var target=problem_list_table.$data.problem_list;
                        var tmp=target[first];
                        problem_list_table.$set(problem_list_table.problem_list,first,target[second]);
                        problem_list_table.$set(problem_list_table.problem_list,second,tmp);

                    },
                    deletePosition:function(pos)
                    {
                        problem_list_table.$delete(problem_list_table.problem_list,pos);
                    },
                    addByProblemID:function()
                    {},
                    getProblemIDByInfo:function(id)
                    {
                        if(id=='')
                            return;
                        var pos1=id.indexOf("/",-1);
                        if(pos1>=0)
                        {
                            var oj_name=id.substr(0,pos1);
                            var problem_name=id.substr(pos1+1,id.length-pos1-1);
                            var obj=new Object();
                            obj.oj_name=oj_name;
                            obj.problem_identity=problem_name;
                            axios.post(this.basic_url+'vJudgeAPI/getProblemByInfo/',JSON.stringify(obj))
                                .then(function(response)
                                {
                                    if(response.data.status==0){
                                        problem_list_table.new_problem_id=response.data.problem_id;
                                        problem_list_table.updateProblemTitle();
                                    }
                                    else
                                    {
                                        Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                    }

                                });
                        }
                        else
                            problem_list_table.updateProblemTitle();

                    },
                    updateProblemTitle:function()
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getProblemInfo/id/'+this.new_problem_id)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    problem_list_table.new_problem_title=response.data.problem_info.problem_title;

                                    }

                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);

                                }

                            });
                    },
                    addProblem:function(){
                        if(this.new_problem_id=='' || this.new_problem_title=='')
                        {
                            Materialize.toast("请填写完全相关信息" , 2000);
                            return;
                        }
                        problem_list_table.$set(problem_list_table.problem_list,problem_list_table.problem_list.length,
                            {
                                problem_id:this.new_problem_id,
                                problem_title:this.new_problem_title
                            }
                        );
                        this.new_problem_id='';
                        this.new_problem_title='';
                    },
                    saveList:function(){
                        Materialize.toast('正在提交中.....', 2000);
                        var obj=new Object();
                        obj.list_title=this.list_title;
                        obj.list_desc=this.list_desc;
                        obj.problem_list=this.problem_list;
                        obj.list_id=this.list_id;
                        var target='';
                        if(this.list_id==0)
                            target=this.basic_url+'vJudgeAPI/newList/';
                        else
                            target=this.basic_url+'vJudgeAPI/updateList/';

                        axios.post(target,JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('列表新建成功！', 2000);
                                    delayJump(problem_list_table.basic_url+"vJudge/problemList/id/"+response.data.list_id,1000);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">提交失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            })
                            .catch(function(error)
                            {
                                Materialize.toast('<span class="">提交失败：'+'网络通信错误'+'</span>' , 2000);
                            })
                    }
                }


            }
        );
    </script>
<?php include('footer.php');?>