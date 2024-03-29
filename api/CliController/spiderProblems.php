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

    function scutse()
    {
        /** @var curlRequest $curl */
        $url="http://127.0.0.1:8000/JudgeOnline";
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/submit.php");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("$url/problem.php?id=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }


            if(strpos($html,"This problem is in Contest(s) below:")!==false)
            {
                $tmp=$url . "/" . $this->getSubStr($html,"<br><a href=",">",0);
                while(!($html=$curl->get($tmp,10)))
                {
                    echo "Retry $problem_id\n";
                    sleep(1);
                }
            }

            if(strpos($html,"Problem is not Available")!==false)
                continue;
            /** @var problemInfo $problem_info */
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();
            $problem_info->examples[0]=new problemExample();
            $divs=explode("<h2>",$html);
            for($i=1;$i<count($divs)-1;$i++)
            {
                $divs[$i]=$divs[$i] . "<h2>";
                $divs[$i]=str_replace("pre class=",'div class=',$divs[$i]);
                $divs[$i]=str_replace("</pre>",'</div>',$divs[$i]);

                $innerHtml='<div>' . $this->getSubStr($divs[$i],'<div class=content>','</div><h2>',0) . '</div>';
                $innerHtml=str_replace("content",'"content"',$innerHtml);
                $innerHtml=str_replace("example",'"example"',$innerHtml);
                $innerHtml=str_replace("sampledata",'"sampledata"',$innerHtml);
                $innerHtml=str_replace("center",'"center"',$innerHtml);

                $innerHtml=str_replace("\n","<br />",$innerHtml);

                $innerHtml=$this->filter($innerHtml,"http://116.56.140.75:8000",array());
                $innerHtml=str_replace(array("<div>","</div>","<pre>","</pre>"),array("","","",""),$innerHtml);
                switch(trim(substr($divs[$i],0,strpos($divs[$i],"</h2>"))))
                {
                    case "Description":
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
                        $innerHtml=str_replace(array("<span>","</span>"),array("",""),$innerHtml);
                        $problem_info->examples[0]->example_input=$innerHtml;
                        break;
                    case "Sample Output":
                        $innerHtml=str_replace(array("<span>","</span>"),array("",""),$innerHtml);
                        $problem_info->examples[0]->example_output=$innerHtml;
                        break;
                    case "Author":
                        break;
                    default:
                        //echo "$problem_id unrecognized ". substr($divs[$i],0,strpos($divs[$i],"</div>")) . "\n";
                }
            }

            $problem_desc=$problem_info->generate();
            $compiler='["C","C++"]';

            $time_limit=doubleval($this->getSubStr($html,"Time Limit: </span>","Sec",0));
            $time_limit=$time_limit*1000;

            $memory_limit=$this->getSubStr($html,"Memory Limit: </span>","MB",0);


            $memory_limit=$memory_limit*1024*1024;

            $problem_title=$this->getSubStr($html,"<center><h2>","</h2>",0).'</h2>';
            if(strpos($problem_title,":")!==false)
                $problem_title=trim($this->getSubStr($problem_title,":","</h2>",0));

            $problem_url="http://116.56.140.75:8000/JudgeOnline/problem.php?id=$problem_id";
            $this->model("vj_problem_model")->insertProblem(5,$problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function _51nod()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://www.51nod.com/onlineJudge/problemList.html");
        $curl->setHeader("Accept: */*");
        $curl->setHeader("Accept-Language: zh-CN,zh;q=0.8");
        $page_start=intval(self::$cliArg['start']);
        $page_end=intval(self::$cliArg['end']);
        for($page_id=$page_start;$page_id<=$page_end;$page_id++)
        {
            echo "start page $page_id \n";
            $problem_list=array();
            while(!($html=$curl->get("http://www.51nod.com/ajax?n=/onlineJudge/problemList.html&v=&c=fastCSharp.IndexPool.Get%284%2C1%29.CallBack&j=%7B%22groupId%22%3A%22-1%22%2C%22isAsc%22%3A1%2C%22page%22%3A". $page_id ."%7D&t=1501771361223",10)))
            {
                echo "Retry $page_id\n";
                sleep(1);
            }
            $html=$this->getSubStr($html,"Problems:",".FormatView()",0,false);

            $html=$this->jsonFormatHex($html);
            //$html=iconv("gb2312","UTF-8//IGNORE",$html);
            $html=mb_convert_encoding($html,"UTF-8","gb2312");
            /** @var Services_JSON $json_service */
            $json_service = $this->newClass("JSON",'Services_JSON');
            $json= $json_service->decode($html);

            if(!$json)
            {
                echo 'decode error';
                exit();
            }
            unset($json[0]);
            foreach($json as &$one)
            {
                if(count($one)!=6)
                    continue;
                $two=$one[5];
                /** @var problemInfo $problem_info */
                $problem_info=$this->newClass("problemInfo");
                $problem_info->examples=array();
                $problem_info->examples[0]=new problemExample();
                $problem_id=$two[3];
                $time_limit=$two[8];
                $memory_limit=$two[5];
                $all_tags=array();
                foreach($two[7][1][0] as $three)
                {
                    $all_tags[$three[1]]=0;
                }
                if(strpos($memory_limit,"0x")!==false)
                {
                    $memory_limit=str_replace("0x","",$memory_limit);
                    $memory_limit=hexdec($memory_limit);
                }
                $problem_title=$two[9];
                $problem_url="http://www.51nod.com/onlineJudge/questionCode.html#!problemId=" . $problem_id;
                while(!($html=$curl->get("http://www.51nod.com/ajax?n=/onlineJudge/questionCode.html&c=fastCSharp.Pub.AjaxCallBack&j=%7B%22problemId%22%3A%22". $problem_id ."%22%7D",10)))
                {
                    echo "Retry $problem_id\n";
                    sleep(1);
                }

                $json1=$this->getSubStr($html,"diantou.problem.Get(",",Remote:{",0,false) . "}";
                $json1=iconv("gb2312","UTF-8//IGNORE",$json1);
                $json1=str_replace('\n',"<br />",$json1);
                $problem_info->description=$this->getSubStr($json1,'Description:"','",',0,false);

                $problem_info->input= $this->getSubStr($json1,'InputDescription:"','",',0,false) ;
                $problem_info->output= $this->getSubStr($json1,'OutputDescription:"','",',0,false) ;
                $problem_info->examples[0]->example_input=$this->getSubStr($json1,'Input:"','",',0,false);
                $problem_info->examples[0]->example_output=$this->getSubStr($json1,'Output:"','",',0,false);
                /*
                $problem_info->input=$this->filter($problem_info->input,"http://www.51nod.com/",array());
                $problem_info->output=$this->filter($problem_info->output,"http://www.51nod.com/",array());
                $problem_info->examples[0]->example_input=$this->filter($problem_info->examples[0]->example_input,"http://www.51nod.com/",array());
                $problem_info->examples[0]->example_output=$this->filter($problem_info->examples[0]->example_output,"http://www.51nod.com/",array());
                */
                $problem_info->description=$this->filter($problem_info->description,"http://www.51nod.com/",array());
                $problem_info->description=str_replace('<div>',"<p>",$problem_info->description);
                $problem_info->description=str_replace('</div>',"</p>",$problem_info->description);


                $problem_desc=$problem_info->generate();
                $compiler='{"1":"C","2":"C 11","11":"C++","12":"C++ 11","21":"C#","31":"Java","41":"Python2","42":"Python3","45":"PyPy2","46":"PyPy3","51":"Ruby","61":"PHP","71":"Haskell","81":"Scala","91":"Javascript","101":"Go","111":"Visual C++","121":"Objective-C","131":"Pascal"}';
                $id=$this->model("vj_problem_model")->insertProblem(4,$problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);


                $tag=$this->getSubStr($html,'diantou.problemGroup.Get(','),',0,false);
                $tag=iconv("gb2312","UTF-8//IGNORE",$tag);
                $tag=$this->getSubStr($tag,'Name:"','"',0,false);
                if(!empty($tag))
                    $all_tags[$tag]=0;
                foreach($all_tags as $key=>&$one)
                {
                    $tag_info=$this->model("tag_model")->getTagInfoByTagAlias($key);
                    if(!$tag_info)
                    {
                        $tag_id=$this->model("tag_model")->newTag($key,"");
                        $this->model("tag_model")->addTagAlias($tag_id,$key);
                        $tag_info=$this->model("tag_model")->getTagInfoByTagAlias($key);
                    }

                    $this->model("tag_model")->addProblemTag($id,$tag_info['tag_id']);

                }

                echo "import $problem_id\n";


            }

        }
    }
    function poj()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://poj.org/");
        $curl->setHeader("Origin: http://poj.org/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("http://poj.org/problem?id=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }
            if(strpos($html,"Can not find problem")!==false)
                continue;
            /** @var problemInfo $problem_info */
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();
            $problem_info->examples[0]=new problemExample();
            $divs=explode('<p class="pst">',$html);
            for($i=1;$i<count($divs)-1;$i++)
            {
                $innerHtml=$this->getSubStr($divs[$i] . '<end>','</p>','<end>',0) ;
                $str="";
                while(true){
                    $str=$this->getSubStr($innerHtml,"src=",">",0);
                    if(empty($str)) break;
                    $innerHtml=str_replace('src='. $str . '>','src = "'. $str . '">',$innerHtml);
                }
                $innerHtml=str_replace("\n","<br />",$innerHtml);

                $innerHtml=$this->filter($innerHtml,"http://poj.org/",array());
                $innerHtml=str_replace(array("<div>","</div>","<pre>","</pre>"),array("","","",""),$innerHtml);
                switch($this->getSubStr("<p>" . $divs[$i],"<p>","</p>",0))
                {
                    case "Description":
                        $innerHtml=str_replace("<br>","</p><p>",$innerHtml);
                        $innerHtml=str_replace("<p></p>","",$innerHtml);
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
                        //$innerHtml=str_replace("\n","<br />",$innerHtml);
                        //$innerHtml=str_replace("<br />","<br>",$innerHtml);
                        //$innerHtml=str_replace(" ","&nbsp;",$innerHtml);

                        $problem_info->examples[0]->example_input=$innerHtml;
                        break;
                    case "Sample Output":
                        //$innerHtml=str_replace("\n","<br />",$innerHtml);
                        //$innerHtml=str_replace("<br />","<br>",$innerHtml);
                        //$innerHtml=str_replace(" ","&nbsp;",$innerHtml);

                        $problem_info->examples[0]->example_output=$innerHtml;
                        break;
                    case "Hint":
                        $innerHtml=str_replace("<br>","</p><p>",$innerHtml);
                        $innerHtml=str_replace("<p></p>","",$innerHtml);
                        $innerHtml= "<p>" . $innerHtml . "</p>";
                        $problem_info->hint=$innerHtml;
                        break;
                    case "Source":
                        break;
                    default:
                        echo "$problem_id unrecognized ". substr($divs[$i],0,strpos($divs[$i],"</div>")) . "\n";
                }
            }

            $problem_desc=$problem_info->generate();
            $compiler='["G++","GCC","Java","Pascal","C++","C","Fortran"]';

            $tmp=$this->getSubStr($html,"Time Limit:</b>","MS",0);
            list($time_limit)=sscanf($tmp,"%d");

            $tmp=$this->getSubStr($html,"Memory Limit:</b>","K",0);
            list($memory_limit)=sscanf($tmp,"%d");

            $memory_limit=$memory_limit*1024;

            $problem_title=$this->getSubStr($html,'<div class="ptt" lang="en-US">',"</div>",0);
            $problem_url="http://poj.org/problem?id=$problem_id";
            $this->model("vj_problem_model")->insertProblem(6,"$problem_id",$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function bzoj()
    {
        /** @var curlRequest $curl */
        $url="http://www.lydsy.com/JudgeOnline";
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/submit.php");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("$url/problem.php?id=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }


            if(strpos($html,"This problem is in Contest(s) below:")!==false)
            {
                $tmp=$url . "/" . $this->getSubStr($html,"<br><a href=",">",0);
                while(!($html=$curl->get($tmp,10)))
                {
                    echo "Retry $problem_id\n";
                    sleep(1);
                }
            }

            if(strpos($html,"Please contact ")!==false)
                continue;
            /** @var problemInfo $problem_info */
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();
            $problem_info->examples[0]=new problemExample();
            $divs=explode("<h2>",$html);
            for($i=1;$i<count($divs)-1;$i++)
            {
                $divs[$i]=$divs[$i] . "<h2>";
                $divs[$i]=str_replace("pre class=",'div class=',$divs[$i]);
                $divs[$i]=str_replace("</pre>",'</div>',$divs[$i]);

                $innerHtml='<div>' . $this->getSubStr($divs[$i],'<div class=content>','</div><h2>',0) . '</div>';
                $innerHtml=str_replace("content",'"content"',$innerHtml);
                $innerHtml=str_replace("example",'"example"',$innerHtml);
                $innerHtml=str_replace("sampledata",'"sampledata"',$innerHtml);
                $innerHtml=str_replace("center",'"center"',$innerHtml);

                $innerHtml=str_replace("\n","<br />",$innerHtml);

                $innerHtml=$this->filter($innerHtml,"$url/",array());
                $innerHtml=str_replace(array("<div>","</div>","<pre>","</pre>"),array("","","",""),$innerHtml);
                switch(trim(substr($divs[$i],0,strpos($divs[$i],"</h2>"))))
                {
                    case "Description":
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
                        $innerHtml=str_replace(array("<span>","</span>"),array("",""),$innerHtml);
                        $problem_info->examples[0]->example_input=$innerHtml;
                        break;
                    case "Sample Output":
                        $innerHtml=str_replace(array("<span>","</span>"),array("",""),$innerHtml);
                        $problem_info->examples[0]->example_output=$innerHtml;
                        break;
                    case "HINT":
                        $innerHtml=str_replace("<br>","</p><p>",$innerHtml);
                        $innerHtml=str_replace("<p></p>","",$innerHtml);
                        $innerHtml= "<p>" . $innerHtml . "</p>";
                        $problem_info->hint=$innerHtml;
                        break;
                    case "Author":
                        break;
                    default:
                        //echo "$problem_id unrecognized ". substr($divs[$i],0,strpos($divs[$i],"</div>")) . "\n";
                }
            }

            $problem_desc=$problem_info->generate();
            $compiler='{"0":"C","1":"C++","2":"Pascal","3":"Java","6":"Python"}';

            $time_limit=doubleval($this->getSubStr($html,"Time Limit: </span>","Sec",0));
            $time_limit=$time_limit*1000;

            $memory_limit=$this->getSubStr($html,"Memory Limit: </span>","MB",0);


            $memory_limit=$memory_limit*1024*1024;

            $problem_title=$this->getSubStr($html,"<center><h2>","</h2>",0).'</h2>';
            if(strpos($problem_title,":")!==false)
                $problem_title=trim($this->getSubStr($problem_title,":","</h2>",0));

            $problem_url="$url/problem.php?id=$problem_id";
            $this->model("vj_problem_model")->insertProblem(7,$problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function tyvj()
    {
        /** @var curlRequest $curl */
        $url="http://www.tyvj.cn";
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: $url/");
        $curl->setHeader("Origin: $url/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("$url/p/$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }




            if(strpos($html,"NullReferenceException")!==false)
                continue;
            if(strpos($html,"没有找到题目")!==false)
                continue;
            /** @var problemInfo $problem_info */
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();
            $example_count=0;
            $problem_info->examples[0]=new problemExample();
            $divs=explode("<h2>",$html);
            for($i=1;$i<count($divs);$i++)
            {


                $innerHtml= $this->getSubStr($divs[$i],'</h2>','<div class="tyvj-boder-top',0) ;
                if(!$innerHtml)
                    $innerHtml= $this->getSubStr($divs[$i],'</h2>','</div>',0) . '</div>' ;
                //$innerHtml=str_replace("\n","<br />",$innerHtml);
                $innerHtml=str_replace("<br>","<br />",$innerHtml);

                $innerHtml=$this->filter($innerHtml,"$url/",array());
                $innerHtml=str_replace("<div>","<p>",$innerHtml);
                $innerHtml=str_replace("</div>","</p>",$innerHtml);
                $innerHtml=str_replace("<p><p>","<p>",$innerHtml);
                $innerHtml=str_replace("<p> <p>","<p>",$innerHtml);

                $innerHtml=str_replace("</p></p>","</p>",$innerHtml);
                $innerHtml=str_replace("</p> </p>","</p>",$innerHtml);

                switch(trim(substr($divs[$i],0,strpos($divs[$i],"</h2>"))))
                {
                    case "描述":
                        $innerHtml=str_replace("<br /><br />","</p><p>",$innerHtml);
                        $problem_info->description=$innerHtml;
                        break;
                    case "输入格式":
                        $problem_info->input=$innerHtml;
                        break;
                    case "输出格式":
                        $problem_info->output=$innerHtml;
                        break;
                    case "备注":
                        $innerHtml=str_replace("<br>","</p><p>",$innerHtml);
                        $innerHtml=str_replace("<p></p>","",$innerHtml);
                        $problem_info->hint=$innerHtml;
                        break;
                    case "背景":
                        break;
                    default:
                        if(strpos(trim(substr($divs[$i],0,strpos($divs[$i],"</h2>"))),"测试样例")!==false)
                        {
                            $pos1=strpos($innerHtml,"<h3>输入</h3>");
                            $pos2=strpos($innerHtml,"<h3>输出</h3>");
                            if($pos1!==false && $pos2!==false)
                            {
                                $example=new problemExample();
                                $example->example_input=$this->getSubStr($innerHtml,"<blockquote>","</blockquote>",$pos1);
                                $example->example_output=$this->getSubStr($innerHtml,"<blockquote>","</blockquote>",$pos2);
                                $problem_info->examples[$example_count++]=$example;
                            }
                        }
                        else
                        {
                            //echo "Unknown " .trim(substr($divs[$i],0,strpos($divs[$i],"</h2>")));
                        }
                }
            }

            $problem_desc=$problem_info->generate();
            //$("option").each(function(index,ele){tmp[ele.value]=ele.innerText})
            $compiler='{"0":"C","1":"C++","2":"C++11","3":"Java","4":"Pascal","5":"Python 2.7","6":"Python 3.3","7":"Ruby","8":"C#","9":"VB.Net","10":"F#","11":"C++14"}';

            $time_limit=doubleval($this->getSubStr($html,"时间: ","ms",0));

            $memory_limit=$this->getSubStr($html,"空间: ","K",0);


            $memory_limit=$memory_limit*1024;

            $problem_title=trim($this->getSubStr($html,"</strong>","</div>",0));
            if(strpos($problem_title,"<span")!==false)
                $problem_title=$this->getSubStr("xxx" . $problem_title,"xxx","<span",0);
            $problem_url="$url/p/$problem_id";
            $this->model("vj_problem_model")->insertProblem(8,$problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function zoj()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: http://acm.zju.edu.cn/onlinejudge/");
        $curl->setHeader("Origin: http://acm.zju.edu.cn/onlinejudge/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("http://acm.zju.edu.cn/onlinejudge/showProblem.do?problemCode=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }
            if(strpos($html,"No such problem")!==false)
                continue;
            $html=str_replace("<PRE>","<pre>",$html);
            $html=str_replace("</PRE>","</pre>",$html);
            $html=str_replace("<CENTER>","<center>",$html);
            $html=str_replace("</CENTER>","</center>",$html);
            $html=str_replace("SRC","src",$html);
            $html=str_replace("IMG","img",$html);
            $html=str_replace("showImage.do?name=","http://acm.zju.edu.cn/onlinejudge/showImage.do?name=",$html);

            /** @var problemInfo $problem_info */
            $problem_info=$this->newClass("problemInfo");
            $problem_info->examples=array();

            if(stripos($html,"<pre>")!==false)
            {
                $problem_info->examples[0]=new problemExample();
                $problem_info->examples[0]->example_input=$this->getSubStr($html,"<pre>","</pre>",0);
                $pos1=stripos($html,"</pre>",0);
                $problem_info->examples[0]->example_output=$this->getSubStr($html,"<pre>","</pre>",$pos1);
                $pos1=stripos($html,"</center>");
                $pos1=stripos($html,"</center>",$pos1+10);
                $pos1+=9;
                $pos2=strripos($html,"Sample Input");
                if(!$pos2)
                    $pos2=strripos($html,"Example input");
                $innerHtml=substr($html,$pos1,$pos2-$pos1);
            }
            else
            {
                $pos1=stripos($html,"</center>");
                $pos1=stripos($html,"</center>",$pos1+10);
                $pos1+=9;
                $pos2=strripos($html,"<hr>");
                $innerHtml=substr($html,$pos1,$pos2-$pos1);
            }


            $innerHtml=$this->filter($innerHtml,"http://acm.zju.edu.cn",array());
            $problem_info->description=$innerHtml;




            $problem_desc=$problem_info->generate();



            $compiler='{"1":"C (gcc 4.7.2)","2":"C++ (g++ 4.7.2)","3":"FPC (fpc 2.6.0)","4":"Java (java 1.7.0)","5":"Python (Python 2.7.3)","6":"Perl (Perl 5.14.2)","7":"Scheme (Guile 1.8.8)","8":"PHP (PHP 5.4.4)","9":"C++0x (g++ 4.7.2)"}';

            $time_limit=doubleval($this->getSubStr($html,"Time Limit: </font>","Secon",0))*1000;

            $memory_limit=doubleval($this->getSubStr($html,"Memory Limit: </font>","KB",0));


            $memory_limit=$memory_limit*1024;

            $problem_title=trim($this->getSubStr($html,'<span class="bigProblemTitle">',"</span>",0));
            $problem_url="http://acm.zju.edu.cn/onlinejudge/showProblem.do?problemCode=$problem_id";
            $this->model("vj_problem_model")->insertProblem(9,$problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function uva()
    {
        /** @var curlRequest $curl */
        $curl=$this->newClass("curlRequest");
        $curl->setHeader("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36");
        $curl->setHeader("Referer: https://uva.onlinejudge.org/");
        $curl->setHeader("Origin: https://uva.onlinejudge.org/");
        $curl->setHeader("Upgrade-Insecure-Requests: 1");
        $curl->setHeader("Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8");
        $curl->setFollowRedirect(true);
        $problem_start_id=intval(self::$cliArg['start']);
        $problem_end_id=intval(self::$cliArg['end']);
        for($problem_id=$problem_start_id;$problem_id<=$problem_end_id;$problem_id++)
        {
            $problem_list=array();
            while(!($html=$curl->get("https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem=$problem_id",10)))
            {
                echo "Retry $problem_id\n";
                sleep(1);
            }
            if(strpos($html,"Download as PDF")===false)
            {
                echo "Empty $problem_id\n";
                continue;
            }
            $content_start=strpos($html,"<!-- #col3: Main Content -->");
            $pdf_url='https://uva.onlinejudge.org/external' . $this->getSubStr($html,'<a href="external','">',$content_start);
            $problem_desc='<div id="pdf-div" data-pdf-url="' . str_replace('"',"\\\"",$pdf_url)  . '"></div>';

            $compiler='{"1":"ANSI C 5.3.0 - GNU C Compiler with options: -lm -lcrypt -O2 -pipe -ansi -DONLINE_JUDGE","2":"JAVA 1.8.0 - OpenJDK Java","3":"C++ 5.3.0 - GNU C++ Compiler with options: -lm -lcrypt -O2 -pipe -DONLINE_JUDGE","4":"PASCAL 3.0.0 - Free Pascal Compiler","5":"C++11 5.3.0 - GNU C++ Compiler with options: -lm -lcrypt -O2 -std=c++11 -pipe -DONLINE_JUDGE","6":"PYTH3 3.5.1 - Python 3"}';
            $problem_title=trim($this->getSubStr($html,' - ',"</h3>",$content_start));
            $time_limit=doubleval($this->getSubStr($html,"Time limit: "," seconds",$content_start))*1000;
            $real_problem_id=intval($this->getSubStr($html,'<h3>',' - ',$content_start));
            $memory_limit=0;

            $problem_url="https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem=$problem_id";
            $this->model("vj_problem_model")->insertProblem(10,$real_problem_id,$problem_title,$problem_desc,$problem_url,$time_limit,$memory_limit,$compiler);
            echo "Done $problem_id\n";

        }
    }
    function importCodeForcesTags()
    {

        $json=json_decode(file_get_contents("http://codeforces.com/api/problemset.problems"),true);
        $problems=$json['result']['problems'];
        $all_tags=array();
        foreach($problems as $one)
        {
            foreach($one['tags'] as $two)
            {
                $all_tags[$two]=0;
            }
        }
        foreach($all_tags as $key=>&$one)
        {
            $tag_info=$this->model("tag_model")->getTagInfoByTagAlias($key);
            if(!$tag_info)
            {
                $tag_id=$this->model("tag_model")->newTag($key,"");
                $this->model("tag_model")->addTagAlias($tag_id,$key);
                $tag_info=$this->model("tag_model")->getTagInfoByTagAlias($key);
            }
            $one=$tag_info['tag_id'];
        }
        foreach($problems as $one)
        {
            $problem_info=$this->model("vj_problem_model")->getProblemByOjIDAndProblemIdentity(1,$one['contestId'] . "/" . $one['index']);
            if(!$problem_info)  continue;
            $problem_id=$problem_info['problem_id'];

            foreach($one['tags'] as $two)
            {
                if(isset($all_tags[$two]))
                {
                    $this->model("tag_model")->addProblemTag($problem_id,$all_tags[$two]);
                }
            }
            echo $problem_id . " ". count($one['tags']) ."\n";
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

                if(substr($origin_src,0,4)!='data')
                {
                    if(substr($origin_src,0,4)!='http')
                        $origin_src=$oj_url . $origin_src;
                    $origin_src=str_replace('../','',$origin_src);
                }

                $value->getTag()->setAttribute('src',$origin_src);
            }


        });
        return strval($dom);
    }
    protected function getSubStr($str,$needle1,$needle2,$start_pos,$case_sensitive = true)
    {
        if($case_sensitive)
            $pos1=strpos($str,$needle1,$start_pos);
        else
            $pos1=stripos($str,$needle1,$start_pos);
        if($pos1===false) return false;
        if($case_sensitive)
            $pos2=strpos($str,$needle2,$pos1+strlen($needle1));
        else
            $pos2=stripos($str,$needle2,$pos1+strlen($needle1));
        if($pos2===false)   return false;
        return substr($str,$pos1+strlen($needle1),$pos2-$pos1-strlen($needle1));
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