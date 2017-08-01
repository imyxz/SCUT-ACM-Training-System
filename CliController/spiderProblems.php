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
    function codeForces()
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
                if(strpos($html,"Statement is not available.")!==false)
                    continue;
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
                $time_limit=doubleval($problem_div->find(".problem-time-limit",0)->text())*1000;
                $problem_div->find(".problem-memory-limit > .problem-small-title",0)->delete();
                $memory_limit=doubleval($problem_div->find(".problem-memory-limit",0)->text())*1024*1024;
                $problem_title=$problem_div->find(".problem-title",0)->text();
                $problem_url="http://codeforces.com/contest/$contest_id/problem/$problem_id";
                $this->model("vj_problem_model")->insertProblem(1,"$contest_id/$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
                //echo "add $contest_id/$problem_id\n";
            }
        }
    }
    function gym()
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
            while(!($html=$curl->get("http://codeforces.com/gym/$contest_id/attachments",10)) && $curl->getResponseCode()!=302)
            {
                echo "Retry attachments $contest_id\n";
                sleep(1);
            }
            if($curl->getResponseCode()=='302')
                continue;
            $dom=new Dom();
            $dom->load($html);
            $tmp1=$dom->find(".datatable a",0);
            if($tmp1)
            {
                $file_url="http://codeforces.com". $dom->find(".datatable a",0)->getTag()->getAttribute("href")['value'];

                while(!($file=$curl->get($file_url,300)) && $curl->getResponseCode()!=302)
                {
                    echo "Retry pdf $contest_id\n";
                    sleep(1);
                }
                if($curl->getResponseCode()=='302')
                    continue;
                $tmp1=strrpos($file_url,".");
                $back=substr($file_url,$tmp1+1,strlen($file_url)-$tmp1-1);
                if($back=='pdf')
                {
                    $pdf_filename="gym_$contest_id" . ".pdf";
                    $resource=fopen(_Root . "files/$pdf_filename","wb");
                    fwrite($resource,$file);
                    fclose($resource);
                    $problem_desc='<div id="pdf-div" data-pdf-url="files/' . $pdf_filename . '"></div>';

                }
                else if(in_array($back,array("doc","docx","xls")))
                {
                    $pdf_filename="gym_$contest_id" . ".$back";
                    $resource=fopen(_Root . "files/$pdf_filename","wb");
                    fwrite($resource,$file);
                    fclose($resource);
                    $problem_desc='<div>'. "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=". urlencode($file_url) ."' width='100%' height='800px' frameborder='0'>This is an embedded <a target='_blank' href='http://office.com'>Microsoft Office</a> document, powered by <a target='_blank' href='http://office.com/webapps'>Office Online</a>.</iframe>" .'<a href="'. _Http .'files/'. $pdf_filename .'" target="_blank">Click here to download statement</a></div>';
                }
                else if(in_array($back,array("zip","rar","txt")))
                {
                    $pdf_filename="gym_$contest_id" . ".$back";
                    $resource=fopen(_Root . "files/$pdf_filename","wb");
                    fwrite($resource,$file);
                    fclose($resource);
                    $problem_desc='<div><a href="'. _Http .'files/'. $pdf_filename .'" target="_blank">Click here to download statement</a></div>';
                }
                else
                {
                    $problem_desc='<div><a href="'. $file_url .'" target="_blank">Click here to download statement</a></div>';

                }

                while(!($html=$curl->get("http://codeforces.com/gym/$contest_id",10)) && $curl->getResponseCode()!=302)
                {
                    echo "Retry $contest_id\n";
                    sleep(1);
                }
                if($curl->getResponseCode()=='302')
                    continue;
                $dom->load($html);
                $list=$dom->find(".problems td");
                for($i=0;$i<$list->count();$i++)//库有bug，这样反而能取
                {

                    $tmp= $list[$i]->find("div a",0);
                    if(!$tmp)
                        continue;
                    $problem_id=substr($tmp->getTag()->getAttribute("href")['value'],-1,1);
                    $problem_title=$problem_id . ". ". $tmp->text();
                    $list[$i]->find("div > div > div",0)->delete();
                    $tmp=trim($list[$i]->find("div > div",1)->text(true));
                    list($time_limit,$memory_limit)=sscanf($tmp,"%f s, %f MB");
                    $time_limit*=1000;
                    $memory_limit*=1024*1024;
                    $compiler='{"1":"GNU G++ 5.1.0","2":"Microsoft Visual C++ 2010","3":"Delphi 7","4":"Free Pascal 2.6.4","6":"PHP 7.0.12","7":"Python 2.7.12","8":"Ruby 2.0.0p645","9":"C# Mono 3.12.1.0","10":"GNU GCC 5.1.0","12":"Haskell GHC 7.8.3","13":"Perl 5.20.1","19":"OCaml 4.02.1","20":"Scala 2.11.8","28":"D DMD32 v2.071.2","29":"MS C# .NET 4.0.30319","31":"Python 3.5.2","32":"Go 1.7.3","34":"JavaScript V8 4.8.0","36":"Java 1.8.0_112","40":"PyPy 2.7.10 (2.6.1)","41":"PyPy 3.2.5 (2.4.0)","42":"GNU G++11 5.1.0","43":"GNU GCC C11 5.1.0","48":"Kotlin 1.0.5-2","49":"Rust 1.12.1","50":"GNU G++14 6.2.0"}';
                    $problem_url="http://codeforces.com/gym/$contest_id/problem/$problem_id";
                    $this->model("vj_problem_model")->insertProblem(2,"$contest_id/$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);

                }
            }
            else//说明是带题目类型的
            {
                $problem_list=array();
                while(!($html=$curl->get("http://codeforces.com/gym/$contest_id",10)) && $curl->getResponseCode()!=302)
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
                    while(!($html=$curl->get("http://codeforces.com/gym/$contest_id/problem/$problem_id",10))  && ($curl->getResponseCode()!=302))
                    {
                        echo "Retry $contest_id/$problem_id\n";
                        sleep(1);
                    }
                    if($curl->getResponseCode()=='302')
                        continue;
                    if(strpos($html,"html")===false)
                        continue;
                    $dom=new Dom();
                    $dom->load($html);
                    $problem_div=$dom->find(".problem-statement",0);
                    if(strpos($html,"Statement is not available.")!==false)
                        continue;
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
                    if(!$problem_div)
                        continue;
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
                    $time_limit=doubleval($problem_div->find(".problem-time-limit",0)->text())*1000;
                    $problem_div->find(".problem-memory-limit > .problem-small-title",0)->delete();
                    $memory_limit=doubleval($problem_div->find(".problem-memory-limit",0)->text())*1024*1024;
                    $problem_title=$problem_div->find(".problem-title",0)->text();
                    $problem_url="http://codeforces.com/gym/$contest_id/problem/$problem_id";
                    $this->model("vj_problem_model")->insertProblem(2,"$contest_id/$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
                    //echo "add $contest_id/$problem_id\n";
                }
            }

        }
    }
    function hdu()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://acm.hdu.edu.cn/submit.php");
        $curl->setHeader("Origin: http://acm.hdu.edu.cn/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("http://acm.hdu.edu.cn/showproblem.php?pid=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }
            if(strpos($html,"No such problem - ")!==false)
                continue;
            /** @var problemInfo $problem_info */
            $html=iconv("gb2312","UTF-8//IGNORE",$html);
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();
            $problem_info->examples[0]=new problemExample();
            $divs=explode("<div class=panel_title align=left>",$html);
            for($i=1;$i<count($divs)-1;$i++)
            {
                $innerHtml=$this->getSubStr($divs[$i],'</div>','</div><br>',0) . '</div>';
                $innerHtml=str_replace("panel_content",'"panel_content"',$innerHtml);
                $innerHtml=str_replace("panel_title",'"panel_title"',$innerHtml);
                $innerHtml=str_replace("panel_bottom",'"panel_bottom"',$innerHtml);
                $innerHtml=str_replace("\n","<br />",$innerHtml);

                $innerHtml=$this->filter($innerHtml,"http://acm.hdu.edu.cn/",array());
                $innerHtml=str_replace(array("<div>","</div>","<pre>","</pre>"),array("","","",""),$innerHtml);
                switch(trim(substr($divs[$i],0,strpos($divs[$i],"</div>"))))
                {
                    case "Problem Description":
                        $innerHtml=str_replace("<br /><br />","</p><p>",$innerHtml);
                        $innerHtml= "<p>" . $innerHtml . "</p>";
                        $problem_info->description=$innerHtml;
                        break;
                    case "Input":
                        $innerHtml= "<p>" . $innerHtml . "</p>";
                        $problem_info->input=$innerHtml;
                        break;
                    case "Output":
                        $innerHtml= "<p>" . $innerHtml . "</p>";
                        $problem_info->output=$innerHtml;
                        break;
                    case "Sample Input":
                        $problem_info->examples[0]->example_input=$innerHtml;
                        break;
                    case "Sample Output":
                        $innerHtml=str_replace("\n","<br />",$innerHtml);

                        $problem_info->examples[0]->example_output=$innerHtml;
                        break;
                    case "Author":
                        break;
                    default:
                        echo "$problem_id unrecognized ". substr($divs[$i],0,strpos($divs[$i],"</div>")) . "\n";
                }
            }

            $problem_desc=$problem_info->generate();
            $compiler='["G++","GCC","C++","C","Pascal","Java","C#"]';

            $tmp=$this->getSubStr($html,"Time Limit:","MS",0);
            list($java,$time_limit)=sscanf($tmp,"%d/%d");

            $tmp=$this->getSubStr($html,"Memory Limit:","K",0);
            list($java,$memory_limit)=sscanf($tmp,"%d/%d");

            $memory_limit=$memory_limit*1024;

            $problem_title=$this->getSubStr($html,"<h1 style='color:#1A5CC8'>","</h1>",0);
            $problem_url="http://acm.hdu.edu.cn/showproblem.php?pid=$problem_id";
            $this->model("vj_problem_model")->insertProblem(3,"$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    protected function filter($html,$oj_url,$replace_class)
    {
        $dom=new Dom();
        $dom->load($html);

        $dom->find("script,style,iframe")->each(function($value,$key)
        {
            $value->delete();
        });//过滤注入标签
        $dom->find("*,* *, * * *,* * * *,* * * * *")->each(function($value,$key) use($replace_class,$oj_url)
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
                    $origin_src=$oj_url . $origin_src;
                $origin_src=str_replace('../','',$origin_src);
                $value->getTag()->setAttribute('src',$origin_src);
            }


        });
        return strval($dom);
    }

    protected function getSubStr($str,$needle1,$needle2,$start_pos)
    {
        $pos1=strpos($str,$needle1,$start_pos);
        if($pos1===false) return false;
        $pos2=strpos($str,$needle2,$pos1+1);
        if($pos2===false)   return false;
        return substr($str,$pos1+strlen($needle1),$pos2-$pos1-strlen($needle1));
    }
}