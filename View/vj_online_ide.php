<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        .button-save{
            position: absolute;
            right: 50px;
            bottom: 80px;
        }
        .button-run{
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
<div class="row" id="problem_list_table">
    <div class="l12">
        <div id="editor" >#include &lt;iostream&gt;
using namespace std;
int main()
{
    cout&lt;&lt;"Hello World!"&lt;&lt;endl;
}</div>
        <div class="button-save">
            <a class="btn-floating btn-large waves-effect waves-light blue " onclick="$('#modal_draft').modal('open');"><i class="material-icons">save</i></a>
        </div>
        <div class="button-run">
            <a class="btn-floating btn-large waves-effect waves-light green " onclick="$('#result-modal').modal('open');"><i class="material-icons">play_arrow</i></a>
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
                    <div class="col l4 left-align" style="padding:8px;">
                        <span>状态：{{ running_status }}</span>
                    </div>
                    <div class="col l4" >

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
    </div>
</div>

    <script>
        var editor;
        var problem_list_table=new Vue(
            {
                el: "#problem_list_table",
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
                    old_code:''
                },
                filters:{
                },
                created: function(){
                    setTimeout(this.updateStatus,1000);
                    setTimeout(this.autoSave,60000);
                    this.getUserDraft();

                },
                methods:{
                    submitJob:function()
                    {
                        var obj=new Object;
                        obj.source_code=editor.getValue();
                        obj.input_code=this.input_code;
                        Materialize.toast('任务提交中', 2000);
                        axios.post(this.basic_url+'onlineIDE/submitJob/',JSON.stringify(obj))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('任务提交成功！', 2000);
                                    problem_list_table.job_id=response.data.job_id;
                                    problem_list_table.is_updating=true;
                                    problem_list_table.output_code='';
                                    problem_list_table.error_code='';
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
                        if(problem_list_table.is_updating==true)
                        {
                            axios.get(this.basic_url+'onlineIDE/getJobResult/job_id/' + this.job_id)
                                .then(function(response)
                                {
                                    if(response.data.status==0)
                                    {
                                        var result=response.data.result;
                                        if(result.is_finished==1)
                                        {
                                            problem_list_table.running_status="运行完成";
                                            problem_list_table.output_code=result.output_code;
                                            problem_list_table.error_code=result.error_code;
                                            if(result.error_code!='')
                                                $('ul.tabs').tabs('select_tab', 'error_div');

                                            problem_list_table.is_updating=false;
                                        }
                                        else
                                        {
                                            if(result.is_running==0)
                                                problem_list_table.running_status='正在排队中';
                                            else
                                                problem_list_table.running_status='正在执行';
                                        }
                                        Materialize.toast(problem_list_table.running_status, 2000);


                                    }
                                    else
                                    {
                                        Materialize.toast('<span class="">更新失败：'+response.data.err_msg+'</span>' , 2000);
                                    }
                                    setTimeout(problem_list_table.updateStatus,1000);

                                });
                        }
                        else
                            setTimeout(problem_list_table.updateStatus,1000);
                    },
                    getUserDraft:function()
                    {
                        axios.get(this.basic_url+'onlineIDE/getUserDraft/')
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    problem_list_table.drafts=response.data.drafts;
                                    problem_list_table.auto_saves=response.data.autosave;
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
                                    editor.setValue(response.data.code,1);
                                    problem_list_table.old_code=response.data.code;
                                    Materialize.toast('<span class="">读取成功！</span>' , 2000);
                                    $('#modal_draft').modal('close');
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
                                    this.getUserDraft();

                                }
                                else
                                {
                                    Materialize.toast('<span class="">保存失败：'+response.data.err_msg+'</span>' , 2000);
                                }
                            });
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
            problem_list_table.old_code=editor.getValue();



        });
    </script>
<?php include('footer.php');?>