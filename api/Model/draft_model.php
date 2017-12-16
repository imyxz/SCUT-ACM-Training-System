<?php
/**
 * User: imyxz
 * Date: 2017-08-06
 * Time: 19:38
 * Github: https://github.com/imyxz/
 */
class draft_model extends SlimvcModel
{
    function getUserDraft($user_id,$limit)
    {
        return $this->queryStmt("select draft_id,draft_title,save_time from code_draft where user_id=? and is_autosave=false order by save_time desc limit ?",
            "ii",
            $user_id,
            $limit)->all();
    }
    function getUserAutoSave($user_id,$limit)
    {
        return $this->queryStmt("select draft_id,draft_title,save_time from code_draft where user_id=? and is_autosave=true order by save_time desc limit ?",
            "ii",
            $user_id,
            $limit)->all();
    }
    function newDraft($user_id,$draft_title,$source_code,$is_autosave)
    {
        if(!$this->queryStmt("insert into code_draft SET user_id=?,draft_title=?,source_code=?,is_autosave=?,save_time=now()",
            "issi",
            $user_id,
            $draft_title,
            $source_code,
            $is_autosave))  return false;
        return $this->InsertId;

    }
    function getDraftInfo($draft_id)
    {
        return $this->queryStmt("select * from code_draft where draft_id=?",
            "i",
            $draft_id)->row();
    }
}