<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        .button-tools{
            position: absolute;
            right: 50px;
            bottom: 80px;
        }
        .button-run {
            position: absolute;
            right: 50px;
            bottom: 150px;
        }
        #result-modal .tabs .tab a{
            color:white;
        }
        #result-modal .tabs .tab a:hover,.tabs .tab a.active {
            background-color:transparent;
            color:white;
        }
        #result-modal .tabs .tab.disabled a,.tabs .tab.disabled a:hover {
            color:rgba(255,255,255,0.7);
        }
        #result-modal .tabs .indicator {
            background-color:white;
        }
    </style>
<div class="row" id="vm">
    <div class="l12">
        <div id="editor" ></div>

        <div class="button-run">
            <a class="btn-floating btn-large waves-effect waves-light green " onclick="$('#result-modal').modal('open');"><i class="material-icons">play_arrow</i></a>

        </div>
        <div class="fixed-action-btn horizontal button-tools">
            <a class="btn-floating btn-large blue">
                <i class="large material-icons">toc</i>
            </a>
            <ul>
                <li>
                    <a class="btn-floating waves-effect waves-light green lighten-1 " onclick="$('#modal_setting').modal('open');"><i class="material-icons">settings</i></a>
                </li>
                <li>
                    <a class="btn-floating  waves-effect waves-light blue " onclick="$('#modal_draft').modal('open');"><i class="material-icons">save</i></a>
                </li>
                <li>
                    <a class="btn-floating waves-effect waves-light yellow darken-4 " @click="formatCode"><i class="material-icons">spellcheck</i></a>
                </li>
                <li>
                    <a class="btn-floating  waves-effect waves-light red " @click="shareCode"><i class="material-icons">share</i></a>
                </li>

            </ul>
        </div>

        <div id="result-modal" class="modal modal-fixed-footer black white-text" style="overflow: visible;">
            <div class="modal-content " style="padding:0;overflow: visible;">
                        <ul class="tabs black white-text tabs-fixed-width">
                            <li class="tab col s4"><a class="active" href="#input_div">输入数据</a></li>
                            <li class="tab col s4"><a href="#output_div">输出结果</a></li>
                            <li class="tab col s4"><a href="#error_div">编译器输出</a></li>
                        </ul>

                <div id="input_div" class="col s12 input-field" style="height: 90%">
                    <textarea id="input_code" rows="100" style="height: 100%" v-model="input_code"></textarea>
                    <label for="input_code"></label>
                </div>
                <div id="output_div" class="col s12 input-field" style="height: 90%">
                    <textarea id="output_code" rows="100" style="height: 100%" v-model="output_code"></textarea>
                    <label for="output_code"></label>
                </div>
                <div id="error_div" class="col s12 input-field" style="height: 90%">
                    <textarea id="error_code" rows="100" style="height: 100%" v-model="error_code"></textarea>
                    <label for="error_code"></label>
                </div>


            </div>
            <div class="modal-footer black white-text" id="modal_footer">
                <div class="row" key="div_2" style="font-size: 20px; ">
                    <div class="col l8 left-align" style="padding:8px;">
                        <span>状态：{{ running_status }}</span>
                    </div>

                    <div class="col l4 right-align" >
                        <a href="#" class="btn waves-effect waves-blue" @click="submitJob">提交运行</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_draft" class="modal bottom-sheet">
            <div class="modal-content">
                <div class="row">
                    <div class="col l6">
                        <h4>手动保存：</h4>
                        <div class="collection">
                            <div class="collection-item input-field"><span class="badge"><a href="#" @click="saveCode(false)"><i class="material-icons green-text text-darken-1">save</i></a></span><input type="text" style="width:80%;margin:0;height:22px;" placeholder="请输入保存代码的标题" v-model="new_draft_title"/></div>
                            <a href="#" v-for="draft in drafts" @click="readCode(draft.draft_id)" class="collection-item" ><span class="badge">{{draft.save_time}}</span>{{draft.draft_title}}</a>
                        </div>
                    </div>
                    <div class="col l6">
                        <h4>自动保存：</h4>
                        <div class="collection">
                            <a href="#"  class="collection-item" v-for="draft in auto_saves" @click="readCode(draft.draft_id)"><span class="new badge" data-badge-caption="自动保存"></span>{{draft.save_time}}</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_setting" class="modal bottom-sheet">
            <div class="modal-content">
                <div class="row">
                    <div class="col l11">
                        <p>选择编程语言：</p>
                        <input type="radio" value="0" id="code-type-c" v-model="code_type"/><label for="code-type-c" class="green-text text-lighten-2 medium-text">C</label>
                        <input type="radio" value="1" id="code-type-cpp" v-model="code_type"/><label for="code-type-cpp" class="green-text text-lighten-2 medium-text">C++</label>
                        <input type="radio" value="2" id="code-type-java" v-model="code_type"/><label for="code-type-java" class="green-text text-lighten-2 medium-text">JAVA</label>
                        <input type="radio" value="3" id="code-type-php7" v-model="code_type"/><label for="code-type-php7" class="green-text text-lighten-2 medium-text">PHP7</label>
                        <input type="radio" value="4" id="code-type-pascal" v-model="code_type"/><label for="code-type-pascal" class="green-text text-lighten-2 medium-text">PASCAL</label>
                        <input type="radio" value="5" id="code-type-python" v-model="code_type"/><label for="code-type-python" class="green-text text-lighten-2 medium-text">PYTHON3</label>


                    </div>

                </div>
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
</div>

    <script>
        var editor;
        var basic_url='<?php echo _Http;?>';

        var vm=new Vue(
            {
                el: "#vm",
                data: {
                    loading: true,
                    basic_url:'<?php echo _Http;?>',
                    running_status:'',
                    input_code:'',
                    output_code:'',
                    error_code:'',
                    job_id:0,
                    is_updating:false,
                    drafts:[],
                    auto_saves:[],
                    new_draft_title:'',
                    old_code:'',
                    init_code:'',
                    job_code_id:<?php echo $job_code_id;?>,
                    share_code_id:<?php echo $share_code_id;?>,
                    code_type:-1,
                    cur_share_id:0
                },
                created: function(){
                    setTimeout(this.updateStatus,1000);
                    setTimeout(this.autoSave,60000);
                    this.getUserDraft();


                },
                watch:{
                  code_type:function(new_type)
                  {
                      switch( parseInt(new_type)) {
                          case 0:
                              editor.getSession().setMode("ace/mode/c_cpp");
                              break;
                          case 1:
                              editor.getSession().setMode("ace/mode/c_cpp");
                              break;
                          case 2:
                              editor.getSession().setMode("ace/mode/java");
                              break;
                          case 3:
                              editor.getSession().setMode("ace/mode/php");
                              break;
                          case 4:
                              editor.getSession().setMode("ace/mode/pascal");
                              break;
                          case 5:
                              editor.getSession().setMode("ace/mode/python");
                              break;
                      }
                      localStorage.code_type=new_type;


                  }
                },
                methods:{
                    submitJob:function()
                    {
                        var obj=new Object;
                        obj.source_code=editor.getValue();
                        obj.input_code=this.input_code;
                        obj.code_type=this.code_type;
                        Materialize.toast('任务提交中', 2000);
                        axios.post(this.basic_url+'onlineIDE/submitJob/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('任务提交成功！', 2000);
                                    vm.job_id=response.data.job_id;
                                    vm.is_updating=true;
                                    vm.output_code='';
                                    vm.error_code='';
                                    $('ul.tabs').tabs('select_tab', 'output_div');


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
                    updateStatus:function()
                    {
                        if(vm.is_updating==true)
                        {
                            axios.get(this.basic_url+'onlineIDE/getJobResult/job_id/' + this.job_id)
                                .then(function(response)
                                {
                                    if(response.data.status==0)
                                    {
                                        var result=response.data.result;
                                        var tips="";
                                        switch(parseInt(result.job_status))
                                        {
                                            case 0:
                                            case 1:
                                                vm.running_status='正在排队中';
                                                tips='正在排队中';
                                                break;
                                            case 2:
                                                vm.running_status='正在执行';
                                                tips='正在执行';

                                                break;
                                            case 3:
                                                vm.running_status="运行完成   时间: " + vm.time_filter(result.job_info.time_usage) +"  内存: "+vm.ram_filter(result.job_info.mem_usage);
                                                vm.output_code=result.job_info.program_stdout;
                                                vm.error_code=result.job_info.compile_error;
                                                tips='运行完成';

                                                if(result.job_info.compile_state!=0)
                                                    $('ul.tabs').tabs('select_tab', 'error_div');

                                                vm.is_updating=false;
                                                break;
                                        }
                                        Materialize.toast(tips, 2000);


                                    }
                                    else
                                    {
                                        Materialize.toast('<span class="">更新失败：'+response.data.err_msg+'</span>' , 2000);
                                    }
                                    setTimeout(vm.updateStatus,1000);

                                });
                        }
                        else
                            setTimeout(vm.updateStatus,1000);
                    },
                    getUserDraft:function()
                    {
                        axios.get(this.basic_url+'onlineIDE/getUserDraft/')
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    vm.drafts=response.data.drafts;
                                    vm.auto_saves=response.data.autosave;
                                }
                                else
                                {
                                    Materialize.toast('<span class="">读取失败：'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    autoSave:function()
                    {
                        var code=editor.getValue();
                        if(code!=this.old_code)
                        {
                            this.saveCode(true);
                        }
                        setTimeout(this.autoSave,60000);
                    },
                    readCode:function(id)
                    {
                        axios.get(this.basic_url+'onlineIDE/getDraftCode/id/'+id)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.code,-1);
                                    vm.old_code=response.data.code;
                                    Materialize.toast('<span class="">读取成功！</span>' , 2000);
                                    $('#modal_draft').modal('close');
                                }
                                else
                                {
                                    Materialize.toast('<span class="">读取失败：'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    readJobCode:function(id)
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getJobSourceCode/id/'+id)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.source_code,-1);
                                    vm.old_code=response.data.source_code;
                                    Materialize.toast('<span class="">读取成功！</span>' , 2000);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">读取失败：'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    readShareCode:function(id)
                    {
                        axios.get(this.basic_url+'onlineIDE/getShareCode/id/'+id)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.source_code,-1);
                                    vm.old_code=response.data.source_code;
                                    vm.code_type=response.data.code_type;
                                    Materialize.toast('<span class="">读取成功！</span>' , 2000);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">读取失败：'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    },
                    saveCode:function(is_autosave)
                    {
                        var obj=new Object;
                        if(is_autosave)
                        {
                            obj.is_autosave=true;
                            obj.draft_title='';
                            this.old_code=editor.getValue();
                        }
                        else
                        {
                            obj.is_autosave=false;
                            obj.draft_title=this.new_draft_title;
                        }
                        obj.source_code=editor.getValue();
                        axios.post(this.basic_url+'onlineIDE/saveDraft/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    if(is_autosave)
                                    {
                                        Materialize.toast('<span class="">自动保存成功！</span>' , 2000);
                                    }
                                    else
                                    {
                                        Materialize.toast('<span class="">保存成功！</span>' , 2000);
                                        $('#modal_draft').modal('close');

                                    }
                                    vm.getUserDraft();

                                }
                                else
                                {
                                    Materialize.toast('<span class="">保存失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    },
                    formatCode:function()
                    {
                        var obj=new Object;
                        obj.source_code=editor.getValue();
                        obj.code_type=this.code_type;
                        axios.post(this.basic_url+'onlineIDE/formatCode/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.format_code,-1);
                                    Materialize.toast('<span class="">格式化代码成功！</span>' , 2000);

                                }
                                else
                                {
                                    Materialize.toast('<span class="">格式化代码失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    },
                    loadDefaultCode:function(code_type)
                    {
                        axios.get(this.basic_url+'onlineIDE/getCodeTypeDefaultCode/codeType/'+code_type)
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    editor.setValue(response.data.code,-1);
                                    //Materialize.toast('<span class="">读取默认代码成功</span>' , 2000);

                                }
                                else
                                {
                                    Materialize.toast('<span class="">读取默认代码失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
                    },
                    shareCode:function()
                    {
                        var obj=new Object;
                        obj.source_code=editor.getValue();
                        obj.code_type=this.code_type;
                        axios.post('<?php echo _Http;?>onlineIDE/shareCode/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    vm.cur_share_id=response.data.share_id;
                                    $('#share-modal').modal('open');
                                    Materialize.toast('<span class="">代码已分享</span>' , 3000);

                                }
                                else
                                {
                                    Materialize.toast('<span class="">设置失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });

                    },
                    ram_filter: function (val) {
                        var unit = 'B';
                        if (val >= 1000) {
                            val /= 1024;
                            unit = 'KB';
                            if (val >= 1000) {
                                val /= 1024;
                                unit = 'MB';
                                if (val >= 1000) {
                                    val /= 1024;
                                    unit = 'GB';
                                    if (val >= 1000) {
                                        val /= 1024;
                                        unit = 'TB';
                                    }
                                }
                            }
                        }
                        return "" + parseFloat(val).toFixed(2) + " " + unit;
                    },
                    time_filter: function (val) {
                        var unit = 'ms';
                        if (val >= 1000) {
                            val /= 1000;
                            unit = 's';
                        }
                        return "" + parseFloat(val).toFixed(2) + " " + unit;
                    }
                },
                filters:{
                    share_url:function(val)
                    {
                        return basic_url+"vJudge/onlineIDE/shareCode/"+val;
                    }
                }



            }
        );
    </script>
    <script>

        $(document).ready(function(){
            ace.require("ace/ext/language_tools");
            editor = ace.edit("editor");
            editor.getSession().setMode("ace/mode/c_cpp");
            editor.setTheme("ace/theme/vibrant_ink");
            editor.setFontSize(16);
            editor.setOptions({
                enableBasicAutocompletion: true,
                enableSnippets: true,
                enableLiveAutocompletion: true
            });
            $('.modal').modal({
                    ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                        $('ul.tabs').tabs();
                        $('ul.tabs').tabs('select_tab', 'input_div');
                    }
                }
            );
            vm.old_code=editor.getValue();
            if(localStorage.code_type==null)
                localStorage.code_type=1;
            vm.code_type=localStorage.code_type;
            if(vm.job_code_id>0)
                vm.readJobCode(vm.job_code_id);
            else if(vm.share_code_id>0)
                vm.readShareCode(vm.share_code_id);
            else
                vm.loadDefaultCode(localStorage.code_type);


        });
    </script>
<?php include('footer.php');?>