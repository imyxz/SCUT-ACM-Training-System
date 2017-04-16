<?php


class indexs extends AmysqlModel
{
	function databases()
	{
		$sql = "SHOW DATABASES";

		Return $this -> _all($sql);		// 取得Sql所有数据
	}

}

?>