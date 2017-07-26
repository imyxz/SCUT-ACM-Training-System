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

        try{
            $problem_id=intval($_GET['id']);
            $problem_info=$this->model("vj_problem_model")->getProblemInfo($problem_id);
            if(!$problem_info) throw new Exception("Problem 不存在");
            //unset($problem_info['compiler_info']);
            $return['problem_info']=$problem_info;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function submitCode()
    {
        try{
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("请先登录");
            $json=$this->getRequestJson();
            $problem_id=intval($json['problem_id']);
            $source_code=$json['source_code'];
            $compiler_id=intval($json['compiler_id']);
            $problem_info=$this->model("vj_problem_model")->getProblemInfo($problem_id);
            if(!$problem_info) throw new Exception("Problem 不存在");
            $oj_id=$problem_info['oj_id'];
            $job_id=$this->model("vj_job_model")->newJob($problem_id,$oj_id,$this->helper("user_helper")->getUserID(),$source_code,$compiler_id);
            if(!$job_id)    throw new Exception("新建任务失败");
            $return['status']=0;
            $return['job_id']=$job_id;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getMyStatus()
    {
        try{
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("请先登录");
            $tmp=$this->model("vj_job_model")->getUserJobStatus($this->helper("user_helper")->getUserID(),30);
            /*$return['status_info']=array();
            foreach($tmp as $one)
            {
                $return['status_info'][$one['job_id']]=$one;
            }*/
            $return['status_info']=$tmp;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getJobStatus()
    {
        try{
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("请先登录");
            $job_id=intval($_GET['id']);
            $tmp=$this->model("vj_job_model")->getOneJobStatus($job_id);
            /*$return['status_info']=array();
            foreach($tmp as $one)
            {
                $return['status_info'][$one['job_id']]=$one;
            }*/
            $return['status_info']=$tmp;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getJobSourceCode()
    {
        try{
            $job_id=intval($_GET['id']);
            $tmp=$this->model("vj_job_model")->getJobInfo($job_id);
            if(!$tmp || $tmp['user_id']!=$this->helper("user_helper")->getUserID()) throw new Exception("您无权查看此代码");
            $return['source_code']=$tmp['source_code'];
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
}