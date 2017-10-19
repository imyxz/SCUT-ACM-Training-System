<?php
/**
 * User: imyxz
 * Date: 2017-08-06
 * Time: 19:38
 * Github: https://github.com/imyxz/
 */
class share_code_model extends SlimvcModel
{

    function newShareCode($user_id,$source_code,$code_type)
    {
        if(!$this->queryStmt("insert into share_code SET user_id=?,source_code=?,code_type=?,share_time=now(),is_share=1",
            "isi",
            $user_id,
             $source_code,
            $code_type))  return false;
        return $this->InsertId;

    }
    function getShareCodeInfo($share_id)
    {
        return $this->queryStmt("select * from share_code where share_id=?",
            "i",
            $share_id)->row();
    }
}