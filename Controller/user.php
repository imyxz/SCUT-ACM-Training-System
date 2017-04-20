<?php

class user extends AmysqlController
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
    function goRegister()
    {

        $return=array();
        try{
            $user_name=strtolower(trim($_POST['register-username']));
            $password=trim($_POST['register-password']);
            $email=strtolower(trim($_POST['register-email']));
            if(strlen($user_name)>16 || strlen($user_name)<3)
                throw new Exception("用户名长度需介于3-16");
            if(empty($password)|| $password!=trim($_POST['register-repassword']))
                throw new Exception("两次密码输入不一致");
            if(strlen($email)>32 || strlen($user_name)<3)
                throw new Exception("Email长度需介于3-32");
            if($this->_model("user_model")->isUserExist(addslashes($user_name)))
                throw new Exception("该用户名已被注册！");
            $password=md5($password);
            $user_id=$this->_model("user_model")->newUser(addslashes($user_name),$password,addslashes($email),addslashes($user_name),addslashes($_SERVER['REMOTE_ADDR']));
            if($user_id<=0)
                throw new Exception("注册失败！");
            $this->_model('user_model')->userLogin($user_id,addslashes($user_name));
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
    function goLogin()
    {

        $return=array();
        try{
            $user_name=addslashes(strtolower(trim($_POST['login-username'])));
            $password=trim($_POST['login-password']);
            $password=md5($password);
            $user_id=$this->_model("user_model")->checkUserPassword($user_name,$password);
            if(!$user_id)
                throw new Exception("登录失败！密码错误");
            $this->_model('user_model')->userLogin($user_id,addslashes($user_name));
            if($_POST['login-remember']=='true')
            {
                $remember_pass=$this->getRandMd5();
                $remember_id=$this->_model('remember_model')->newRemember($remember_pass,$user_id,time(),addslashes($_SERVER["REMOTE_ADDR"]));
                ob_clean();
                setcookie('remember_' . $remember_id, $remember_pass , time()+60*60*24*365,'/');
            }

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
    function updateAcStatus()
    {
        try{
            if($this->if_login==false || $this->isPlayer==false)
                throw new Exception("请先登录");
            $contest_id=intval($_POST['contest_id']);
            $contest_info=$this->_model("contest_model")->getContestInfo($contest_id);
            if(!$contest_info)
                throw new Exception("contest id 有误");
            for($i=1;$i<=intval($contest_info['contest_problem_count']);$i++)
            {
                if(isset($_POST['problem_' . $i]))
                    $ac_status=intval($_POST['problem_' . $i]);
                else
                    $ac_status=4;
                $this->_model("contest_model")->updatePlayerContestSummary($contest_id,$this->player_id,$i,$ac_status);
            }
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
    function addTeam()
    {
        $this->title="添加小队";
        $this->active=3;
        $this->_view("add_group");
    }
    function goAddTeam()
    {
        try{
            if($this->if_login==false)
                throw new Exception("您还未登录，请先登录");
            $group_name=addslashes(trim($_POST['addgroup-groupname']));
            $vj_username=addslashes(trim($_POST['addgroup-vjusername']));
            if(strlen($group_name)>128 || strlen($group_name)<3)
                throw new Exception("队名长度需介于3-16");
            if(strlen($vj_username)>128 || strlen($vj_username)<3)
                throw new Exception("VJ账号名长度需介于3-16");
            $group_id= $this->_model("group_model")->addGroup($group_name,$vj_username);
            if($group_id<=0)
                throw new Exception("系统内部出错！");
            $return['group_id']=$group_id;
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
    function bindPlayer()
    {
        $this->title="绑定小队";
        $this->active=4;
        $this->all_group=$this->_model("group_model")->getAllGroup();
        $this->_view("bind_player");
    }
    function goBindPlayer()
    {
        try{
            if($this->if_login==false)
                throw new Exception("您还未登录，请先登录");
            $group_id=intval(trim($_POST['bindplayer-groupid']));
            $player_name=addslashes(trim($_POST['bindplayer-playername']));
            if($this->isPlayer)
                throw new Exception("您已补全了队员信息");
            if(!$this->_model("group_model")->getGroupInfo($group_id))
                throw new Exception("小队不存在！");
            if(strlen($player_name)>128 || strlen($player_name)<1)
                throw new Exception("姓名长度需介于1-128");
            $player_id= $this->_model("user_model")->bindPlayer($this->userid,$group_id,$player_name);
            if($player_id<=0)
                throw new Exception("系统内部出错！");
            $return['player_id']=$player_id;
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
    protected function getRandMd5()
    {
        return md5((time()/2940*time()/rand(1024,2325333)) . time() . "awoefpewofiajwepoisdnvsiejfwwaeifhpwhaaerghwrifpspdvnw");
    }
    protected function http_post($url, $post = NULL,$timeout=5)
    {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POSTFIELDS => $post
        );

        $ch = curl_init();
        curl_setopt_array($ch, ( $defaults));
        if( ! $result = curl_exec($ch))
        {
            return false;
        }
        curl_close($ch);
        return $result;
    }



}