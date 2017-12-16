<?php


class group_model extends SlimvcModel
{
    function getGroupMember($group_id)
    {
        return $this->queryStmt("select * from acm_player where player_group_id=?",
            "i",
            $group_id)->all();
    }
    function getGroupInfo($group_id)
    {
        return $this->queryStmt("select * from acm_group where group_id=?",
            "i",
            $group_id)->row();
    }
    function getAllGroup()
    {
        return $this->query("select * from acm_group where group_id>0")->all();
    }
    function addGroup($group_name,$vj_username,$leader_id=0)
    {
        $this->queryStmt("insert into acm_group set group_name=?,group_vj_name=?,group_leader_id=?",
            "ssi",
            $group_name,
            $vj_username,
            $leader_id);
        return $this->InsertId;
    }
    function getGroupIDByVJUsername($vj_username)
    {
        $row=$this->queryStmt("select group_id from acm_group where group_vj_name=?",
            "s",
            $vj_username)->row();
        if(!$row)
            return false;
        return $row['group_id'];
    }
}

?>