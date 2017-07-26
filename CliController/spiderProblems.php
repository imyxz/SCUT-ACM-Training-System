<?php
/**
 * User: imyxz
 * Date: 2017-07-26
 * Time: 10:46
 * Github: https://github.com/imyxz/
 */
require (_Root."vendor/autoload.php");
use PHPHtmlParser\Dom;
class spiderProblems extends SlimvcControllerCli
{
    function codeForeces()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://codeforces.com/");
        $curl->setHeader("Origin: http://codeforces.com");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $start_contest_id=intval(self::$cliArg['start']);
        $end_contest_id=intval(self::$cliArg['end']);
        for($contest_id=$start_contest_id;$contest_id<=$end_contest_id;$contest_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("http://codeforces.com/contest/$contest_id",10)) && $curl->getResponseCode()!=302)
            {
                echo "Retry $contest_id\n";
                sleep(1);
            }
            if($curl->getResponseCode()=='302')
                continue;
            $dom=new Dom();
            $dom->load($html);
            $list=$dom->find(".id");
            for($i=0;$i<$list->count();$i++)
            {
                $problem_list[]=trim($list[$i]->find("a",0)->innerHtml());
            }
            foreach($problem_list as $problem_id)
            {
                while(!$html=$curl->get("http://codeforces.com/contest/$contest_id/problem/$problem_id",10))
                {
                    echo "Retry $contest_id/$problem_id\n";
                    sleep(1);
                }
                $dom=new Dom();
                $dom->load($html);
                $problem_div=$dom->find(".problem-statement",0);

                $replace_class=array(
                    "header"=>"problem-header",
                    "title"=>"problem-title",
                    "time-limit"=>"problem-time-limit",
                    "memory-limit"=>"problem-memory-limit",
                    "input-file"=>"problem-input-file",
                    "output-file"=>"problem-output-file",
                    "property-title"=>"problem-small-title",
                    "tex-span"=>"problem-tex-span",
                    "input-specification"=>"problem-input-div",
                    "input"=>"problem-input",
                    "output-specification"=>"problem-output-div",
                    "output"=>"problem-output",
                    "sample-test"=>"problem-example"

                );

                $problem_div->find("script,style")->each(function($value,$key)
                {
                    $value->delete();
                });//过滤注入标签
                $problem_div->find("*,* *, * * *,* * * *")->each(function($value,$key) use($replace_class)
                {
                    $origin_class=$value->getTag()->getAttribute('class')['value'];
                    $origin_src=$value->getTag()->getAttribute('src')['value'];
                    $value->getTag()->removeAllAttributes();
                    $origin_class=explode(" ",$origin_class);
                    $filter_class=array();
                    foreach($origin_class as $one)
                    {

                        if(isset($replace_class[$one]))
                            $filter_class[]=$replace_class[$one];
                    }
                    if(!empty($filter_class))
                        $value->getTag()->setAttribute('class',implode(" ",$filter_class));
                    if(!empty($origin_src))
                    {
                        if(substr($origin_src,0,4)!='http')
                            $origin_src='http://codeforces.com' . $origin_src;
                        $value->getTag()->setAttribute('src',$origin_src);
                    }


                });//属性过滤
                $problem_div->getTag()->removeAllAttributes();
                $problem_desc=strval($problem_div);
                $compiler='{"1":"GNU G++ 5.1.0","2":"Microsoft Visual C++ 2010","3":"Delphi 7","4":"Free Pascal 2.6.4","6":"PHP 7.0.12","7":"Python 2.7.12","8":"Ruby 2.0.0p645","9":"C# Mono 3.12.1.0","10":"GNU GCC 5.1.0","12":"Haskell GHC 7.8.3","13":"Perl 5.20.1","19":"OCaml 4.02.1","20":"Scala 2.11.8","28":"D DMD32 v2.071.2","29":"MS C# .NET 4.0.30319","31":"Python 3.5.2","32":"Go 1.7.3","34":"JavaScript V8 4.8.0","36":"Java 1.8.0_112","40":"PyPy 2.7.10 (2.6.1)","41":"PyPy 3.2.5 (2.4.0)","42":"GNU G++11 5.1.0","43":"GNU GCC C11 5.1.0","48":"Kotlin 1.0.5-2","49":"Rust 1.12.1","50":"GNU G++14 6.2.0"}';
                $problem_div->find(".problem-time-limit > .problem-small-title",0)->delete();
                $time_limit=intval($problem_div->find(".problem-time-limit",0)->text())*1000;
                $problem_div->find(".problem-memory-limit > .problem-small-title",0)->delete();
                $memory_limit=intval($problem_div->find(".problem-memory-limit",0)->text())*1024*1024;
                $problem_title=$problem_div->find(".problem-title",0)->text();
                $problem_url="http://codeforces.com/contest/$contest_id/problem/$problem_id";
                $this->model("vj_problem_model")->insertProblem(1,"$contest_id/$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
                //echo "add $contest_id/$problem_id\n";
            }
        }
    }
}