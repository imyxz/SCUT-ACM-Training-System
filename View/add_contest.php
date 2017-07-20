<?php include('header.php');?>
    <div class="container" id="add_contest">
        <p class="big-text">添加比赛</p>
        <div class="row">
            <div class="col l4">

                        <div class="row">
                            <div class="input-field col l12">
                                <input id="contest_name" type="text" class="validate" v-model="contest_name">
                                <label for="contest_name">比赛名称</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col l12">
                                <input id="problem_count" type="text" class="validate" v-model.number="problem_count">
                                <label for="problem_count">比赛题数<span v-if="problem_count>0"> A-{{ String.fromCharCode(problem_count+64) }}</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col l12">
                                <input id="contest_start_date" type="date" class="datepicker" onchange="add_contest.contest_start_date=$(this).val()">
                                <label for="contest_start_date">开始日期</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col l12">
                                <input id="contest_start_time" type="text" class="timepicker"  onchange="add_contest.contest_start_time=$(this).val()" >
                                <label for="contest_start_time">开始时间</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col l12">
                                <input id="contest_end_date" type="date" class="datepicker"  onchange="add_contest.contest_end_date=$(this).val()">
                                <label for="contest_end_date">结束日期</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col l12">
                                <input id="contest_end_time" type="text" class="timepicker"  onchange="add_contest.contest_end_time=$(this).val()" >
                                <label for="contest_end_time">结束时间</label>
                            </div>
                        </div>
                <div class="row right-align">
                    <a class="waves-effect waves-light btn " @click="submitContest()">保存</a>

                </div>


            </div>
            <div class="col l8">
                        <div class="row">
                            <div class="input-field col l12">
                                <textarea id="contest_desc" class="materialize-textarea" rows="6" v-model="contest_desc"></textarea>
                                <label for="contest_desc">比赛简述</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col l12">
                                <textarea id="csv_data" class="materialize-textarea" rows="12" v-model="csv_data"></textarea>
                                <label for="csv_data">比赛board</label>
                            </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        var add_contest=new Vue(
            {
                el: "#add_contest",
                data: {
                    contest_name:'',
                    problem_count:'',
                    contest_start_date:'',
                    contest_start_time:'',
                    contest_end_date:'',
                    contest_end_time:'',
                    csv_data:'',
                    contest_desc:'',
                    basic_url:'<?php echo _Http;?>'
                },
                created: function(){

                },
                methods:
                {
                    submitContest: function()
                    {
                        var start_date=new Date();
                        var tmp=this.contest_start_date.split("/");
                        start_date.setFullYear(tmp[0],tmp[1],tmp[2]);
                        tmp=this.contest_start_time.split(":");
                        start_date.setHours(tmp[0],tmp[1],0);
                        var end_date=new Date();
                        tmp=this.contest_end_date.split("/");
                        end_date.setFullYear(tmp[0],tmp[1],tmp[2]);
                        tmp=this.contest_end_time.split(":");
                        end_date.setHours(tmp[0],tmp[1],0);

                        var post=new Object();
                        post.contest_starttime=(start_date.getTime()/1000).toFixed(0);
                        post.contest_endtime=(end_date.getTime()/1000).toFixed(0);
                        post.contest_name=this.contest_name;
                        post.problem_count=this.problem_count;
                        post.contest_desc=this.contest_desc;
                        post.csv_data=this.csv_data;
                        Materialize.toast('正在提交数据', 5000);
                        axios.post(this.basic_url+'contestAPI/addContestFromCSV/',JSON.stringify(post))
                            .then(function(response)
                            {
                                if(response.data.status==0)
                                {
                                    Materialize.toast('提交成功！', 5000);
                                    delayJump(add_contest.basic_url+'contest/summary/id/'+response.data.contest_id,1000);
                                }
                                else
                                {
                                    Materialize.toast('<span class="">提交失败：'+response.data.err_msg+'</span>' , 5000);
                                }
                            })
                            .catch(function(error)
                            {
                                Materialize.toast('<span class="">提交失败：'+'网络通信错误'+'</span>' , 5000);
                            })


                    }
                }

            }
        );
    </script>

<?php include('footer.php');?>