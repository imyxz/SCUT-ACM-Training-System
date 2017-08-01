<?php
class indexs extends SlimvcControllerCli{
    function IndexAction()
    {

    }
    function test()
    {
        var_dump($this->model("vj_oj_model")->getOjByName('wedf'));
    }
}