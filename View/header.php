<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="<?php echo _Http;?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _Http;?>css/flat-ui.min.css">

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
    <link rel="stylesheet" href="<?php echo _Http;?>css/style.css?20170216">
    <title><?php echo $title;?> - </title>

</head>
<body>
<nav class="navbar navbar-inverse" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
            <span class="sr-only">Toggle navigation</span>
        </button>
        <a class="navbar-brand" href="<?php echo _Http;?>">站点名</a>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse-01">
        <ul class="nav navbar-nav">
            <?php $index=1;?>
            <li <?php echo $active == $index ? "class=\"active\"" : ' ';
            $index++; ?>><a href="<?php echo _Http . "watchface/newWatchface/";?>">标签一</a></li>

        </ul>
        <ul class="nav navbar-nav navbar-right">
        </ul>

    </div><!-- /.navbar-collapse -->
</nav><!-- /navbar -->
