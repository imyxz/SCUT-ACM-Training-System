<?php
include_once(_Class . "SpiderBasic.php");

class SpiderPoj extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $is_login=true;
    private $submit_result;
    function submitJob()
    {
        $ac_status=new acStatus();
        $this->submit_result=new submitResult();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n****************************************************************\n**/";
        $problem_id=intval($this->problem_info['problem_identity']);
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://poj.org/submit?problem_id=$problem_id");
        $curl->setHeader("Origin: http://poj.org");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $request=array(
            "problem_id"=>$problem_id,
            "language"=>$this->job_info['compiler_id'],
            "source"=>base64_encode($this->job_info['source_code']),
            "submit"=>"Submit",
            "encoded"=>1
        );
        $return=$curl->post("http://poj.org/submit",$request,10);

        if($curl->getResponseCode()=="302" && empty($return))//正常情况无返回（跳转）
        {
            $html=$curl->get("http://poj.org/status?problem_id=&user_id=$username&result=&language=",10);
            if(strpos($html,'Login Log')===false)
            {
                $this->is_login=false;
                return false;//说明掉线了
            }
            $this->remote_runid=$this->getSubStr($html,'<tr align=center><td>','</td>',0);

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
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://poj.org/");
        $curl->setHeader("Origin: http://poj.org");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $username=$this->spider_info['oj_username'];
        $html=$curl->get("http://poj.org/status?problem_id=&user_id=$username&result=&language=",10);
        $table= $this->getSubStr($html,"<TABLE cellSpacing=0 cellPadding=0 width=100% border=1 class=a bordercolor=#FFFFFF>","</table>",0);
        $dom=new DOMDocument();
        $dom->loadHTML($table);
        $trs=$dom->getElementsByTagName("tr");
        for($i=1;$i<$trs->length;$i++)
        {
            $tds= $trs->item($i)->childNodes;
            $result=new jobResult();
            $acStatus=new acStatus();
            $result->remote_run_id=$tds->item(0)->textContent;
            $result->wrong_info=$tds->item(3)->textContent;
            $result->time_usage=doubleval($tds->item(5)->textContent);
            $result->ram_usage=doubleval($tds->item(4)->textContent)*1024;
            switch(substr($tds->item(3)->textContent,0,9))
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
                case 'Compile E':
                    $result->ac_status=$acStatus->COMPILATION_ERROR;
                    break;
                case 'Runtime E':
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
                case 'Queuing':
                    $result->ac_status=$acStatus->TESTING;
                    break;
                case 'Compiling':
                    $result->ac_status=$acStatus->TESTING;
                    break;
                case 'Running &':
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
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("http://poj.org/");
        if(strpos($html,'Login Log')!==false)
            return true;
        else
            return false;
    }
    function login()
    {
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://poj.org/login");
        $curl->setHeader("Origin: http://poj.org");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $request=array(
            "user_id1"=>$username,
            "password1"=>$password,
            "B1"=>'login',
            "url"=>'.');
        $return=$curl->post("http://poj.org/login",$request,10);

        if(empty($return) && $curl->getResponseCode()==302)
        {

            $this->additionInfo['cookie']=$curl->getResponseCookie();
            if($this->checkLogin())
            {
                $this->additionInfoUpdated=true;
                return true;

            }
            else
                return false;
        }
        else
            return false;

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