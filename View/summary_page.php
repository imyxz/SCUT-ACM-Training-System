<?php include('header.php');?>
<div class="container">
    <div class="row">
    <div class="col-sm-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                比赛详情
            </div>
            <div class="panel-body contest-info">
                <div class="">
                    <h4 class="text-primary"><?php echo $contest_name;?></h4>
                    <h6 class="text-info"><?php echo $contest_description;?></h6>
                    <?php
                    foreach($team_info as $one)
                    {
                        echo '<p>' . $one['name'] . ': ' . implode(" ",$one['player']) . "</p>";
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
        <div class="col-sm-9">
            <div class="panel panel-success">
                <div class="panel-heading">做题情况</div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered ">
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
                                        return '<span class="text-success">'. $row['player_name'] . ' </span>';
                                    case 2:
                                        return '<span class="text-warning">'. $row['player_name'] . ' </span>';
                                    case 3:
                                        return '<span class="text-info">'. $row['player_name'] . ' </span>';
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
                                        echo '<td class="text-center">';
                                        if(isset($one[$i]))
                                        {
                                            foreach($one[$i] as $people)
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
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include('footer.php');?>