<?php


class group_model extends AmysqlModel
{
    function getGroupMember($group_id)
    {
        return $this->_all("select * from acm_player where player_group_id=$group_id");
    }
    function getGroupInfo($group_id)
    {
        return $this->_row("select * from acm_group where group_id=$group_id");
    }
    function getGroupIDByVJUsername($vj_username)
    {
        $row=$this->_row("select group_id from acm_group where group_vj_name='$vj_username'");
        if(!$row)
            return false;
        return $row['group_id'];
    }
    function getGroupIDByZOJUsername($zoj_username)
    {
        $row=$this->_row("select group_id from acm_group where group_zoj_name='$zoj_username'");
        if(!$row)
            return false;
        return $row['group_id'];
    }
    function getGroupIDBySEOJUsername($seoj_username)
    {
        $row=$this->_row("select group_id from acm_group where group_seoj_name='$seoj_username'");
        if(!$row)
            return false;
        return $row['group_id'];
    }
    function getAllGroup()
    {
        return $this->_all("select * from acm_group");
    }
    function addGroup($group_name,$vj_username,$leader_id=0)
    {
        $this->_query("insert into acm_group set group_name='$group_name',group_vj_name='$vj_username',group_leader_id=$leader_id");
        return $this->InsertId;
    }
}

?>