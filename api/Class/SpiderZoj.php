<?php
include_once(_Class . "SpiderBasic.php");

class SpiderZoj extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $is_login=true;
    private $submit_result;
    function submitJob()
    {
        $ac_status=new acStatus();
        $url="http://acm.zju.edu.cn/onlinejudge";
        $this->submit_result=new submitResult();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n****************************************************************\n**/";
        $problem_id=intval($this->problem_info['problem_identity']);
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/showProblem.do?problemCode=$problem_id");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get("http://acm.zju.edu.cn/onlinejudge/showProblem.do?problemCode=$problem_id",10);
        if(!$html)
            return false;
        $realID=intval($this->getSubStr($html,'/onlinejudge/submit.do?problemId=','">',0));
        if($realID<=0)
            return false;
        $request=array(
            "problemId"=>$realID,
            "languageId"=>$this->job_info['compiler_id'],
            "source"=>$this->job_info['source_code']
        );

        $return=$curl->post("$url/submit.do",$request,10);
        $id=intval($this->getSubStr($return,"<font color='red'>","</font>",0));
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
        $url="http://acm.zju.edu.cn/onlinejudge";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $username=$this->spider_info['oj_username'];
        $html=$curl->get("$url/showRuns.do?contestId=1&search=true&firstId=-1&lastId=-1&problemCode=&handle=$username&idStart=&idEnd=" ,10);
        $html=str_replace("rowOdd","rowEven",$html);
        $results=explode('<tr class="rowEven">',$html);
        foreach($results as $one)
        {

            $result=new jobResult();
            $acStatus=new acStatus();
            $result->remote_run_id=intval($this->getSubStrByPreg('/<td class="runId">([^<]*)<\/td>/',$one));
            if($result->remote_run_id<=0)
                continue;
            $result->wrong_info=trim($this->getSubStrByPreg('/<span class="[^"]*">([^<]*)<\/span>/',$one));
            if($result->wrong_info=="")
            {
                $result->wrong_info=trim($this->getSubStrByPreg('/<a href="\/onlinejudge\/showJudgeComment\.do\?submissionId=[0-9]*">([^<]*)<\/a>/',$one));
            }
            $result->time_usage=intval($this->getSubStrByPreg('/<td class="runTime">([^<]*)<\/td>/',$one));
            $result->ram_usage=intval($this->getSubStrByPreg('/<td class="runMemory">([^<]*)<\/td>/',$one))*1024;
            switch(substr($result->wrong_info,0,9))
            {
                case 'Accepted':
                    $result->ac_status=$acStatus->OK;
                    break;
                case 'Wrong Ans':
                    $result->ac_status=$acStatus->WRONG_ANSWER;
                    break;
                case 'Presentat':
                    $result->ac_status=$acStatus->PRESENTATION_ERROR;
                    break;
                case 'Compilati':
                    $result->ac_status=$acStatus->COMPILATION_ERROR;
                    break;
                case 'Segmentat':
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 'Time Limi':
                    $result->ac_status=$acStatus->TIME_LIMIT_EXCEEDED;
                    break;
                case 'Memory Li':
                    $result->ac_status=$acStatus->MEMORY_LIMIT_EXCEEDED;
                    break;
                case 'Output Li':
                    $result->ac_status=$acStatus->OUTPUT_LIMIT_EXCEEDED;
                    break;
                case 'Non-zero ':
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 'Floating ':
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 'Queuing':
                    $result->ac_status=$acStatus->TESTING;
                    break;
                case 'Compiling':
                    $result->ac_status=$acStatus->TESTING;
                    break;
                case 'Running':
                    $result->ac_status=$acStatus->TESTING;
                    break;
                default:
                    $result->ac_status=$acStatus->TESTING;
                    break;
            }
            $result->result_info =array();
            $result->result_info['origin']="";
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
        $url="http://acm.zju.edu.cn/onlinejudge";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("$url/");
        if(strpos($html,'/onlinejudge/logout.do')===false)
            return false;
        else
            return true;
    }
    function login()
    {
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $url="http://acm.zju.edu.cn/onlinejudge";
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $request=array(
            "handle"=>$username,
            "password"=>$password,
            "rememberMe"=>"on"
            );
        $return=$curl->post("$url/login.do",$request,10);
        $cookie=$curl->getResponseCookie();
        $this->additionInfo['cookie']=$cookie;
        $curl->setCookieRaw($cookie);
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