<?php
class user_model extends AmysqlModel
{
    /**
     * @param string $user_name 用户名
     * @return bool
     */
    function isUserExist($user_name)
    {
        return $this->_sum("select user_id from user_info where user_name='$user_name' limit 0,1") >=1 ;
    }

    /**
     * @param string $username
     * @param string $password
     * @return int 存在返回userid，不存在返回false
     */
    function checkUserPassword($username,$password)
    {
        $result=$this->_row("select user_id from user_info where user_name='$username' and user_password='$password' limit 0,1");
        if(!$result)    return false;
        return $result['user_id'];
    }

    /**
     * @param string $username
     * @return int 存在返回userid，不存在返回false
     */
    function getUserId($username)
    {
        $result=$this->_row("select user_id from user_info where user_name='$username' limit 0,1");
        if(!$result)    return false;
        return $result['user_id'];
    }

    /**
     * @param integer $userid
     * @return Array|bool 存在返回userinfo，不存在返回false
     */
    function getUserInfo($userid)
    {
        $result = $this->_row("select * from user_info where user_id=$userid limit 0,1");
        if (!$result) return false;
        return $result;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $nickname
     * @param string $reg_ip
     * @return int user_id
     */
    function newUser($username,$password,$email,$nickname,$reg_ip)
    {
        return $this->_insert("user_info",array(
                "user_name"=>$username,
                "user_password"=>$password,
                "user_email"=>$email,
                "user_nickname"=>$nickname,
                "reg_ip"=>$reg_ip,
                "reg_time"=>time(),
                "player_id"=>0));
    }

    function userLogin($userid,$nickname)
    {
        $_SESSION['userid']=$userid;
        $_SESSION['nickname']=$nickname;
    }
    function userLogout()
    {
        unset($_SESSION['userid']);
        unset($_SESSION['nickname']);
    }
    function loginFromDs($ds_userid,$ds_access_token,$user_name,$login_ip)
    {
        $now=time();
        $this->_query("insert into user_info set ds_userid='$ds_userid',ds_access_token='$ds_access_token',user_nickname='$user_name',user_name='_ds_$ds_userid',login_ip='$login_ip',user_password='',reg_time=$now,login_time=$now ".
            "ON DUPLICATE KEY UPDATE ds_access_token='$ds_access_token',user_nickname='$user_name',login_ip='$login_ip',login_time=$now");
        $row=$this->_row("select user_id from user_info where ds_userid='$ds_userid'");
        return $row['user_id'];
    }
    function bindPlayer($userid,$group_id,$player_name)
    {
        $this->_query("insert into acm_player set player_name='$player_name',player_group_id=$group_id");
        if(!($player_id=$this->InsertId))
            return false;
        $this->_query("update user_info set player_id=$player_id where user_id=$userid");
        return $player_id;
    }


}