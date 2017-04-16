<?php

class index extends AmysqlController
{
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