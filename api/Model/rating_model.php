<?php
/**
 * User: imyxz
 * Date: 2018-03-16
 * Time: 21:53
 * Github: https://github.com/imyxz/
 */
class rating_model extends SlimvcModel{
    function addRating($is_system_contest,$contest_id,$contest_name,$rank_data)
    {
        if(!$this->queryStmt("insert into rating_contest set is_system_contest=?,contest_id=?,contest_name=?,rank_data=?,rating_time=now()",
            "iiss",
            $is_system_contest,
            $contest_id,
            $contest_name,
            $rank_data))    return false;
        return $this->InsertId;
    }
    function addRatingHistory($rating_id,$user_id,$rank,$prev_rating_id,$from_rating,$to_rating)
    {
        return $this->queryStmt("insert into rating_history set rating_id=?,user_id=?,rank=?,prev_rating_id=?,from_rating=?,to_rating=?",
            "iiiidd",
            $rating_id,
            $user_id,
            $rank,
            $prev_rating_id,
            $from_rating,
            $to_rating);
    }
    function updateUserRating($user_id,$rating,$cur_rating_id)
    {
        return $this->queryStmt("insert into user_rating set user_id=?,rating=?,start_date=now(),cur_rating_id=?,update_date=now()
               on duplicate key update rating=?,cur_rating_id=?,update_date=now()",
            "ididi",
            $user_id,
            $rating,
            $cur_rating_id,
            $rating,
            $cur_rating_id);
    }
    function getUserRating($user_id)
    {
        return $this->queryStmt("select * from user_rating where user_id=? limit 1",
            "i",
            $user_id)->row();
    }
    function getRank()
    {
        return $this->query("select user_info.user_nickname,user_info.user_avatar,user_rating.*,T.contest_cnt from user_info,user_rating,(select count(*) as contest_cnt,user_id from rating_history group by user_id) T where user_info.user_id=user_rating.user_id and T.user_id=user_rating.user_id order by rating desc")->all();
    }
    function getList()
    {
        return $this->query("select rating_id,contest_id,is_system_contest,rating_time from rating_contest order by rating_time desc")->all();
    }
    function getContestRatingHistory($rating_id)
    {
        return $this->queryStmt("select user_info.user_nickname,user_info.user_avatar,rating_history.* from rating_history,user_info where rating_history.rating_id=? and user_info.user_id=rating_history.user_id order by rating_history.rank asc",
            "i",
            $rating_id)->all();
    }
    function getUserRatingHistory($user_id)
    {
        return $this->queryStmt("select RH.*,RC.rating_id,RC.contest_name,RC.contest_id,RC.is_system_contest,RC.rating_time from rating_contest as RC,rating_history as RH where RH.user_id = ? and RC.rating_id = RH.rating_id order by rating_time desc",
            "i",
            $user_id)->all();
    }
    function getRatingInfo($rating_id)
    {
        return $this->queryStmt("select rating_id,contest_name,contest_id,is_system_contest,rating_time from rating_contest where rating_id=?",
            "i",
            $rating_id)->row();
    }
}