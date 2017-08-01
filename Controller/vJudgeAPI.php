<?php
/**
 * User: imyxz
 * Date: 2017-07-26
 * Time: 14:55
 * Github: https://github.com/imyxz/
 */
class vJudgeAPI extends SlimvcController
{
    function getProblemInfo()
    {

        try {
            $problem_id = intval($_GET['id']);
            $problem_info = $this->model("vj_problem_model")->getProblemInfo($problem_id);
            if (!$problem_info) throw new Exception("Problem 不存在");
            //unset($problem_info['compiler_info']);
            $return['problem_info'] = $problem_info;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function submitCode()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $json = $this->getRequestJson();
            $problem_id = intval($json['problem_id']);
            $source_code = $json['source_code'];
            $compiler_id = intval($json['compiler_id']);
            $problem_info = $this->model("vj_problem_model")->getProblemInfo($problem_id);
            if (!$problem_info) throw new Exception("Problem 不存在");
            $oj_id = $problem_info['oj_id'];
            $job_id = $this->model("vj_job_model")->newJob($problem_id, $oj_id, $this->helper("user_helper")->getUserID(), $source_code, $compiler_id);
            if (!$job_id) throw new Exception("新建任务失败");
            $return['status'] = 0;
            $return['job_id'] = $job_id;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function getMyStatus()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $tmp = $this->model("vj_job_model")->getUserJobStatus($this->helper("user_helper")->getUserID(), 30);
            /*$return['status_info']=array();
            foreach($tmp as $one)
            {
                $return['status_info'][$one['job_id']]=$one;
            }*/
            $return['status_info'] = $tmp;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function getJobStatus()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $job_id = intval($_GET['id']);
            $tmp = $this->model("vj_job_model")->getOneJobStatus($job_id);
            /*$return['status_info']=array();
            foreach($tmp as $one)
            {
                $return['status_info'][$one['job_id']]=$one;
            }*/
            $return['status_info'] = $tmp;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function getJobSourceCode()
    {
        try {
            $job_id = intval($_GET['id']);
            $tmp = $this->model("vj_job_model")->getJobInfo($job_id);
            if (!$tmp || $tmp['user_id'] != $this->helper("user_helper")->getUserID()) throw new Exception("您无权查看此代码");
            $return['source_code'] = $tmp['source_code'];
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function getAllProblem()
    {
        try {
            $page = intval(@$_GET['page']);
            if ($page < 1)
                $page = 1;
            $tmp = $this->model("vj_problem_model")->getProblemList($page, 30);
            $return['problem_list'] = $tmp;
            if (count($tmp) < 30)
                $return['is_end'] = true;
            else
                $return['is_end'] = false;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }

    function getProblemByInfo()
    {
        try {
            $json = $this->getRequestJson();
            $oj_name = trim($json['oj_name']);
            $problem_identity = trim($json['problem_identity']);
            $oj_info = $this->model("vj_oj_model")->getOjByName($oj_name);

            if (!$oj_info) throw new Exception("OJ 名字不存在");
            $oj_id = $oj_info['oj_id'];

            $problem_info = $this->model("vj_problem_model")->getProblemByOjIDAndProblemIdentity($oj_id, $problem_identity);

            if (!$problem_info) throw new Exception("problem identity 不存在");

            $return['problem_id'] = $problem_info['problem_id'];
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);


        }
    }

    function getListProblems()
    {
        try {
            $id=intval($_GET['id']);
            $list_info=$this->model("vj_problem_list_model")->getProblemListInfo($id);
            if(!$list_info) throw new Exception("Problem List 不存在");
            $problem_list=$this->model("vj_problem_list_model")->getProblemListProblems($id);
            $ac_status=array();
            if($this->helper("user_helper")->isLogin())
            {
                $tmps=$this->model("vj_problem_list_model")->getProblemUserAced($id,$this->helper("user_helper")->getUserID());
                foreach($tmps as $one)
                {
                    $ac_status[$one['problem_id']]=true;
                }
            }
            foreach($problem_list as &$one)
            {
                if(isset($ac_status[$one['problem_id']]) && $ac_status[$one['problem_id']]==true)
                    $one['aced']=true;
                else
                    $one['aced']=false;
            }
            $return=array();
            $return['status']=0;
            $return['list_title']=$list_info['list_title'];
            $return['list_desc']=$list_info['list_desc'];
            $return['problem_list']=$problem_list;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);


        }
    }
    function newList()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $json=$this->getRequestJson();
            $list_title=trim($json['list_title']);
            $list_desc=trim($json['list_desc']);
            $problem_list=$json['problem_list'];
            if(empty($list_title))  throw new Exception("请填写标题");
            if(empty($list_desc))  throw new Exception("请填写描述");
            $to_be_add=array();
            $index=1;
            foreach ($problem_list as &$one) {
                $problem_id=intval($one['problem_id']);
                $problem_title=trim($one['problem_title']);
                if(isset($to_be_add[$problem_id])) continue;
                $info=$this->model("vj_problem_model")->getProblemInfo($problem_id);
                if(!$info)  throw new Exception("problem id: $problem_id 不存在！");
                $to_be_add[$problem_id]=array("problem_id"=>$problem_id,
                    "problem_title"=>$problem_title,
                    "problem_index"=>$index++);
            }
            $list_id=$this->model("vj_problem_list_model")->newProblemList($list_title,$list_desc,$this->helper("user_helper")->getUserID());
            if(!$list_id)    throw new Exception("系统内部错误！");
            foreach($to_be_add as &$one)
            {
                $this->model("vj_problem_list_model")->addProblemListProblem($list_id,$one['problem_id'],$one['problem_title'],$one['problem_index']);
            }
            $return=array();
            $return['status']=0;
            $return['list_id']=$list_id;

            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);


        }
    }

    function updateList()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $json=$this->getRequestJson();
            $list_title=trim($json['list_title']);
            $list_desc=trim($json['list_desc']);
            $problem_list=$json['problem_list'];
            $list_id=intval($json['list_id']);
            $list_info=$this->model("vj_problem_list_model")->getProblemListInfo($list_id);
            if(!$list_info) throw new Exception("列表不存在");
            if($list_info['list_creator_id']!=$this->helper("user_helper")->getUserID())
                throw new Exception("您不是该题目列表作者");
            if(empty($list_title))  throw new Exception("请填写标题");
            if(empty($list_desc))  throw new Exception("请填写描述");
            $to_be_add=array();
            $index=1;
            foreach ($problem_list as &$one) {
                $problem_id=intval($one['problem_id']);
                $problem_title=trim($one['problem_title']);
                if(isset($to_be_add[$problem_id])) continue;
                $info=$this->model("vj_problem_model")->getProblemInfo($problem_id);
                if(!$info)  throw new Exception("problem id: $problem_id 不存在！");
                $to_be_add[$problem_id]=array("problem_id"=>$problem_id,
                    "problem_title"=>$problem_title,
                    "problem_index"=>$index++);
            }
            $this->model("vj_problem_list_model")->purgeProblemListProblems($list_id);
            foreach($to_be_add as &$one)
            {
                $this->model("vj_problem_list_model")->addProblemListProblem($list_id,$one['problem_id'],$one['problem_title'],$one['problem_index']);
            }
            $return=array();
            $return['status']=0;
            $return['list_id']=$list_id;

            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);


        }
    }
    function getAllList()
    {
        try {
            $page = intval(@$_GET['page']);
            if ($page < 1)
                $page = 1;
            $tmp = $this->model("vj_problem_list_model")->getAllList($page, 30);
            $return['lists'] = $tmp;
            if (count($tmp) < 30)
                $return['is_end'] = true;
            else
                $return['is_end'] = false;
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }
}