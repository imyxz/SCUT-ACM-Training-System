<?php
include_once(_Class . "SpiderBasic.php");
include_once(_Class . "acStatus.php");
require (_Root."vendor/autoload.php");
use PHPHtmlParser\Dom;
class SpiderCodeforces extends SpiderBasic
{
    private $remote_runid;
    private $query_job_info=array();
    function test()
    {
        $this->additionInfo['cookie']='nocturne.language=en; ';
        $this->problem_info['problem_identity']='757/B';
        $this->job_info['compiler_id']='6';
        $this->job_info['source_code']="<?php echo 'helloworld34324263';?>";
        $this->spider_info['oj_username']='scutvj_1';
        $this->query_jobs=array(28823019,28818197,28823688);
    }
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
        $dom=new Dom();
        $dom->load($html);
        file_put_contents("test.txt",$html);
        $csrf_token=$dom->find(".submit-form > input",0)->getTag()->getAttribute("value")['value'];
        $submit_addon=$dom->find(".submit-form",0)->getTag()->getAttribute("action")['value'];
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
            "contestId"=>$contest_id
        );
        $return=$curl->post("http://codeforces.com/contest/$contest_id/submit" . $submit_addon,$request,10);
        /* 老版
        if($curl->getResponseCode()=="200" && empty($return))//正常情况无返回（跳转）
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
        if($curl->getResponseCode()=="200" && empty($return))//正常情况无返回（跳转）
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
        foreach($json['result'] as $one)
        {
            if(in_array($one['id'],$this->query_jobs))
            {
                $row=array();
                $row['remote_run_id']=$one['id'];
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
                $row['result_info']=$one;
                $this->query_job_info[]=$row;
            }
        }
        return true;
    }
    function getSubmitResult()
    {
        return
            array("remote_run_id"=>$this->remote_runid);
    }
    function getQueryResult()
    {
        return $this->query_job_info;
    }
}