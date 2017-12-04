<?php
class spiderPool extends SlimvcControllerCli
{
    protected $spider_type,$spider_id;
    function startMainThread()
    {
        $this->spider_type='MainThread';
        $this->spider_id=0;
        $start_time = time();
        $file_path = _Root . "index_cli.php";

        /** @var vj_spider_model $vj_spider_model */
        $vj_spider_model=$this->model("vj_spider_model");
        /** @var vj_job_model $vj_job_model */
        $vj_job_model=$this->model("vj_job_model");

        $this->log("started");
        while (true) {
            $mothers = $vj_spider_model->getSpiderMotherUnAlive(10);
            foreach ($mothers as &$one) {
                $this->log("start spider mother " . $one['oj_id']);

                $vj_spider_model->setSpiderMotherAlive($one['oj_id']);
                $this->startProcess("spiderPool startSpiderMother oj_id " . intval($one['oj_id']));
            }
            sleep(60);
            if (time() - $start_time > (60 * 20))//20分钟重启一次，避免数据库链接中断
                break;

        }
        $this->log("Restarting...");
        $this->startProcess("spiderPool startMainThread");
    }

    function startSpiderMother()
    {
        $start_time = time();
        $file_path = _Root . "index_cli.php";
        $oj_id = intval(SlimvcControllerCli::$cliArg['oj_id']);

        $this->spider_type='SpiderMother';
        $this->spider_id=$oj_id;

        /** @var vj_spider_model $vj_spider_model */
        $vj_spider_model=$this->model("vj_spider_model");
        /** @var vj_job_model $vj_job_model */
        $vj_job_model=$this->model("vj_job_model");;

        $mother_info = $vj_spider_model->getSpiderMotherInfo($oj_id);
        if (!$mother_info) return;
        $last_check_time = 0;
        /** @var jobRunningStatus $jobRunningStatus */
        $jobRunningStatus = $this->newClass("jobRunningStatus");
        $this->log("started");

        while (true) {

            if (time() - $last_check_time > 60) {
                $mother_info = $vj_spider_model->getSpiderMotherInfo($oj_id);
                if ($mother_info['is_enable'] != true)
                {
                    $this->log("Disable detected! exit");
                    return;
                }
                $vj_spider_model->setSpiderMotherAlive($oj_id);
                $spiders = $vj_spider_model->getOjSpiderUnAlive($oj_id, 10);
                foreach ($spiders as &$one) {
                    $this->log("start spider " . $one['spider_id']);

                    $vj_spider_model->setSpiderAlive($one['spider_id']);
                    $this->startProcess("spiderPool startSpiderThread spider_id " . intval($one['spider_id']));
                }
                $last_check_time = time();
            }
            $jobs = $vj_job_model->getOjNoSpiderJobs($oj_id);
            if (!empty($jobs)) {
                $spiders = $vj_spider_model->getOjFreeSpider($oj_id, 10);
                while (!empty($spiders) && !empty($jobs)) //循环分配工作，优先将工作分配给任务少的爬虫
                {
                    usort($spiders, function ($a, $b) {
                        return $b['spider_looking_job'] - $a['spider_looking_job'];
                    });

                    $sel_spider = array_pop($spiders);
                    $sel_job = array_pop($jobs);
                    $this->log("Assign job ". $sel_job['job_id'] . " to Spider " .$sel_spider['spider_id']);
                    $vj_job_model->setJobSpider($sel_job['job_id'], $sel_spider['spider_id']);
                    $vj_job_model->updateJobRunningStatus($sel_job['job_id'], $jobRunningStatus->DISTRIBUTED_TO_SPIDER);
                    $vj_spider_model->addSpiderLookingJob($sel_spider['spider_id'],1);
                    $sel_spider['spider_looking_job']++;
                    if ($sel_spider['spider_looking_job'] < 10)//每个爬虫最多手上有10个任务
                        $spiders[] = $sel_spider;
                }

            }
            sleep(1);
            if (time() - $start_time > (60 * 20))//20分钟重启一次，避免数据库链接中断
                break;
        }
        $this->log("Restarting...");
        $this->startProcess("spiderPool startSpiderMother oj_id " . $oj_id);
    }

    /**
     *
     */
    function startSpiderThread()
    {
        $start_time = time();
        $file_path = _Root . "index_cli.php";
        $spider_id = intval(SlimvcControllerCli::$cliArg['spider_id']);

        $this->spider_type='Spider';
        $this->spider_id=$spider_id;

        /** @var vj_spider_model $vj_spider_model */
        $vj_spider_model=$this->model("vj_spider_model");
        /** @var vj_job_model $vj_job_model */
        $vj_job_model=$this->model("vj_job_model");
        $spider_info = $vj_spider_model->getSpiderInfo($spider_id);
        if (!$spider_info) return;
        $last_check_time = 0;
        /** @var SpiderBasic $spider */
        $spider = $this->newClass($spider_info['spider_class_name']);
        $spider->setSpiderInfo($spider_info);
        /** @var jobRunningStatus $jobRunningStatus */
        $jobRunningStatus = $this->newClass("jobRunningStatus");
        /** @var acStatus $ac_status */
        $ac_status = $this->newClass("acStatus");
        $this->log("started");

        while (true) {

            if (time() - $last_check_time > 60) {
                $spider_info = $vj_spider_model->getSpiderInfo($spider_id);
                if ($spider_info['spider_enable'] != true)
                {
                    $this->log("Disable detected! exit");
                    return;
                }
                if($spider_info['need_reboot']==1)
                {
                    $this->log("Reboot detected! exit");
                    $vj_spider_model->setSpiderNeedReboot($spider_id,0);
                    break;
                }
                $vj_spider_model->setSpiderAlive($spider_id);
                $last_check_time = time();
            }
            $limit_job_count=$spider->getJobLimit();
            $query_job_count=$vj_spider_model->getSpiderQueryingJob($spider_id);
            $job_torun=0;
            $job_success=0;
            if($limit_job_count > $query_job_count)
            {
                $jobs = $vj_job_model->getSpiderUnSubmitJobs($spider_id,$limit_job_count - $query_job_count);
                $job_torun = count($jobs);
                $job_success = 0;
                foreach ($jobs as &$one) {
                    $problem_info = $this->model("vj_problem_model")->getProblemInfo($one["problem_id"]);
                    $spider->setSubmitJobInfo($one, $problem_info);
                    if($one['submit_count']>=10)//交太多次了，结束一下
                    {
                        $vj_job_model->updateJobRunningStatus($one['job_id'], $jobRunningStatus->FINISH);
                        $vj_spider_model->addSpiderLookingJob($spider_id,-1);
                        $vj_job_model->updateJobResultInfo($one['job_id'], $ac_status->FAILED, "Submit faild", 0, 0, "");
                    }
                    else
                    {
                        $vj_job_model->addJobSubmitCount($one['job_id']);
                        if ($spider->submitJob()) {
                            /** @var submitResult $result_info */
                            $result_info = $spider->getSubmitResult();
                            if($vj_job_model->isRemoteIdExist($one['oj_id'],$result_info->remote_run_id))
                            {
                                $this->log("Duplicate remote id on job " . $one['job_id'] . ', will resubmit later');
                                continue;
                            }
                            $vj_job_model->updateRemoteRunID($one['job_id'], $result_info->remote_run_id);
                            $vj_job_model->updateJobRunningStatus($one['job_id'], $jobRunningStatus->SUBMITTED_WAITING_RESULT);
                            $job_success++;
                            $this->log("Submit job " . $one['job_id'] . " succeed!");
                        }
                        else
                        {
                            $this->log("Submit job " . $one['job_id'] . " faild!");
                            break;//阿婆跑得快，一定有古怪
                        }

                    }

                }
            }


            $jobs = $vj_job_model->getSpiderWatchingJobs($spider_id);
            $watch_job = array();
            foreach ($jobs as &$one) {
                $watch_job[$one['remote_run_id']] = $one['job_id'];
            }
            if (!empty($watch_job)) {
                $spider->setQueryJobInfo($watch_job);
                if ($spider->queryJob()) {
                    $result_info = $spider->getQueryResult();
                    foreach ($result_info as &$one) {
                        /** @var jobResult $one */
                        $vj_job_model->updateJobResultInfo($one->job_id, $one->ac_status, $one->wrong_info, $one->time_usage, $one->ram_usage, $one->result_info);
                        $this->log("Get Job " . $one->job_id . " status");
                        if ($one->ac_status != $ac_status->TESTING) {
                            $vj_job_model->updateJobRunningStatus($one->job_id, $jobRunningStatus->FINISH);
                            $vj_spider_model->addSpiderLookingJob($spider_id,-1);
                            $this->log("Job " . $one->job_id . " finish " . $one->ac_status);

                        }

                    }
                }
            }
            if ($job_torun > 0 && $job_success == 0)//检查登录态
            {
                if ($spider->checkLogin() == false) {
                    $this->log("Spider offline. ReLogining...");
                    if($spider->login())
                    {
                        $vj_spider_model->updateSpiderLoginTime($spider_id);
                        $this->log("ReLogin Succeed!");

                    }
                    else
                        $this->log("ReLogin Faild!");

                }
            }
            if ($spider->isAdditionInfoUpdated())
                $vj_spider_model->updateSpiderAdditionInfo($spider_id, $spider->getAdditionInfo());
            sleep(1);
            if (time() - $start_time > (60 * 20))//20分钟重启一次，避免数据库链接中断
                break;
        }
        $this->log("Restarting...");
        $this->startProcess("spiderPool startSpiderThread spider_id " . $spider_id);
    }

    protected function startProcess($cmd, $output_path = '/home/log.txt')
    {
        $file_path = _Root . "index_cli.php";
        shell_exec("nohup php $file_path $cmd >> $output_path 2>&1 &");//注意防shell注入

    }

    protected function log($str)
    {
        echo $this->spider_type . " " . $this->spider_id . ": ".$str . "\n";
    }



}