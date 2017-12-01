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
            $return['problem_tags']=$this->model("tag_model")->getProblemTags($problem_id);
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getProblemAcRank()
    {

        try {
            $problem_id = intval($_GET['id']);
            $rank = $this->model("vj_problem_model")->getProblemAcRank($problem_id);
            $return['rank']=$rank;
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
            if (!$tmp || ($tmp['user_id'] != $this->helper("user_helper")->getUserID() && $tmp['is_shared']==false)) throw new Exception("您无权查看此代码");
            $return['source_code'] = $tmp['source_code'];
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function setJobShare()
    {
        try {
            $json=$this->getRequestJson();
            $job_id = intval($json['job_id']);
            if($json['is_shared']==true)
                $is_shared=true;
            else
                $is_shared=false;

            $tmp = $this->model("vj_job_model")->getJobInfo($job_id);
            if (!$tmp || ($tmp['user_id'] != $this->helper("user_helper")->getUserID())) throw new Exception("您无权操作");
            $this->model("vj_job_model")->setJobShare($job_id,$is_shared);
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
    function getTagList()
    {
        try {
            $page = intval(@$_GET['page']);
            if ($page < 1)
                $page = 1;
            $tag_name=trim(urldecode($_GET['name']));
            $tag_info=$this->model("tag_model")->getTagInfoByTagName($tag_name);
            if(!$tag_info)
                throw new Exception("Tag name 不存在");
            $tag_id=$tag_info['tag_id'];
            $tmp = $this->model("tag_model")->getTagProblems($tag_id,$page, 30);
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
    function getAllTags()
    {
        try {
            $return['tags']=$this->model("tag_model")->getAllTag();
            $return['status'] = 0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }

    function getContestInfo()
    {
        try {
            $contest_id=intval($_GET['id']);
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");
            $user_id=0;
            $contest_info=$contest_model->getContestInfo($contest_id);
            $return["contest_problem"]=array();
            if(!$contest_info) throw new Exception("无此比赛");
            $return["need_participant"]=false;
            $return["contest_info"]=$contest_info;
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);

            if($contest_info['contest_type']==$contestType->NormalContest)
            {
                $return["contest_problem"]=array();
                $problems=$contest_model->getContestProblems($contest_id);
                foreach($problems as $one)
                {
                    $return["contest_problem"][]=array(
                        "problem_index"=>$one["problem_index"],
                        "problem_title"=>$one["problem_title"]);
                }
                $return["running_time"]=time()-$contest_info['contest_start_time_ts'];
            }
            else if($contest_info['contest_type']==$contestType->FlexibleContest)
            {
                if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
                $user_id=$this->helper("user_helper")->getUserID();
                $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
                if(!$part_info) $return["need_participant"]=true;
                else
                {
                    $problems=$contest_model->getContestProblems($contest_id);
                    foreach($problems as $one)
                    {
                        $return["contest_problem"][]=array(
                            "problem_index"=>$one["problem_index"],
                            "problem_title"=>$one["problem_title"]);
                    }
                    $return["running_time"]=time()-$part_info['participant_time_ts'];
                    $return["contest_info"]["contest_start_time_ts"]=$part_info['participant_time_ts'];
                }

            }
            $return["user_id"]=$user_id;
            $return["status"]=0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }
    function joinContest()
    {
        try {
            $contest_id=intval($_GET['id']);
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");
            if($contest_id == 18)   throw new Exception("可爱的文龙说要你女装才能进来哦");
            $contest_info=$contest_model->getContestInfo($contest_id);
            if(!$contest_info) throw new Exception("无此比赛");
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);
            $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
            if(!$part_info)
            {
                $contest_model->addParticipant($contest_id,$user_id);
            }
            $return["status"]=0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return=array();
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }
    function getContestProblem()
    {
        try {
            $contest_id=intval($_GET['cid']);
            $problem_id=intval($_GET['pid']);

            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");

            $contest_info=$contest_model->getContestInfo($contest_id);
            if(!$contest_info) throw new Exception("无此比赛");
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);

            if($contest_info['contest_type']==$contestType->FlexibleContest)
            {
                $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
                if(!$part_info)throw new Exception("需先参加比赛");
            }

            $return["problem_info"]=$contest_model->getProblemInfo($contest_id,$problem_id);
            if(!$return["problem_info"])throw new Exception("无此题目");
            unset($return["problem_info"]["problem_id"]);
            unset($return["problem_info"]["oj_id"]);
            if($return['problem_info']["problem_new_desc"]!='')
                $return['problem_info']['problem_desc']=$return['problem_info']["problem_new_desc"];
            unset($return['problem_info']["problem_new_desc"]);
            $return["status"]=0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return=array();
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }
    function getContestJobStatus()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $job_id = intval($_GET['id']);
            $tmp = $this->model("vj_job_model")->getOneJobStatus($job_id);
            unset($tmp['oj_id']);
            unset($tmp['problem_id']);
            unset($tmp['problem_identity']);
            unset($tmp['problem_url']);
            unset($tmp['oj_name']);


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
    function submitContestJob()
    {
        try {
            $contest_id=intval($_GET['cid']);
            $problem_index=intval($_GET['pid']);
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");

            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            $contest_info=$contest_model->getContestInfo($contest_id);
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);

            $json = $this->getRequestJson();
            $problem_info=$contest_model->getProblemInfo($contest_id,$problem_index);
            if(!$problem_info)throw new Exception("无此题目");

            $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
            if(!$part_info)
            {
                $contest_model->addParticipant($contest_id,$user_id);
                $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
            }



            $problem_id = intval($problem_info['problem_id']);
            $source_code = $json['source_code'];
            $compiler_id = intval($json['compiler_id']);

            $oj_id = $problem_info['oj_id'];
            $job_id = $this->model("vj_job_model")->newJob($problem_id, $oj_id, $user_id, $source_code, $compiler_id);
            if (!$job_id) throw new Exception("新建任务失败");
            $seconds=0;
            if($contest_info['contest_type']==$contestType->NormalContest)
            {
                $seconds=time()-$contest_info["contest_start_time_ts"];
            }
            else if($contest_info['contest_type']==$contestType->FlexibleContest)
            {
                $seconds=time()-$part_info["participant_time_ts"];
            }
            $contest_model->addSubmission($contest_id,$user_id,$job_id,$problem_index,$seconds);
            $return['status'] = 0;
            $return['job_id'] = $job_id;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getContestSubmission(){
        try {
            $contest_id=intval($_GET['id']);
            $start_time=intval($_GET['beginTime']);
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");

            $contest_info=$contest_model->getContestInfo($contest_id);
            if(!$contest_info) throw new Exception("无此比赛");
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);
            $return["submissions"]=array();

            if($contest_info['contest_type']==$contestType->NormalContest)
            {
                $submissions=$contest_model->getContestSubmission($contest_id,$start_time,$contest_info["contest_last_seconds"]);
                foreach($submissions as $one)
                {
                    $return["submissions"][]=array($one['user_id'],$one['problem_index'],$one['submit_time'],$one['ac_status'],$one["run_job_id"]);
                }
                $return["running_time"]=time()-$contest_info['contest_start_time_ts'];
            }

            else if($contest_info['contest_type']==$contestType->FlexibleContest)
            {
                if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
                $user_id=$this->helper("user_helper")->getUserID();
                $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
                if(!$part_info) throw new Exception("请先参与比赛");
                $submissions=$contest_model->getContestSubmission($contest_id,$start_time,min(time()-$part_info['participant_time_ts'],intval($contest_info["contest_last_seconds"])));
                foreach($submissions as $one)
                {
                    $return["submissions"][]=array($one['user_id'],$one['problem_index'],$one['submit_time'],$one['ac_status'],$one["run_job_id"]);
                }
                $return["running_time"]=time()-$part_info['participant_time_ts'];
            }
            $return["participants"]=$contest_model->getContestParticipants($contest_id);
            $return["status"]=0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return=array();
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function newContest()
    {
        try {
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $json=$this->getRequestJson();
            $contest_title=trim($json['contest_title']);
            $contest_desc=trim($json['contest_desc']);
            $problem_list=$json['problem_list'];
            $contest_start_time=intval($json['contest_start_time']);
            $contest_last_time=intval($json['contest_last_time']);
            if($contest_start_time<=0 || $contest_last_time<=0)throw new Exception("时间不正确");
            $contest_type=$json['contest_type'];
            if($contest_type=='NormalContest')
                $contest_type=0;
            else if($contest_type=='FlexibleContest')
                $contest_type=1;
            else
                throw new Exception("contest_type不匹配");
            if(empty($contest_title))  throw new Exception("请填写标题");
            if(empty($contest_desc))  throw new Exception("请填写描述");
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
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");
            $contest_id=$contest_model->newContest($contest_title,$contest_desc,$this->helper("user_helper")->getUserID(),$contest_start_time,$contest_last_time,$contest_type);
            if(!$contest_id)    throw new Exception("系统内部错误！");
            foreach($to_be_add as &$one)
            {
                $contest_model->addContestProblem($contest_id,$one['problem_id'],$one['problem_title'],$one['problem_index']);
            }
            $return=array();
            $return['status']=0;
            $return['contest_id']=$contest_id;

            $this->outputJson($return);

        } catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);


        }
    }
    function getContestStatus()
    {
        try {
            $return=array();
            $contest_id=intval($_GET['id']);
            $page=@intval($_GET['page']);
            if($page<=0)
                $page=1;
            /** @var contestType $contestType */
            $contestType=$this->newClass("contestType");

            /** @var vj_contest_model $contest_model */
            $contest_model=$this->model("vj_contest_model");

            $contest_info=$contest_model->getContestInfo($contest_id);
            if(!$contest_info) throw new Exception("无此比赛");
            if ($this->helper("user_helper")->isLogin() == false) throw new Exception("请先登录");
            $user_id=$this->helper("user_helper")->getUserID();
            if($contest_info['contest_start_time_ts']>time())   throw new Exception("比赛还未开始，开始时间：" . $contest_info['contest_start_time']);

            if($contest_info['contest_type']==$contestType->FlexibleContest)
            {
                $part_info=$contest_model->getUserParticipantInfo($contest_id,$user_id);
                if(!$part_info)throw new Exception("需先参加比赛");
                $return["submit_status"]=$contest_model->getContestSubmitStatus($contest_id,time()-$part_info['participant_time_ts'],$page,30);
            }
            else if($contest_info['contest_type']==$contestType->NormalContest)
            {
                $return["submit_status"]=$contest_model->getContestSubmitStatus($contest_id,time()-$contest_info["contest_start_time_ts"],$page,30);
            }
            if (count($return["submit_status"]) < 30)
                $return['is_end'] = true;
            else
                $return['is_end'] = false;

            $return["status"]=0;
            $this->outputJson($return);

        } catch (Exception $e) {
            $return=array();
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getAllContest(){
        try {
            $page = intval(@$_GET['page']);
            if ($page < 1)
                $page = 1;
            $tmp = $this->model("vj_contest_model")->getAllContest($page, 30);
            $return['contests'] = $tmp;
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
    function getSpiderStatus(){
        try {
            $page = intval(@$_GET['page']);
            if ($page < 1)
                $page = 1;
            $tmp = $this->model("vj_spider_model")->getAllSpiders($page, 30);
            $return['spiders'] = $tmp;
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