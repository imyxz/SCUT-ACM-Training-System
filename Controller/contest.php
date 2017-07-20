<?php
/**
 * User: imyxz
 * Date: 2017-07-18
 * Time: 22:52
 * Github: https://github.com/imyxz/
 */
class contest extends SlimvcController
{
    function summary()
    {
        $this->title="比赛详情";
        $this->active=0;
        $this->contest_id=intval($_GET['id']);
        $this->isLogin=$this->helper("user_helper")->isLogin();

        $this->view("summary_page");
    }
    function addContest()
    {
        $this->title="添加比赛";
        $this->active=2;
        $this->isLogin=$this->helper("user_helper")->isLogin();

        $this->view("add_contest");
    }
}