<?php
/**
 * User: imyxz
 * Date: 2017-07-20
 * Time: 13:42
 * Github: https://github.com/imyxz/
 */
class player_model extends SlimvcModel{
    function getPlayerIDByGroupIDAndName($group_id,$name)
    {
        $row=$this->queryStmt("select * from acm_player where player_group_id=? and player_name=?",
            "is",
            $group_id,
            $name)->row();
        if(!$row)
            return false;
        else
            return $row['player_id'];

    }
    function newPlayer($group_id,$name)
    {
        $this->queryStmt("insert into acm_player set player_group_id=?,player_name=?",
            "is",
            $group_id,
            $name);
        return $this->InsertId;
    }
}