<?php
include_once(_Class . "SpiderBasic.php");

class SpiderUva extends SpiderBasic
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

        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=25");
        $curl->setHeader("Origin: https://uva.onlinejudge.org/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $request=array(
            "problemid"=> '',
            "category"=> '',
            "localid"=>$problem_id,
            "language"=>$this->job_info['compiler_id'],
            "code"=>$this->job_info['source_code']
        );
        $curl->setFollowRedirect(true);
        $return=$curl->postFromData("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=25&page=save_submission",$request,10);
        $redirect=$curl->getCurUrl();
        if(strpos($return,'Submission received with ID ')!==false)
        {
            $id=$this->getSubStr($redirect . '#','with+ID+','#',0);
            $id=intval($id);
            if($id<=0)
                return false;
            $this->remote_runid=$id;
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
        $curl->setHeader("Referer: https://uva.onlinejudge.org/");
        $curl->setHeader("Origin: https://uva.onlinejudge.org/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $html=$curl->get("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=9",10);
        $table= $this->getSubStr($html,'<table cellpadding="4" cellspacing="0" border="0" width="100%">',"</table>",0);
        if(empty($table))
        {
            if(!$this->checkLogin())    $this->is_login=false;
            return false;
        }
        $dom=new DOMDocument();
        $table=str_replace('&','xxxx',$table);
        $dom->loadHTML($table);
        $trs=$dom->getElementsByTagName("tr");
        for($i=1;$i<$trs->length;$i++)
        {
            $tds= $trs->item($i)->childNodes;
            $result=new jobResult();
            $acStatus=new acStatus();
            $result->remote_run_id=$tds->item(0)->textContent;
            $result->wrong_info=trim($tds->item(6)->textContent);
            $result->time_usage=doubleval(trim($tds->item(10)->textContent))*1000;
            $result->ram_usage=0;
            switch(substr($result->wrong_info,0,9))
            {
                case 'Accepted':
                    $result->ac_status=$acStatus->OK;
                    break;
                case 'Wrong ans':
                    $result->ac_status=$acStatus->WRONG_ANSWER;
                    break;
                case 'Presentat':
                    $result->ac_status=$acStatus->PRESENTATION_ERROR;
                    break;
                case 'Compilati':
                    $result->ac_status=$acStatus->COMPILATION_ERROR;
                    break;
                case 'Runtime e':
                    $result->ac_status=$acStatus->RUNTIME_ERROR;
                    break;
                case 'Time limi':
                    $result->ac_status=$acStatus->TIME_LIMIT_EXCEEDED;
                    break;
                case 'Memory Li':
                    $result->ac_status=$acStatus->MEMORY_LIMIT_EXCEEDED;
                    break;
                case 'In judge ':
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
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $curl->setHeader("Accept-Encoding: gzip, deflate, br");
        $curl->setHeader("Accept-Language: zh-CN,zh;q=0.9");
        $curl->setCookieRaw($this->additionInfo['cookie']);
        $html=$curl->get("https://uva.onlinejudge.org/");
        if(strpos($html,'cbsecuritym3')===false)
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
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $curl->setHeader("Accept-Encoding: gzip, deflate, br");
        $curl->setHeader("Accept-Language: zh-CN,zh;q=0.9");
        $cookie="dd0fd2e506f96b096ef58f7edf831085=-; dd0fd2e506f96b096ef58f7edf831085=294a44d97a6bf686922dba089d3622b3; __utmc=152284925; __utmz=152284925.1522401181.1.1.utmcsr; __utma=152284925.1808484381.1522401180.1522401180.1522401180.1; __utmt=1; __utmb=152284925.7.10.1522401181; __utma=152284925.1808484381.1522401180.1522401180.1522401180.1; __utmc=152284925; __utmb=152284925.8.10.1522401181;";
        $curl->setCookieRaw($cookie);
        $html=$curl->get("https://uva.onlinejudge.org/index.php?option=com_frontpage&Itemid=1",10);
        //$cookie=$curl->getResponseCookie();
        //$curl->setCookieRaw($cookie . "; 1=1;");
        $return=$this->getSubStr($html,'name="return" value="','"',0);
        $cbsecuritym3=$this->getSubStr($html,'name="cbsecuritym3" value="','"',0);
        $tmp_pos=strpos($html,"cbsecuritym3");
        $csrf=$this->getSubStr($html,'name="','"',$tmp_pos);
        $request=array(
            "username"=>$username,
            "passwd"=>$password,
            "op2"=>'login',
            "lang"=>'english',
            'force_session'=>1,
            'return'=>$return,
            'message'=>0,
            'loginfrom'=>'loginmodule',
            'cbsecuritym3'=>$cbsecuritym3,
            $csrf=>1,
            'remember'=>'yes',
            'Submit'=>'Login');
        $ret=$curl->post("https://uva.onlinejudge.org/index.php?option=com_comprofiler&task=login",$request,10);
        if( $curl->getResponseCode()==301)//200¾ÍÊÇµÇÂ¼Ê§°Ü
        {
            $new=$curl->getResponseCookie();
            $cookie=$curl->mergeCookieRaw($cookie,$new);
            var_dump($cookie);
            $this->additionInfo['cookie']=$cookie;
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