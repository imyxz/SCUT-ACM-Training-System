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
        return $this->queryStmt("select * from problem_info where problem_id=?",
            "i",
            $problem_id)->row();
    }

}