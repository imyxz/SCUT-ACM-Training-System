<?php

class contest extends AmysqlController
{
    // Ä¬ÈÏÊ×Ò³
    function summary()
    {
        $contest_id=intval($_GET['id']);
        $contest_info=$this->_model("contest_model")->getContestInfo($contest_id);
        $contest_board=$this->_model("contest_model")->getContestBoard($contest_id);
        $this->team_info=array();
        $this->summary_info=array();
        foreach($contest_board as $one)
        {
            $row=array();
            $group_info=$this->_model("group_model")->getGroupInfo($one['group_id']);
            $group_player=$this->_model("group_model")->getGroupMember($one['group_id']);

            $row['name']=$group_info['group_name'];
            $this->summary_info[$group_info['group_name']]=array();
            foreach($group_player as $person)
            {
                $row['player'][]=$person['player_name'];
            }
            $this->team_info[]=$row;
        }
        $this->problem_count=$contest_info['contest_problem_count'];
        $this->contest_name=$contest_info['contest_name'];
        $this->contest_description=$contest_info['contest_description'];
        $summary=$this->_model("contest_model")->getContestSummary($contest_id);

        foreach($summary as $one)
        {
            $row=array();
            $row['player_name']=$one['player_name'];
            $row['ac_status']=$one['ac_status'];
            $this->summary_info[$one['group_name']][$one['problem_index']][]=$row;
        }
        $this->_view("summary_page");
    }


}