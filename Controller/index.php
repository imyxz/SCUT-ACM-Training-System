<?php

class index extends AmysqlController
{
	// 默认首页
	function IndexAction()
	{
		$this->title='';
		$this->_view('index');					// 载入index模板
	}


}