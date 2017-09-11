<?php
/**
 * User: imyxz
 * Date: 2017-07-19
 * Time: 18:50
 * Github: https://github.com/imyxz/
 */
class userAPI extends SlimvcController
{
    function updateAcStatus()
    {
        try{
            if($this->helper("user_helper")->isPlayer()==false) throw new Exception("请先登录或填写个人队伍信息");
            $player_id=$this->helper("user_helper")->getPlayerID();
            $contest_id=intval($_GET['id']);
            $contest_info=$this->model("contest_model")->getContestInfo($contest_id);
            $ac_status=$this->getRequestJson();
            if(!$contest_info)
                throw new Exception("contest id 有误");
            if(!$ac_status)
                throw new Exception("提交数据有误");
            for($i=1;$i<=intval($contest_info['contest_problem_count']);$i++)
            {
                if(isset($ac_status[$i]))
                    $status=intval($ac_status[$i]);
                else
                    $status=4;
                $this->model("contest_model")->updatePlayerContestSummary($contest_id,$player_id,$i,$status);
            }
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function addTeam()
    {
        try{
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("请先登录");
            $json=$this->getRequestJson();
            $group_name=$json['team_name'];
            $vj_username=$json['team_vj_account'];
            if(strlen($group_name)>128 || strlen($group_name)<3)
                throw new Exception("队名长度需介于3-16");
            if(strlen($vj_username)>128 || strlen($vj_username)<3)
                throw new Exception("VJ账号名长度需介于3-16");
            $group_id= $this->model("group_model")->addGroup($group_name,$vj_username);
            if(!$group_id) throw new Exception("系统错误");
            $return['team_id']=$group_id;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getAllTeam()
    {
        $all_group=$this->model("group_model")->getAllGroup();
        $return=array();
        $return['teams']=array();
        foreach($all_group as $one)
        {
            $return['teams'][]=array("team_name"=>$one['group_name'],
                "team_id"=>$one['group_id']);
        }
        $return['status']=0;
        $this->outputJson($return);
    }
    function bindPlayer()
    {


        try{
            if($this->helper("user_helper")->isLogin()==false)
                throw new Exception("请先登录");
            $json=$this->getRequestJson();

            $group_id=intval($json['team_id']);
            $player_name=trim($json['real_name']);
            if($this->helper("user_helper")->isPlayer())
                throw new Exception("您已补全了队员信息");
            if($group_id<=0 || !$this->model("group_model")->getGroupInfo($group_id))
                throw new Exception("小队不存在！");
            if(strlen($player_name)>128 || strlen($player_name)<1)
                throw new Exception("姓名长度需介于1-128");
            if(!$player_id=$this->model("player_model")->getPlayerIDByGroupIDAndName($group_id,$player_name))
                $player_id=$this->model("player_model")->newPlayer($group_id,$player_name);
            $this->model("user_model")->bindPlayer($this->helper("user_helper")->getUserID(),$player_id);
            if($player_id<=0)
                throw new Exception("系统内部出错！");
            $return['player_id']=$player_id;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getBgPic()
    {
        $pic=$this->model("bg_pic_model")->getLastPic();
        $return['pic_url']=$pic['pic_url'];
        $return['status']=0;

        header("Cache-control: max-age=" . (60*60));
        $this->outputJson($return);
    }
    function getUserBgPic()
    {
        if($this->helper("user_helper")->isLogin()==false || empty($pic_url=$this->helper("user_helper")->getUserInfo()['user_bgpic']))
        {
            $pic=$this->model("bg_pic_model")->getLastPic();
            $return['pic_url']=$pic['pic_url'];
            $return['status']=0;
            header("Cache-control: max-age=" . (60*60));
            $this->outputJson($return);
        }
        else
        {
            $return['pic_url']=$pic_url;
            $return['status']=0;
            $this->outputJson($return);
        }





    }
}