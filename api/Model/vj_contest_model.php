<?php
/**
 * User: imyxz
 * Date: 2017-09-10
 * Time: 15:16
 * Github: https://github.com/imyxz/
 */
class vj_contest_model extends SlimvcModel
{
    function getContestInfo($contest_id)
    {
        return $this->queryStmt("select *,UNIX_TIMESTAMP(contest_start_time) as contest_start_time_ts  from vj_contest_info where contest_id=?",
            "i",
            $contest_id)->row();
    }
    function getUserParticipantInfo($contest_id,$user_id)
    {
        return $this->queryStmt("select *,UNIX_TIMESTAMP(participant_time) as participant_time_ts from vj_contest_participant where contest_id=? and user_id=?",
            "ii",
            $contest_id,
            $user_id)->row();
    }
    function getContestSubmission($contest_id,$start_time,$end_time)
    {
        return $this->queryStmt("select vj_contest_submission.user_id,vj_contest_submission.problem_index,vj_contest_submission.submit_time,run_job.ac_status,run_job.running_status,vj_contest_submission.run_job_id from vj_contest_submission,run_job where vj_contest_submission.contest_id=? and vj_contest_submission.submit_time<=? and vj_contest_submission.submit_time>=? and run_job.job_id=vj_contest_submission.run_job_id order by vj_contest_submission.submit_time asc",
            "iii",
            $contest_id,
            $end_time,
            $start_time)->all();
    }
    function getContestParticipants($contest_id)
    {
        return $this->queryStmt("select vj_contest_participant.user_id,user_info.user_nickname,user_info.user_avatar,vj_contest_participant.participant_time from vj_contest_participant,user_info where vj_contest_participant.contest_id=? and user_info.user_id=vj_contest_participant.user_id",
            "i",
            $contest_id)->all();
    }
    function getContestProblems($contest_id)
    {
        return $this->queryStmt("select * from vj_contest_problem where contest_id=? order by problem_index ASC",
            "i",
            $contest_id)->all();
    }
    function addSubmission($contest_id,$user_id,$job_id,$problem_index, $seconds)
    {
        return $this->queryStmt("insert into vj_contest_submission SET run_job_id=?,contest_id=?,user_id=?,problem_index=?,submit_time=?",
            "iiiii",
            $job_id,$contest_id,$user_id,$problem_index,$seconds);
    }
    function addParticipant($contest_id,$user_id)
    {
        return $this->queryStmt("insert into vj_contest_participant set contest_id=?,user_id=?,participant_time=now()",
            "ii",
            $contest_id,
            $user_id);
    }
    function getProblemInfo($contest_id,$problem_index)
    {
        return $this->queryStmt("select vj_contest_problem.problem_title,vj_contest_problem.problem_desc as problem_new_desc,problem_info.compiler_info,problem_info.memory_limit,problem_info.problem_desc,problem_info.time_limit,problem_info.problem_id,problem_info.oj_id from vj_contest_problem,problem_info
                                  where vj_contest_problem.contest_id=? and vj_contest_problem.problem_index=? and problem_info.problem_id=vj_contest_problem.problem_id",
            "ii",
            $contest_id,$problem_index)->row();
    }
    function newContest($contest_title,$contest_desc,$creator_id,$contest_start_time,$contest_last_time,$contest_type)
    {
        if(!$this->queryStmt("insert into vj_contest_info set contest_title=?,contest_desc=?,contest_creator_id=?,contest_start_time=FROM_UNIXTIME(?),contest_last_seconds=?,contest_type=?",
            "ssiiii",
            $contest_title,
            $contest_desc,
            $creator_id,
            $contest_start_time,
            $contest_last_time,
            $contest_type)) return false;
        return $this->InsertId;
    }
    function addContestProblem($contest_id,$problem_id,$problem_title,$index)
    {
        return $this->queryStmt("insert into vj_contest_problem set contest_id=?,problem_id=?,problem_title=?,problem_index=?",
            "iisi",
            $contest_id,
            $problem_id,
            $problem_title,
            $index);
    }
    function getContestSubmitStatus($contest_id,$submit_time,$page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select vj_contest_submission.*,run_job.ac_status,run_job.time_usage,run_job.ram_usage,run_job.wrong_info,user_info.user_nickname,run_job.running_status,run_job.is_shared
        from run_job,user_info,vj_contest_submission where vj_contest_submission.contest_id=? and vj_contest_submission.submit_time<=? and run_job.job_id=vj_contest_submission.run_job_id and user_info.user_id=run_job.user_id
        order by vj_contest_submission.submit_time desc limit ?,?",
            "iiii",
            $contest_id,
            $submit_time,
            $start,
            $limit)->all();
    }
    function getAllContest($page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select * from vj_contest_info order by contest_start_time desc limit ?,?",
            "ii",
            $start,
            $limit)->all();
    }


}