<?php
include_once(_Class . "SpiderBasic.php");

class SpiderCodeforces extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    private $submit_result;
    function submitJob()
    {
        $ac_status=new acStatus();
        $this->job_info['source_code']=$this->job_info['source_code'] . "\n/**\n  *https://acm.scut.space/\n  *Job ID:". $this->job_info['job_id'] . "\n  *Time: ".time() . "\n**/";
        list($contest_id,$problem_id)=explode("/",$this->problem_info['problem_identity']);
        $username=$this->spider_info['oj_username'];
        $password=$this->spider_info['oj_password'];
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://codeforces.com/contest/$contest_id/submit");
        $curl->setHeader("Origin: http://codeforces.com");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get("http://codeforces.com/contest/$contest_id/submit",20);
        $cookie=$curl->cookieStr2Arr($this->additionInfo['cookie']);
        $dom=new PHPHtmlParser\Dom();
        $dom->load($html);
        $tmp=$dom->find(".submit-form > input",0);
        if(!$tmp)
            return false;

        $csrf_token=$tmp->getTag()->getAttribute("value")['value'];
        $tmp=$dom->find(".submit-form",0);
        if(!$tmp)
            return false;
        $submit_addon=$tmp->getTag()->getAttribute("action")['value'];
        $ftaa=$this->getSubStr($html,'window._ftaa = "','";',0);
        $bfaa =$this->getSubStr($html,'window._bfaa = "','";',0);
        $participantid=$this->getSubStr($html,'participantId: ',', ',0);

        $cookie2=$curl->cookieStr2Arr($curl->getResponseCookie());
        $cookie=array_merge($cookie,$cookie2);
        $curl->setCookieRaw($curl->cookieArr2Str($cookie));
        /*$request=array("problemIndex"=>$problem_id,
            "participantId"=>$participantid,
            "contestId"=>$contest_id,
            "action"=>"checkProblemLock",
            "csrf_token"=>$csrf_token
        );
        file_put_contents("test1.txt",$curl->post("http://codeforces.com/data/problemLock",$request,10));
*/
        $request=array("csrf_token"=>$csrf_token,
            "ftaa"=>$ftaa,
            "bfaa"=>$bfaa,
            "submittedProblemIndex"=>$problem_id,
            "programTypeId"=>$this->job_info['compiler_id'],
            "source"=>$this->job_info['source_code'],
            "tabSize"=>4,
            "action"=>"submitSolutionFormSubmitted",
            "sourceFile"=>'',
            "_tta"=>183,
            "contestId"=>$contest_id,
            "doNotShowWarningAgain"=>'on'
        );
        $return=$curl->post("http://codeforces.com/contest/$contest_id/submit" . $submit_addon,$request,10);
        /* �ϰ�
        if($curl->getResponseCode()=="200" && empty($return))//��������޷��أ���ת��
        {
            $html=$curl->get("http://codeforces.com/contest/$contest_id/my",10);
            $dom->load($html);
            $tr=$dom->find(".status-frame-datatable tr",1);
            $remote_runid=$tr->getTag()->getAttribute("data-submission-id")['value'];
            $status=$tr->find("td",5)->find("span",0)->getTag()->getAttribute("submissionVerdict")['value'];
            $wrong_info=$tr->find("td",5)->find("span",0)->text();
            $time=$tr->find("td",6)->text();
            $ram=$tr->find("td",7)->text();

        }
        */
        if($curl->getResponseCode()=="302" && empty($return))//��������޷��أ���ת��
        {
            $html=$curl->get("http://codeforces.com/api/user.status?handle=$username&from=1&count=1",10);
            $json=json_decode($html,true);
            $json=$json['result'][0];
            $this->remote_runid=$json['id'];
            /*$this->status=$ac_status->getInt($json['verdict']);
            $this->wrong_info=$json['verdict'];
            $this->time=$json['timeConsumedMillis']/1000;
            $this->ram=$json['memoryConsumedBytes']/1024/1024;
            unset($json['problem']);
            unset($json['author']);
            $json['error_info']='';

            switch($this->status)
            {
                case $ac_status->COMPILATION_ERROR:
                    $json['error_info']=$curl->post("http://codeforces.com/data/judgeProtocol",array("csrf_token"=>$csrf_token,"submissionId"=>$this->remote_runid));
                    break;
                case $ac_status->WRONG_ANSWER:
                    $this->wrong_info="Wrong Answer On Test " . ($json['passedTestCount']+1);
                    break;
            }
            $this->result_info=$json;
            */
        }
        else
            return false;
        return true;
    }
    function queryJob()
    {
        $curl=new curlRequest();
        $username=$this->spider_info['oj_username'];
        $html=$curl->get("http://codeforces.com/api/user.status?handle=$username&from=1&count=20",10);
        $json=json_decode($html,true);
        if(!$json)  return false;
        $ac_status=new acStatus();
        $query_job_info=array();
        foreach($json['result'] as $one)
        {
            if(isset($this->query_jobs[$one['id']]))
            {
                $row=array();
                $row['remote_run_id']=$one['id'];
                if(empty(trim(@$one['verdict'])))
                    $one['verdict']=$ac_status->TESTING;
                $row['ac_status']=$ac_status->getInt($one['verdict']);
                $row['wrong_info']=$one['verdict'];
                $row['time_usage']=$one['timeConsumedMillis'];
                $row['ram_usage']=$one['memoryConsumedBytes'];
                unset($one['problem']);
                unset($one['author']);
                $row['error_info']='';

                switch($row['ac_status'])
                {
                    case $ac_status->COMPILATION_ERROR:
                        $row['error_info']='';
                        break;
                    case $ac_status->WRONG_ANSWER:
                        $row['error_info']="Wrong Answer On Test " . ($json['passedTestCount']+1);
                        break;
                }
                $row['result_info']=json_encode($one);
                $row['job_id']=$this->query_jobs[$one['id']];


                $query_result=new jobResult();
                $query_result->remote_run_id=$row['remote_run_id'];
                $query_result->ac_status=$row['ac_status'];
                $query_result->job_id=$row['job_id'];
                $query_result->ram_usage=$row['ram_usage'];
                $query_result->time_usage=$row['time_usage'];
                $query_result->result_info=$row['result_info'];
                $query_result->wrong_info=$row['wrong_info'];


                $query_job_info[$row['remote_run_id']]=$query_result;
            }
        }
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://codeforces.com/submissions/$username");
        $curl->setHeader("Origin: http://codeforces.com");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get("http://codeforces.com/submissions/$username",10);
        if(!$html) return false;
        $dom=new PHPHtmlParser\Dom();
        $dom->load($html);
        $dom->find(".status-frame-datatable tr")->each(function($tr,$key) use(&$query_job_info)
            {
                $remote_runid=$tr->getTag()->getAttribute("data-submission-id")['value'];
                if(!$tr->find("td",5))   return;
                if(!$tr->find("td",5)->find("span",0))  return;
                //$status=$tr->find("td",5)->find("span",0)->getTag()->getAttribute("submissionVerdict")['value'];
                $wrong_info=htmlspecialchars_decode($tr->find("td",5)->find("span",0)->text(true));
                //$time=$tr->find("td",6)->text();
                //$ram=$tr->find("td",7)->text();
                if(isset($query_job_info[$remote_runid]))
                    $query_job_info[$remote_runid]->wrong_info=$wrong_info;
            });
        $this->query_job_info=$query_job_info;
        return true;
    }
    function getSubmitResult()
    {
        $tmp=new submitResult();
        $tmp->remote_run_id=$this->remote_runid;
        return $tmp;
    }
    function getQueryResult()
    {
        return $this->query_job_info;
    }
    function checkLogin()
    {
        $curl=new curlRequest();
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("http://codeforces.com/",20);
        if(strpos($html,'<a href="/register">')!==false || $curl->getResponseCode()=='302')
            return false;
        else
            return true;
    }
    function login()
    {
        $curl=new curlRequest();
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://codeforces.com/enter");
        $curl->setHeader("Origin: http://codeforces.com");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        //$curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("http://codeforces.com/enter?back=%2F");
        $cookie=$curl->getResponseCookie();

        $curl->setCookieRaw($cookie);
        $cookie=$curl->cookieStr2Arr($cookie);
        $ftaa=$this->getRandomStr(18);
        $bfaa=$this->getRandomStr(32);
        $cookie['evercookie_cache']=$ftaa;
        $cookie['evercookie_etag']=$ftaa;
        $cookie['evercookie_png']=$ftaa;
        $curl->setCookie($cookie);
        $csrf_token=$this->getSubStr($html,"data-csrf='","'>",0);
        $curl->setHeader("X-Csrf-Token: $csrf_token");
        $curl->setHeader("X-Requested-With: XMLHttpRequest");

        $token1=$this->getSubStr($html,"phpuri: '/","',",0);
        $token2=$this->getSubStr($html,'ec.get("','",',0);
        $curl->post("http://codeforces.com/$token1/ees?name=$token2&cookie=evercookie_etag",array("bfaa"=>$bfaa,
            "ftaa"=>$ftaa,
            "csrf_token"=>$csrf_token),10);
        /*$curl->get("http://codeforces.com/$token1/ees?name=$token2&cookie=evercookie_etag",10);
        $curl->get("http://codeforces.com/$token1/ecs?name=$token2&cookie=evercookie_cache",10);
        $curl->get("http://codeforces.com/$token1/eps?name=$token2&cookie=evercookie_png",10);
        */
        $request=array(
            "csrf_token"=>$csrf_token,
            "action"=>"enter",
            "handle"=>$this->spider_info['oj_username'],
            "password"=>$this->spider_info['oj_password'],
            "remember"=>"on",
            "_tta"=>905,
            "ftaa"=>$ftaa,
            "bfaa"=>$bfaa);
        $return=$curl->post("http://codeforces.com/enter?back=%2F",$request,10);


        if(empty($return) && $curl->getResponseCode()==302)
        {
            $this->additionInfoUpdated=true;
            $this->additionInfo['cookie']=$curl->cookieArr2Str($cookie);
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
}