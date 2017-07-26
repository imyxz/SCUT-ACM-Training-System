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
    <div class="container" id="status_list_table">

        <div class="card-panel hoverable" >
            <table class="highlight bordered">
                <thead>
                <tr>
                    <th class="center-align">job id</th>
                    <th class="center-align">user name</th>
                    <th class="center-align">oj</th>
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
                        <td class="center-align">{{ status.job_id }}</td>
                        <td class="center-align">{{ status.user_nickname }}</td>
                        <td class="center-align">{{ status.oj_name }}</td>
                        <td class="center-align"><a :href="status.problem_id | generate_url" target="_blank">{{ status.problem_identity }}</a></td>
                        <td class="center-align" :class="{'green-text':status.ac_status==1,'red-text':status.ac_status!=1}">{{ status.wrong_info  }}</td>
                        <td class="center-align">{{ status.time_usage | time_filter }}</td>
                        <td class="center-align">{{ status.ram_usage | ram_filter }}</td>
                        <td class="center-align">{{ status.submit_time }}</td>
                        <td class="center-align" ><a class="btn-floating"  @click="displaySourceCode(status.job_id)"><i class="material-icons">search</i></a></td>

                    </tr>
                </tbody>
            </table>
            <div style="position: fixed;top:80px;width:100%;left:0;">
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
        </div>
        <div id="code-modal" class="modal" style="width:90%;overflow: visible;height:100%">
            <div class="modal-content">
                <div id="editor" ></div>
            </div>
        </div>
    </div>
    <script>
        var editor;
        var status_list_table=new Vue(
            {
                el: "#status_list_table",
                data: {
                    status_info:[],
                    loading: true,
                    ac_status:["In queue","Accept","PARTIAL","COMPILATION ERROR","RUNTIME ERROR","WRONG ANSWER","PRESENTATION ERROR","TIME LIMIT EXCEEDED","MEMORY LIMIT EXCEEDED","IDLENESS LIMIT EXCEEDED","SECURITY VIOLATED","CRASHED","INPUT PREPARATION CRASHED","CHALLENGED","SKIPPED","Testing","REJECTED"],
                    waiting:0,
                    basic_url:'<?php echo _Http;?>'
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
                        return "" +parseFloat(val).toFixed(2)+ " "+unit;
                    },
                    time_filter:function(val)
                    {
                        var unit='ms';
                        if(val>=1000)
                        {
                            val/=1000;
                            unit='s';
                        }
                        return "" + parseFloat(val).toFixed(2)+ " "+unit;
                    },
                    result_filter:function(val)
                    {
                        var tmp=parseInt(val);
                        if(status_list_table.ac_status.length>tmp)
                            return status_list_table.ac_status[tmp];
                        else
                            return '';
                    },
                    generate_url:function(val)
                    {
                        return status_list_table.basic_url+"vJudge/viewProblem/id/"+val;
                    }
                },
                created: function(){
                    this.updateStatus();
                },
                methods:{
                    updateStatus:function(){
                        axios.get('<?php echo _Http;?>vJudgeAPI/getMyStatus/')
                            .then(function(response)
                            {
                                status_list_table.status_info=response.data.status_info;
                                status_list_table.waiting=0;
                                for(var i in response.data.status_info)
                                {
                                    if(response.data.status_info[i].running_status!=3)
                                        status_list_table.waiting++;
                                }
                                setTimeout(function(){status_list_table.loading=false;},1000);
                                if(status_list_table.waiting>0)
                                    setTimeout(status_list_table.updateStatus,3000);
                                Materialize.toast('<span class="">页面已刷新</span>' , 2000);

                            })

                            .catch(function(err)
                            {
                                setTimeout(status_list_table.updateStatus,3000);
                            });
                    },
                    displaySourceCode:function(job_id)
                    {
                        axios.get('<?php echo _Http;?>vJudgeAPI/getJobSourceCode/id/'+job_id)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.source_code);
                                    $('#code-modal').modal('open');
                                }
                                else
                                {
                                    Materialize.toast('<span class="">查看失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    }
                }

            }
        );
        $(document).ready(function() {
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
            $('.modal').modal();

        });
    </script>
<?php include('footer.php');?>