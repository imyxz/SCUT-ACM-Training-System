<?php
include_once("curlRequest.php");
include_once(_Class . "acStatus.php");
include_once(_Class . "jobResult.php");
include_once(_Class . "submitResult.php");
require (_Root."vendor/autoload.php");
use PHPHtmlParser\Dom;
abstract class SpiderBasic
{
    protected $job_info,$problem_info,$spider_info;
    protected $additionInfoUpdated=false;
    protected $additionInfo;
    protected $query_jobs;

    public function setSpiderInfo($spider_info)
    {
        $this->spider_info=$spider_info;
        $this->additionInfo=json_decode($spider_info['spider_addition_info'],true);
    }
    public function setSubmitJobInfo($job_info,$problem_info)
    {
        $this->job_info=$job_info;
        $this->problem_info=$problem_info;
    }
    public function setQueryJobInfo($job_id_array)
    {
        $this->query_jobs=$job_id_array;
    }
    public function isAdditionInfoUpdated()
    {
        return $this->additionInfoUpdated;
    }
    public function getAdditionInfo()
    {
        return json_encode($this->additionInfo);
    }
    protected function getSubStr($str,$needle1,$needle2,$start_pos)
    {
        $pos1=strpos($str,$needle1,$start_pos);
        if($pos1===false) return false;
        $pos2=strpos($str,$needle2,$pos1+strlen($needle1));
        if($pos2===false)   return false;
        return substr($str,$pos1+strlen($needle1),$pos2-$pos1-strlen($needle1));
    }
    abstract function submitJob();
    abstract function queryJob();
    abstract function getSubmitResult();
    abstract function getQueryResult();
    abstract function checkLogin();
    abstract function login();
}