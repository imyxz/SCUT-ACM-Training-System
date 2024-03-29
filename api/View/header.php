<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0,maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="<?php echo _Http;?>css/materialize-icon.css">
    <link rel="stylesheet" href="<?php echo _Http;?>lib/materialize/dist/css/materialize.min.css">
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
    <script src="<?php echo _Http;?>lib/jquery/dist/jquery.min.js"></script>

    <script src="<?php echo _Http;?>lib/materialize/dist/js/materialize.min.js"></script>

    <script src="<?php echo _Http;?>lib/axios/dist/axios.min.js"></script>
    <script src="<?php echo _Http;?>lib/vue/dist/vue.min.js"></script>
    <script src="<?php echo _Http;?>lib/ace-builds/src-min/ace.js"></script>
    <script src="<?php echo _Http;?>lib/ace-builds/src-min/ext-language_tools.js"></script>
    <script src="<?php echo _Http;?>lib/pdfobject/pdfobject.min.js"></script>
    <script src="<?php echo _Http;?>lib/MathJax/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
    <script src="<?php echo _Http;?>js/common.js"></script>
    <link rel="stylesheet" href="<?php echo _Http;?>css/style.css?201708611">
    <title><?php echo $title;?> - SCUT Training System</title>

</head>
<body>
<div class="">
    <nav class="nav-extended">
        <div class="nav-wrapper light-blue">
            <a href="<?php echo _Http?>" class="brand-logo" style="margin-left: 20px;">SCUT-ACM</a>
            <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down ">
                <?php $index=1;?>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>" class="">首页</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>contest/addContest" class="waves-effect waves-teal">添加比赛</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>user/addTeam" class="waves-effect waves-teal">添加队伍</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>user/bindPlayer" class="waves-effect waves-teal">绑定小队</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>vJudge/allProblem" class="waves-effect waves-teal">SCUTVJ</a>
                </li>
                <?php if($isLogin)
                    {
                        ?><li><a class="btn-floating green dropdown-button" data-activates='user_dropdown'><i class="material-icons">perm_identity</i></a></li>
                        <ul id='user_dropdown' class='dropdown-content'>
                            <li><a href="<?php echo _Http;?>user/editInfo">更改信息</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo _Http;?>user/logOut">退出登录</a></li>
                        </ul>
                        <?php
                    }
                    else {
                        ?>
                <li><a class="btn-floating red" onclick='(function(){window.location="http://encuss.yxz.me/userAPI/loginFromQQ/site_id/3/viewing/" + encodeURIComponent("<?php echo _Http;?>user/loginFromEncuss/viewing/<?php echo urlencode(_Http . $_SERVER["REQUEST_URI"]);?>/")})()'><i class="material-icons">perm_identity</i></a></li>
                        <?php
                    }?>

            </ul>
            <ul class="side-nav" id="mobile-demo">
                <?php $index=1;?>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>" class="">首页</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>contest/addContest" class="waves-effect waves-teal">添加比赛</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>user/addTeam" class="waves-effect waves-teal">添加队伍</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>user/bindPlayer" class="waves-effect waves-teal">绑定队员</a>
                </li>
                <li <?php echo $active == $index ? "class=\"active\"" : ' ';
                $index++; ?>><a href="<?php echo _Http;?>vJudge/allProblem" class="waves-effect waves-teal">SCUTVJ</a>
                </li>

                    <?php if($isLogin)
                    {
                        ?>
                <li><a href="#" onclick=''>已登录</a></li>
                <li><a href="<?php echo _Http;?>user/editInfo">更改信息</a></li>
                <li><a href="<?php echo _Http;?>user/logOut">退出登录</a></li>
                        <?php
                    }
                    else {
                        ?>
                <li><a class="waves-effect waves-teal btn" onclick='(function(){window.location="http://encuss.yxz.me/userAPI/loginFromQQ/site_id/3/viewing/" + encodeURIComponent("<?php echo _Http;?>user/loginFromEncuss/viewing/<?php echo urlencode(_Http . $_SERVER["REQUEST_URI"]);?>/")})()'>立即登录</a></li>
                        <?php
                    }?>

            </ul>

        </div>
        <?php
        if(isset($addon_header))
            include($addon_header);
        ?>


    </nav>
</div>

