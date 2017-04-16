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
}

?>