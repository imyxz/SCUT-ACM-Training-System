<?php


class contest_model extends SlimvcModel
{
    function getContestList($page,$limit)
    {
        $page--;
        if($page>0)
            $start=$page*$limit;
        else
            $start=0;
        return $this->queryStmt("select * from acm_contest ORDER by contest_begin_time desc limit ?,?",
            "ii",
            $start,
            $limit)->all();
    }
    function newContest($contest_name,$problem_count,$begin_time,$end_time)
    {
        $this->queryStmt("insert into acm_contest set contest_name=?,contest_begin_time=FROM_UNIXTIME(?),contest_end_time=FROM_UNIXTIME(?),contest_problem_count=?",
            "siii",
            $contest_name,
            $begin_time,
            $end_time,
            $problem_count);
        return $this->InsertId;
    }
    function getContestSummary($contest_id)
    {
        return $this->queryStmt("select acm_contest_summary.*,acm_group.group_name,acm_player.player_name,acm_player.player_group_id from acm_group,acm_player,acm_contest_summary where acm_contest_summary.contest_id=? and acm_player.player_id=acm_contest_summary.player_id and acm_group.group_id=acm_player.player_group_id order by acm_player.player_group_id asc,acm_contest_summary.problem_index asc",
            "i",
            $contest_id)->all();

    }
    function getPlayerContestSummary($contest_id,$player_id)
    {
        return $this->queryStmt("select * from acm_contest_summary where contest_id=? and player_id=? order by problem_index asc",
            "ii",
            $contest_id,
            $player_id)->all();

    }
    function getContestBoard($contest_id)
    {
        return $this->queryStmt("select acm_contest_board.*,acm_group.group_name from acm_contest_board,acm_group where acm_contest_board.contest_id=? and acm_group.group_id=acm_contest_board.group_id order by acm_contest_board.rank_index asc",
            "i",
            $contest_id)->all();
    }
    function getGroupMember($group_id)
    {
        return $this->queryStmt("select * from acm_player where player_group_id=?",
            "i",
            $group_id)->all();
    }
    function getContestInfo($contest_id)
    {
        return $this->queryStmt("select * from acm_contest where contest_id=?",
            "i",
            $contest_id)->row();
    }
    function updateContestDescription($contest_id,$description)
    {
        return $this->queryStmt("update acm_contest set contest_description=? where contest_id=?",
            "si",
            $description,
            $contest_id);
    }
    function getAllContest()
    {
        return $this->query("select * from acm_contest ORDER by contest_begin_time desc")->all();
    }
    function insertBoardSubmission($contest_id,$group_id,$problem_index,$ac_status,$submit_time)
    {
        $this->queryStmt("insert into acm_contest_board_submission set contest_id=?,group_id=?,problem_index=?,ac_status=?,submit_time=?",
            "iiiii",
            $contest_id,
            $group_id,
            $problem_index,
            $ac_status,
            $submit_time);
        return $this->InsertId;
    }
    function insertBoardInfo($contest_id,$group_id,$problem_solved,$rank_index,$penalty,$ac_info)
    {
        $this->queryStmt("insert into acm_contest_board set contest_id=?,group_id=?,problem_solved=?,rank_index=?,penalty=?,ac_info=?",
            "iiiiis",
            $contest_id,
            $group_id,
            $problem_solved,
            $rank_index,
            $penalty,
            $ac_info);
        return $this->InsertId;
    }
    function updatePlayerContestSummary($contest_id,$player_id,$problem_index,$ac_status)
    {
        if($this->queryStmt("select summary_id from acm_contest_summary where contest_id=? and player_id=? and problem_index=?",
            "iii",
            $contest_id,
            $player_id,
            $problem_index)->sum()>0)
            return $this->queryStmt("update acm_contest_summary set ac_status=? where contest_id=? and player_id=? and problem_index=?",
                "iiii",
                $ac_status,
                $contest_id,
                $player_id,
                $problem_index);
        else
        {
            $this->queryStmt("insert into acm_contest_summary set contest_id=?,player_id=?,problem_index=?,ac_status=?",
                "iiii",
                $contest_id,
                $player_id,
                $problem_index,
                $ac_status);
            return $this->InsertId;
        }

    }

}

?>