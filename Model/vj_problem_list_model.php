<?php
/**
 * User: imyxz
 * Date: 2017-08-01
 * Time: 12:07
 * Github: https://github.com/imyxz/
 */
class vj_problem_list_model extends SlimvcModel
{
    function getProblemListProblems($problem_list_id)
    {
        return $this->queryStmt("select problem_list_relation.*,problem_info.problem_id,problem_info.problem_title,problem_info.problem_identity from problem_list_relation,problem_info where problem_list_relation.list_id=? and problem_info.problem_id=problem_list_relation.problem_id order by problem_list_relation.problem_index asc",
            "i",
            $problem_list_id)->all();
    }
    function getProblemListInfo($problem_list_id)
    {
        return $this->queryStmt("select * from problem_list where list_id=? limit 1",
            "i",
            $problem_list_id)->row();
    }
    function purgeProblemListProblems($problem_list_id)
    {
        return $this->queryStmt("delete from problem_list_relation where list_id=?",
            "i"
            ,$problem_list_id);
    }
    function addProblemListProblem($problem_list_id,$problem_id,$problem_title,$index)
    {
        return $this->queryStmt("insert into problem_list_relation set list_id=?,problem_id=?,problem_title=?,problem_index=?",
            "iisi",
            $problem_list_id,
            $problem_id,
            $problem_title,
            $index);
    }
    function getProblemUserAced($list_id,$user_id)
    {
        return $this->queryStmt("select problem_list_relation.*,run_job.problem_id,run_job.user_id,run_job.ac_status from problem_list_relation,run_job
                                 where problem_list_relation.list_id=? and run_job.problem_id=problem_list_relation.problem_id and run_job.user_id=? and run_job.ac_status=1",
            "ii",
            $list_id,
            $user_id)->all();
    }
    function newProblemList($list_title,$list_desc,$creator_id)
    {
        if(!$this->queryStmt("insert into problem_list set list_title=?,list_desc=?,list_creator_id=?,list_create_time=now()",
            "ssi",
            $list_title,
            $list_desc,
            $creator_id)) return false;
        return $this->InsertId;
    }
    function getAllList($page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select * from problem_list order by list_id desc limit ?,?",
            "ii",
            $start,
            $limit)->all();
    }

}