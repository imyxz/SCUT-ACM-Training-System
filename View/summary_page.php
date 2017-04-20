<?php include('header.php');?>
<div class="container-fluid">
    <div class="row">
    <div class="col-sm-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                比赛详情
            </div>
            <div class="panel-body contest-info">
                <div class="">
                    <h4 class="text-primary"><?php echo $contest_name;?></h4>
                    <h6 class="text-info"><?php echo $contest_description;?></h6>
                    <?php
                    foreach($team_info as &$one)
                    {
                        echo '<p>' . $one['name'] . ': ' . implode(" ",$one['player']) . "</p>";
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
        <div class="col-sm-10">
            <div class="panel panel-success">
                <div class="panel-heading">做题情况 <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#update_ac">更新我的做题状况</button></div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered " id="summary_table">
                        <thead>
                        <tr>
                            <th class="text-left">Team</th>
                            <?php
                                for($i=0;$i<$problem_count;$i++)
                                {
                                    echo '<th class="text-center">' . chr($i+65) .'</th>';
                                }
                            ?>

                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            function getSpan($row)
                            {
                                switch (intval($row['ac_status'])){
                                    case 1:
                                        return '<span class="text-success">'. $row['player_name'] . ' </span><br />';
                                    case 2:
                                        return '<span class="text-warning">'. $row['player_name'] . ' </span><br />';
                                    case 3:
                                        return '<span class="text-info">'. $row['player_name'] . ' </span><br />';
                                }
                            }
                                foreach($summary_info as $key => &$one)
                                {
                                    echo '<tr>'.
                                        '<td class="text-danger">'.
                                        $key.
                                        '</td>';
                                    for($i=1;$i<=$problem_count;$i++)
                                    {
                                        echo '<td class="text-center summary-td" data-problem_id="'. $i .'">';
                                        if(isset($one[$i]))
                                        {
                                            foreach($one[$i] as &$people)
                                            {
                                                echo getSpan($people);
                                            }
                                        }
                                        echo '</td>';
                                    }
                                        echo '</tr>';
                                }
                            ?>

                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-4 ">

                        </div>
                        <div class="col-sm-4">
                            <span class="label label-success ">AC</span>
                            <span class="label label-warning">TRY</span>
                            <span class="label label-info">补题</span>
                        </div>
                        <div class="col-sm-4">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-danger">
        <div class="panel-heading">Board</div>
        <div class="panel-body">
            <table class="table table-hover table-bordered ">
                <thead>
                <tr>
                    <th class="text-center">Rank</th>
                    <th class="text-left">Team</th>
                    <th class="text-center">Solve</th>
                    <th class="text-center">Penalty</th>
                    <?php
                    for($i=0;$i<$problem_count;$i++)
                    {
                        echo '<th class="text-center">' . chr($i+65) .'</th>';
                    }
                    ?>

                </tr>
                </thead>
                <tbody>
                <?php
                function secToTime($sec)
                {
                    $c=floor($sec/3600);
                    $sec=$sec-$c*3600;
                    $b=floor($sec/60);
                    $sec=$sec-$b*60;
                    $a=$sec;
                    if($a<10)
                        $a='0' . $a;
                    if($b<10)
                        $b='0' . $b;
                    if($c<10)
                        $c='0' . $c;
                    return "$c:$b:$a";
                }
                foreach($contest_board as &$one)
                {
                    echo '<tr>'.
                        '<td class="text-center">'.
                        $one['rank_index'].
                        '</td>'.
                        '<td class="text-left">'.
                        $one['group_name'].
                        '</td>'.
                        '<td class="text-center">'.
                        $one['problem_solved'].
                        '</td>'.
                        '<td class="text-center">'.
                        $one['penalty'].
                        '</td>';
                    for($i=1;$i<=$problem_count;$i++)
                    {

                        if(isset($one['ac_info']['submission'][$i]))
                        {
                            if($one['ac_info']['submission'][$i]['ac'])
                            {
                                echo '<td class="text-center bg-success">';
                                echo '<span>' . secToTime($one['ac_info']['submission'][$i]['ac_time']) .'</span>';
                                if($one['ac_info']['submission'][$i]['try'])
                                    echo '<br /><span>(-' . $one['ac_info']['submission'][$i]['try'] .')</span>';
                                echo '</td>';
                            }
                            else
                            {
                                if($one['ac_info']['submission'][$i]['try'])
                                {
                                    echo '<td class="text-center bg-danger">';
                                    echo '<span>(-' . $one['ac_info']['submission'][$i]['try'] .')</span>';
                                    echo '</td>';
                                }

                            }
                        }
                        else
                        {
                            echo '<td class="text-center">';
                            echo '</td>';
                        }

                    }
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="update_ac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h6 class="modal-title" id="myModalLabel">更新做题状况</h6>
                </div>
                <div class="modal-body" id="update_ac_body">
                    <?php
                    for($i=1;$i<=$problem_count;$i++)
                    {
                        echo '<form class="form-inline">';
                        echo '<label class="ac_label">' .  chr($i+64) .':</label>';
                        echo '<label class="ac_label text-success"><input type="radio" name="problem_'. $i .'" value="1" '. ($player_ac[$i]==1?'checked':'').'> 赛内参与做题并AC</label>';
                        echo '<label class="ac_label text-warning"><input type="radio" name="problem_'. $i .'" value="2" '. ($player_ac[$i]==2?'checked':'').'> 赛内参与做题但WA</label>';
                        echo '<label class="ac_label text-info"><input type="radio" name="problem_'. $i .'" value="3" '. ($player_ac[$i]==3?'checked':'').'> 赛后补题</label>';
                        echo '<label class="ac_label text-danger"><input type="radio" name="problem_'. $i .'" value="4" '. ($player_ac[$i]==4?'checked':'').'> 未动</label>';
                        echo '</form>';
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="window.location=window.location;">取消</button>
                    <button type="button" class="btn btn-primary" onclick="submitAcStatus();">保存</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function submitAcStatus()
    {

        var problem_count=<?php echo $problem_count?>;
        var upload="contest_id=" + <?php echo $contest_id;?>;
        for(var i=0;i<problem_count;i++)
        {
            upload+="&problem_"+(i+1)+"="+$("input[name='problem_"+ (i+1) +"']:checked").val();
        }
        $.post("<?php echo _Http;?>user/updateAcStatus",upload,function(response)
        {
            if(response.status==1)
            {
                $("#login-alert").html("登录成功！").show();
                //delayRefresh(1000);
            }
            else
            {
                $('#login-btn').removeAttr("disabled");
                $("#login-alert").html(decodeURI(response.message)).show();
            }
        })
    }
</script>
<?php include('footer.php');?>