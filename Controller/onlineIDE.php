<?php
class onlineIDE extends SlimvcController
{
    function submitJob()
    {
        try {
            $json=$this->getRequestJson();
            $source_code=$json['source_code'];
            $input_code=$json['input_code'];
            $code_type=$json['code_type'];
            if(empty($source_code)) throw new Exception("请输入运行代码");

            $request=array(
                "source_code"=>$source_code,
                "program_stdin"=>$input_code,
                "code_type"=>$code_type
            );
            /** @var curlRequest $curl */
            $curl=$this->newClass("curlRequest");
            $response=$curl->post("http://runcenter.yxz.me/jobAPI/newJob",json_encode($request),10);
            $response=json_decode($response,true);
            if(!$response)    throw new Exception("后端服务器错误");
            if($response['status']!=0)  throw new Exception("后端服务器返回:" . $response['err_msg']);
            $return['job_id']=$response['job_id'];
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
            $response=$curl->get("http://runcenter.yxz.me/jobAPI/getJobResult/job_id/$job_id",10);
            $response=json_decode($response,true);
            if(!$response)  throw new Exception("后端服务器错误");
            if($response['status']!=0)  throw new Exception("后端服务器返回:" . $response['err_msg']);
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
    function getCodeTypeDefaultCode()
    {
        try {
            $code_type=$_GET['codeType'];

            $return['code']="";
            switch($code_type)
            {
                case 0:
                    $return['code']="#include <stdio.h>\nint main()\n{\n    printf(\"Hello World!\\n\");\n}";
                    break;
                case 1:
                    $return['code']="#include <iostream>\nusing namespace std;\nint main()\n{\n    cout<<\"Hello World!\"<<endl;\n}";
                    break;
                case 2:
                    $return['code']="public class Main {\n    public static void main(String[] args){\n        System.out.println(\"Hello World!\");\n    }\n}";
                    break;
                case 3:
                    $return['code']="<?php\n    echo \"Hello World!\";\n?>";
                    break;
                case 4:
                    $return['code']="program hello;\nbegin\n  writeln ('Hello, World.')\nend.  ";
                    break;
                case 5:
                    $return['code']="print(\"Hello World!\")";
                    break;

            }
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
    function shareCode()
    {
        try {
            if($this->helper("user_helper")->isLogin()==false)
                $user_id=0;
            else
                $user_id=$this->helper("user_helper")->getUserID();
            $json=$this->getRequestJson();
            $source_code=$json['source_code'];
            $code_type=intval($json['code_type']);
            if(empty($source_code)) throw new Exception("代码不能为空！");
            if(!$share_id=$this->model("share_code_model")->newShareCode($user_id,$source_code,$code_type))   throw new Exception("系统出错");

            $return['share_id']=$share_id;
            $return['status'] = 0;
            $this->outputJson($return);

        }
        catch (Exception $e) {
            $return['status'] = 1;
            $return['err_msg'] = $e->getMessage();
            $this->outputJson($return);

        }
    }
    function getShareCode()
    {
        try {

            $id=intval($_GET['id']);
            $code=$this->model("share_code_model")->getShareCodeInfo($id);
            if(!$code ||$code['is_share']!=1) throw new Exception("无权限");
            $return['source_code']=$code['source_code'];
            $return['code_type']=$code['code_type'];
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
            $style="ansi";
            if($json['code_type']==2)
                $style="java";
            shell_exec("/usr/bin/astyle --style=$style --stdin=$input_file --stdout=$output_file");
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