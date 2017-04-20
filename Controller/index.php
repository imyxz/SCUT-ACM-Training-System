<?php

class index extends AmysqlController
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
	function IndexAction()
	{
		$this->title='首页';
		$this->active=1;
		$this->contest_list=array();
		$this->contest_list=$this->_model("contest_model")->getAllContest();
		$this->_view('index');

	}
	function test()
	{
		echo 'a';
	}


}