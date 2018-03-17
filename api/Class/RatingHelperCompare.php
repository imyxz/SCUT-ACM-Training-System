<?php
/**
 * User: imyxz
 * Date: 2018-03-17
 * Time: 14:26
 * Github: https://github.com/imyxz/
 */
class RatingHelperCompare{
    const K = 16;
    private $selfRating;
    private $expectedScore;
    private $actualScore;
    private $compareCount;
    function __construct($selfRating)
    {
        $this->selfRating=$selfRating;
        $this->expectedScore=0;
        $this->actualScore=0;
        $this->compareCount=0;
    }
    function addComparer($rating,$isSelfWin)
    {
        if($isSelfWin)
        {
            $this->actualScore++;
        }
        $tmp=1+pow(10.0,($rating-$this->selfRating)/400.0);
        $this->expectedScore+=1/$tmp;
        $this->compareCount++;
    }
    function getRating()
    {
        $newRate = RatingHelperCompare::K * ($this->actualScore - $this->expectedScore);
        //return $this->selfRating + $newRate;
        return $this->selfRating+ $newRate / ($this->compareCount+1) * 4;
    }

}