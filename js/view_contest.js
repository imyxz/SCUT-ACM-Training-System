vm=new Vue(
    {
        el: "#container",
        data: {
            loading: true,
            basic_url:'',
            problem_list:[],
            problem_info:[],
            problem_status:[],
            contest_id:0,
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
            this.basic_url=basic_url;
            this.contest_id=contest_id;
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
                axios.get(vm.basic_url+'vJudgeAPI/getJobSourceCode/id/'+job_id)
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
                axios.post(vm.basic_url+'vJudgeAPI/setJobShare/',JSON.stringify(obj))
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
