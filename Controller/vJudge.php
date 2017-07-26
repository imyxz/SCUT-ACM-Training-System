<?php
/**
 * User: imyxz
 * Date: 2017-07-26
 * Time: 15:01
 * Github: https://github.com/imyxz/
 */
class vJudge extends SlimvcController
{
    function viewProblem()
    {
        $this->title="题目";
        $this->active=5;
        $this->problem_id=intval($_GET['id']);
        $this->isLogin=$this->helper("user_helper")->isLogin();


        $this->view("vj_view_problem");
    }
    function myStatus()
    {
        $this->title="My Status";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();


        $this->view("vj_view_my_status");
    }
}