<?php


class contest_model extends AmysqlModel
{
    function newContest($contest_name,$problem_count,$begin_time,$end_time)
    {
        $this->_query("insert into acm_contest set contest_name='$contest_name',contest_begin_time=FROM_UNIXTIME($begin_time),contest_end_time=FROM_UNIXTIME($end_time),contest_problem_count=$problem_count");
        return $this->InsertId;
    }
    function getContestSummary($contest_id)
    {
        return $this->_all("select acm_contest_summary.*,acm_group.group_name,acm_player.player_name,acm_player.player_group_id from acm_group,acm_player,acm_contest_summary where acm_contest_summary.contest_id=$contest_id and acm_player.player_id=acm_contest_summary.player_id and acm_group.group_id=acm_player.player_group_id order by acm_player.player_group_id asc,acm_contest_summary.problem_index asc");

    }
    function getPlayerContestSummary($contest_id,$player_id)
    {
        return $this->_all("select * from acm_contest_summary where contest_id=$contest_id and player_id=$player_id order by problem_index asc");

    }
    function getContestBoard($contest_id)
    {
        return $this->_all("select acm_contest_board.*,acm_group.group_name from acm_contest_board,acm_group where acm_contest_board.contest_id=$contest_id and acm_group.group_id=acm_contest_board.group_id order by acm_contest_board.rank_index asc");
    }
    function getGroupMember($group_id)
    {
        return $this->_all("select * from acm_player where player_group_id=$group_id");
    }
    function getContestInfo($contest_id)
    {
        return $this->_row("select * from acm_contest where contest_id=$contest_id");
    }
    function updateContestDescription($contest_id,$description)
    {
        return $this->_query("update acm_contest set contest_description='$description' where contest_id=$contest_id");
    }
    function getAllContest()
    {
        return $this->_all("select * from acm_contest ORDER by contest_begin_time desc");
    }
    function insertBoardSubmission($contest_id,$group_id,$problem_index,$ac_status,$submit_time)
    {
        $this->_query("insert into acm_contest_board_submission set contest_id=$contest_id,group_id=$group_id,problem_index=$problem_index,ac_status=$ac_status,submit_time=$submit_time");
        return $this->InsertId;
    }
    function insertBoardInfo($contest_id,$group_id,$problem_solved,$rank_index,$penalty,$ac_info)
    {
        $this->_query("insert into acm_contest_board set contest_id=$contest_id,group_id=$group_id,problem_solved=$problem_solved,rank_index=$rank_index,penalty=$penalty,ac_info='$ac_info'");
        return $this->InsertId;
    }
    function updatePlayerContestSummary($contest_id,$player_id,$problem_index,$ac_status)
    {
        if($this->_sum("select summary_id from acm_contest_summary where contest_id=$contest_id and player_id=$player_id and problem_index=$problem_index"))
            return $this->_query("update acm_contest_summary set ac_status=$ac_status where contest_id=$contest_id and player_id=$player_id and problem_index=$problem_index");
        else
            return $this->_insert("acm_contest_summary",array(
                "contest_id"=>$contest_id,
                "player_id"=>$player_id,
                "problem_index"=>$problem_index,
                "ac_status"=>$ac_status
            ));
    }

}

?>