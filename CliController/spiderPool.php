<?php
class spiderPool extends SlimvcControllerCli
{
    function startMainThread()
    {
        $start_time=time();
        $file_path=_Root . "index_cli.php";
        while(true)
        {
            $mothers=$this->model("vj_spider_model")->getSpiderMotherUnAlive(10);
            foreach($mothers as $one)
            {
                $this->model("vj_spider_model")->setSpiderMotherAlive($one['oj_id']);
                shell_exec("nohup php $file_path spiderPool startSpiderMother oj_id ". intval($one['oj_id']) ." > /dev/null 2>&1 &");//注意防shell注入
            }
            sleep(60);
            if(time()-$start_time>(60*20))//20分钟重启一次，避免数据库链接中断
                break;

        }
        shell_exec("nohup php $file_path spiderPool startMainThread");
    }
    function startSpiderMother()
    {
        $start_time=time();
        $file_path=_Root . "index_cli.php";
        $oj_id=intval(SlimvcControllerCli::$cliArg['oj_id']);
        $mother_info=$this->model("vj_spider_model")->getSpiderMotherInfo($oj_id);
        if(!$mother_info)   return;
        $last_check_time=0;
        while(true)
        {

            if(time()-$last_check_time>60)
            {
                $this->model("vj_spider_model")->setSpiderMotherAlive($oj_id);
                $spiders=$this->model("vj_spider_model")->getOjSpiderUnAlive($oj_id,10);
                foreach($spiders as $one)
                {
                    $this->model("vj_spider_model")->setSpiderAlive($one['spider_id']);
                    shell_exec("nohup php $file_path spiderPool startSpiderThread spider_id ". intval($one['spider_id']) ." > /dev/null 2>&1 &");//注意防shell注入
                }
                $last_check_time=time();
            }
            $jobs=$this->model("vj_job_model")->getOjNoSpiderJobs($oj_id);
            if(!empty($jobs))
            {
                $spiders=$this->model("vj_job_model")->getOjFreeSpider($oj_id,20);
                while(!empty($spiders) && !empty($jobs))
                {
                    usort($spiders,function($a,$b)
                    {
                        return $b['spider_looking_job']-$a['spider_looking_job'];
                    });

                    $sel_spider=array_pop($spiders);
                    $sel_job=array_pop($jobs);
                    $this->model("vj_job_model")->setJobSpider($sel_job['job_id'],$sel_spider['spider_id']);
                    $this->model("vj_spider_model")->addSpiderLookingJob($sel_spider['spider_id'],1);
                    $sel_spider['spider_looking_job']++;
                    if($sel_spider['spider_looking_job']<20)
                        $spiders[]=$sel_spider;
                }
                sleep(1);
            }
            if(time()-$start_time>(60*20))//20分钟重启一次，避免数据库链接中断
                break;
        }
        shell_exec("nohup php $file_path spiderPool startSpiderMother oj_id ". $oj_id ." > /dev/null 2>&1 &");//注意防shell注入
    }
    function startSpiderThread()
    {
        //to do
    }

}