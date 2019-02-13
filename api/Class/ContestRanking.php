<?php
/**
 * User: imyxz
 * Date: 2018-03-16
 * Time: 21:34
 * Github: https://github.com/imyxz/
 */
class ContestRanking{
    private $submitLog;
    private $groupByUserId;
    function init($submitLog){
        $this->submitLog = $submitLog;
    }
    private function updateGroupByUserId(){
        $group=[];
        foreach($this->submitLog as $one) {
            list($user_id,$problem_index,$submit_time,$ac_status,$runjob_id) = $one;
            if(!isset($group[$user_id]))    $group[$user_id]=array("submissions"=>[]);
        }
    }
}
