<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0,maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="<?php echo _Http;?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _Http;?>css/flat-ui.min.css">
    <link rel="stylesheet" href="<?php echo _Http;?>css/bootstrap-datetimepicker.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo _Http;?>js/html5shiv.min.js"></script>
    <script src="<?php echo _Http;?>js/respond.min.js"></script>
    <![endif]-->
    <!-- <style>
        h1,h3,h4,textarea,button,a {
            font-family: Microsoft YaHei;
        }
    </style> -->

    <script src="<?php echo _Http;?>js/jquery-2.2.4.min.js"></script>
    <!--<script src="<?php echo _Http;?>js/bootstrap.min.js"></script>-->
    <script src="<?php echo _Http;?>js/flat-ui.min.js"></script>
    <script src="<?php echo _Http;?>js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo _Http;?>js/common.js"></script>
    <link rel="stylesheet" href="<?php echo _Http;?>css/style.css?20170216">
    <title><?php echo $title;?> - SCUT Training System</title>

</head>
<body>
<nav class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="<?php echo _Http;?>">SCUT Training System</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-01">
            <ul class="nav navbar-nav">
                <?php $index=1;?>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http . "";?>">首页</a></li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http . "contest/addContest";?>">添加比赛</a></li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http . "user/addTeam";?>">添加小队</a></li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http . "user/bindPlayer";?>">绑定小队</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
<?php if($if_login==true)
    echo '<li><p class="navbar-text">你好, <a class="navbar-link" href="#">' . $nickname .'</a></p></li>';
else {
    ?>
    <li><a href="#" data-toggle="modal" data-target="#login_form">登录</a></li>
    <li><a href="#" data-toggle="modal" data-target="#register_form">注册</a></li>
    <?php
}
                ?>

            </ul>

        </div><!-- /.navbar-collapse -->
    </div>

</nav><!-- /navbar -->
