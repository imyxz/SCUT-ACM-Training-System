<?php
class remember_model extends AmysqlModel
{
    /**
     * @param string $remember_pass
     * @param int $user_id
     * @param int $login_time
     * @param string $login_ip
     * @return int rememberid
     */
    function newRemember($remember_pass,$user_id,$login_time,$login_ip)
    {
        return $this->_insert("remember_info",array("remember_pass"=>$remember_pass,
            "user_id"=>$user_id,
            "login_time"=>$login_time,
            "login_ip"=>$login_ip));
    }

    /**
     * @param int $remember_id
     * @return Array rememberinfo
     */
    function getRemember($remember_id)
    {
        return $this->_row("select * from remember_info where remember_id=$remember_id");
    }

    /**
     * @param $remember_id
     * @return Object
     */
    function delRemember($remember_id)
    {
        return $this->_query("delete from remember_info where remember_id=$remember_id");
    }

    /**
     * @return int ·µ»Øuserid
     */
    function checkRememberLogin()
    {
        if(!$remember_id=$this->getRememberId())    return false;
        $remember_pass=$_COOKIE['remember_' . $remember_id];
        $remember_info=$this->getRemember($remember_id);
        if(!empty($remember_pass) && $remember_pass==$remember_info['remember_pass']) return $remember_info['user_id'];
        return false;
    }

    /**
     * @return bool|int
     */
    function getRememberId()
    {
        foreach($_COOKIE as $key => $value)
        {
            if(substr($key,0,9)=='remember_')
            {
                return intval(substr($key,9));
            }
        }
        return false;
    }

    /**
     *
     */
    function logoutRemember()
    {
        if(!$remember_id=$this->getRememberId()) return;
        $remember_pass=$_COOKIE['remember_' . $remember_id];
        $remember_info=$this->getRemember($remember_id);
        if(!empty($remember_pass) && $remember_pass==$remember_info['remember_pass']) $this->delRemember($remember_id);
        setcookie('remember_' . $remember_id,'',time()-3600,'/');
    }

}

?>