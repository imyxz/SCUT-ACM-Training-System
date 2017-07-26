<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
    </style>

    <div class="container" id="vj_view_problem">
        <div class="row">
            <div class="col l3">
                <div class="card-panel " >

                    <div class="center-align row"><button class="btn blue btn-float" style="width: 90%" onclick="$('#code-modal').modal('open');">提交</button></div>
                    <div class="center-align row">
                            <a class="btn green btn-float" style="width: 90%" href="<?php echo _Http;?>vJudge/myStatus/">status</a>

                    </div>

                </div>
            </div>
            <div class="col l9">
                <div class="card-panel hoverable" >

                    <div class="title">
                        <p>{{ problem_info.problem_title }}<a class="problem-soruce" :href="problem_info.problem_url" target="_blank">{{problem_info.oj_name}}-{{problem_info.problem_identity}}</a></p>
                    </div>
                    <div class="limits center-align">
                        <div class="memory-limit">Memory: {{ problem_info.memory_limit | ram_filter }}</div>
                        <div class="time-limit">Time: {{ problem_info.time_limit | time_filter}}</div>
                    </div>
                    <div class="content" v-html="problem_info.problem_desc">

                    </div>
                </div>
            </div>
        </div>
        <div id="code-modal" class="modal modal-fixed-footer" style="width:90%;overflow: visible;">
            <div class="modal-content">
                <div id="editor" ></div>
            </div>
            <div class="modal-footer" id="modal_footer">
                <template v-if="!submited">
                    <div class="row" key="div_1">
                        <div class="col l3">
                            <select id="select_compiler" onchange="vj_view_problem.compiler=$('#select_compiler').val();" >
                                <option value="0" disabled selected>选择编译器</option>
                                <option v-for="(name,id) in compiler_info" :value="id">{{ name }}</option>

                            </select>
                        </div>
                        <div class="col l9">
                            <button class="waves-effect waves-green btn blue" :class="{'disabled':compiler==0}" @click="submitCode">提交</button>
                        </div>
                    </div>
                </template>
                <template v-if="submited">
                    <div class="row" key="div_2" style="font-size: 20px; padding:8px;">
                        <div class="col l4 left-align" >
                            <span class="" style="margin:auto">时间：{{ job_status.time_usage | time_filter }}</span>

                        </div>
                        <div class="col l4 left-align" >
                            <span class="" style="margin:auto" :class="{'green-text':job_status.ac_status==1,'red-text':job_status.ac_status!=1}">状态：{{ job_status.wrong_info  }}</span>

                        </div>
                        <div class="col l4 right-align" >
                            <span class="" style="margin:auto">内存：{{ job_status.ram_usage | ram_filter }}</span>

                        </div>
                    </div>
                </template>


            </div>
        </div>


    </div>


    <script>
        var editor;
        var vj_view_problem=new Vue(
            {
                el: "#vj_view_problem",
                data: {
                    problem_info: [],
                    compiler_info:[],
                    loading: true,
                    problem_id:<?php echo $problem_id;?>,
                    basic_url:'<?php echo _Http;?>',
                    compiler:0,
                    submited:false,
                    job_status:[],
                    job_id:0
                },
                filters:{
                    ram_filter:function(val)
                    {
                        var unit='B';
                        if(val>=1000)
                        {
                            val/=1024;
                            unit='KB';
                            if(val>=1000)
                            {
                                val/=1024;
                                unit='MB';
                                if(val>=1000)
                                {
                                    val/=1024;
                                    unit='GB';
                                    if(val>=1000)
                                    {
                                        val/=1024;
                                        unit='TB';
                                    }
                                }
                            }
                        }
                        return  val.toFixed(2)+ " "+unit;
                    },
                    time_filter:function(val)
                    {
                        var unit='ms';
                        if(val>=1000)
                        {
                            val/=1000;
                            unit='s';
                        }
                        return  val.toFixed(2)+ " "+unit;
                    }
                },
                created: function(){
                    axios.get(this.basic_url+'vJudgeAPI/getProblemInfo/id/'+this.problem_id)
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                vj_view_problem.problem_info=response.data.problem_info;
                                vj_view_problem.compiler_info=JSON.parse(response.data.problem_info.compiler_info);
                                setTimeout(function(){vj_view_problem.loading=false;},1000);
                                Vue.nextTick(function () {
                                    $('select').material_select();
                                    $('.modal').modal();
                                    ace.require("ace/ext/language_tools");
                                    editor = ace.edit("editor");
                                    editor.getSession().setMode("ace/mode/c_cpp");
                                    editor.setTheme("ace/theme/twilight");
                                    editor.setFontSize(16);
                                    editor.setOptions({
                                        enableBasicAutocompletion: true,
                                        enableSnippets: true,
                                        enableLiveAutocompletion: true
                                    });
                                });
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);

                            }

                        });

                },
                methods:{
                    submitCode:function()
                    {
                        Materialize.toast('正在提交中.....', 2000);
                        var obj=new Object();
                        obj.source_code=editor.getValue();
                        obj.problem_id=this.problem_id;
                        obj.compiler_id=this.compiler;
                        console.log(obj);
                        axios.post(this.basic_url+'vJudgeAPI/submitCode/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('任务提交成功！', 2000);
                                    vj_view_problem.job_id=response.data.job_id;
                                    $('#select_compiler').hide();
                                    vj_view_problem.submited=true;
                                    vj_view_problem.getJobStatus();
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
                    },
                    getJobStatus:function()
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getJobStatus/id/'+this.job_id)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    Materialize.toast('<span class="">状态已更新</span>' , 2000);
                                    if(response.data.status_info.wrong_info=="")
                                        response.data.status_info.wrong_info="waiting";
                                    vj_view_problem.job_status=response.data.status_info;

                                }
                                setTimeout(vj_view_problem.getJobStatus,3000);


                            }).catch(function(msg){
                                setTimeout(vj_view_problem.getJobStatus,3000);

                            });
                    }
                }

            }
        );
    </script>
<?php include('footer.php');?>