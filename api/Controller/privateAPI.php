<?php
/**
 * User: imyxz
 * Date: 2017-08-12
 * Time: 0:42
 * Github: https://github.com/imyxz/
 */
class privateAPI extends SlimvcController
{
    function getBingPic()
    {
        $json=file_get_contents("http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1");
        $json=json_decode($json,true);
        if(isset($json['images'][0]))
        {
            $this->model("bg_pic_model")->insertPic('https://www.bing.com' . $json['images'][0]['url']);
        }
        echo $json['images'][0]['url'];
    }
    private function checkReJudge()
    {
        /** @var contest_model $model */
        $model=$this->model("contest_model");
        $result=$model->query("update run_job set running_status=1  where running_status=3 and ac_status=0 and wrong_info like '%Judgement failed%'");
        var_dump($result);
        return "done\n";
    }
}