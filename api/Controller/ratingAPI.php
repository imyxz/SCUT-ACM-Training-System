<?php
/**
 * User: imyxz
 * Date: 2018-03-17
 * Time: 13:43
 * Github: https://github.com/imyxz/
 */
class ratingAPI extends SlimvcController
{
    function getRank()
    {
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $return['rank']=$rating_model->getRank();
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getRankByGroup(){
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $return['rank']=$rating_model->getRankByGroup();
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getGroupPlayers(){
        try{
            $id = intval($_GET['id']);
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            /** @var group_model $group_model */
            $group_model=$this->model("group_model");
            $group_info=$group_model->getGroupInfo($id);
            if(!$group_info)  throw new Exception("No such group!");
            $return['group_name'] = $group_info['group_name'];
            $return['players']=$rating_model->getGroupPlayers($id);
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getList()
    {
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $return['list']=$rating_model->getList();
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getRatingInfo()
    {
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $id=intval($_GET['id']);

            $info=$rating_model->getRatingInfo($id);
            if(!$info)  throw new Exception("没有此排位赛");
            $return['info']=$info;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getUserRatingHistory()
    {
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            /** @var user_model $user_model */
            $user_model = $this->model("user_model");
            $user_id=intval($_GET['id']);

            $info=$rating_model->getUserRatingHistory($user_id);
            if(!$info)  throw new Exception("查无此人");
            $user_info=$user_model->getUserInfo($user_id);
            if(!$user_info) throw new Exception("查无此人");
            $return['nickname']=$user_info['user_nickname'];
            $return['history']=$info;
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function getContestRatingHistory()
    {
        try{
            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $rating_id=intval($_GET['id']);

            $info=$rating_model->getContestRatingHistory($rating_id);
            if(!$info)  throw new Exception("没有此排位赛");
            $rating_info=$rating_model->getRatingInfo($rating_id);
            $return['history']=$info;
            $return['contest_name']=$rating_info['contest_name'];
            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);

        }
    }
    function addNewRating()
    {
        try{


            /** @var rating_model $rating_model */
            $rating_model=$this->model("rating_model");
            $json=$this->getRequestJson();
            $contest_id=intval($json['contest_id']);
            $contest_name=$json['contest_name'];
            $is_in_system=boolval($json['is_in_system']);
            $is_preview=boolval($json['is_preview']);
            $rank=$json['rank'];
            //user_id ranking penalty
            require_once(_Class . 'RatingHelper.php');
            $cur_rating=$rating_model->getRank();
            $helper=new RatingHelper($cur_rating,$rank);
            if($is_preview)
            {
                $return['preview']=$helper->getNewRating();
            }
            else
            {
                if($this->helper("user_helper")->isLogin()==false)  throw new Exception('请先登录');
                if($this->helper("user_helper")->getUserID()!=1)   throw new Exception('您没有权限');
                $rating = $helper->getNewRating();
                $rating_id=$rating_model->addRating($is_in_system,$contest_id,$contest_name,json_encode($rank));
                if(!$rating_id)  throw new Exception("系统错误，请重试！");

                foreach($rating as $one)
                {
                    $rating_model->addRatingHistory($rating_id,$one['user_id'],intval($one['rank']),$one['prev_rating_id'],$one['from_rating'],$one['to_rating']);
                    $rating_model->updateUserRating($one['user_id'],$one['to_rating'],$rating_id);
                }
            }

            $return['status']=0;
            $this->outputJson($return);

        }catch(Exception $e)
        {
            $return['status']=1;
            $return['err_msg']=$e->getMessage();
            $this->outputJson($return);
        }
    }
}