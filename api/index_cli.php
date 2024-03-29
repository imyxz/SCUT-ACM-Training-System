<?php
/**
 * User: imyxz
 * Date: 2017/5/24
 * Time: 14:18
 * Github: https://github.com/imyxz/
 */
if(PHP_SAPI !== 'cli')
    exit();
define('_DS_',DIRECTORY_SEPARATOR);          //目录分隔符
define('_Root',dirname(__FILE__) . _DS_);
define('_Slimvc',_Root . 'Slimvc' . _DS_);
define('_Controller',_Root . 'CliController' . _DS_);
define('_Model',_Root . 'Model' . _DS_);
define('_View',_Root . 'View' . _DS_);
define('_Class',_Root . 'Class' . _DS_);
define('_Helper',_Root . 'Helper' . _DS_);
define('_Log',_Root . 'Log' . _DS_);
define('_Http','');
include(_Slimvc . 'Config.php');
include(_Slimvc . 'Slimvc.php');


$Slimvc=new Slimvc();
$Slimvc->processor->initCliProcess();
$Slimvc->processor->startController();

