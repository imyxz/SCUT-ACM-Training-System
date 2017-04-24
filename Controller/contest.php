<?php

class contest extends AmysqlController
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
    // 默认首页
    function summary()
    {
        $contest_id=intval($_GET['id']);
        $contest_info=$this->_model("contest_model")->getContestInfo($contest_id);
        $contest_board=$this->_model("contest_model")->getContestBoard($contest_id);
        $this->team_info=array();
        $this->summary_info=array();
        foreach($contest_board as &$one)
        {
            $row=array();
            $group_info=$this->_model("group_model")->getGroupInfo($one['group_id']);
            $group_player=$this->_model("group_model")->getGroupMember($one['group_id']);

            $row['name']=$group_info['group_name'];
            $this->summary_info[$group_info['group_name']]=array();
            foreach($group_player as &$person)
            {
                $row['player'][]=$person['player_name'];
            }
            $this->team_info[]=$row;
            $one['ac_info']=json_decode($one['ac_info'],true);
        }
        $problem_count=$contest_info['contest_problem_count'];
        $this->problem_count=$problem_count;
        $this->contest_name=$contest_info['contest_name'];
        $this->contest_description=$contest_info['contest_description'];
        $summary=$this->_model("contest_model")->getContestSummary($contest_id);
        foreach($summary as &$one)
        {
            $row=array();
            $row['player_name']=$one['player_name'];
            $row['ac_status']=$one['ac_status'];
            $this->summary_info[$one['group_name']][$one['problem_index']][]=$row;
        }
        $this->contest_board=$contest_board;
        if($this->isPlayer)
        {
            $player_ac=$summary=$this->_model("contest_model")->getPlayerContestSummary($contest_id,$this->player_id);
            $this->player_ac=array();
            for($i=1;$i<=$problem_count;$i++)
            {
                $this->player_ac[$i]=4;
            }
            foreach($player_ac as &$one)
            {
                $this->player_ac[$one['problem_index']]=intval($one['ac_status']);
            }
        }

        $this->contest_id=$contest_id;
        $this->title=$contest_info['contest_name'];
        $this->_view("summary_page");
    }
    function addContest()
    {
        $this->title="添加比赛";
        $this->active=2;
        $this->_view("add_contest");
    }
    function goAddContest()
    {
        try{
            if($this->if_login==false)
                throw new Exception("您还未登录，请先登录");
            $problem_count=intval($_POST['addcontest-problem_count']);
            $contest_name=addslashes(trim($_POST['addcontest-name']));
            $contest_description=addslashes(trim($_POST['addcontest-contest_description']));
            $contest_starttime=intval($_POST['addcontest-start_time']);
            $contest_endtime=intval($_POST['addcontest-end_time']);
            if($problem_count<1)
                throw new Exception("题目数量输入有误");
            if($contest_starttime<1 || $contest_endtime<1)
                throw new Exception("开始结束时间输入错误");
            if($_POST['addcontest-source']=='vj')
            {
                $json=json_decode($_POST['addcontest-contest_board'],true);
                if(!$json)
                    throw new Exception("Board数据解析出错！");
            }
            $contest_id= $this->_model("contest_model")->newContest($contest_name,$problem_count,$contest_starttime,$contest_endtime);
            if(!$contest_id)
                throw new Exception("系统内部出错！");
            $this->_model("contest_model")->updateContestDescription($contest_id,$contest_description);
            $groups=array();
            $is_ok=true;
            switch($_POST['addcontest-source'])
            {
                case 'vj':
                    $groups=$this->addFromVj($json,$contest_id);
                    break;
                case 'zoj':
                    $groups=$this->addFromZoj($_POST['addcontest-contest_board'],$problem_count);
                    break;
                case 'seoj':
                    $groups=$this->addFromSeoj($_POST['addcontest-contest_board'],$problem_count);
                    break;
                default:
                    $is_ok=false;

            }
            if(!$is_ok)
                throw new Exception("未知的board信息来源！");
            foreach($groups as &$one)
            {
                $this->_model('contest_model')->insertBoardInfo($contest_id, $one['group_id'], $one['total_ac'], $one['rank_index'], intval($one['total_penalty']),addslashes(json_encode($one)));
            }
            $return['result']=var_export($groups,true);
            $return['contest_id']=$contest_id;
            $return['status']=1;
            header("Content-type: application/json");
            echo json_encode($return);
        }catch (Exception $e)
        {
            $return['status']=0;
            $return['message']=urlencode($e->getMessage());
            header("Content-type: application/json");
            echo json_encode($return);
        }
    }
    protected function addFromZoj($data,$problem_count)
    {
        $data=explode("\n",$data);
        $format="%d %s %s %d";
        for($i=0;$i<$problem_count;$i++)
            $format = $format . ' %s';
        $format=$format . ' %d';
        $arr_size=5+$problem_count;
        $groups=array();
        $rank=1;
        for($i=6;$i<count($data);$i++)
        {
            $info=sscanf($data[$i],$format);
            if(count($info)<$arr_size)
                continue;
            $row=array();
            $row['group_id']=$this->_model("group_model")->getGroupIDByZOJUsername(addslashes($info[1]));
            $row['group_username']=$info[1];
            if(!$row['group_id'])
                continue;
            $row['submission']=array();
            $row['total_ac']=intval($info[3]);
            $row['total_ac_time']=0;
            $row['total_penalty']=$info[$arr_size -1];

            for($pi=1;$pi<=$problem_count;$pi++)
            {
                $try=0;
                $ac_time=0;
                if(strpos($info[3+$pi],')'))
                {
                    sscanf($info[3+$pi],"%d(%d)",$ac_time,$try);
                }
                else
                    $try=intval($info[3+$pi]);
                if($try>0)
                {
                    $row['submission'][$pi]=array();
                    $row['submission'][$pi]['info']=array();
                    if($ac_time>0)
                    {
                        $row['submission'][$pi]['ac']=true;
                        $row['submission'][$pi]['ac_time']=60*$ac_time;
                        $row['total_ac_time']+=60*$ac_time;
                        $row['submission'][$pi]['try']=$try-1;
                    }
                    else
                    {
                        $row['submission'][$pi]['ac']=false;
                        $row['submission'][$pi]['ac_time']=0;
                        $row['submission'][$pi]['try']=$try;
                    }

                }
            }
            $row['rank_index']=$rank;
            $rank++;
            $groups[]=$row;
        }
        return $groups;

    }
    protected function addFromSeoj($data,$problem_count)
    {
        $data=explode("\n",$data);
        $format="%d %s %s %d %d";
        for($i=0;$i<$problem_count;$i++)
            $format = $format . ' %s';
        $arr_size=5+$problem_count;
        $groups=array();
        $rank=1;
        for($i=1;$i<count($data);$i++)
        {
            $info=sscanf($data[$i],$format);
            if(count($info)<$arr_size)
                continue;
            $row=array();
            $row['group_id']=$this->_model("group_model")->getGroupIDBySEOJUsername(addslashes($info[1]));
            $row['group_username']=$info[1];
            if(!$row['group_id'])
                continue;
            $row['submission']=array();
            $row['total_ac']=intval($info[3]);
            $row['total_ac_time']=0;
            $row['total_penalty']=0;
            for($pi=1;$pi<=$problem_count;$pi++)
            {
                $try=0;
                $ac_time=0;
                $h=0;
                $m=0;
                $s=0;

                if(strpos($info[4+$pi],':') || strpos($info[4+$pi],')'))
                {
                    if(strpos($info[4+$pi],':') && strpos($info[4+$pi],')'))
                    {
                        sscanf($info[4+$pi],"%d:%d:%d(%d)",$h,$m,$s,$try);
                        $ac_time=$h*3600+$m*60+$s;
                        $try= $try*-1;
                        $row['total_penalty']+=$try*20;
                        $row['total_penalty']+=$ac_time/60;

                    }
                    else if(strpos($info[4+$pi],':'))
                    {
                        sscanf($info[4+$pi],"%d:%d:%d",$h,$m,$s);
                        $ac_time=$h*3600+$m*60+$s;
                        $try= 0;
                        $row['total_penalty']+=$ac_time/60;
                    }
                    else if(strpos($info[4+$pi],')'))
                    {
                        sscanf($info[4+$pi],"(%d)",$try);
                        $try= $try*-1;
                    }
                }
                else
                    continue;
                if($ac_time>0 || $try>0)
                {
                    $row['submission'][$pi]=array();
                    $row['submission'][$pi]['info']=array();
                    if($ac_time>0)
                    {
                        $row['submission'][$pi]['ac']=true;
                        $row['submission'][$pi]['ac_time']=$ac_time;
                        $row['total_ac_time']+=$ac_time;
                        $row['submission'][$pi]['try']=$try;
                    }
                    else
                    {
                        $row['submission'][$pi]['ac']=false;
                        $row['submission'][$pi]['ac_time']=0;
                        $row['submission'][$pi]['try']=$try;
                    }

                }
            }
            $row['rank_index']=$rank;
            $rank++;
            $groups[]=$row;
        }
        return $groups;

    }
    protected function addFromVj($json,$contest_id)
    {
        $groups=array();
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
                $this->_model('contest_model')->insertBoardSubmission($contest_id, $groups[$one[0]]['group_id'], $problem_id, $row['ac_status'], $row['submit_time']);
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
            $one['rank_index']=$i;
            $i++;
        }
        return $groups;
    }


}