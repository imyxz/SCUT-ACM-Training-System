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
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=0;

        $this->view("vj_view_problem");
    }
    function myStatus()
    {
        $this->title="My Status";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=5;

        $this->view("vj_view_my_status");
    }
    function allProblem()
    {
        $this->title="所有题目";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=1;

        $this->view("vj_view_all_problem");
    }
    function problemList()
    {
        $this->title="查看题目集";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=2;
        $this->list_id=intval($_GET['id']);
        $this->view("vj_list_problems");

    }
    function editList()
    {
        $this->title="编辑题目集";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=0;
        $this->list_id=intval($_GET['id']);
        $this->view("vj_edit_problem_list");
    }
    function newList()
    {
        $this->title="新建题目集";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=6;
        $this->list_id=0;
        $this->view("vj_edit_problem_list");
    }
    function newContest()
    {
        $this->title="新建比赛";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=7;
        $this->contest_id=0;
        $this->view("vj_edit_contest");
    }
    function allProblemList()
    {
        $this->title="所有题目集";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=2;
        $this->view("vj_view_all_list");
    }
    function onlineIDE()
    {
        $this->title="online IDE";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=8;
        if(isset($_GET['jobCode']))
            $this->job_code_id=intval($_GET['jobCode']);
        else
            $this->job_code_id=0;
        $this->view("vj_online_ide");
    }
    function viewTag()
    {
        $this->title="Tags";
        $this->active=5;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=4;
        $this->tag_name=urldecode(@$_GET['name']);
        $this->view("vj_view_tag_list");
    }
    function contest()
    {
        $contest_id=intval($_GET["id"]);
        $this->title="比赛";
        $this->active=3;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=3;
        $this->contest_id=$contest_id;
        $this->view("vj_view_contest");
    }
    function allContest()
    {
        $this->title="比赛列表";
        $this->active=3;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=3;
        $this->view("vj_view_all_contest");

    }
    function spiderStatus()
    {
        $this->title="爬虫状态";
        $this->active=3;
        $this->isLogin=$this->helper("user_helper")->isLogin();
        $this->addon_header="vj_addon_menu.php";
        $this->sub_active=9;
        $this->view("spider_info");
    }
}