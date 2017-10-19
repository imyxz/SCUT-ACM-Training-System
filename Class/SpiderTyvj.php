<?php
include_once(_Class . "SpiderBasic.php");

class SpiderTyvj extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $is_login=true;
    private $submit_result;
    function submitJob()
    {
        $ac_status=new acStatus();
        $url="http://www.tyvj.cn";
        $this->submit_result=new submitResult();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n****************************************************************\n**/";
        $problem_id=intval($this->problem_info['problem_identity']);
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/p/$problem_id");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get("www.tyvj.cn/p/$problem_id");
        $token=$this->getSubStr($html,'<form action="/Status/Create" id="frmSubmitCode" method="post"><input name="__RequestVerificationToken" type="hidden" value="','" />',0);

        $request=array(
            "problem_id"=>$problem_id,
            "language_id"=>$this->job_info['compiler_id'],
            "code"=>$this->job_info['source_code'],
            "__RequestVerificationToken"=>$token
        );

        $return=$curl->post("$url/Status/Create",$request,10);
        $id=intval($return);
        if($id>0)//��������޷��أ���ת��
        {
            $this->remote_runid=$id;

            $this->submit_result->remote_run_id=$id;
            return true;
        }
        else
            return false;
    }
    function queryJob()
    {
        $curl=new curlRequest();
        $this->query_job_info=array();
        $url="http://www.tyvj.cn";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $username=$this->spider_info['oj_username'];
        $json=$curl->get("$url/Status/GetStatuses?page=0&contestid=&problemid=&username=$username&result=&rnd=" . rand(0.0,1.0),10);
        $json=json_decode($json,true);
        foreach($json as $one)
        {
            $result=new jobResult();
            $acStatus=new acStatus();
            $result->remote_run_id=$one['ID'];
            $result->wrong_info=$one['Result'];
            $result->time_usage=$one['TimeUsage'];
            $result->ram_usage=$one['MemoryUsage']*1024;
            switch($one['ResultAsInt'])
            {
                case 0:
                    $result->ac_status=$acStatus->OK;
                    break;
                case 2:
                    $result->ac_status=$acStatus->WRONG_ANSWER;
                    break;
                case 1:
                    $result->ac_status=$acStatus->PRESENTATION_ERROR;
                    break;
                case 7:
                    $result->ac_status=$acStatus->COMPILATION_ERROR;
                    break;
                case 6:
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 4:
                    $result->ac_status=$acStatus->TIME_LIMIT_EXCEEDED;
                    break;
                case 5:
                    $result->ac_status=$acStatus->MEMORY_LIMIT_EXCEEDED;
                    break;
                case 3:
                    $result->ac_status=$acStatus->OUTPUT_LIMIT_EXCEEDED;
                    break;
                case 11:
                    $result->ac_status=$acStatus->TESTING;
                    break;
                case 9:
                    $result->ac_status=$acStatus->FAILED;
                    break;
                case 8:
                    $result->ac_status=$acStatus->FAILED;
                    break;
                case 10:
                    $result->ac_status=$acStatus->TESTING;
                    break;
                default:
                    $result->ac_status=$acStatus->TESTING;
                    break;
            }
            $result->result_info =array();
            $result->result_info['origin']=$one;
            $result->result_info['error_info']="";
            $result->result_info=json_encode($result->result_info);
            if(isset($this->query_jobs[$result->remote_run_id]))
            {
                $result->job_id=$this->query_jobs[$result->remote_run_id];
                $this->query_job_info[$result->remote_run_id]=$result;
            }
        }

        return true;
    }
    function getSubmitResult()
    {
        return  $this->submit_result;
    }
    function getQueryResult()
    {
        return $this->query_job_info;
    }
    function checkLogin()
    {
        if(!$this->is_login)
            return false;
        $curl=new curlRequest();
        $url="http://www.tyvj.cn";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("$url/");
        if(strpos($html,'href="/Register"')!==false)
            return false;
        else
            return true;
    }
    function login()
    {
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $url="http://www.tyvj.cn";
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get($url);
        $token=$this->getSubStr($html,'<form action="/Login" class="login-form" method="post" style="display:none;color:gray;"><input name="__RequestVerificationToken" type="hidden" value="','" />',0);
        $this->additionInfo['cookie']=$curl->getResponseCookie() . ";1=1;";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $request=array(
            "__RequestVerificationToken"=>$token,
            "Username"=>$username,
            "Password"=>$password,
            "Remember"=>"true"
            );
        $return=$curl->post("$url/Login",$request,10);
        $cookie=array_merge($curl->cookieStr2Arr($this->additionInfo['cookie']),$curl->cookieStr2Arr($curl->getResponseCookie()));
        $this->additionInfo['cookie']=$curl->cookieArr2Str($cookie);
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $this->is_login=true;
        if($this->checkLogin())
        {
            $this->additionInfoUpdated=true;
            return true;
        }
        else
        {
            $this->is_login=false;
            return false;
        }


    }
    function getRandomStr($cnt)
    {
        $ret='';
        $str="abcdefghijklmnopqrstuvwxyz1234567890";
        for($i=0;$i<$cnt;$i++)
        {
            $ret= $ret . substr($str,rand(0,strlen($str)-1),1);
        }
        return $ret;
    }
}