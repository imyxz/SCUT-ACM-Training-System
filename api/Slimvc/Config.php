<?php
/**
 * User: imyxz
 * Date: 2017/5/24
 * Time: 14:16
 * Github: https://github.com/imyxz/
 */
$Config=array();
$Config['DebugSql']=false;
$Config['Session']=true;
$Config['CharSet'] = 'utf-8';

$Config['XSS']=false;

$Config['Host'] = '127.0.0.1';
$Config['User'] = 'root';
$Config['Password'] = '';
$Config['DBname'] = 'acm_training_system';
$_SERVER['REQUEST_URI']=str_replace("ACM-System/","",@$_SERVER['REQUEST_URI']);