<?php
/**
 * User: imyxz
 * Date: 2018-03-17
 * Time: 14:49
 * Github: https://github.com/imyxz/
 */
require_once('RatingHelperCompare.php');
class RatingHelper
{
    const INIT_SCORE = 1500;
    private $userRating=[];
    private $newRank=[];
    private $userNewRating=[];
    function __construct($cur_rating, $new_rank)
    {
        foreach($cur_rating as $one)
        {
            $this->userRating[$one['user_id']]=$one;
        }
        $this->newRank=$new_rank;
        foreach($new_rank as $one){
            if(!isset($this->userRating[$one[0]]))//加入新的选手
                $this->userRating[$one[0]]=['rating'=>RatingHelper::INIT_SCORE,
                    'user_id'=>$one[0],
                    'cur_rating_id'=>0];
        }
        for($i=0;$i<count($new_rank);$i++)
        {
            $me=$this->userRating[$new_rank[$i][0]];
            $helper=new RatingHelperCompare($me['rating']);
            for($x=0;$x<$i;$x++)
            {
                $target=$new_rank[$x][0];
                $helper->addComparer($this->getUserRating($target),false);
            }
            for($x=$i+1;$x<count($new_rank);$x++)
            {
                $target=$new_rank[$x][0];
                $helper->addComparer($this->getUserRating($target),true);
            }
            $this->userNewRating[]=[
                'user_id'=>$me['user_id'],
                'from_rating'=>$me['rating'],
                'to_rating'=>$helper->getRating(),
                'prev_rating_id'=>$me['cur_rating_id'],
                'rank'=>$new_rank[$i][1]
            ];
        }
    }
    function getNewRating()
    {
        return $this->userNewRating;
    }
    private function getUserRating($user_id)
    {
        return $this->userRating[$user_id]['rating'];
    }

}