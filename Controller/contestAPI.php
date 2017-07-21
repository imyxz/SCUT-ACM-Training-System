<?php
/**
 * User: imyxz
 * Date: 2017-07-18
 * Time: 19:40
 * Github: https://github.com/imyxz/
 */
class contestAPI extends SlimvcController
{
    function getAllContest()
    {
        $contest=$this->model("contest_model")->getAllContest();
        $return=array();
        $return['contest']=array();
        foreach($contest as $one)
        {
            $return['contest'][]=array(
                "id"=>$one['contest_id'],
                "name"=>$one['contest_name'],
                "desc"=>$one['contest_description']);
        }
        $return['status']=0;
        $this->outputJson($return);
    }
    function getContestSummary()
    {
        $contest_id=intval($_GET['id']);
        $contest_info=$this->model("contest_model")->getContestInfo($contest_id);
        $contest_board=$this->model("contest_model")->getContestBoard($contest_id);
        $team_info=array();
        $summary_info=array();
        $return=array();
        foreach($contest_board as &$one)
        {
            $row=array();
            $one['ac_info']=json_decode($one['ac_info'],true);
            $submission=$one['ac_info']['submission'];
            $one['ac_info']['submission']=array();
            $one_submission=&$one['ac_info']['submission'];
            for($i=1;$i<=$contest_info['contest_problem_count'];$i++)
            {
                if(isset($submission[$i]))
                {
                    $one_submission[$i]=$submission[$i];
                    if(!isset($one_submission[$i]['ac']))
                        $one_submission[$i]['ac']=false;
                    $one_submission[$i]['is_try']=true;
                }
                else
                {
                    $one_submission[$i]['ac']=false;
                    $one_submission[$i]['is_try']=false;
                }
                if(!isset($one_submission[$i]['try']))
                    $one_submission[$i]['try']=0;

            }
            if($one['group_id']>0)
            {
                $group_info=$this->model("group_model")->getGroupInfo($one['group_id']);
                $group_player=$this->model("group_model")->getGroupMember($one['group_id']);

                $row['name']=$group_info['group_name'];
                $summary_info[$group_info['group_name']]=array_pad(array(),$contest_info['contest_problem_count']+1,array());
                unset($summary_info[$group_info['group_name']][0]);
                foreach($group_player as &$person)
                {
                    $row['player'][]=$person['player_name'];
                }

                $team_info[]=$row;

            }
            else
            {
                $one['group_name']=$one['ac_info']['group_username'];
            }



        }
        $return['problem_count']=$contest_info['contest_problem_count'];
        $return['contest_name']=$contest_info['contest_name'];
        $return['contest_desc']=$contest_info['contest_description'];
        $return['team_info']=$team_info;
        $summary=$this->model("contest_model")->getContestSummary($contest_id);
        foreach($summary as &$one)
        {
            $row=array();
            $row['player_name']=$one['player_name'];
            $row['ac_status']=$one['ac_status'];
            $summary_info[$one['group_name']][$one['problem_index']][]=$row;
        }

        $return['contest_board']=$contest_board;
        $return['contest_summary']=$summary_info;
        $return['status']=0;

        $return['player_summary']=array();
        for($i=1;$i<=$return['problem_count'];$i++)
        {
            $return['player_summary'][$i]=4;
        }
        if($this->helper("user_helper")->isPlayer())
        {
            $player_ac=$summary=$this->model("contest_model")->getPlayerContestSummary($contest_id,$this->helper("user_helper")->getPlayerID());

            foreach($player_ac as &$one)
            {
                $return['player_summary'][intval($one['problem_index'])]=intval($one['ac_status']);
            }
        }
        $this->outputJson($return);


    }
    function addContestFromCSV()
    {
        try{
            $json=$this->getRequestJson();
            $contest_name=trim($json['contest_name']);
            $contest_starttime=intval($json['contest_starttime']);
            $contest_endtime=intval($json['contest_endtime']);
            $contest_description=$json['contest_desc'];
            $problem_count=$json['problem_count'];
            $csv_data=$json['csv_data'];
            $csv_data=explode("\n",$csv_data);
            if($problem_count<1)
                throw new Exception("题目数量输入有误");
            if($contest_starttime<1 || $contest_endtime<1)
                throw new Exception("开始结束时间输入错误");
            $contest_id= $this->model("contest_model")->newContest($contest_name,$problem_count,$contest_starttime,$contest_endtime);
            if(!$contest_id)
                throw new Exception("系统内部出错！");
            $this->model("contest_model")->updateContestDescription($contest_id,$contest_description);


            $groups=array();
            foreach($csv_data as $oneline)
            {
                $oneline=explode(",",$oneline);

                if(count($oneline)<4+$problem_count+1)
                    continue;
                if(empty(trim($oneline[0])) || intval($oneline[0])<=0)
                    continue;
                $rank_index=intval($oneline[0]);
                $group_name=trim($oneline[1]);
                $problem_solved=intval($oneline[2]);
                $row=array();
                $row['group_id']=$this->model("group_model")->getGroupIDByVJUsername($oneline[count($oneline)-1]);
                if(!$row['group_id'])
                {
                    $row['group_id']=0;
                    $row['in_system']=false;
                }
                else
                    $row['in_system']=true;

                $row['group_username']=$group_name;
                $row['submission']=array();
                $row['total_ac']=$problem_solved;
                $row['total_ac_time']=0;
                $row['total_penalty']=0;
                $row['rank_index']=$rank_index;
                for($x=4;$x<4+$problem_count;$x++)
                {
                    $oneline[$x]=trim($oneline[$x]);
                    $oneline[$x]=str_replace(array("\t","\r","\n","\0","\x0B",chr(160),chr(194)),array('','','','','','',''),$oneline[$x]);
                    if(empty($oneline[$x]))
                        continue;
                    $try=0;
                    $ac_time=0;
                    $h=0;
                    $m=0;
                    $s=0;
                    $str=$oneline[$x];
                    /*
                    for($a=0;$a<strlen($str);$a++)
                    {
                        echo ord($str[$a])." ";
                    }
                    echo "\n";
                    */


                    if(strpos($str,':') || strpos($str,')'))
                    {
                        if(strpos($str,':') && strpos($str,')'))
                        {
                            sscanf($str,"%d:%d:%d(%d)",$h,$m,$s,$try);
                            $ac_time=$h*3600+$m*60+$s;
                            $try= $try*-1;
                            $row['total_penalty']+=$try*20;
                            $row['total_penalty']+=$ac_time/60;

                        }
                        else if(strpos($str,':'))
                        {
                            sscanf($str,"%d:%d:%d",$h,$m,$s);
                            $ac_time=$h*3600+$m*60+$s;
                            $try= 0;
                            $row['total_penalty']+=$ac_time/60;
                        }
                        else if(strpos($str,')'))
                        {
                            sscanf($str,"(%d)",$try);
                            $try= $try*-1;
                        }
                    }
                    else
                        continue;
                    $pi=$x-3;
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
                $groups[]=$row;

            }
            foreach($groups as &$one)
            {
                $this->model('contest_model')->insertBoardInfo($contest_id, $one['group_id'], $one['total_ac'], $one['rank_index'], intval($one['total_penalty']),json_encode($one));

            }
            $return['result']=var_export($groups,true);
            $return['contest_id']=$contest_id;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
}