<div class="nav-content light-blue">
    <ul class="tabs tabs-transparent">
        <?php $index=1;?>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/allProblem/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">所有题目</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/allProblemList/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">题目列表</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/allContest/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">比赛列表</a>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/viewTag/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">标签列表</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/myStatus/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">提交状态</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/newList/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">新建题目列表</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/newContest/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">新建比赛</a>
        </li>
        <li class="tab"><a href="<?php echo _Http;?>vJudge/onlineIDE/" onclick="" class=" <?php echo $sub_active == $index ? "active " : ' ';
            $index++; ?>">Online IDE</a>
        </li>
    </ul>
</div>