<?php include('header.php');?>
    <div class="container">
        <h4>添加比赛</h4>
        <hr />
        <div class="row">
            <div class="col-md-5">

                <div class="input-group">
                    <span class="input-group-addon active">比赛名称</span>
                    <input type="text" class="form-control" value="" placeholder="contest name" size="8" id="addcontest-name" required/>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">比赛题数</span>
                    <input type="text" class="form-control" value="" placeholder="" id="addcontest-count" required/>
                </div>

                    <div class="input-group date form_datetime" data-date="" data-date-format="hh:ii - yyyy MM dd" data-link-field="dtp_input1" id="addcontest-starttime">
                        <span class="input-group-addon">开始时间</span>
                        <input class="form-control" type="text" value="">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                        <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                        <input type="hidden" id="dtp_input1" value="" />
                    </div>
                <div class="input-group date form_datetime" data-date="" data-date-format="hh:ii - yyyy MM dd" data-link-field="dtp_input2" id="addcontest-endtime">
                    <span class="input-group-addon">结束时间</span>
                    <input class="form-control" type="text" value="">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                    <input type="hidden" id="dtp_input2" value="" />
                </div>
                <div class="input-group">
                    <label class="form-label label label-info">Board数据</label>
                    <form class="form-inline ">
                        <label class="ac_label">数据来源：</label>
                        <label class="ac_label">
                            <input class="board_source_radio" type="radio" name="board_source" value="vj" checked>VJudge
                        </label>
                        <label class="ac_label">
                            <input class="board_source_radio" type="radio" name="board_source" value="zoj">ZOJ
                        </label>
                        <label class="ac_label">
                            <input class="board_source_radio" type="radio" name="board_source" value="seoj">软院oj
                        </label>

                    </form>
                    <textarea class="form-control" cols="100" rows="8" placeholder="此处粘贴来自vjudge的比赛数据，格式为JSON" id="addcontest-board"></textarea>
                </div>
                <label class="form-label label label-danger" id="source_board_tip">获取方式：https://vjudge.net/contest/rank/single/ + contest-id</label>
                <div class="input-group">
                    <label class="form-label label label-info">比赛备注</label>
                    <textarea class="form-control" cols="100" rows="8" placeholder="如比赛网址，题目来源" id="addcontest-description"></textarea>
                </div>
                <div class="alert alert-info" role="alert" style="display: none;" id="addcontest-alert">
                </div>
                <button class="btn btn-info btn-default pull-left" id="addcontest-btn" onclick="submitAddContest()">提交</button>
            </div>
        </div>

    </div>
    <script>
        function submitAddContest()
        {
            var start_time=new Date($("#addcontest-starttime").datetimepicker("getDate"));
            start_time=start_time.getTime()/1000;
            var end_time=new Date($("#addcontest-endtime").datetimepicker("getDate"));
            end_time=end_time.getTime()/1000;
            var contest_name=$("#addcontest-name").val();
            var problem_count=$("#addcontest-count").val();
            var contest_board=$("#addcontest-board").val();
            var contest_description=$("#addcontest-description").val();
            var source=$("input[name='board_source']:checked").val();
            $('#addcontest-btn').attr("disabled","disabled");
            $('#addcontest-btn').html("正在添加...数据量较大，请耐心等待");
            $.post("<?php echo _Http;?>contest/goAddContest","addcontest-name=" + encodeURI(contest_name) +
                "&addcontest-problem_count="  + encodeURI(problem_count)+
                "&addcontest-start_time="  + encodeURI(start_time)+
                "&addcontest-end_time="  + encodeURI(end_time)+
                "&addcontest-contest_board="  + encodeURI(contest_board) +
                "&addcontest-contest_description="  + encodeURI(contest_description) +
                "&addcontest-source="+encodeURI(source)
                ,function(response){

                    if(response.status==1)
                    {
                        $("#addcontest-alert").html("比赛已成功添加！正在跳转....." ).show();
                        delayJump("<?php echo _Http;?>contest/summary/id/"+response.contest_id,1000);
                    }
                    else
                    {
                        $('#addcontest-btn').removeAttr("disabled");
                        $("#addcontest-alert").html(decodeURI(response.message)).show();
                    }
                    $('#addcontest-btn').html("提交");

                });
        }
        $.fn.datetimepicker.dates['zh-CN'] = {
            days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
            daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
            daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            today: "今天",
            suffix: [],
            meridiem: ["上午", "下午"]
        };
        $('.form_datetime').datetimepicker({
            language:  'zh-CN',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1
        });
        $('.board_source_radio').on("change",function(e){
            switch($("input[name='board_source']:checked").val())
            {
                case 'vj':
                    $('#addcontest-board')[0].placeholder="此处粘贴来自vjudge的比赛数据，格式为JSON";
                    $('#source_board_tip').text("获取方式：https://vjudge.net/contest/rank/single/ + contest-id");
                    break;
                case 'zoj':
                    $('#addcontest-board')[0].placeholder="此处粘贴来自zoj的比赛数据，格式为文本，在榜单页点Export to txt";
                    $('#source_board_tip').text("获取方式：http://acm.zju.edu.cn/onlinejudge/showContestRankList.do?export=txt&contestId= + contest-id");
                    break;
                case 'seoj':
                    $('#addcontest-board')[0].placeholder="此处粘贴来自软院oj的比赛数据，格式为文本，复制榜单区域并经过填0处理";
                    $('#source_board_tip').text("获取方式：复制榜单区域并经过填0处理");
                    break;
            }
        });
    </script>

<?php include('footer.php');?>