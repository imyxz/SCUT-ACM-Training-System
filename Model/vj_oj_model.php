<?php
/**
 * User: imyxz
 * Date: 2017-07-27
 * Time: 19:00
 * Github: https://github.com/imyxz/
 */
class vj_oj_model extends SlimvcModel
{
    function getOjByName($oj_name)
    {
        return $this->queryStmt("select * from oj_site_info where oj_name=? limit 1",
            "s",
            $oj_name)->row();
    }
}