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
    function getSpiderUnfinishJobs($spider_id)
    {
        return $this->queryStmt("select * from run_job where spider_id=? and (running_status=1 or running_status=2)",
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

}