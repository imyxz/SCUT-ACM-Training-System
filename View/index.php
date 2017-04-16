<?php include('header.php');?>
<div class="container">
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th class="text-left">比赛名称</th>
                <th class="text-left">描述</th>
                <th class="text-center">操作</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach($contest_list as $one)
            {
                echo '<tr>'.
                        '<td class="text-danger">'.
                            $one['contest_name'] .
                        '</td>'.
                        '<td class="text-info">'.
                            $one['contest_description'] .
                        '</td>'.
                        '<td class="text-center">'.
                            '<a class="btn btn-sm btn-primary" href="'. _Http  .'contest/summary/id/' . $one['contest_id'] .'">查看报表</a>' .
                        '</td>'.
                    '</tr>';
            }
        ?>

        </tbody>
    </table>
</div>
<?php include('footer.php');?>