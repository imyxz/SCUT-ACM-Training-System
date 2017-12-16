<?php
/**
 * User: imyxz
 * Date: 2017-07-24
 * Time: 20:09
 * Github: https://github.com/imyxz/
 */
class vj_job_model extends SlimvcModel
{
    function getSpiderUndoJobs($spider_id)
    {
        return $this->queryStmt("select * from run_job where spider_id=? ORDER by job_id ASC ",
            "i",
            $spider_id)->all();
    }
    function getOjNoSpiderJobs($oj_id)
    {
        return $this->queryStmt("select * from run_job where oj_id=? and spider_id=0",
            "i",
            $oj_id)->all();
    }
    function getSpiderWatchingJobs($spider_id)
    {
        return $this->queryStmt("select * from run_job where spider_id=? and running_status=2",
            "i",
            $spider_id)->all();
    }
    function setJobSpider($job_id,$spider_id)
    {
        return $this->queryStmt("update run_job set spider_id=? where job_id=?",
            "ii",
            $spider_id,
            $job_id);
    }
    function updateJobRunningStatus($job_id,$running_status)
    {
        return $this->queryStmt("update run_job set running_status=? where job_id=?",
            "ii",
            $running_status,
            $job_id);
    }
    function getSpiderUnSubmitJobs($spider_id,$limit=10)
    {
        return $this->queryStmt("select * from run_job where spider_id=? and running_status=1 order by job_id asc limit ?",
            "ii",
            $spider_id,$limit)->all();
    }
    function addJobSubmitCount($job_id)
    {
        return $this->queryStmt("update run_job set submit_count=submit_count+1 where job_id=?",
            "i",
            $job_id);
    }
    function updateRemoteRunID($job_id,$remote_run_id)
    {
        return $this->queryStmt("update run_job set remote_run_id=? where job_id=?",
            "ii",
            $remote_run_id,
            $job_id);
    }

    function updateJobResultInfo($job_id,$ac_status,$wrong_info,$time_usage,$ram_usage,$addition_info)
    {
        return $this->queryStmt("update run_job set ac_status=?,wrong_info=?,time_usage=?,ram_usage=?,addition_info=? where job_id=?",
            "isiisi",
            $ac_status,
            $wrong_info,
            $time_usage,
            $ram_usage,
            $addition_info,
            $job_id);
    }
    function newJob($problem_id,$oj_id,$user_id,$source_code,$compiler_id)
    {
        $this->queryStmt("insert into run_job set problem_id=?,oj_id=?,user_id=?,source_code=?,compiler_id=?,submit_time=now()",
            "iiisi",
            $problem_id,
            $oj_id,
            $user_id,
            $source_code,
            $compiler_id);
        return $this->InsertId;
    }
    function setJobIsInContest($job_id,$is_in_contest)
    {
        return $this->queryStmt("update run_job set is_in_contest=? where job_id=?",
            "ii",
            $is_in_contest,
            $job_id);
    }
    function getUserJobStatus($user_id,$page, $limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select run_job.job_id,run_job.problem_id,run_job.oj_id,run_job.user_id,run_job.ac_status,run_job.submit_time,run_job.time_usage,run_job.ram_usage,run_job.wrong_info,problem_info.problem_identity,oj_site_info.oj_name,user_info.user_nickname,problem_info.problem_url,run_job.running_status,run_job.is_shared,run_job.is_in_contest
        from run_job,problem_info,oj_site_info,user_info where run_job.user_id=? and run_job.is_in_contest=false and problem_info.problem_id=run_job.problem_id and oj_site_info.oj_id=run_job.oj_id and user_info.user_id=run_job.user_id
        order by job_id desc limit ?,?",
            "iii",
            $user_id,
            $start,
            $limit)->all();
    }
    function getOneJobStatus($job_id)
    {
        return $this->queryStmt("select run_job.job_id,run_job.problem_id,run_job.oj_id,run_job.user_id,run_job.ac_status,run_job.submit_time,run_job.time_usage,run_job.ram_usage,run_job.wrong_info,problem_info.problem_identity,oj_site_info.oj_name,user_info.user_nickname,problem_info.problem_url,run_job.running_status
        from run_job,problem_info,oj_site_info,user_info where run_job.job_id=? and problem_info.problem_id=run_job.problem_id and oj_site_info.oj_id=run_job.oj_id and user_info.user_id=run_job.user_id
        ",
            "i",
            $job_id)->row();
    }
    function getJobInfo($job_id)
    {
        return $this->queryStmt("select * from run_job where job_id=?",
            "i",
            $job_id)->row();
    }
    function setJobShare($job_id,$is_shared)
    {
        return $this->queryStmt("update run_job set is_shared=? where job_id=?",
            "ii",
            $is_shared,
            $job_id);
    }
    function isRemoteIdExist($oj_id,$remote_id)
    {
        return $this->queryStmt("select * from run_job where oj_id=? and remote_run_id=?",
            "ii",
            $oj_id,$remote_id)->sum()>0;
    }
}