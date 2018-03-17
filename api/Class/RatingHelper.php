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
        for($i=0;$i<count($new_rank);$i++)
        {
            if(!isset($this->userRating[$new_rank[$i][0]]))
                $me=['rating'=>intval(count($new_rank)/2 +1),
                'user_id'=>$new_rank[$i][0],
                'cur_rating_id'=>0];
            else
                $me=$this->userRating[$new_rank[$i][0]];

            $helper=new RatingHelperCompare($me['rating']);
            for($x=0;$x<$i;$x++)
            {
                $helper->addComparer($new_rank[$x][1],false);
            }
            for($x=$i+1;$x<count($new_rank);$x++)
            {
                $helper->addComparer($new_rank[$x][1],true);
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

}