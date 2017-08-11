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
    function saveDraft()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("您未登录，无法保存代码");
            $json=$this->getRequestJson();
            $title=@$json['draft_title'];
            if($title==null)    $title=' ';
            $source_code=$json['source_code'];
            $is_autosave=$json['is_autosave'];
            if(empty($source_code)) throw new Exception("代码不能为空！");
            if($is_autosave==true)
                $is_autosave=true;
            else
                $is_autosave=false;
            $user_id=$this->helper("user_helper")->getUserID();
            if(!$draft_id=$this->model("draft_model")->newDraft($user_id,$title,$source_code,$is_autosave))   throw new Exception("系统出错");

            $return['draft_id']=$draft_id;
            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getUserDraft()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("您未登录，无法读取代码列表");
            $user_id=$this->helper("user_helper")->getUserID();

            $return['drafts']=$this->model("draft_model")->getUserDraft($user_id,30);
            $return['autosave']=$this->model("draft_model")->getUserAutoSave($user_id,30);

            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getDraftCode()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false) throw new Exception("您未登录，无法读取代码");
            $user_id=$this->helper("user_helper")->getUserID();
            $id=intval($_GET['id']);
            $draft=$this->model("draft_model")->getDraftInfo($id);
            if($draft['user_id']!=$user_id) throw new Exception("无权限");
            $return['code']=$draft['source_code'];
            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function formatCode()
    {
        try {
            $json=$this->getRequestJson();
            $code=$json['source_code'];
            $input_file=tempnam(sys_get_temp_dir(),"scutvj") . ".cpp";
            $output_file=tempnam(sys_get_temp_dir(),"scutvj") . ".cpp";
            if(strlen($code)>65536) throw new Exception("代码长度不得超过64k");
            file_put_contents($input_file,$code);
            shell_exec("/usr/bin/astyle --style=ansi --stdin=$input_file --stdout=$output_file");
            $return['format_code']=file_get_contents($output_file);
            if(empty($return['format_code']))   throw new Exception("原因未知！");
            @unlink($input_file);
            @unlink($output_file);
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