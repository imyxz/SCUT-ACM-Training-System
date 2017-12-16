<?php
class indexs extends SlimvcControllerCli{
    function IndexAction()
    {

    }
    function test()
    {
        $tmp=$this->newClass("SpiderScutse");
        echo $tmp->ocr(file_get_contents("http://116.56.140.75:8000/JudgeOnline/vcode.php"));
        echo "\n";
    }
}