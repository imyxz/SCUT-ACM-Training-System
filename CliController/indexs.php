<?php
class indexs extends SlimvcControllerCli{
    function IndexAction()
    {

    }
    function test()
    {
        $tmp=$this->newClass("SpiderCodeforces");
        $tmp->test();
        $tmp->queryJob();
        var_dump($tmp->getQueryResult());
    }
}