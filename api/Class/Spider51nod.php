<?php
include_once(_Class . "SpiderBasic.php");

class Spider51nod extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $is_login=true;
    private $submit_result;
    function getJobLimit()
    {
        return 1;
    }

    function submitJob()
    {
        $ac_status=new acStatus();
        $this->submit_result=new submitResult();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n****************************************************************\n**/";
        $problem_id=intval($this->problem_info['problem_identity']);
        $compiler_id=$this->job_info['compiler_id'];
        $source_code=$this->job_info['source_code'];
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/questionCode.html");
        $curl->setHeader("Origin: http://www.51nod.com/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Content-Type: application/json; charset=UTF-8");
        $curl->setHeader("Accept: */*");
        $language='{"1":"C","2":"C 11","11":"C++","12":"C++ 11","21":"C#","31":"Java","41":"Python2","42":"Python3","45":"PyPy2","46":"PyPy3","51":"Ruby","61":"PHP","71":"Haskell","81":"Scala","91":"Javascript","101":"Go","111":"Visual C++","121":"Objective-C","131":"Pascal"}';
        $compiler='{"1":"C","2":"C11","11":"CPlus","12":"CPlus11","21":"CSharp","31":"Java","41":"Python2","42":"Python3","45":"PyPy2","46":"PyPy3","51":"Ruby","61":"Php","71":"Haskell","81":"Scala","91":"Javascript","101":"Go","111":"VCPlus","121":"OC","131":"Pascal"}';
        $compiler=json_decode($compiler,true);
        $request=array();
        $request['value']=array("IsPublic"=>1,
            "Language"=>$compiler[$compiler_id],
            "ProblemId"=>intval($problem_id),
            "ProgramContent"=>$source_code);
        $request=json_encode($request);
        $curl->get("http://www.51nod.com/ajax?n=/onlineJudge/questionCode.html&c=fastCSharp.Pub.AjaxCallBack&j=%7B%22problemId%22%3A%22". $problem_id ."%22%7D");
        $html=$curl->post("http://www.51nod.com/ajax?n=judge.Append&c=fastCSharp.IndexPool.Get%281%2C1%29.CallBack",$request,10);
        $return=$this->getSubStr($html,"CallBack(",")",0);
        if(strpos($html,"Get(1,1)")!==false &&!empty($return))
        {
                $user_id=$this->getSubStr($this->additionInfo['cookie'],"User=",".",0);
            $curl=new curlRequest();
            $curl->setCookieRaw($this->additionInfo['cookie']);
            $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
            $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/userProblemSubmitList.html");
            $curl->setHeader("Origin: http://www.51nod.com/");
            $curl->setHeader("Accept: */*");
            //$html=$curl->get("http://www.51nod.com/ajax?n=/onlineJudge/userProblemSubmitList.html&c=fastCSharp.Pub.AjaxCallBack&j=%7B%22userId%22%3A%2229003%22%7D");
                $html=$curl->get("http://www.51nod.com/ajax?n=/include/userProblemJudges.html&c=fastCSharp.Pub.AjaxCallBack&j=%7B%22userId%22%3A". $user_id ."%2C%22problemId%22%3A". $problem_id ."%7D&t" . time(),10);
                $id=$this->getSubStr($html,'",[',',',0);
                if(!$id)
                    return false;
                if(strpos($id,"0x")!==false)
                {
                    $id=str_replace("0x","",$id);
                    $id=trim($id);
                    $id=hexdec($id);
                }
                $this->submit_result->remote_run_id=$id;
                return true;

        }
        else
        {
            $this->is_login=false;
            return false;
        }
    }
    function queryJob()
    {
        $curl=new curlRequest();
        $query_job_info=array();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/submitDetail.html");
        $curl->setHeader("Accept: */*");
        $curl->setHeader("Accept-Language: zh-CN,zh;q=0.8");
        $username=$this->spider_info['oj_username'];
        foreach($this->query_jobs as $remote_run_id => &$one)
        {
            $html=$curl->get("http://www.51nod.com/ajax?n=/onlineJudge/submitDetail.html&c=fastCSharp.Pub.AjaxCallBack&j=%7B%22judgeId%22%3A%22". $remote_run_id  ."%22%7D",10);
            $start_pos=strpos($html,'judge:diantou.Judge.Get');
            if($start_pos===false)
            {
                $start_pos=strpos($html,'Judge:diantou.Judge.Get');
                if($start_pos===false)
                    continue;
            }
            $result=new jobResult();
            $acStatus=new acStatus();
            $IsFinished=$this->getSubStr($html,"IsFinished:",",",$start_pos);
            $JudgeValue=$this->getSubStr($html,'JudgeValue:"','",',$start_pos);
            $MemoryUse=$this->getSubStr($html,"MemoryUse:",",",$start_pos);
            $TestCount=$this->getSubStr($html,"TestCount:",",",$start_pos);
            $TimeUse=$this->getSubStr($html,"TimeUse:",",",$start_pos);
            $pos2=strpos($html,'diantou.JudgeItem',$start_pos);
            $items=$this->getSubStr($html,'[','].FormatView()',$pos2);
            $items = '[[' . $items . ']';
            $items=$this->jsonFormatHex($items);

            $result->result_info=$items;
            $items=json_decode($items,true);


            if(strpos($MemoryUse,"0x")!==false)
            {
                $MemoryUse=str_replace("0x","",$MemoryUse);
                $MemoryUse=hexdec($MemoryUse);
            }

            if(strpos($TimeUse,"0x")!==false)
            {
                $TimeUse=str_replace("0x","",$TimeUse);
                $TimeUse=hexdec($TimeUse);
            }
            if(strpos($TestCount,"0x")!==false)
            {
                $TestCount=str_replace("0x","",$TestCount);
                $TestCount=hexdec($TestCount);
            }
            $result->wrong_info=$JudgeValue;

            if($IsFinished==0)
            {
                $result->ac_status=$acStatus->TESTING;
            }
            else
            {
                switch(trim($JudgeValue))
                {
                    case 'Accepted':
                        $result->ac_status=$acStatus->OK;
                        break;
                    case 'WrongAnswer':
                        $result->ac_status=$acStatus->WRONG_ANSWER;
                        break;
                    case 'CompileError':
                        $result->ac_status=$acStatus->COMPILATION_ERROR;
                        break;
                    case 'RunTimeError':
                        $result->ac_status=$acStatus->RUNTIME_ERROR;
                        break;
                    case 'TimeLimitExceed':
                        $result->ac_status=$acStatus->TIME_LIMIT_EXCEEDED;
                        break;
                    case 'MemoryLimitExceed':
                        $result->ac_status=$acStatus->MEMORY_LIMIT_EXCEEDED;
                        break;
                    case 'Running':
                        $result->ac_status=$acStatus->TESTING;
                        break;
                    case 'Processing':
                        $result->ac_status=$acStatus->TESTING;
                        break;
                    default:
                        $result->ac_status=$acStatus->FAILED;
                        break;
                }
                if($result->ac_status!=$acStatus->OK)
                {
                    $finded=false;
                    for($item = 0;$item<count($items);$item++)
                    {
                        if($items[$item][3][2]!='Accepted')
                        {
                            $result->wrong_info=$result->wrong_info . ' on Case #' . ($item +1);
                            $finded =true;
                            break;
                        }
                    }
                    if(!$finded)
                    {
                        $result->wrong_info=$result->wrong_info . ' on Case UNKNOWN';
                    }
                }



            }
            $result->time_usage=$TimeUse;
            $result->ram_usage=$MemoryUse;
            $result->job_id=$one;
            $this->query_job_info[$result->remote_run_id]=$result;

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
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/questionCode.html");
        $curl->setHeader("Origin: http://www.51nod.com/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("http://www.51nod.com/ajax?n=/today.html&c=fastCSharp.Pub.AjaxCallBack");
        if(strpos($html,'currentUser:null')!==false)
            return false;
        else
            return true;
    }
    function login()
    {
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/questionCode.html");
        $curl->setHeader("Origin: http://www.51nod.com/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Content-Type: application/json; charset=UTF-8");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $request=array(
            "email"=>$username,
            "isEnterprise"=>0,
            "isRemember"=>true,
            "password"=>$password,
            "verify"=>"ligudan");

        $request=json_encode($request);
        $return=$curl->post("http://www.51nod.com/ajax?n=pub.Login&c=fastCSharp.IndexPool.Get%281%2C2%29.CallBack",$request,10);
        $return=$this->getSubStr($return,"CallBack(",")",0);


        if(strpos($return,"0x01")!==false)
        {
            $this->additionInfo=array();
            $this->additionInfo['cookie']=$curl->getResponseCookie();
            $this->additionInfoUpdated=true;
            return true;
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
    protected function jsonFormatHex($html)
    {
        while(true)
        {
            $tmp=$this->getSubStr($html,",0x",",",0);
            if($tmp===false)
                break;
            $tmp='0x' . $tmp ;
            $html=str_replace($tmp . ',','"' . $tmp . '",',$html);
        }
        while(true)
        {
            $tmp=$this->getSubStr($html,"[0x",",",0);
            if($tmp===false)
                break;
            $tmp='0x' . $tmp ;
            $html=str_replace($tmp . ',','"' . $tmp . '",',$html);
        }
        while(true)
        {
            $tmp=$this->getSubStr($html,":0x",",",0);
            if($tmp===false)
                break;
            $tmp='0x' . $tmp ;
            $html=str_replace($tmp . ',','"' . $tmp . '",',$html);
        }
        return $html;
    }
}
