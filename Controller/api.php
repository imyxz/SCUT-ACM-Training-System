<?php

class api extends AmysqlController
{
    var $userid,$nickname,$if_login=false,$isPlayer,$player_id;
    function _init()
    {
        if (!isset($_SESSION['userid'])) {
            if ($userid = $this->_model('remember_model')->checkRememberLogin()) {
                $user_info = $this->_model('user_model')->getUserInfo($userid);
                $user_name = $user_info['user_nickname'];
                $this->_model('user_model')->userLogin($userid, $user_name);


            } else
                return;
        }
        $this->userid = $_SESSION['userid'];
        $this->nickname = $_SESSION['nickname'];
        $this->if_login = true;
        $user_info = $this->_model('user_model')->getUserInfo($this->userid);
        if(($player_id=$user_info['player_id'])>0)
        {
            $this->player_id=intval($player_id);
            $this->isPlayer=true;
        }
        else
            $this->isPlayer=false;
    }
    protected function temp()
    {
        $_POST['board_json']='';
        $json=json_decode($_POST['board_json'],true);

        $groups=array();
        $contest_id=1;
        $end_time=intval ($json['length']/1000);
        foreach($json['participants'] as $key=> &$one)
        {
            $row=array();
            $row['group_id']=$this->_model("group_model")->getGroupIDByVJUsername(addslashes($one[0]));
            $row['group_username']=$one[0];
            if(!$row['group_id'])
                continue;
            $row['submission']=array();
            $row['total_ac']=0;
            $row['total_ac_time']=0;
            $row['total_penalty']=0;
            $groups[$key]=$row;
        }
        foreach($json['submissions'] as &$one) {
            $vj_id=intval($one[0]);
            $problem_id = intval($one[1]) + 1;
            $ac_status=intval($one[2]);
            $ac_time=intval($one[3]);
            if (isset($groups[$vj_id]) && $ac_time<=$end_time) {
                $row = array();
                $row['ac_status'] = $ac_status;
                $row['submit_time'] = $ac_time;
                $groups[$vj_id]['submission'][$problem_id]['info'][] = $row;
                if ($row['ac_status'] == 1) {
                    if (!isset($groups[$vj_id]['submission'][$problem_id]['ac'])) {
                        $groups[$vj_id]['submission'][$problem_id]['ac'] = true;
                        $groups[$vj_id]['submission'][$problem_id]['ac_time'] = $ac_time;
                        $groups[$vj_id]['total_ac_time'] += $ac_time;
                        $groups[$vj_id]['total_ac']++;
                    }

                } else {
                    $groups[$vj_id]['submission'][$problem_id]['try'] += 1;

                }
                //$this->_model('contest_model')->insertBoardSubmission($contest_id, $groups[$one[0]]['group_id'], $one[1], $row['ac_status'], $row['submit_time']);
            }
        }
        foreach($groups as &$one)
        {
            foreach($one['submission'] as $problem)
            {
                if($problem['ac'])
                {
                    foreach($problem['info'] as $three)
                    {
                        if($three['ac_status']==0)
                            $one['total_penalty']+=20;
                        else
                            $one['total_penalty']+=$problem['ac_time']/60;
                    }
                }
            }
        }
        uasort($groups,function($a,$b)
        {

            if($a['total_ac']==$b['total_ac'])
                return $a['total_penalty']>=$b['total_penalty'];
            return $a['total_ac']<$b['total_ac'];
        });
        $i=1;
        foreach($groups as &$one)
        {
            uksort($one['submission'],function($a,$b)
            {
                return $a>=$b;
            });
            $this->_model('contest_model')->insertBoardInfo($contest_id, $one['group_id'], $one['total_ac'], $i, intval($one['total_penalty']),addslashes(json_encode($one)));
            $i++;

        }
    }
}