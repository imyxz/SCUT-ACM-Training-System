<?php
/**
 * User: imyxz
 * Date: 2017-08-12
 * Time: 0:50
 * Github: https://github.com/imyxz/
 */
class bg_pic_model extends SlimvcModel
{
    function insertPic($url)
    {
        if(!$this->queryStmt("insert into bg_pic set pic_url=?,pic_time=now()",
            "s",
            $url))  return false;
        return $this->InsertId;
    }
    function getLastPic()
    {
        return $this->query("select * from bg_pic order by pic_id desc limit 1")->row();
    }
}