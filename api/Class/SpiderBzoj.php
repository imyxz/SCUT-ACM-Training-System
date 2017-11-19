<?php
include_once(_Class . "SpiderBasic.php");

class SpiderBzoj extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $is_login=true;
    private $submit_result;
    function submitJob()
    {
        $ac_status=new acStatus();
        $url="http://www.lydsy.com/JudgeOnline";
        $this->submit_result=new submitResult();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n****************************************************************\n**/";
        $problem_id=intval($this->problem_info['problem_identity']);
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/submit.php?pid=$problem_id");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $request=array(
            "id"=>$problem_id,
            "language"=>$this->job_info['compiler_id'],
            "source"=>$this->job_info['source_code'],
            "input_text"=>"",
            "out"=>""
        );

        $return=$curl->post("$url/submit.php?action=submit",$request,10);

        if($curl->getResponseCode()=="302" && empty($return))//��������޷��أ���ת��
        {
            $html=$curl->get("$url/status.php?user_id=$username",10);
            $this->remote_runid=trim($this->getSubStr($html,"<tr align=center class='evenrow'><td>",'<td>',0));

            $this->submit_result->remote_run_id=$this->remote_runid;
            return true;
        }
        else
            return false;
    }
    function queryJob()
    {
        $curl=new curlRequest();
        $this->query_job_info=array();
        $url="http://www.lydsy.com/JudgeOnline";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $username=$this->spider_info['oj_username'];
        $html=$curl->get("$url/status.php?user_id=$username",10);
        $table= $this->getSubStr($html,"<table align=center>","</table>",0) ;
        $dom=new DOMDocument();
        @$dom->loadHTML($table);
        $trs=$dom->getElementsByTagName("tr");
        for($i=1;$i<$trs->length;$i++)
        {


            $tds= $trs->item($i)->childNodes;
            $result=new jobResult();
            $acStatus=new acStatus();
            $result->remote_run_id=trim($tds->item(0)->textContent);
            $result->wrong_info=trim($tds->item(3)->textContent);
            $result->time_usage=doubleval($tds->item(5)->textContent);
            $result->ram_usage=doubleval($tds->item(4)->textContent)*1024;

            switch(substr($result->wrong_info,0,9))
            {
                case 'Accepted':
                    $result->ac_status=$acStatus->OK;
                    break;
                case 'Wrong_Ans':
                    $result->ac_status=$acStatus->WRONG_ANSWER;
                    break;
                case 'Presentat':
                    $result->ac_status=$acStatus->PRESENTATION_ERROR;
                    break;
                case 'Compile_E':
                    $result->ac_status=$acStatus->COMPILATION_ERROR;
                    break;
                case 'Runtime_E':
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 'Time_Limi':
                    $result->ac_status=$acStatus->TIME_LIMIT_EXCEEDED;
                    break;
                case 'Memory_Li':
                    $result->ac_status=$acStatus->MEMORY_LIMIT_EXCEEDED;
                    break;
                case 'Output_Li':
                    $result->ac_status=$acStatus->OUTPUT_LIMIT_EXCEEDED;
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
            $result->result_info['origin']=$trs->item($i)->textContent;
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
        $url="http://www.lydsy.com/JudgeOnline";
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("$url/");
        if(strpos($html,'Register')!==false)
            return false;
        else
            return true;
    }
    function login()
    {
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $url="http://www.lydsy.com/JudgeOnline";
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/submit.php");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        //$vcode_gif=$curl->get("$url/vcode.php",10);



        //$vcode=$this->ocr($vcode_gif);
        $request=array(
            "user_id"=>$username,
            "password"=>$password,
            "submit"=>'Submit');
        $return=$curl->post("$url/login.php",$request,10);
        $this->additionInfo['cookie']=$curl->getResponseCookie() . ";1=1;";
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