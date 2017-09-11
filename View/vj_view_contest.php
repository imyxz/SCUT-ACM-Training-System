<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        #editor-view {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        #container .tabs .tab a{
            color:deepskyblue!important;
            font-size: 18px;

        }
        #container .tabs .tab a:hover,.tabs .tab a.active {
            background-color:transparent;
            color:deepskyblue;
        }
        #container  .tabs .tab.disabled a,.tabs .tab.disabled a:hover {
            color:rgba(255,255,255,0.7);
        }
        #container  .tabs .indicator {
            background-color:deepskyblue;
        }
    </style>
    <div class="container" id="container">
        <div class="row">
            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <h1 class="center-align">{{ contest_name }}</h1>
                        <div class="progress">
                            <div class="determinate" :style="'width: ' + (running_time/contest_long)*100 + '%'"></div>
                        </div>
                        <div style="position: relative;min-height: 22px;font-size: 18px;">
                            <span style="position: absolute;left: 0" >{{contestStartTime}}</span>
                            <span style="position: absolute;right: 0">{{contestEndTime}}</span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col s12" v-if="err_info!=''">
                <div class="card">
                    <div class="card-content white lighten-3 red-text">
                        <span class="card-title center-align">{{ err_info }}</span>
                        <div class="center-align"><button class=" btn" v-if="need_participant" @click="startContest()">点此开始比赛</button></div>
                    </div>
                </div>
            </div>
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s3"><a class="active" href="#contest_info">比赛详情</a></li>
                    <li class="tab col s3"><a  href="#problem_info">题目列表</a></li>
                    <li class="tab col s3"><a href="#status_info">提交状态</a></li>
                    <li class="tab col s3"><a href="#rank_info">排行榜</a></li>
                </ul>
            </div>
            <div id="contest_info" class="col s12">
                <div class="row">
                    <div class="col l3 s12">
                        <div class="card">
                            <div class="card-content red lighten-3 white-text">
                                <span class="card-title">{{ contest_name }}</span>
                                <p>{{ contest_desc }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col l9 s12">
                        <div class="card-panel hoverable over-flow-auto" style="position: relative">
                            <table class="highlight bordered" style="font-size: 20px;">
                                <thead>
                                <tr>
                                    <th class="center-align">AC状态</th>
                                    <th class="center-align">State</th>
                                    <th class="center-align">#</th>
                                    <th class="center-align">Title</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="problem in problem_list">
                                    <td class="center-align"><span v-show="problem_status[problem.problem_index].is_ac" class="green-text"><i class="material-icons">done</i></span></td>
                                    <td class="center-align">{{problem_status[problem.problem_index].ac}}/{{problem_status[problem.problem_index].trys}}</td>
                                    <td class="center-align">{{String.fromCharCode(problem.problem_index+64)}}</td>
                                    <td class="center-align blue-text"><a href="#" @click="goProblem(problem.problem_index)">{{ problem.problem_title }}</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
            <div id="problem_info" class="col s12">
                <div class="row">
                    <div class="col l3 s12">
                        <div class="card-panel " >
                            <ul class="pagination">
                                <li v-for="index in problem_count" :class="{'active':index==cur_problem_index,'waves-effect':index!=cur_problem_index}"><a @click="goProblem(index)">{{String.fromCharCode(index+64)}}</a></li>
                            </ul>
                        </div>
                        <div class="card-panel " >
                            <div class="center-align row"><button class="btn blue btn-float" style="width: 90%" onclick="vm.openCode();">提交</button></div>
                        </div>

                    </div>
                    <div class="col l8 s12">
                        <div class="card-panel hoverable" >
                            <div class="title">
                                <p>{{ cur_problem_info.problem_title }}</p>
                            </div>
                            <div class="limits center-align">
                                <div class="memory-limit">Memory: {{ cur_problem_info.memory_limit | ram_filter }}</div>
                                <div class="time-limit">Time: {{ cur_problem_info.time_limit | time_filter}}</div>
                            </div>
                            <div class="content" data-disable-for-latex-v-html="cur_problem_info.problem_desc" id="problem_desc">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="code-modal" class="modal modal-fixed-footer" style="width:90%;overflow: visible;">
                    <div class="modal-content">
                        <div id="editor"></div>
                    </div>
                    <div class="modal-footer" id="modal_footer">
                        <template v-if="!submited">
                            <div class="row" key="div_1">
                                <div class="col l3">
                                    <select id="select_compiler" onchange="vm.compiler=$('#select_compiler').val();" >
                                        <option value="0" disabled selected>选择编译器</option>
                                        <option v-for="(name,id) in cur_problem_info.compiler_info" :value="id">{{ name }}</option>
                                    </select>
                                </div>
                                <div class="col l9">
                                    <button class="waves-effect waves-green btn blue" :class="{'disabled':compiler==-1}" @click="submitCode">提交</button>
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
            <div id="status_info" class="col s12">
                <div class="card-panel hoverable over-flow-auto" >
                    <table class="highlight bordered">
                        <thead>
                        <tr>
                            <th class="center-align">job id</th>
                            <th class="center-align">user name</th>
                            <th class="center-align">problem</th>
                            <th class="center-align">Result</th>
                            <th class="center-align">Time</th>
                            <th class="center-align">Mem</th>
                            <th class="center-align">submit time</th>
                            <th class="center-align">view source</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="status in status_info">
                            <td class="center-align">{{ status.run_job_id }}</td>
                            <td class="center-align">{{ status.user_nickname }}</td>
                            <td class="center-align"><a href="#" @click="goProblem(status.problem_index)">{{ String.fromCharCode(status.problem_index+64) }}</a></td>
                            <td class="center-align" :class="{'green-text':status.ac_status==1,'red-text':status.ac_status!=1}">{{ status.wrong_info  }}</td>
                            <td class="center-align">{{ status.time_usage | time_filter }}</td>
                            <td class="center-align">{{ status.ram_usage | ram_filter }}</td>
                            <td class="center-align">{{ status.submit_time |fromSeconds }}</td>
                            <td class="center-align" >
                                <a class="btn-floating"  @click="displaySourceCode(status.run_job_id)"><i class="material-icons">search</i></a>
                                <a class="btn-floating" :class="{'red':status.is_shared,'blue':!status.is_shared }" @click="setJobShare(status.run_job_id,!status.is_shared,status)"  ><i class="material-icons">share</i></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                <div id="code-modal-view" class="modal"  style="width:90%;overflow: visible;height:100%">
                    <div class="modal-content">
                        <div id="editor-view" ></div>
                    </div>
                </div>
                <div id="share-modal" class="modal">
                    <div class="modal-content">
                        <h4>代码链接</h4>
                        <h5><a :href="cur_share_id | share_url">{{cur_share_id | share_url}}</a></h5>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">ok</a>
                    </div>
                </div>
            </div>
            <div id="rank_info" class="col s12">
                <div class="card">
                    <div class="card-content" style="overflow-x:auto">
                        <table class="highlight bordered medium-text">
                            <thead>
                            <tr>
                                <th class="center-align">Rank</th>
                                <th class="left-align">Team</th>
                                <th class="center-align">Solve</th>
                                <th class="center-align">Penalty</th>
                                <th class="center-align" v-for="x in problem_count"><a href="#" @click="goProblem(x)">{{ String.fromCharCode(x+64) }}</a></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="user in rank_board" v-if="user!=null">
                                <td class="center-align">{{ user.rank_index }}</td>
                                <td class="left-align">{{ user.user_name }}</td>
                                <td class="center-align">{{ user.problem_solved }}</td>
                                <td class="center-align">{{ user.penalty }}</td>
                                <td v-for="problem in user.problems" v-if=" problem!=null"
                                    :class="{
                            'green':problem.is_ac,
                            'red':!problem.is_ac && problem.is_try}"
                                    class="center-align lighten-4">
                            <span v-if="problem.is_ac" style="display: block">
                                {{ problem.ac_time | getAcTime}}
                            </span>
                            <span v-if="problem.trys!=0" style="display: block">
                                (-{{ problem.trys }})
                            </span>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var editor;
        var editor_view;
        var all_submissions=[];
        var already_in_submissioins={};
        var user_status=[];
        var vm=new Vue(
            {
                el: "#container",
                data: {
                    loading: true,
                    basic_url:'<?php echo _Http;?>',
                    problem_list:[],
                    problem_info:[],
                    problem_status:[],
                    contest_id:<?php echo $contest_id;?>,
                    submissions:[],
                    participants:[],
                    submissions_begin_time:-1,
                    cur_problem_info:{
                        problem_title:'',
                        memory_limit:'',
                        time_limit:'',
                        compiler_info:[],
                        problem_desc:''
                    },
                    compiler:-1,
                    submited:false,
                    job_status:[],
                    job_id:0,
                    problem_count:0,
                    cur_problem_index:0,
                    user_id:0,
                    rank_board:[],
                    cur_tab_id:'',
                    contest_name:'',
                    contest_desc:'',
                    status_info:[],
                    running_time:1,
                    contest_long:1,
                    need_participant:false,
                    err_info:'',
                    contest_start_time_ts:0,
                    cur_share_id:0
                },
                filters:{
                    generate_url:function(val)
                    {
                        return vm.basic_url+"vJudge/viewProblem/id/"+val;
                    },
                    generate_url_for_edit:function(val)
                    {
                        return vm.basic_url + "vJudge/editList/id/"+val;
                    },
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
                        return  parseFloat(val).toFixed(2)+ " "+unit;
                    },
                    time_filter:function(val)
                    {
                        var unit='ms';
                        if(val>=1000)
                        {
                            val/=1000;
                            unit='s';
                        }
                        return  parseFloat(val).toFixed(2)+ " "+unit;
                    },
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
                        return vm.addZero(hour) + ":" + vm.addZero(minute) + ":" + vm.addZero(second);

                    },
                    share_url:function(val)
                    {
                        if(vm==null)    return "";
                        return vm.basic_url+"vJudge/onlineIDE/jobCode/"+val;
                    },
                    fromSeconds:function(val)
                    {
                        if(val==null)   return 0;
                        var tmp=parseInt(val);
                        tmp=tmp/60;
                        tmp=tmp.toFixed(0);
                        return vm.addZero((tmp/60).toFixed(0))+":"+vm.addZero((tmp%60))

                    }
                },
                created: function(){
                    axios.get(this.basic_url+'vJudgeAPI/getContestInfo/id/'+this.contest_id)
                        .then(function(response)
                        {
                            var data=response.data;
                            if(!(data.status==0 && data.need_participant==false))
                            {
                                if(data.need_participant==true)
                                {
                                    vm.need_participant=true;
                                    vm.err_info="该比赛为异步比赛，您可在任何时候开始";

                                }
                                else
                                {
                                    vm.err_info=data.err_msg;

                                }

                            }
                            vm.problem_list=data.contest_problem;
                            vm.problem_info=new Array(data.contest_problem.length+1);
                            vm.problem_status=new Array(data.contest_problem.length+1);
                            vm.problem_count=data.contest_problem.length;
                            vm.user_id=data.user_id;
                            vm.contest_name=data.contest_info.contest_title;
                            vm.contest_desc=data.contest_info.contest_desc;
                            vm.running_time=data.running_time;
                            vm.contest_long=data.contest_info.contest_last_seconds;
                            vm.contest_start_time_ts=data.contest_info.contest_start_time_ts*1000;
                            var p_status=new Array(vm.problem_count+1);
                            for(var i=0;i<=vm.problem_count;i++)
                            {
                                p_status[i]={
                                    'ac':0,
                                    'trys':0,
                                    'is_ac':false
                                };
                            }
                            vm.problem_status=p_status;
                            if(vm.running_time>vm.contest_long)
                                vm.running_time=vm.contest_long;
                            if(data.status==0 && data.need_participant==false)
                                vm.refreshSubmission();

                            $("ul.tabs").tabs({ onShow: function(tab) {

                                vm.cur_tab_id=tab[0].id;
                                if(tab[0].id=='problem_info')
                                {
                                    if(vm.cur_problem_index==0)
                                        vm.goProblem(1);
                                }
                                else if(tab[0].id=='rank_info')
                                {
                                    vm.refreshRank();
                                }
                                else if(tab[0].id=='status_info')
                                {
                                    vm.getSubmitStatus();
                                }

                            } });
                            Vue.nextTick(function () {
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

                                editor_view = ace.edit("editor-view");
                                editor_view.getSession().setMode("ace/mode/c_cpp");
                                editor_view.setTheme("ace/theme/twilight");
                                editor_view.setFontSize(16);
                                editor_view.setOptions({
                                    enableBasicAutocompletion: true,
                                    enableSnippets: true,
                                    enableLiveAutocompletion: true
                                });
                            });

                        });
                },
                methods:{
                    refreshSubmission:function(){
                        axios.get(this.basic_url+'vJudgeAPI/getContestSubmission/id/'+this.contest_id +"/beginTime/"+this.submissions_begin_time)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    var data=response.data;
                                    vm.running_time=data.running_time;
                                    if(data.submissions.length>0)
                                        vm.submissions_begin_time=data.submissions[data.submissions.length-1][2];
                                    vm.participants=data.participants;
                                    var prev=vm.submissions;
                                    var p_status=vm.problem_status;
                                    for(var i in data.submissions)
                                    {
                                        var run_id=data.submissions[i][4];
                                        if(already_in_submissioins[run_id]==null)//新记录
                                        {
                                            already_in_submissioins[run_id]=true;
                                            prev.splice(-1,0,data.submissions[i]);
                                            var submit=data.submissions[i];
                                            p_status[submit[1]].trys++;
                                            if(submit[3]==1)
                                            {
                                                p_status[submit[1]].ac++;
                                                if(submit[0]==vm.user_id)
                                                {
                                                    p_status[submit[1]].is_ac=true;
                                                }
                                            }
                                        }
                                    }
                                    vm.problem_status.splice(0,0);


                                    /*
                                    var p_status=new Array(vm.problem_count+1);
                                    for(var i=0;i<=vm.problem_count;i++)
                                    {
                                        p_status[i]={
                                            'ac':0,
                                            'trys':0,
                                            'is_ac':false
                                        };
                                    }
                                    for(var i in prev)
                                    {
                                        p_status[prev[i][1]].trys++;
                                        if(prev[i][3]==1)
                                        {
                                            p_status[prev[i][1]].ac++;
                                            if(prev[i][0]==vm.user_id)
                                            {
                                                p_status[prev[i][1]].is_ac=true;
                                            }
                                        }

                                    }
                                    vm.submissions= $.extend(true,[],prev);
                                    vm.problem_status=p_status;*/
                                    vm.refreshRank();
                                    setTimeout(vm.refreshSubmission,60*1000);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    refreshRank:function()
                    {
                        if(vm.cur_tab_id!='rank_info')
                            return;
                        var u_status=[];


                        for(var i in vm.participants)
                        {
                            var user=vm.participants[i];
                            var new_problem=[];
                            for(var x=1;x<=vm.problem_count;x++)
                            {
                                new_problem[x]={
                                    is_ac:false,
                                    ac_time:0,
                                    trys:0,
                                    is_try:false
                                };
                            }
                            u_status[user.user_id]={
                                rank_index:0,
                                user_name:user.user_nickname,
                                user_id:user.user_id,
                                problem_solved:0,
                                penalty:0,
                                problems:new_problem
                            };
                        }
                        for(var i in vm.submissions)
                        {
                            var submit=vm.submissions[i];
                            var user=u_status[submit[0]];
                            if(user.problems[submit[1]].is_ac==false)
                            {
                                if(submit[3]==1)
                                {
                                    user.problems[submit[1]].is_ac=true;
                                    user.problems[submit[1]].ac_time=submit[2];
                                    user.problem_solved++;
                                    user.penalty+=submit[2];
                                }
                                else
                                {
                                    user.problems[submit[1]].trys++;
                                    user.penalty+=20*60;
                                }
                                user.problems[submit[1]].is_try=true;
                            }
                        }
                        u_status.sort(function(a,b){
                            if(a.problem_solved== b.problem_solved)
                            {
                                return a.penalty-b.penalty;
                            }
                            else
                                return b.problem_solved- a.problem_solved;
                        });
                        var index=1;
                        for(var i in u_status)
                        {
                            u_status[i].rank_index=index++;
                        }
                        vm.rank_board=u_status;


                    },
                    getSubmitStatus:function()
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getContestStatus/id/'+this.contest_id +"/page/"+1)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    var data=response.data;
                                    vm.status_info=data.submit_status;
                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    displaySourceCode:function(job_id)
                    {
                        axios.get('<?php echo _Http;?>vJudgeAPI/getJobSourceCode/id/'+job_id)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor_view.setValue(response.data.source_code,-1);
                                    $('#code-modal-view').modal('open');
                                }
                                else
                                {
                                    Materialize.toast('<span class="">查看失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    },
                    setJobShare:function(job_id,is_shared,row)
                    {
                        var obj=new Object;
                        obj.job_id=job_id;
                        obj.is_shared=is_shared;
                        axios.post('<?php echo _Http;?>vJudgeAPI/setJobShare/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    if(is_shared)
                                    {
                                        vm.cur_share_id=job_id;
                                        $('#share-modal').modal('open');
                                        Materialize.toast('<span class="">代码已设置为分享</span>' , 3000);
                                    }
                                    else
                                        Materialize.toast('<span class="">代码已取消分享</span>' , 3000);
                                    row.is_shared=is_shared;

                                }
                                else
                                {
                                    Materialize.toast('<span class="">设置失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });

                    },

                    submitCode:function()
                    {
                        Materialize.toast('正在提交中.....', 2000);
                        var obj=new Object();
                        obj.source_code=editor.getValue();
                        obj.compiler_id=this.compiler;
                        axios.post(this.basic_url+'vJudgeAPI/submitContestJob/cid/'+this.contest_id+"/pid/"+this.cur_problem_index,JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('任务提交成功！', 2000);
                                    vm.job_id=response.data.job_id;
                                    $('#select_compiler').hide();
                                    vm.submited=true;
                                    vm.getJobStatus();
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
                                    vm.job_status=response.data.status_info;
                                    if(response.data.status_info.running_status!=3)
                                        setTimeout(vm.getJobStatus,3000);
                                }



                            }).catch(function(msg){
                                setTimeout(vm.getJobStatus,3000);

                            });
                    },
                    openCode:function()
                    {
                        this.submited=false;
                        Vue.nextTick(function () {
                            $('select').material_select();
                            $('#code-modal').modal('open');
                        });
                    },
                    goProblem:function(val)
                    {
                        if(vm.problem_info[val]==null)
                            vm.loadProblem(val,true);
                        else
                        {
                            vm.cur_problem_index=val;
                            vm.cur_problem_info=vm.problem_info[val];
                            $('ul.tabs').tabs('select_tab', 'problem_info');
                            Vue.nextTick(function () {
                                document.getElementById("problem_desc").innerHTML=vm.cur_problem_info.problem_desc;
                                if($('#pdf-div').length>0)
                                    PDFObject.embed(vm.basic_url+$('#pdf-div').data("pdf-url"), "#pdf-div",{width:"100%",height:$(window).height() +"px"});
                                //for mathjax
                                MathJax.Hub.Config({
                                    extensions: ["tex2jax.js"],
                                    jax: ["input/TeX", "output/HTML-CSS"],
                                    tex2jax: {
                                        inlineMath: [ ['$','$'], ["\\(","\\)"] ],
                                        processEscapes: true
                                    },
                                    "HTML-CSS": { availableFonts: ["TeX"] }
                                });
                                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                            });
                        }
                    },
                    loadProblem:function(problem_index,is_display)
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getContestProblem/cid/'+this.contest_id +"/pid/"+problem_index)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    var data=response.data;
                                    data.problem_info.compiler_info=JSON.parse(data.problem_info.compiler_info);
                                    vm.problem_info[problem_index]=data.problem_info;
                                    if(is_display)
                                        vm.goProblem(problem_index);

                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    startContest:function()
                    {
                        var obj=new Object();
                        axios.post(this.basic_url+'vJudgeAPI/joinContest/id/'+this.contest_id,"")
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('开始比赛成功！', 2000);
                                    delayRefresh(1000);
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
                    addZero:function(val)
                    {
                        if(val<10)
                            return '0'+val;
                        return val;
                    }
                },
                computed:{
                    contestStartTime:function()
                    {

                        var time=new Date(this.contest_start_time_ts);
                        /*
                        var tmp=this.running_time<this.contest_long?this.running_time:this.contest_long;
                        var val=new Date(time.getTime()+tmp*1000);
                        */
                        var val=time;
                        return val.getFullYear()+"/"+(val.getMonth()+1)+"/"+(val.getDate()+1)+" "+this.addZero(val.getHours())+":"+this.addZero(val.getMinutes());
                    },
                    contestEndTime:function()
                    {
                        var time=new Date(this.contest_start_time_ts);
                        var val=new Date(time.getTime()+this.contest_long*1000);
                        return val.getFullYear()+"/"+(val.getMonth()+1)+"/"+(val.getDate()+1)+" "+this.addZero(val.getHours())+":"+this.addZero(val.getMinutes());
                    }
                }


            }
        );
    </script>
<?php include('footer.php');?>