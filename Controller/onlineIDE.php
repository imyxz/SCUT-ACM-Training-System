<?php
class onlineIDE extends SlimvcController
{
    function submitJob()
    {
        try {
            $json=$this->getRequestJson();
            $source_code=$json['source_code'];
            $input_code=$json['input_code'];
            if(empty($source_code)) throw new Exception("请输入运行代码");

            $request=array(
                "source_code"=>$source_code,
                "input_code"=>$input_code,
                "code_type"=>"cpp"
            );
            /** @var curlRequest $curl */
            $curl=$this->newClass("curlRequest");
            $response=$curl->post("http://c.yxz.me/api/newJob",json_encode($request),10);
            $response=json_decode($response,true);
            if(!$response || $response['status']==false)    throw new Exception("后端服务器错误");
            $return['job_id']=$response['jobid'];
            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }

    }

    function getJobResult()
    {
        try {
            $job_id=$_GET['job_id'];
            if($job_id<=0)  throw new Exception("job_id错误");
            /** @var curlRequest $curl */
            $curl=$this->newClass("curlRequest");
            $response=$curl->get("http://c.yxz.me/api/getJobDetailResult/jobid/$job_id",10);
            $response=json_decode($response,true);
            if(!$response)  throw new Exception("后端服务器错误");
            $return['result']=$response;
            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
}