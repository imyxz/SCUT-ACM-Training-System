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
        .tabs .tab a{
            color:white;
        }
        .tabs .tab a:hover,.tabs .tab a.active {
            background-color:transparent;
            color:white;
        }
        .tabs .tab.disabled a,.tabs .tab.disabled a:hover {
            color:rgba(255,255,255,0.7);
        }
        .tabs .indicator {
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
            <a class="btn-floating btn-large waves-effect waves-light blue "><i class="material-icons">save</i></a>
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
    </div>
</div>
    <script>
        var editor;
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



        });
    </script>
    <script>
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
                    is_updating:false
                },
                filters:{
                },
                created: function(){
                    setTimeout(this.updateStatus,1000);
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

                                })
                                .catch(function(error)
                                {
                                    Materialize.toast('<span class="">更新失败：'+'网络通信错误'+'</span>' , 2000);
                                    setTimeout(problem_list_table.updateStatus,1000);

                                });
                        }
                        else
                            setTimeout(problem_list_table.updateStatus,1000);
                    }
                }


            }
        );
    </script>
<?php include('footer.php');?>