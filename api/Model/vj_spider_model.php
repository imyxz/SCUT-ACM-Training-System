<?php
/**
 * User: imyxz
 * Date: 2017-07-24
 * Time: 20:10
 * Github: https://github.com/imyxz/
 */
class vj_spider_model extends SlimvcModel
{
    function getSpiderInfo($spider_id)
    {
        return $this->queryStmt("select * from spider_info where spider_id=?",
            "i",
            $spider_id)->row();
    }
    function updateSpiderAdditionInfo($spider_id,$info)
    {
        return $this->queryStmt("update spider_info set spider_addition_info=? where spider_id=?",
            "si",
            $info,
            $spider_id);
    }
    function getOjFreeSpider($oj_id,$limit)
    {
        return $this->queryStmt("select spider_id,spider_looking_job from spider_info where oj_id=? and spider_enable=true and spider_looking_job<?",
            "ii",
            $oj_id,$limit)->all();
    }
    function addSpiderLookingJob($spider_id,$how_much)
    {
        return $this->queryStmt("update spider_info set spider_looking_job=spider_looking_job+(?) where spider_id=?",
            "ii",
            $how_much,
            $spider_id);
    }
    function getSpiderMotherInfo($oj_id)
    {
        return $this->queryStmt("select * from spider_mother_info where oj_id=?",
            "i",
            $oj_id)->row();
    }
    function setSpiderAlive($spider_id)
    {
        return $this->queryStmt("update spider_info set last_alive_time=now() where spider_id=?",
            "i",
            $spider_id);
    }
    function setSpiderMotherAlive($oj_id)
    {
        return $this->queryStmt("update spider_mother_info set last_alive_time=now() where oj_id=?",
            "i",
            $oj_id);
    }
    function getSpiderMotherUnAlive($minutes)
    {
        return $this->queryStmt("select oj_id from spider_mother_info where is_enable=true and last_alive_time<DATE_ADD(now(),INTERVAL ? MINUTE )",
            "i",
            $minutes*-1)->all();
    }
    function getOjSpiderUnAlive($oj_id,$minutes)
    {
        return $this->queryStmt("select spider_id from spider_info where oj_id=? and spider_enable=true and  last_alive_time<DATE_ADD(now(),INTERVAL ? MINUTE )",
            "ii",
            $oj_id,
            $minutes*-1)->all();
    }
    function updateSpiderLoginTime($spider_id)
    {
        return $this->queryStmt("update spider_info set oj_logintime=now() where spider_id=?","i",
            $spider_id);
    }
    function getAllSpiders($page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select spider_info.spider_id,spider_info.oj_id,spider_info.last_alive_time,spider_info.oj_logintime,spider_info.spider_enable,spider_info.spider_status,spider_info.spider_looking_job,oj_site_info.oj_name from spider_info,oj_site_info where oj_site_info.oj_id=spider_info.oj_id order by spider_info.spider_id desc limit ?,?",
            "ii",
            $start,
            $limit)->all();
    }




}