<?php
/**
 * User: imyxz
 * Date: 2017-07-24
 * Time: 20:13
 * Github: https://github.com/imyxz/
 */
class vj_problem_model extends SlimvcModel
{
    function getProblemInfo($problem_id)
    {
        return $this->queryStmt("select problem_info.*,oj_site_info.oj_name from problem_info,oj_site_info where problem_info.problem_id=? and oj_site_info.oj_id=problem_info.oj_id",
            "i",
            $problem_id)->row();
    }
    function insertProblem($oj_id,$problem_identity,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler_info)
    {
        $this->queryStmt("insert into problem_info set oj_id=?,problem_identity=?,problem_title=?,problem_desc=?,problem_url=?,compiler_info=?,time_limit=?,memory_limit=?,add_time=now()",
            "isssssii",
            $oj_id,
            $problem_identity,
            $problem_title,
            $problem_desc,
            $problem_url,
            $compiler_info,
            $time_limit,
            $memory_limit);
        return $this->InsertId;
    }
    function getProblemList($page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select problem_info.problem_id,problem_info.oj_id,problem_info.add_time,problem_info.problem_title,problem_info.time_limit,problem_info.memory_limit,problem_info.problem_identity,oj_site_info.oj_name from problem_info,oj_site_info
              where oj_site_info.oj_id=problem_info.oj_id order by problem_info.problem_id desc limit ?,?",
            "ii",
            $start,
            $limit)->all();
    }
    function getProblemByOjIDAndProblemIdentity($oj_id,$problem_id)
    {
        return $this->queryStmt("select * from problem_info where oj_id=? and problem_identity=? limit 1",
            "is",
            $oj_id,
            $problem_id)->row();
    }

}